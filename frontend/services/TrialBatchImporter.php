<?php

namespace frontend\services;

use frontend\models\ClinicalTrial;
use frontend\models\EthicalApproval;
use frontend\models\Funding;
use frontend\models\InvestigatorTeam;
use frontend\models\OpendataAccess;
use frontend\models\StudyDescription;
use frontend\models\StudyIntervention;
use frontend\models\StudyPopulationEligibility;
use frontend\models\StudyPurpose;
use frontend\models\StudyResults;
use frontend\models\StudyTimeline;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\web\UploadedFile;

/**
 * Reads a batch-upload workbook (see TrialTemplateBuilder / clinical_trials_batch_import_template.xlsx)
 * and persists ClinicalTrial + related records.
 *
 * One transaction PER TRIAL: if row T3 fails validation the trials before and after it still import;
 * only T3's own rows (across every sheet) are rolled back and reported as an error.
 */
class TrialBatchImporter
{
    /**
     * Column layout for every sheet — the single source of truth also used by TrialTemplateBuilder
     * to generate the downloadable template, so the two can never drift apart.
     */
    public const SHEET_COLUMNS = [
        'Trials' => [
            'trial_ref',
            'scientific_title',
            'public_title',
            'scientific_acronym',
            'protocol_version',
            'registration_status',
            'protocol_number',
            'registration_number',
            'area_of_specialization',
            'specialization_sub_section',
            'timeline_study_duration',
            'timeline_study_site_location',
            'timeline_centre_postal_address',
            'timeline_anticipated_start_date',
            'timeline_anticipated_end_date',
            'timeline_recruitment_status',
            'timeline_recruiting_country',
            'timeline_centre_pysical_address',
            'timeline_centre_region',
            'ethics_ethical_regulatory_body',
            'ethics_approved_by_ethical_committee',
            'ethics_document_number',
            'ethics_document_path',
            'funding_sponsor_name',
            'funding_amount',
            'funding_country',
            'funding_sector',
            'description_study_website',
            'description_lay_summary',
            'description_scientific_summary',
            'intervention_name',
            'intervention_description',
            'intervention_control_comparator',
            'intervention_type_of_outcome',
            'intervention_outcome_description',
            'results_permission_to_publish',
            'results_summary_results',
            'results_authority_committe_name',
            'results_publisher',
            'results_url_doi',
            'results_publication_type',
            'results_publication_title',
            'opendata_allow_publishing',
            'opendata_repository_name',
            'opendata_study_identification_variable',
            'opendata_sensitivity_analysis_result',
            'opendata_effective_size_value',
            'opendata_adjustable_miltiple_comparison',
            'opendata_handling_missing_data',
            'opendata_quality_assessment_variable',
            'opendata_risk_of_bias_assessment',
            'opendata_study_limitation',
            'opendata_potential_conflict_of_interest',
            'opendata_publication_bias_indicator',
            'opendata_heterogenity_measure',
            'opendata_confidential_interval',
            'opendata_significant_p_value',
            'opendata_statistical_method_used',
        ],
        'StudyPurposes' => [
            'trial_ref',
            'study_purpose',
            'study_objective',
            'study_hypothesis',
            'type_of_study',
            'intervention',
            'control_group_name',
            'design_control_group_presence',
            'phase_of_study',
            'randomization_method_name',
            'masking_description',
            'masking_status',
        ],
        'PopulationEligibility' => [
            'trial_ref',
            'health_condition_studied',
            'type_of_eligibility',
            'participant_target_number',
            'sample_size',
            'final_number_of_participants',
        ],
        'Investigators' => [
            'trial_ref',
            'role',
            'institution',
            'country',
            'name',
            'mobile_number',
            'email_address',
            'postal_address',
            'city',
        ],
    ];

    /**
     * Prefix => [modelClass, isSingular]. Every "Trials" sheet column starting with this prefix
     * (after stripping it) becomes an attribute on one instance of modelClass, tied to the new
     * clinical_trial.id via trial_id. This drives the 1-to-1 child tables without repeating the
     * same save() boilerplate seven times.
     */
    private const ONE_TO_ONE_CHILDREN = [
        'timeline_' => StudyTimeline::class,
        'ethics_' => EthicalApproval::class,
        'funding_' => Funding::class,
        'description_' => StudyDescription::class,
        'intervention_' => StudyIntervention::class,
        'results_' => StudyResults::class,
        'opendata_' => OpendataAccess::class,
    ];

    /** Sheet name => [modelClass, multiple-rows-per-trial] for the one-to-many child sheets. */
    private const ONE_TO_MANY_CHILDREN = [
        'StudyPurposes' => StudyPurpose::class,
        'PopulationEligibility' => StudyPopulationEligibility::class,
        'Investigators' => InvestigatorTeam::class,
    ];

    /** @var TrialImportResult */
    private $result;

