<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "opendata_access".
 *
 * @property int $id
 * @property int|null $allow_publishing
 * @property string|null $repository_name
 * @property string|null $study_identification_variable
 * @property string|null $sensitivity_analysis_result
 * @property int|null $effective_size_value
 * @property string|null $adjustable_miltiple_comparison
 * @property string|null $handling_missing_data
 * @property string|null $document_path
 * @property string|null $quality_assessment_variable
 * @property string|null $risk_of_bias_assessment
 * @property string|null $study_limitation
 * @property string|null $funding_source
 * @property string|null $potential_conflict_of_interest
 * @property string|null $publication_bias_indicator
 * @property string|null $heterogenity_measure
 * @property float|null $confidential_interval
 * @property string|null $significant_p_value
 * @property string|null $statistical_method_used
 * @property int $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ClinicalTrial $trial
 */
class OpendataAccess extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opendata_access';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            'blameable' => [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['allow_publishing', 'repository_name', 'study_identification_variable', 'sensitivity_analysis_result', 'effective_size_value', 'adjustable_miltiple_comparison', 'handling_missing_data', 'document_path', 'quality_assessment_variable', 'risk_of_bias_assessment', 'study_limitation', 'funding_source', 'potential_conflict_of_interest', 'publication_bias_indicator', 'heterogenity_measure', 'confidential_interval', 'created_at', 'updated_at', 'created_by', 'updated_by', 'statistical_method_used'], 'default', 'value' => null],
            [['allow_publishing', 'effective_size_value', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['sensitivity_analysis_result', 'study_limitation'], 'string'],
            [['confidential_interval'], 'number'],
            [['trial_id'], 'required'],
            [['repository_name', 'risk_of_bias_assessment'], 'string', 'max' => 150],
            [['study_identification_variable', 'handling_missing_data', 'quality_assessment_variable', 'funding_source', 'statistical_method_used'  ], 'string', 'max' => 255],
            [['adjustable_miltiple_comparison', 'document_path', 'potential_conflict_of_interest', 'publication_bias_indicator', 'heterogenity_measure'], 'string', 'max' => 250],
            [['trial_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClinicalTrial::class, 'targetAttribute' => ['trial_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'allow_publishing' => Yii::t('app', 'Allow Publishing'),
            'repository_name' => Yii::t('app', 'Repository Name'),
            'study_identification_variable' => Yii::t('app', 'Study Identification Variable'),
            'sensitivity_analysis_result' => Yii::t('app', 'Sensitivity Analysis Result'),
            'effective_size_value' => Yii::t('app', 'Effective Size Value'),
            'adjustable_miltiple_comparison' => Yii::t('app', 'Adjustable Miltiple Comparison'),
            'handling_missing_data' => Yii::t('app', 'Handling Missing Data'),
            'document_path' => Yii::t('app', 'Document Path'),
            'quality_assessment_variable' => Yii::t('app', 'Quality Assessment Variable'),
            'risk_of_bias_assessment' => Yii::t('app', 'Risk Of Bias Assessment'),
            'study_limitation' => Yii::t('app', 'Study Limitation'),
            'funding_source' => Yii::t('app', 'Funding Source'),
            'potential_conflict_of_interest' => Yii::t('app', 'Potential Conflict Of Interest'),
            'publication_bias_indicator' => Yii::t('app', 'Publication Bias Indicator'),
            'heterogenity_measure' => Yii::t('app', 'Heterogenity Measure'),
            'confidential_interval' => Yii::t('app', 'Confidential Interval'),
            'significant_p_value' => Yii::t('app', 'Significant P Value'),
            'statistical_method_used' => Yii::t('app', 'Statistical Method Used'),
            'trial_id' => Yii::t('app', 'Trial ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[Trial]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrial()
    {
        return $this->hasOne(ClinicalTrial::class, ['id' => 'trial_id']);
    }

    // options for study identification variable: Authors, publication title, publication date, journal name, journal volume, journal issue, journal page range, DOI, PMID
    public static function getStudyIdentificationVariableOptions()
    {
        return [
            'Authors' => 'Authors',
            'Publication title' => 'Publication title',
            'Publication date' => 'Publication date',
            'Journal name' => 'Journal name',
            'Journal volume' => 'Journal volume',
            'Journal issue' => 'Journal issue',
            'Journal page range' => 'Journal page range',
            'DOI' => 'DOI',
            'PMID' => 'PMID',
        ];
    }

    // options for significant p-value: statistically-significant, not statistically-significant, highly statistically-significant, marginal statistically-significant, moderate statistically-significant
    public static function getSignificantPValueOptions()
    {
        return [
            0 => 'Not statistically significant',
            1 => 'Statistically significant',
            2 => 'Highly statistically significant',
            3 => 'Moderate statistically significant',
            4 => 'Marginal statistically significant',
        ];
    }

}
