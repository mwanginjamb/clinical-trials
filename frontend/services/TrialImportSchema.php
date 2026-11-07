<?php

namespace frontend\services;

use common\library\CurrencyHelper;
use frontend\models\AreaOfSpecialization;
use frontend\models\ClinicalTrial;
use frontend\models\Country;
use frontend\models\EthicalApproval;
use frontend\models\Funding;
use frontend\models\InvestigatorTeam;
use frontend\models\OpendataAccess;
use frontend\models\Publisher;
use frontend\models\Region;
use frontend\models\StudyDescription;
use frontend\models\StudyIntervention;
use frontend\models\StudyPopulationEligibility;
use frontend\models\StudyPurpose;
use frontend\models\StudyResults;
use frontend\models\StudyTimeline;
use frontend\models\Studytype;
use frontend\models\Town;
use yii\helpers\ArrayHelper;

/**
 * Single source of truth for the bulk-import workbook.
 *
 * Every column is mapped EXPLICITLY to a model attribute (no prefix stripping — that is what
 * silently dropped funding_amount / intervention_name before). TrialBatchImporter reads by
 * header name using this map; TrialTemplateBuilder generates headers, notes, dropdowns and
 * the Lookups sheet from it, so the two cannot drift apart.
 *
 * Column definition keys:
 *   model   target ActiveRecord class
 *   attr    target attribute (exact, case-sensitive)
 *   type    text | int | number | date | lookup
 *   lookup  lookup name (see lookups()) when type = lookup; label OR id is accepted
 *   hint    shown in the template header note
 */
final class TrialImportSchema
{
    public const SHEET_TRIALS = 'Trials';
    public const SHEET_PURPOSES = 'StudyPurposes';
    public const SHEET_ELIGIBILITY = 'PopulationEligibility';
    public const SHEET_INVESTIGATORS = 'Investigators';
    public const SHEET_LOOKUPS = 'Lookups';

    /** Column that links child-sheet rows to their Trials row. */
    public const REF = 'trial_ref';

    /** Rows with this trial_ref are the template's worked example and are never imported. */
    public const EXAMPLE_REF = 'EXAMPLE';

    /** Hard cap per upload, to keep the request short. */
    public const MAX_TRIALS = 500;

    /**
     * Child sheets: sheet => [model, max rows per trial (null = unlimited), label].
     * Purpose and eligibility are one-per-trial to match the wizard and the trial view.
     */
    public const CHILD_SHEETS = [
        self::SHEET_PURPOSES => [StudyPurpose::class, 1, 'Study purpose'],
        self::SHEET_ELIGIBILITY => [StudyPopulationEligibility::class, 1, 'Population eligibility'],
        self::SHEET_INVESTIGATORS => [InvestigatorTeam::class, null, 'Investigator'],
    ];

    /** One-to-one tables filled from the Trials sheet, in save order. */
    public const TRIAL_SHEET_CHILDREN = [
        StudyTimeline::class => 'Timeline',
        EthicalApproval::class => 'Ethics approval',
        Funding::class => 'Funding',
        StudyDescription::class => 'Study description',
        StudyIntervention::class => 'Intervention',
        StudyResults::class => 'Results',
        OpendataAccess::class => 'Open data',
    ];

