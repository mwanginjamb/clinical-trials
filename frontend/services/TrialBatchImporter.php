<?php

namespace frontend\services;

use frontend\models\ClinicalTrial;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use yii\db\ActiveRecord;

/**
 * Validates and imports a bulk-upload workbook (layout: TrialImportSchema).
 *
 * - Columns are read by HEADER NAME, so reordered or extra columns don't shift data.
 * - Coded columns accept the label ("Registered", "Kenya") or the stored id.
 * - Every problem in a trial is reported (not just the first), with sheet, row and column.
 * - Duplicate protocol numbers (already in the system, or repeated in the file) are skipped.
 * - Child-sheet rows whose trial_ref matches no trial are reported, not silently dropped.
 *
 * Everything runs in one outer transaction with a savepoint per trial: a failing trial rolls
 * back only its own rows. With $dryRun = true the outer transaction is always rolled back, so
 * validation goes through exactly the same model rules, behaviours and DB constraints as a
 * real import without saving anything.
 */
class TrialBatchImporter
{
    private TrialImportResult $result;

    /** @var array<string, array<int|string,string>> lookup => [stored value => label] */
    private array $lookups = [];

    /** @var array<string, array<string, array<int|string>>> lookup => [normalised label|id => stored values] */
    private array $lookupIndex = [];

    /** @var array<int, string[]> spl_object_id(model) => attributes whose cell value could not be converted */
    private array $unconverted = [];

    public function import(string $path, bool $dryRun): TrialImportResult
    {
        $this->result = new TrialImportResult($dryRun);

        $spreadsheet = $this->load($path);
        if ($spreadsheet === null) {
            return $this->result;
        }

        $sheets = [];
        foreach (array_keys(TrialImportSchema::sheets()) as $name) {
            $sheets[$name] = $this->readSheet($spreadsheet, $name);
        }
        if ($sheets[TrialImportSchema::SHEET_TRIALS] === null) {
            $this->result->addFileIssue("The workbook has no '" . TrialImportSchema::SHEET_TRIALS . "' sheet. Download a fresh template.", 'fatal');
            return $this->result;
        }

        $this->buildLookups();
        $trialRows = $this->registerTrials($sheets[TrialImportSchema::SHEET_TRIALS]);
        if ($this->result->hasFatal()) {
            return $this->result;
        }
        $this->skipDuplicateProtocols($trialRows);
        $childRows = $this->groupChildRows($sheets);

        $db = Yii::$app->db;
        $outer = $db->beginTransaction();
        try {
            foreach ($trialRows as $ref => [$rowNum, $values]) {
                if ($this->result->trialStatus($ref) !== TrialImportResult::STATUS_SKIPPED) {
                    $this->importTrial((string) $ref, $rowNum, $values, $childRows);
                }
            }
            $dryRun ? $outer->rollBack() : $outer->commit();
        } catch (\Throwable $e) {
            $outer->rollBack();
            Yii::error($e, __METHOD__);
            $this->result->addFileIssue('Import aborted, nothing was saved: ' . $e->getMessage(), 'fatal');
        }

        return $this->result;
    }

    /* ─────────────────────────── Reading ─────────────────────────── */

    private function load(string $path): ?Spreadsheet
    {
        try {
            $type = IOFactory::identify($path);
            if (!in_array($type, ['Xlsx', 'Xls'], true)) {
                $this->result->addFileIssue('This is not an Excel workbook. Upload the .xlsx template (CSV and other formats are not supported).', 'fatal');
                return null;
            }
            $reader = IOFactory::createReader($type);
            $reader->setReadDataOnly(true);
            // Formatted-but-empty cells (whole formatted columns, dropdown ranges) are skipped,
            // otherwise a styled template loads tens of thousands of empty cells.
            $reader->setReadEmptyCells(false);

            // Load only our sheets that are present (asking for a missing one makes load() fail).
            $present = array_values(array_intersect(array_keys(TrialImportSchema::sheets()), $reader->listWorksheetNames($path)));
            if (!in_array(TrialImportSchema::SHEET_TRIALS, $present, true)) {
                $this->result->addFileIssue("The workbook has no '" . TrialImportSchema::SHEET_TRIALS . "' sheet. Download a fresh template.", 'fatal');
                return null;
            }
            $reader->setLoadSheetsOnly($present);
            return $reader->load($path);
        } catch (\Throwable $e) {
            $this->result->addFileIssue('The file could not be read as an Excel workbook. Re-save it as .xlsx and try again.', 'fatal');
            return null;
        }
    }

