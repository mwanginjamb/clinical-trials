<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "study_purpose".
 *
 * @property int $id
 * @property string|null $study_purpose
 * @property string|null $study_objective
 * @property string|null $study_hypothesis
 * @property int|null $type_of_study
 * @property string|null $intervention
 * @property int|null $control_group_name
 * @property int|null $design_control_group_presence
 * @property int|null $phase_of_study
 * @property string|null $randomization_method_name
 * @property string|null $masking_description
 * @property int|null $masking_status
 * @property int|null $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ClinicalTrial $trial
 */
class StudyPurpose extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_purpose';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['study_purpose', 'study_objective', 'study_hypothesis', 'type_of_study', 'intervention', 'control_group_name', 'design_control_group_presence', 'phase_of_study', 'randomization_method_name', 'masking_description', 'masking_status', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['study_purpose', 'study_objective'], 'string'],
            [['type_of_study', 'control_group_name', 'design_control_group_presence', 'phase_of_study', 'masking_status', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['study_hypothesis', 'intervention', 'randomization_method_name', 'masking_description'], 'string', 'max' => 255],
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
            'study_purpose' => Yii::t('app', 'Study Purpose'),
            'study_objective' => Yii::t('app', 'Study Objective'),
            'study_hypothesis' => Yii::t('app', 'Study Hypothesis'),
            'type_of_study' => Yii::t('app', 'Type Of Study'),
            'intervention' => Yii::t('app', 'Intervention'),
            'control_group_name' => Yii::t('app', 'Control Group Name'),
            'design_control_group_presence' => Yii::t('app', 'Design Control Group Presence'),
            'phase_of_study' => Yii::t('app', 'Phase Of Study'),
            'randomization_method_name' => Yii::t('app', 'Randomization Method Name'),
            'masking_description' => Yii::t('app', 'Masking Description'),
            'masking_status' => Yii::t('app', 'Masking Status'),
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

}
