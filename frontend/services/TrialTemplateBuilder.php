<?php

namespace frontend\services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Builds the bulk-import workbook from TrialImportSchema, so headers, required markers,
 * dropdowns and lookup lists always match what TrialBatchImporter accepts.
 */
class TrialTemplateBuilder
{
    /** Rows that get dropdowns / text formatting. */
    private const LAST_ROW = 1000;

    private const COLOR_HEADER = '1F4E78';
    private const COLOR_REQUIRED = 'C00000';
    private const COLOR_REF = 'BF9000';
    private const COLOR_EXAMPLE = 'E2EFDA';
    private const COLOR_EXAMPLE_REF = 'FFF2CC';

    /** Sample values for the EXAMPLE row (lookup columns are filled from live lookup data). */
    private const EXAMPLE_VALUES = [
        'scientific_title' => 'Efficacy of Drug X in Adult Asthma',
        'public_title' => 'Drug X for Asthma Study',
        'scientific_acronym' => 'DXAS-1',
        'protocol_version' => 'v1.0',
        'registration_status' => 'Registered',
        'protocol_number' => 'PRO-2026-001',
        'registration_number' => 'REG-2026-0001',
        'timeline_study_duration' => 24,
        'timeline_study_site_location' => 'Nairobi Hospital Site A',
        'timeline_centre_postal_address' => 'PO Box 100, Nairobi',
        'timeline_anticipated_start_date' => '2026-09-01',
        'timeline_anticipated_end_date' => '2028-09-01',
        'timeline_recruitment_status' => 'Recruiting',
        'timeline_centre_pysical_address' => '123 Hospital Rd, Nairobi',
        'ethics_ethical_regulatory_body' => 'Internal Review Board',
        'ethics_approved_by_ethical_committee' => 'Yes',
        'ethics_document_number' => 'ETH/2026/001',
        'funding_sponsor_name' => 'Acme Health Foundation',
        'funding_amount' => 150000,
        'funding_currency' => 'KES',
        'funding_sector' => 'Non-Profit',
        'description_study_website' => 'https://trial-x.example.org',
        'description_lay_summary' => 'This study tests whether Drug X helps adults with asthma.',
        'description_scientific_summary' => 'A randomised, double-blind, placebo-controlled trial of Drug X in adult asthma.',
        'intervention_name' => 'Drug X 10mg',
        'intervention_description' => 'Oral tablet once daily for 12 weeks',
        'intervention_control_comparator' => 'Placebo tablet',
        'intervention_type_of_outcome' => 'Primary',
        'intervention_outcome_description' => 'Change in FEV1 at week 12',
        'results_permission_to_publish' => 'Yes',
        'results_summary_results' => 'Drug X reduced exacerbations by 30% vs placebo.',
        'results_authority_committe_name' => 'Institutional Review Board',
        'results_url_doi' => '10.1234/jrm.2027.001',
        'results_publication_type' => 'Journal Article',
        'results_publication_title' => 'Efficacy of Drug X in Adult Asthma: an RCT',
        'opendata_allow_publishing' => 'Yes',
        'opendata_repository_name' => 'Zenodo',
        'opendata_study_identification_variable' => 'trial_x_2026',
        'opendata_effective_size_value' => 128,
        'opendata_handling_missing_data' => 'Multiple imputation',
        'opendata_risk_of_bias_assessment' => 'Low',
        'opendata_confidential_interval' => 95,
        'opendata_significant_p_value' => 'p<0.05',
        'opendata_statistical_method_used' => 'Mixed-effects regression',
        // StudyPurposes
        'study_purpose' => 'Treatment',
        'study_objective' => 'Assess efficacy of Drug X vs placebo in adult asthma',
        'study_hypothesis' => 'Drug X reduces exacerbations by at least 25% vs placebo',
        'intervention' => 'Drug X 10mg daily',
        'control_group_name' => 'Placebo arm',
        'design_control_group_presence' => 'Yes',
        'phase_of_study' => 'Phase 2',
        'randomization_method_name' => 'Block randomisation, 1:1',
        'masking_description' => 'Participants and outcome assessors are blinded',
        'masking_status' => 'Yes',
        // PopulationEligibility
        'health_condition_studied' => 'Adult persistent asthma',
        'type_of_eligibility' => 'Inclusion Criteria',
        'participant_target_number' => 200,
        'sample_size' => 200,
        'final_number_of_participants' => 0,
        // Investigators
        'role' => 'Principal Investigator',
        'name' => 'Dr. Jane Mwangi',
        'institution' => 'Nairobi Hospital',
        'mobile_number' => '+254700000000',
        'email_address' => 'jane.mwangi@example.org',
        'postal_address' => 'PO Box 100, Nairobi',
    ];