    /** @return array<string, array<string, array>> sheet => header => definition */
    public static function sheets(): array
    {
        $t = ClinicalTrial::class;
        $tl = StudyTimeline::class;
        $ea = EthicalApproval::class;
        $fu = Funding::class;
        $de = StudyDescription::class;
        $in = StudyIntervention::class;
        $re = StudyResults::class;
        $od = OpendataAccess::class;
        $sp = StudyPurpose::class;
        $el = StudyPopulationEligibility::class;
        $iv = InvestigatorTeam::class;

        $ref = [self::REF => ['type' => 'ref', 'hint' => 'Your own short label for the trial (T1, T2…). Child sheets repeat it to link their rows.']];

        return [
            self::SHEET_TRIALS => $ref + [
                'scientific_title' => ['model' => $t, 'attr' => 'scientific_title', 'type' => 'text'],
                'public_title' => ['model' => $t, 'attr' => 'public_title', 'type' => 'text', 'hint' => 'Lay title.'],
                'scientific_acronym' => ['model' => $t, 'attr' => 'scientific_acronym', 'type' => 'text'],
                'protocol_version' => ['model' => $t, 'attr' => 'protocol_version', 'type' => 'text'],
                'registration_status' => ['model' => $t, 'attr' => 'registration_status', 'type' => 'lookup', 'lookup' => 'registrationStatus'],
                'protocol_number' => ['model' => $t, 'attr' => 'protocol_number', 'type' => 'text', 'hint' => 'Must be unique: a protocol number that already exists is skipped.'],
                'registration_number' => ['model' => $t, 'attr' => 'registration_number', 'type' => 'text', 'hint' => 'Required when registration_status is Registered.'],
                'area_of_specialization' => ['model' => $t, 'attr' => 'area_of_specialization', 'type' => 'lookup', 'lookup' => 'areaOfSpecialization'],
                'specialization_sub_section' => ['model' => $t, 'attr' => 'specialization_sub_section', 'type' => 'int'],

                'timeline_study_duration' => ['model' => $tl, 'attr' => 'study_duration', 'type' => 'int', 'hint' => 'Months.'],
                'timeline_study_site_location' => ['model' => $tl, 'attr' => 'study_site_location', 'type' => 'text'],
                'timeline_centre_postal_address' => ['model' => $tl, 'attr' => 'centre_postal_address', 'type' => 'text'],
                'timeline_anticipated_start_date' => ['model' => $tl, 'attr' => 'anticipated_start_date', 'type' => 'date'],
                'timeline_anticipated_end_date' => ['model' => $tl, 'attr' => 'anticipated_end_date', 'type' => 'date'],
                'timeline_recruitment_status' => ['model' => $tl, 'attr' => 'recruitment_status', 'type' => 'lookup', 'lookup' => 'recruitmentStatus'],
                'timeline_recruiting_country' => ['model' => $tl, 'attr' => 'recruiting_country', 'type' => 'lookup', 'lookup' => 'country'],
                'timeline_centre_pysical_address' => ['model' => $tl, 'attr' => 'centre_pysical_address', 'type' => 'text'],
                'timeline_centre_region' => ['model' => $tl, 'attr' => 'centre_region', 'type' => 'lookup', 'lookup' => 'region'],

                'ethics_ethical_regulatory_body' => ['model' => $ea, 'attr' => 'ethical_regulatory_body', 'type' => 'lookup', 'lookup' => 'ethicsBody'],
                'ethics_approved_by_ethical_committee' => ['model' => $ea, 'attr' => 'approved_by_ethical_committee', 'type' => 'lookup', 'lookup' => 'yesNo'],
                'ethics_document_number' => ['model' => $ea, 'attr' => 'document_number', 'type' => 'text'],
                'ethics_document_path' => ['model' => $ea, 'attr' => 'document_path', 'type' => 'text'],

                'funding_sponsor_name' => ['model' => $fu, 'attr' => 'sponsor_name', 'type' => 'text'],
                'funding_amount' => ['model' => $fu, 'attr' => 'Amount', 'type' => 'number'],
                'funding_currency' => ['model' => $fu, 'attr' => 'currency', 'type' => 'lookup', 'lookup' => 'currency', 'hint' => 'ISO code, e.g. KES, USD.'],
                'funding_country' => ['model' => $fu, 'attr' => 'country', 'type' => 'lookup', 'lookup' => 'country'],
                'funding_sector' => ['model' => $fu, 'attr' => 'funding_Sector', 'type' => 'lookup', 'lookup' => 'fundingSector'],

                'description_study_website' => ['model' => $de, 'attr' => 'study_website', 'type' => 'text', 'hint' => 'Full URL starting with http:// or https://'],
                'description_lay_summary' => ['model' => $de, 'attr' => 'lay_summary', 'type' => 'text'],
                'description_scientific_summary' => ['model' => $de, 'attr' => 'scientific_summary', 'type' => 'text'],

                'intervention_name' => ['model' => $in, 'attr' => 'intervention_name', 'type' => 'text'],
                'intervention_description' => ['model' => $in, 'attr' => 'intervention_description', 'type' => 'text'],
                'intervention_control_comparator' => ['model' => $in, 'attr' => 'control_comparator', 'type' => 'text'],
                'intervention_type_of_outcome' => ['model' => $in, 'attr' => 'type_of_outcome', 'type' => 'lookup', 'lookup' => 'outcomeType'],
                'intervention_outcome_description' => ['model' => $in, 'attr' => 'outcome_description', 'type' => 'text'],

                'results_permission_to_publish' => ['model' => $re, 'attr' => 'permission_to_publish', 'type' => 'lookup', 'lookup' => 'yesNo'],
                'results_summary_results' => ['model' => $re, 'attr' => 'summary_results', 'type' => 'text'],
                'results_authority_committe_name' => ['model' => $re, 'attr' => 'authority_committe_name', 'type' => 'text'],
                'results_publisher' => ['model' => $re, 'attr' => 'publisher', 'type' => 'lookup', 'lookup' => 'publisher'],
                'results_url_doi' => ['model' => $re, 'attr' => 'url_doi', 'type' => 'doi', 'hint' => 'URL, or a bare DOI such as 10.1234/abc (converted to https://doi.org/…).'],
                'results_publication_type' => ['model' => $re, 'attr' => 'publication_type', 'type' => 'lookup', 'lookup' => 'publicationType'],
                'results_publication_title' => ['model' => $re, 'attr' => 'publication_title', 'type' => 'text'],

                'opendata_allow_publishing' => ['model' => $od, 'attr' => 'allow_publishing', 'type' => 'lookup', 'lookup' => 'yesNo'],
                'opendata_repository_name' => ['model' => $od, 'attr' => 'repository_name', 'type' => 'text'],
                'opendata_study_identification_variable' => ['model' => $od, 'attr' => 'study_identification_variable', 'type' => 'text'],
                'opendata_sensitivity_analysis_result' => ['model' => $od, 'attr' => 'sensitivity_analysis_result', 'type' => 'text'],
                'opendata_effective_size_value' => ['model' => $od, 'attr' => 'effective_size_value', 'type' => 'int'],
                'opendata_adjustable_miltiple_comparison' => ['model' => $od, 'attr' => 'adjustable_miltiple_comparison', 'type' => 'text'],
                'opendata_handling_missing_data' => ['model' => $od, 'attr' => 'handling_missing_data', 'type' => 'text'],
                'opendata_quality_assessment_variable' => ['model' => $od, 'attr' => 'quality_assessment_variable', 'type' => 'text'],
                'opendata_risk_of_bias_assessment' => ['model' => $od, 'attr' => 'risk_of_bias_assessment', 'type' => 'text'],
                'opendata_study_limitation' => ['model' => $od, 'attr' => 'study_limitation', 'type' => 'text'],
                'opendata_potential_conflict_of_interest' => ['model' => $od, 'attr' => 'potential_conflict_of_interest', 'type' => 'text'],
                'opendata_publication_bias_indicator' => ['model' => $od, 'attr' => 'publication_bias_indicator', 'type' => 'text'],
                'opendata_heterogenity_measure' => ['model' => $od, 'attr' => 'heterogenity_measure', 'type' => 'text'],
                'opendata_confidential_interval' => ['model' => $od, 'attr' => 'confidential_interval', 'type' => 'number', 'hint' => 'Confidence level as a number, e.g. 95.'],
                'opendata_significant_p_value' => ['model' => $od, 'attr' => 'significant_p_value', 'type' => 'text'],
                'opendata_statistical_method_used' => ['model' => $od, 'attr' => 'statistical_method_used', 'type' => 'text'],
            ],

            self::SHEET_PURPOSES => $ref + [
                'study_purpose' => ['model' => $sp, 'attr' => 'study_purpose', 'type' => 'text'],
                'study_objective' => ['model' => $sp, 'attr' => 'study_objective', 'type' => 'text'],
                'study_hypothesis' => ['model' => $sp, 'attr' => 'study_hypothesis', 'type' => 'text'],
                'type_of_study' => ['model' => $sp, 'attr' => 'type_of_study', 'type' => 'lookup', 'lookup' => 'studyType'],
                'intervention' => ['model' => $sp, 'attr' => 'intervention', 'type' => 'text'],
                'control_group_name' => ['model' => $sp, 'attr' => 'control_group_name', 'type' => 'text'],
                'design_control_group_presence' => ['model' => $sp, 'attr' => 'design_control_group_presence', 'type' => 'lookup', 'lookup' => 'yesNo'],
                'phase_of_study' => ['model' => $sp, 'attr' => 'phase_of_study', 'type' => 'lookup', 'lookup' => 'phase'],
                'randomization_method_name' => ['model' => $sp, 'attr' => 'randomization_method_name', 'type' => 'text'],
                'masking_description' => ['model' => $sp, 'attr' => 'masking_description', 'type' => 'text'],
                'masking_status' => ['model' => $sp, 'attr' => 'masking_status', 'type' => 'lookup', 'lookup' => 'yesNo', 'hint' => 'Is the study blinded?'],
            ],

            self::SHEET_ELIGIBILITY => $ref + [
                'health_condition_studied' => ['model' => $el, 'attr' => 'health_condition_studied', 'type' => 'text'],
                'type_of_eligibility' => ['model' => $el, 'attr' => 'type_of_eligibility', 'type' => 'lookup', 'lookup' => 'eligibilityType'],
                'participant_target_number' => ['model' => $el, 'attr' => 'participant_target_number', 'type' => 'int'],
                'sample_size' => ['model' => $el, 'attr' => 'sample_size', 'type' => 'int'],
                'final_number_of_participants' => ['model' => $el, 'attr' => 'final_number_of_participants', 'type' => 'int', 'hint' => 'Enter 0 if recruitment has not started.'],
            ],

            self::SHEET_INVESTIGATORS => $ref + [
                'role' => ['model' => $iv, 'attr' => 'role', 'type' => 'lookup', 'lookup' => 'investigatorRole'],
                'name' => ['model' => $iv, 'attr' => 'name', 'type' => 'text'],
                'institution' => ['model' => $iv, 'attr' => 'institution', 'type' => 'text'],
                'country' => ['model' => $iv, 'attr' => 'country', 'type' => 'lookup', 'lookup' => 'country'],
                'city' => ['model' => $iv, 'attr' => 'city', 'type' => 'lookup', 'lookup' => 'town'],
                'mobile_number' => ['model' => $iv, 'attr' => 'mobile_number', 'type' => 'text'],
                'email_address' => ['model' => $iv, 'attr' => 'email_address', 'type' => 'text'],
                'postal_address' => ['model' => $iv, 'attr' => 'postal_address', 'type' => 'text'],
            ],
        ];
    }