    /**
     * @return array<int, array<string,mixed>>|null  sheet row number => [header => raw value]; null if sheet missing
     */
    private function readSheet(Spreadsheet $spreadsheet, string $name): ?array
    {
        $ws = $spreadsheet->getSheetByName($name);
        if ($ws === null) {
            return null;
        }
        $columns = TrialImportSchema::columns($name);
        // Only the range that actually holds data (not the formatted/dropdown range).
        $grid = $ws->rangeToArray('A1:' . $ws->getHighestDataColumn() . $ws->getHighestDataRow(), null, true, false, false);
        $headerRow = array_shift($grid) ?? [];

        // Map spreadsheet column index => schema header, by name (case/space-insensitive).
        $known = [];
        foreach (array_keys($columns) as $header) {
            $known[strtolower($header)] = $header;
        }
        $map = [];
        foreach ($headerRow as $i => $cell) {
            $key = strtolower(trim((string) $cell));
            if ($key === '') {
                continue;
            }
            if (isset($known[$key])) {
                $map[$i] = $known[$key];
            } else {
                $this->result->addFileIssue("{$name}: column '{$cell}' is not part of the template and was ignored.", 'warning');
            }
        }
        foreach ($columns as $header => $def) {
            if (!in_array($header, $map, true) && TrialImportSchema::isRequired($def)) {
                $this->result->addFileIssue("{$name}: required column '{$header}' is missing.", 'error');
            }
        }

        $rows = [];
        foreach ($grid as $i => $cells) {
            $assoc = [];
            $blank = true;
            foreach ($map as $index => $header) {
                $value = $cells[$index] ?? null;
                if (is_string($value)) {
                    $value = trim($value);
                }
                if ($value !== null && $value !== '') {
                    $blank = false;
                }
                $assoc[$header] = $value;
            }
            if (!$blank) {
                $rows[$i + 2] = $assoc; // +1 for 0-index, +1 for the header row
            }
        }

        return $rows;
    }

    /* ─────────────────────────── Pre-checks ─────────────────────────── */

    /** @return array<string, array{0:int,1:array}> ref => [row, values] */
    private function registerTrials(array $rows): array
    {
        $trials = [];
        $exampleSkipped = false;
        foreach ($rows as $rowNum => $values) {
            $ref = $this->refOf($values);
            if (strcasecmp($ref, TrialImportSchema::EXAMPLE_REF) === 0) {
                $exampleSkipped = true;
                continue;
            }
            if ($ref === '') {
                $this->result->addFileIssue("Trials row {$rowNum}: trial_ref is empty, so the row was not imported.");
                continue;
            }
            if (isset($trials[$ref])) {
                $this->result->addFileIssue("Trials row {$rowNum}: trial_ref '{$ref}' is already used on row {$trials[$ref][0]}; this row was not imported.");
                continue;
            }
            $trials[$ref] = [$rowNum, $values];
            $this->result->addTrial($ref, $rowNum, (string) ($values['scientific_title'] ?? ''));
        }

        if ($exampleSkipped) {
            $this->result->addFileIssue("Rows with trial_ref '" . TrialImportSchema::EXAMPLE_REF . "' are the template's worked example and were skipped.", 'info');
        }
        if (!$trials && !$this->result->getFileIssues()) {
            $this->result->addFileIssue('The Trials sheet has no data rows.', 'fatal');
        } elseif (!$trials) {
            $this->result->addFileIssue('No trials to import.', 'fatal');
        }
        if (count($trials) > TrialImportSchema::MAX_TRIALS) {
            $this->result->addFileIssue('The file has ' . count($trials) . ' trials; the limit is ' . TrialImportSchema::MAX_TRIALS . ' per upload. Split it into smaller files.', 'fatal');
        }

        return $trials;
    }