    private array $lookups = [];

    /** @var array<string,string> lookup name => absolute range on the Lookups sheet */
    private array $lookupRanges = [];

    public function build(): Spreadsheet
    {
        $this->lookups = TrialImportSchema::lookups();

        // Reuse the default sheet rather than deleting it: removing the only sheet first
        // corrupts the workbook's sheet bookkeeping and triggers Excel's repair prompt.
        $spreadsheet = new Spreadsheet();
        $this->buildInstructionsSheet($spreadsheet->getActiveSheet());

        // Lookups first (the dropdowns reference its ranges), then the data sheets are
        // inserted in front of it so it ends up last in the tab order.
        $this->buildLookupsSheet($spreadsheet->createSheet()->setTitle(TrialImportSchema::SHEET_LOOKUPS));

        $index = 1;
        foreach (TrialImportSchema::sheets() as $name => $columns) {
            $this->buildDataSheet($spreadsheet->createSheet($index++)->setTitle($name), $columns);
        }

        $spreadsheet->setActiveSheetIndex(0);
        return $spreadsheet;
    }

    private function buildDataSheet(Worksheet $sheet, array $columns): void
    {
        $i = 0;
        foreach ($columns as $header => $def) {
            $i++;
            $col = Coordinate::stringFromColumnIndex($i);
            $required = TrialImportSchema::isRequired($def);
            $type = $def['type'];

            // Header
            $sheet->setCellValue("{$col}1", $header);
            $color = $type === 'ref' ? self::COLOR_REF : ($required ? self::COLOR_REQUIRED : self::COLOR_HEADER);
            $sheet->getStyle("{$col}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getColumnDimension($col)->setWidth(max(16, min(40, strlen($header) + 4)));

            // Header note: what goes here
            $note = [($required ? 'REQUIRED. ' : 'Optional. ') . ($def['hint'] ?? '')];
            if ($type === 'lookup') {
                $note[] = 'Pick from the list (Lookups sheet) or type the id.';
            } elseif ($type === 'date') {
                $note[] = 'Date: YYYY-MM-DD.';
            } elseif ($type === 'int') {
                $note[] = 'Whole number.';
            } elseif ($type === 'number') {
                $note[] = 'Number.';
            }
            $comment = $sheet->getComment("{$col}1");
            $comment->getText()->createTextRun(trim(implode(' ', $note)));
            $comment->setWidth('220pt')->setHeight('70pt');

            // Whole-column formats (stored on the column, not per cell, so the file stays small)
            // so Excel doesn't mangle values as they are typed.
            $range = "{$col}2:{$col}" . self::LAST_ROW;
            if (in_array($type, ['text', 'ref', 'doi'], true)) {
                $sheet->getStyle("{$col}:{$col}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            } elseif ($type === 'date') {
                $sheet->getStyle("{$col}:{$col}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            }

            // Dropdown for coded columns (warning style, so typing an id is still allowed)
            if ($type === 'lookup' && isset($this->lookupRanges[$def['lookup']])) {
                $dv = new DataValidation();
                $dv->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_WARNING)
                    ->setAllowBlank(true)
                    ->setShowDropDown(true)
                    ->setShowErrorMessage(true)
                    ->setErrorTitle('Not in the list')
                    ->setError('Pick a value from the list (see the Lookups sheet), or enter its id.')
                    ->setFormula1($this->lookupRanges[$def['lookup']]);
                $sheet->setDataValidation($range, $dv);
            }

            // Worked example (row 2), skipped by the importer
            $value = $type === 'ref' ? TrialImportSchema::EXAMPLE_REF : $this->exampleValue($header, $def);
            if ($value !== null && $value !== '') {
                if (in_array($type, ['text', 'ref', 'doi'], true)) {
                    $sheet->setCellValueExplicit("{$col}2", (string) $value, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue("{$col}2", $value);
                }
            }
            $sheet->getStyle("{$col}2")->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($type === 'ref' ? self::COLOR_EXAMPLE_REF : self::COLOR_EXAMPLE);
        }

        $sheet->freezePane('B2');
        $sheet->setAutoFilter('A1:' . Coordinate::stringFromColumnIndex(count($columns)) . '1');
    }

    /** Example value for a column; lookup columns use a live label so the example is valid here. */
    private function exampleValue(string $header, array $def)
    {
        $preferred = self::EXAMPLE_VALUES[$header] ?? null;
        if ($def['type'] !== 'lookup') {
            return $preferred;
        }
        $labels = array_map('strval', array_values($this->lookups[$def['lookup']] ?? []));
        if ($preferred !== null && in_array((string) $preferred, $labels, true)) {
            return $preferred;
        }
        return $labels[0] ?? null; // e.g. the first country configured in this system
    }

    private function buildLookupsSheet(Worksheet $sheet): void
    {
        $titles = [
            'registrationStatus' => 'Registration status', 'areaOfSpecialization' => 'Area of specialization',
            'recruitmentStatus' => 'Recruitment status', 'country' => 'Country', 'region' => 'Centre / region',
            'ethicsBody' => 'Ethics regulatory body', 'yesNo' => 'Yes / No', 'currency' => 'Currency',
            'fundingSector' => 'Funding sector', 'outcomeType' => 'Type of outcome', 'publisher' => 'Publisher',
            'publicationType' => 'Publication type', 'studyType' => 'Type of study', 'phase' => 'Phase of study',
            'eligibilityType' => 'Type of eligibility', 'investigatorRole' => 'Investigator role', 'town' => 'City / town',
        ];

        $sheet->setCellValue('A1', 'Allowed values for coded columns. Use the label (or the id in brackets). Maintained in the system: do not edit here.');
        $sheet->getStyle('A1')->getFont()->setItalic(true)->getColor()->setRGB('555555');

        $i = 0;
        foreach ($this->lookups as $name => $options) {
            $i++;
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->setCellValue("{$col}2", $titles[$name] ?? $name);
            $sheet->getStyle("{$col}2")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle("{$col}2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::COLOR_HEADER);
            $sheet->getColumnDimension($col)->setWidth(26);

            $row = 3;
            foreach ($options as $value => $label) {
                $sheet->setCellValueExplicit("{$col}{$row}", (string) $label, DataType::TYPE_STRING);
                $row++;
            }
            if ($options) {
                $this->lookupRanges[$name] = "'" . TrialImportSchema::SHEET_LOOKUPS . "'!\${$col}\$3:\${$col}\$" . ($row - 1);
            } else {
                $sheet->setCellValue("{$col}3", '(none configured yet)');
                $sheet->getStyle("{$col}3")->getFont()->setItalic(true)->getColor()->setRGB('999999');
            }
        }
        $sheet->freezePane('A3');
    }

    private function buildInstructionsSheet(Worksheet $sheet): void
    {
        $sheet->setTitle('Instructions');
        $sheet->setShowGridlines(false);
        $sheet->getColumnDimension('A')->setWidth(120);

        $required = function (string $sheetName, ?string $model = null): string {
            $names = [];
            foreach (TrialImportSchema::columns($sheetName) as $header => $def) {
                if (($def['type'] ?? '') === 'ref') {
                    continue;
                }
                if (($model === null || ($def['model'] ?? null) === $model) && TrialImportSchema::isRequired($def)) {
                    $names[] = $header;
                }
            }
            return $names ? implode(', ', $names) : 'none';
        };
        $trialModel = \frontend\models\ClinicalTrial::class;

        $lines = [
            ['title', 'Clinical Trials: Bulk Import Template'],
            ['h', '1. How it works'],
            ['p', '• Fill in the Trials sheet: one row per trial. Then add the trial\'s rows on StudyPurposes, PopulationEligibility and Investigators.'],
            ['p', '• Upload the file. The system first CHECKS it and shows a report, nothing is saved yet. Fix any problems and re-upload, or confirm to import the valid trials.'],
            ['p', '• Up to ' . TrialImportSchema::MAX_TRIALS . ' trials per file. Do not rename sheets or column headers (column order does not matter).'],
            ['h', '2. Linking rows with trial_ref'],
            ['p', '• trial_ref (gold column) is your own short label, e.g. T1, T2. Repeat the same trial_ref on every row that belongs to that trial on the other sheets.'],
            ['p', '• Rows whose trial_ref does not match a Trials row are reported and ignored.'],
            ['p', '• StudyPurposes and PopulationEligibility: ONE row per trial. Investigators: as many rows as needed.'],
            ['h', '3. Required columns (red headers)'],
            ['p', '• Trials: ' . $required(TrialImportSchema::SHEET_TRIALS, $trialModel) . '. registration_number is also required when registration_status is Registered.'],
            ['p', '• The other sections on the Trials sheet (timeline_, ethics_, funding_, description_, intervention_, results_, opendata_) are optional: leave all of a section\'s columns empty to skip it and complete it later in the system. Once you fill any column of a section, its required columns apply:'],
        ];
        foreach (TrialImportSchema::TRIAL_SHEET_CHILDREN as $class => $label) {
            $lines[] = ['p', "      {$label}: " . $required(TrialImportSchema::SHEET_TRIALS, $class)];
        }
        foreach (TrialImportSchema::CHILD_SHEETS as $sheetName => [, , $label]) {
            $lines[] = ['p', "• {$sheetName}: " . $required($sheetName)];
        }
        $lines = array_merge($lines, [
            ['h', '4. Coded columns'],
            ['p', '• Columns such as registration_status, country, phase_of_study or role have a dropdown. Pick a value, or type its label or id. The Lookups sheet lists every allowed value; it reflects this system on the day you downloaded the template.'],
            ['p', '• Yes/No columns accept Yes, No, 1 or 0.'],
            ['h', '5. Dates and numbers'],
            ['p', '• Dates: YYYY-MM-DD (e.g. 2026-09-01). Excel dates and DD/MM/YYYY are also accepted.'],
            ['p', '• Amounts may contain thousands separators (150,000). Enter funding_currency as an ISO code such as KES or USD.'],
            ['p', '• results_url_doi accepts a full URL or a bare DOI such as 10.1234/abc.'],
            ['h', '6. Duplicates'],
            ['p', '• A trial whose protocol_number already exists in the system, or appears twice in the file, is skipped and reported. Existing trials are never overwritten.'],
            ['h', '7. The EXAMPLE rows'],
            ['p', '• Row 2 of each sheet (trial_ref = EXAMPLE, shaded green) is a worked example. It is never imported: leave it, or delete it.'],
            ['p', '• Hover over a column header to see what it expects.'],
        ]);

        $row = 1;
        foreach ($lines as [$kind, $text]) {
            $sheet->setCellValue("A{$row}", $text);
            $style = $sheet->getStyle("A{$row}");
            $style->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
            if ($kind === 'title') {
                $style->getFont()->setBold(true)->setSize(16)->getColor()->setRGB(self::COLOR_HEADER);
                $row++;
            } elseif ($kind === 'h') {
                $style->getFont()->setBold(true)->setSize(12)->getColor()->setRGB(self::COLOR_HEADER);
                $sheet->getRowDimension($row)->setRowHeight(24);
            } else {
                $style->getFont()->setSize(10);
            }
            $row++;
        }
    }

    /**
     * Streams the workbook as an .xlsx download. Written to a temp file first and the output
     * buffers cleared, because any stray byte in the response corrupts the zip.
     */
    public function outputAsDownload(string $filename = 'clinical_trials_batch_import_template.xlsx'): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'trial_tpl_');
        (new Xlsx($this->build()))->save($tmpFile);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tmpFile));
        header('Cache-Control: max-age=0');
        readfile($tmpFile);
        unlink($tmpFile);
    }
}
