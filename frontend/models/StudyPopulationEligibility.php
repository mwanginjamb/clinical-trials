<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "study_population_eligibility".
 *
 * @property int $id
 * @property string|null $health_condition_studied
 * @property string|null $type_of_eligibility
 * @property int|null $participant_target_number
 * @property int|null $sample_size
 * @property int|null $final_number_of_participants
 * @property int|null $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ClinicalTrial $trial
 */
class StudyPopulationEligibility extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_population_eligibility';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['health_condition_studied', 'type_of_eligibility', 'participant_target_number', 'sample_size', 'final_number_of_participants', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['type_of_eligibility'], 'string'],
            [['participant_target_number', 'sample_size', 'final_number_of_participants', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['health_condition_studied'], 'string', 'max' => 255],
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
            'health_condition_studied' => Yii::t('app', 'Health Condition Studied'),
            'type_of_eligibility' => Yii::t('app', 'Type Of Eligibility'),
            'participant_target_number' => Yii::t('app', 'Participant Target Number'),
            'sample_size' => Yii::t('app', 'Sample Size'),
            'final_number_of_participants' => Yii::t('app', 'Final Number Of Participants'),
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