    private function skipDuplicateProtocols(array $trialRows): void
    {
        $byProtocol = [];
        foreach ($trialRows as $ref => [$rowNum, $values]) {
            $pn = $this->text($values['protocol_number'] ?? null);
            if ($pn === null) {
                continue;
            }
            $key = mb_strtolower($pn);
            if (isset($byProtocol[$key])) {
                [$firstRef, $firstRow] = $byProtocol[$key];
                $this->result->skipTrial((string) $ref, "Skipped: protocol_number '{$pn}' is also used by trial_ref '{$firstRef}' (Trials row {$firstRow}).");
                continue;
            }
            $byProtocol[$key] = [(string) $ref, $rowNum];
        }
        if (!$byProtocol) {
            return;
        }

        $existing = ClinicalTrial::find()
            ->select(['id', 'protocol_number'])
            ->where(['protocol_number' => array_map(fn($k) => $k, array_keys($byProtocol))])
            ->asArray()
            ->all();
        foreach ($existing as $row) {
            $key = mb_strtolower((string) $row['protocol_number']);
            if (isset($byProtocol[$key])) {
                $this->result->skipTrial($byProtocol[$key][0], "Skipped: protocol_number '{$row['protocol_number']}' already exists (trial #{$row['id']}).");
            }
        }
    }

    /** @return array<string, array<string, array<int, array>>> sheet => ref => [row => values] */
    private function groupChildRows(array $sheets): array
    {
        $grouped = [];
        foreach (TrialImportSchema::CHILD_SHEETS as $sheet => [, $max, $label]) {
            foreach ($sheets[$sheet] ?? [] as $rowNum => $values) {
                $ref = $this->refOf($values);
                if (strcasecmp($ref, TrialImportSchema::EXAMPLE_REF) === 0) {
                    continue;
                }
                if ($ref === '') {
                    $this->result->addFileIssue("{$sheet} row {$rowNum}: trial_ref is empty, so the row was ignored.");
                    continue;
                }
                if (!$this->result->hasTrial($ref)) {
                    $this->result->addFileIssue("{$sheet} row {$rowNum}: trial_ref '{$ref}' does not match any row on the Trials sheet, so the row was ignored.");
                    continue;
                }
                $grouped[$sheet][$ref][$rowNum] = $values;
            }

            if ($max !== null) {
                foreach ($grouped[$sheet] ?? [] as $ref => $rows) {
                    if (count($rows) > $max && $this->result->trialStatus($ref) !== TrialImportResult::STATUS_SKIPPED) {
                        $this->result->addTrialError($ref, "{$label}: only {$max} row per trial is allowed, found " . count($rows)
                            . " ({$sheet} rows " . implode(', ', array_keys($rows)) . ').');
                    }
                }
            }
        }
        return $grouped;
    }

    /* ─────────────────────────── Import one trial ─────────────────────────── */