    /**
     * Allowed values for coded columns: name => [storedValue => label].
     * Built from the same option lists / lookup tables the wizard forms use.
     *
     * @return array<string, array<int|string, string>>
     */
    public static function lookups(): array
    {
        $currencies = array_keys(CurrencyHelper::getSymbolList());

        return [
            'registrationStatus' => ClinicalTrial::getRegistrationStatusOptions(),
            'areaOfSpecialization' => ArrayHelper::map(AreaOfSpecialization::find()->orderBy('title')->all(), 'id', 'title'),
            'recruitmentStatus' => (new StudyTimeline())->getRecruitmentStatus(),
            'country' => ArrayHelper::map(Country::find()->orderBy('name')->all(), 'id', 'name'),
            'region' => ArrayHelper::map(Region::find()->orderBy('name')->all(), 'id', 'name'),
            'ethicsBody' => (new EthicalApproval())->getEthicalRegulatoryBodyOptions(),
            'yesNo' => [1 => 'Yes', 0 => 'No'],
            'currency' => array_combine($currencies, $currencies) ?: [],
            'fundingSector' => (new Funding())->getFundingSectors(),
            'outcomeType' => StudyIntervention::getTypeOfOutcomeOptions(),
            'publisher' => ArrayHelper::map(Publisher::find()->orderBy('publisher')->all(), 'publisher', 'publisher'),
            'publicationType' => StudyResults::getPublicationTypeOptions(),
            'studyType' => ArrayHelper::map(Studytype::find()->orderBy('name')->all(), 'id', 'name'),
            'phase' => (new StudyPurpose())->getPhaseOfStudy(),
            'eligibilityType' => (new StudyPopulationEligibility())->getTypeOfEligibilityOptions(),
            'investigatorRole' => InvestigatorTeam::getRoleOptions(),
            'town' => ArrayHelper::map(Town::find()->orderBy('name')->all(), 'id', 'name'),
        ];
    }

    /** Header => definition for one sheet. */
    public static function columns(string $sheet): array
    {
        return self::sheets()[$sheet] ?? [];
    }

    /**
     * Whether the model marks the attribute as always required (conditional rules excluded).
     */
    public static function isRequired(array $def): bool
    {
        if (($def['type'] ?? null) === 'ref') {
            return true;
        }
        static $models = [];
        $model = $models[$def['model']] ??= new $def['model']();

        return $model->isAttributeRequired($def['attr']);
    }
}