    public function import(UploadedFile $file): TrialImportResult
    {
        $this->result = new TrialImportResult();

        $spreadsheet = IOFactory::load($file->tempName);
        $sheets = $this->readAllSheets($spreadsheet);

        if (empty($sheets['Trials'])) {
            $this->result->addFatal('Trials sheet is empty or missing.');
            return $this->result;
        }

        // Index the child sheets by trial_ref so each trial can pull only its own rows.
        $childrenByRef = [];
        foreach (self::ONE_TO_MANY_CHILDREN as $sheetName => $modelClass) {
            foreach ($sheets[$sheetName] ?? [] as $row) {
                $ref = trim((string) ($row['trial_ref'] ?? ''));
                if ($ref === '') {
                    continue;
                }
                $childrenByRef[$sheetName][$ref][] = $row;
            }
        }

        foreach ($sheets['Trials'] as $rowNumber => $row) {
            $this->importOneTrial($rowNumber, $row, $childrenByRef);
        }

        return $this->result;
    }

    private function importOneTrial(int $rowNumber, array $row, array $childrenByRef): void
    {
        $ref = trim((string) ($row['trial_ref'] ?? ''));
        $context = "Trials row {$rowNumber} (trial_ref='{$ref}')";

        if (
            $ref === '' || trim((string) ($row['scientific_title'] ?? '')) === ''
            || trim((string) ($row['public_title'] ?? '')) === ''
        ) {
            $this->result->addError($context, 'trial_ref, scientific_title and public_title are required.');
            return;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $trial = new ClinicalTrial();
            $trial->setAttributes([
                'scientific_title' => $row['scientific_title'],
                'public_title' => $row['public_title'],
                'scientific_acronym' => $row['scientific_acronym'] ?: null,
                'protocol_version' => $row['protocol_version'] ?: null,
                'registration_status' => $this->intOrNull($row['registration_status']),
                'protocol_number' => $row['protocol_number'] ?: null,
                'registration_number' => $row['registration_number'] ?: null,
                'area_of_specialization' => $this->intOrNull($row['area_of_specialization']),
                'specialization_sub_section' => $this->intOrNull($row['specialization_sub_section']),
                'created_by' => Yii::$app->user->id ?? null,
                'updated_by' => Yii::$app->user->id ?? null,
            ], false);

            if (!$trial->save()) {
                throw new \RuntimeException(implode(' ', $trial->getFirstErrors()));
            }

            // --- one-to-one children, driven by column prefix -----------------------------
            foreach (self::ONE_TO_ONE_CHILDREN as $prefix => $modelClass) {
                $attributes = $this->extractPrefixed($row, $prefix);
                if ($this->allBlank($attributes)) {
                    continue; // nothing entered for this child table on this row
                }
                /** @var \yii\db\ActiveRecord $child */
                $child = new $modelClass();
                $child->setAttributes($attributes, false);
                $child->trial_id = $trial->id;
                if (!$child->save()) {
                    throw new \RuntimeException("{$modelClass}: " . implode(' ', $child->getFirstErrors()));
                }
            }

            // --- one-to-many children, driven by the matching sheet -----------------------
            foreach (self::ONE_TO_MANY_CHILDREN as $sheetName => $modelClass) {
                foreach ($childrenByRef[$sheetName][$ref] ?? [] as $childRow) {
                    unset($childRow['trial_ref']);
                    /** @var \yii\db\ActiveRecord $child */
                    $child = new $modelClass();
                    $child->setAttributes($childRow, false);
                    $child->trial_id = $trial->id;
                    if (!$child->save()) {
                        throw new \RuntimeException(
                            "{$sheetName}: " . implode(' ', $child->getFirstErrors())
                        );
                    }
                }
            }

            $transaction->commit();
            $this->result->addSuccess($ref, $trial->id);
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->result->addError($context, $e->getMessage());
        }
    }

    /**
     * @return array<int, array<string,mixed>> rowNumber(in-sheet, 1-based, header excluded) => assoc row
     */
    private function readAllSheets($spreadsheet): array
    {
        $out = [];
        foreach (self::SHEET_COLUMNS as $sheetName => $columns) {
            $out[$sheetName] = [];
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if ($sheet === null) {
                continue;
            }
            $highestRow = $sheet->getHighestDataRow();
            for ($r = 2; $r <= $highestRow; $r++) {
                $assoc = [];
                foreach (array_values($columns) as $col => $header) {
                    $cellValue = $sheet->getCellByColumnAndRow($col + 1, $r)->getFormattedValue();
                    $assoc[$header] = is_string($cellValue) ? trim($cellValue) : $cellValue;
                }
                if ($this->allBlank($assoc)) {
                    continue; // skip fully empty rows (e.g. trailing rows in the sheet)
                }
                $out[$sheetName][$r] = $assoc;
            }
        }
        return $out;
    }

    private function extractPrefixed(array $row, string $prefix): array
    {
        $out = [];
        foreach ($row as $key => $value) {
            if (str_starts_with($key, $prefix)) {
                $out[substr($key, strlen($prefix))] = $value === '' ? null : $value;
            }
        }
        return $out;
    }

    private function allBlank(array $attributes): bool
    {
        foreach ($attributes as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }
        return true;
    }

    private function intOrNull($value): ?int
    {
        return ($value === null || $value === '') ? null : (int) $value;
    }
}