    private function importTrial(string $ref, int $rowNum, array $values, array $childRows): void
    {
        $sheetTrials = TrialImportSchema::SHEET_TRIALS;
        $columns = TrialImportSchema::columns($sheetTrials);
        $savepoint = Yii::$app->db->beginTransaction();

        try {
            $trial = new ClinicalTrial();
            $trial->loadDefaultValues();
            $trialCols = $this->fill($trial, $columns, $values, $ref, "{$sheetTrials} row {$rowNum}");
            $trialSaved = $this->saveOrValidate($trial, null, $trialCols, $ref, 'Trial', "{$sheetTrials} row {$rowNum}");

            $children = [];
            foreach (TrialImportSchema::TRIAL_SHEET_CHILDREN as $class => $label) {
                $classCols = array_filter($columns, fn($d) => ($d['model'] ?? null) === $class);
                if ($this->allBlank(array_intersect_key($values, $classCols))) {
                    continue; // section left empty: that wizard step can be completed later
                }
                $model = new $class();
                $model->loadDefaultValues();
                $cols = $this->fill($model, $classCols, $values, $ref, "{$sheetTrials} row {$rowNum}");
                if ($this->saveOrValidate($model, $trialSaved ? $trial : null, $cols, $ref, $label, "{$sheetTrials} row {$rowNum}")) {
                    $children[$label] = 1;
                }
            }

            foreach (TrialImportSchema::CHILD_SHEETS as $sheet => [$class, , $label]) {
                $sheetCols = TrialImportSchema::columns($sheet);
                foreach ($childRows[$sheet][$ref] ?? [] as $childRow => $childValues) {
                    $model = new $class();
                    $model->loadDefaultValues();
                    $cols = $this->fill($model, $sheetCols, $childValues, $ref, "{$sheet} row {$childRow}");
                    if ($this->saveOrValidate($model, $trialSaved ? $trial : null, $cols, $ref, $label, "{$sheet} row {$childRow}")) {
                        $children[$label] = ($children[$label] ?? 0) + 1;
                    }
                }
            }

            if ($this->result->trialStatus($ref) === TrialImportResult::STATUS_OK) {
                $savepoint->commit();
                $this->result->setSaved($ref, $trial->id, $children);
            } else {
                $savepoint->rollBack();
            }
        } catch (\Throwable $e) {
            $savepoint->rollBack();
            Yii::error($e, __METHOD__);
            $this->result->addTrialError($ref, 'Could not be saved: ' . $e->getMessage());
        }
    }

    /**
     * Copies converted cell values onto the model.
     * @return array<string,string> attribute => header, for error messages
     */
    private function fill(ActiveRecord $model, array $columns, array $values, string $ref, string $where): array
    {
        $attrToHeader = [];
        foreach ($columns as $header => $def) {
            if (($def['model'] ?? null) !== get_class($model)) {
                continue;
            }
            $attrToHeader[$def['attr']] = $header;
            $error = null;
            $value = $this->convert($def, $values[$header] ?? null, $error);
            if ($error !== null) {
                $this->result->addTrialError($ref, "{$where}, {$header}: {$error}");
                $this->unconverted[spl_object_id($model)][] = $def['attr'];
                continue;
            }
            if (is_int($value) && $this->isStringAttribute($model, $def['attr'])) {
                // Some coded attributes (e.g. type_of_eligibility) are validated as strings,
                // matching what the wizard's dropdown posts ("1"), so store the code as a string.
                $value = (string) $value;
            }
            $model->setAttribute($def['attr'], $value);
        }
        return $attrToHeader;
    }

    private function isStringAttribute(ActiveRecord $model, string $attr): bool
    {
        foreach ($model->getActiveValidators($attr) as $validator) {
            if ($validator instanceof \yii\validators\StringValidator) {
                return true;
            }
        }
        return false;
    }

    /**
     * Saves the model under $trial, or (when the trial itself could not be saved) only
     * validates the imported columns so the report still lists every problem.
     */
    private function saveOrValidate(ActiveRecord $model, ?ClinicalTrial $trial, array $attrToHeader, string $ref, string $label, string $where): bool
    {
        $isTrial = $model instanceof ClinicalTrial;
        if (!$isTrial && $trial !== null) {
            $model->setAttribute('trial_id', $trial->id);
        }

        $ok = ($isTrial || $trial !== null) ? $model->save() : $model->validate(array_keys($attrToHeader));
        if (!$ok) {
            $alreadyReported = $this->unconverted[spl_object_id($model)] ?? [];
            foreach ($model->getFirstErrors() as $attr => $message) {
                if (in_array($attr, $alreadyReported, true)) {
                    continue; // e.g. "'maybe' is not a recognised value" already explains the blank
                }
                $column = $attrToHeader[$attr] ?? $attr;
                $this->result->addTrialError($ref, "{$label} ({$where}), {$column}: {$message}");
            }
        }
        return $ok && ($isTrial || $trial !== null);
    }

    /* ─────────────────────────── Value conversion ─────────────────────────── */

