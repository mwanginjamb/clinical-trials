<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "clinical_trial".
 *
 * @property int $id
 * @property string|null $scientific_title
 * @property string|null $public_title
 * @property string|null $scientific_acronym
 * @property string|null $protocol_version
 * @property int|null $registration_status
 * @property string|null $protocol_number
 * @property string|null $registration_number
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property StudyPopulationEligibility[] $studyPopulationEligibilities
 * @property StudyPurpose[] $studyPurposes
 */
class ClinicalTrial extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clinical_trial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scientific_title', 'public_title', 'scientific_acronym', 'protocol_version', 'registration_status', 'protocol_number', 'registration_number', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['registration_status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['scientific_title', 'public_title', 'scientific_acronym', 'protocol_version', 'protocol_number', 'registration_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'scientific_title' => Yii::t('app', 'Scientific Title'),
            'public_title' => Yii::t('app', 'Public Title'),
            'scientific_acronym' => Yii::t('app', 'Scientific Acronym'),
            'protocol_version' => Yii::t('app', 'Protocol Version'),
            'registration_status' => Yii::t('app', 'Registration Status'),
            'protocol_number' => Yii::t('app', 'Protocol Number'),
            'registration_number' => Yii::t('app', 'Registration Number'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[StudyPopulationEligibilities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyPopulationEligibilities()
    {
        return $this->hasMany(StudyPopulationEligibility::class, ['trial_id' => 'id']);
    }

    /**
     * Gets query for [[StudyPurposes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyPurposes()
    {
        return $this->hasMany(StudyPurpose::class, ['trial_id' => 'id']);
    }

}