    /** @return mixed converted value; sets $error on bad input */
    private function convert(array $def, $raw, ?string &$error)
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        switch ($def['type']) {
            case 'int':
                $n = $this->number($raw);
                if ($n === null || floor($n) != $n) {
                    $error = "'{$raw}' must be a whole number.";
                    return null;
                }
                return (int) $n;

            case 'number':
                $n = $this->number($raw);
                if ($n === null) {
                    $error = "'{$raw}' must be a number.";
                }
                return $n;

            case 'date':
                return $this->date($raw, $error);

            case 'doi':
                $s = $this->text($raw);
                $s = preg_replace('~^doi:\s*~i', '', $s);
                return preg_match('~^10\.\d{4,9}/\S+$~', $s) ? 'https://doi.org/' . $s : $s;

            case 'lookup':
                return $this->resolveLookup($def['lookup'], $raw, $error);

            default:
                return $this->text($raw);
        }
    }

    /** Text without Excel artefacts: 463.0 → "463". */
    private function text($raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        if (is_float($raw) && floor($raw) == $raw && abs($raw) < 1e15) {
            return (string) (int) $raw;
        }
        $s = trim((string) $raw);
        return $s === '' ? null : $s;
    }

    private function number($raw): ?float
    {
        if (is_int($raw) || is_float($raw)) {
            return (float) $raw;
        }
        $s = str_replace([',', ' ', "\u{00A0}"], '', (string) $raw);
        return is_numeric($s) ? (float) $s : null;
    }

    /** Excel date serials, YYYY-MM-DD or DD/MM/YYYY → Y-m-d. */
    private function date($raw, ?string &$error): ?string
    {
        if (is_int($raw) || is_float($raw)) {
            if ($raw > 0 && $raw < 2958466) { // Excel's valid date range
                return ExcelDate::excelToDateTimeObject($raw)->format('Y-m-d');
            }
        } else {
            $s = trim((string) $raw);
            foreach (['Y-m-d', 'd/m/Y', 'Y/m/d', 'd-m-Y'] as $format) {
                $d = \DateTime::createFromFormat('!' . $format, $s);
                if ($d !== false && $d->format($format) === $s) {
                    return $d->format('Y-m-d');
                }
            }
        }
        $error = "'{$raw}' is not a valid date. Use YYYY-MM-DD, e.g. 2026-09-01.";
        return null;
    }

    private function buildLookups(): void
    {
        $this->lookups = TrialImportSchema::lookups();
        foreach ($this->lookups as $name => $options) {
            foreach ($options as $value => $label) {
                $this->lookupIndex[$name][$this->norm((string) $label)][] = $value;
                $this->lookupIndex[$name]['#' . $value][] = $value; // match by id/stored value
            }
        }
        // Common spellings for yes/no columns.
        foreach (['y' => 1, 'true' => 1, 'n' => 0, 'false' => 0] as $word => $value) {
            $this->lookupIndex['yesNo'][$word][] = $value;
        }
    }

    private function resolveLookup(string $name, $raw, ?string &$error)
    {
        $text = $this->text($raw);
        $matches = $this->lookupIndex[$name][$this->norm($text)]
            ?? $this->lookupIndex[$name]['#' . $text]
            ?? $this->lookupIndex[$name]['#' . strtoupper($text)] // currency codes
            ?? [];
        $matches = array_values(array_unique($matches, SORT_REGULAR));

        if (count($matches) === 1) {
            return $matches[0];
        }
        if (count($matches) > 1) {
            $error = "'{$text}' matches several entries (ids " . implode(', ', $matches) . '). Enter the id instead.';
            return null;
        }

        $labels = array_values($this->lookups[$name] ?? []);
        $sample = implode(', ', array_slice($labels, 0, 8)) . (count($labels) > 8 ? ', …' : '');
        $error = $labels
            ? "'{$text}' is not a recognised value. Use one of: {$sample} (full list on the Lookups sheet)."
            : "'{$text}' cannot be matched: this list is empty in the system. Add entries first (Lookups menu).";
        return null;
    }

    private function norm(?string $s): string
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', trim((string) $s)));
    }

    private function refOf(array $values): string
    {
        return (string) $this->text($values[TrialImportSchema::REF] ?? null);
    }

    private function allBlank(array $values): bool
    {
        foreach ($values as $v) {
            if ($v !== null && $v !== '') {
                return false;
            }
        }
        return true;
    }
}
