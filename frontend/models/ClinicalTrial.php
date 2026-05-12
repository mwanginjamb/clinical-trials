<?php

namespace frontend\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

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
 * @property int|null $area_of_specialization
 * @property int|null $specialization_sub_section
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

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
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
            [['scientific_title', 'protocol_number', 'registration_status'], 'required', 'message' => 'This field is required.'],
            [['area_of_specialization', 'specialization_sub_section'], 'integer'],
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
            'area_of_specialization' => Yii::t('app', 'Area of Specialization'),
            'specialization_sub_section' => Yii::t('app', 'Specialization Sub Section'),
        ];
    }

    public function attributeHints(): array
    {
        return [
            'scientific_title' => 'Describe the therapeutic or scientific problem this study intends to solve.',
            'public_title' => 'Provide a layperson-friendly title that accurately reflects the study’s purpose and design.',
            'objective' => 'Primary endpoint and specific research aim.',
            'intervention' => 'e.g. Compound X, 50mg',
            'has_control_group' => 'Is there a comparator group in this study?',
            'masking_enabled' => 'Are participants or investigators blinded?',
            'control_group_name' => 'e.g. Placebo Arm A',
            'randomization_method' => 'e.g. Permuted Block Randomization',
            'masking_description' => 'Detail who is blinded (e.g., Participant, Care Provider, Outcomes Assessor) and how it is maintained.',
            'area_of_specialization' => 'Select the area of specialization for this clinical trial.',
            'specialization_sub_section' => 'Select the sub-section within the chosen area of specialization.',
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

    public static function getRegistrationStatusOptions(): array
    {
        return [
            0 => 'Not Registered',
            1 => 'Registered',
            2 => 'Pending',
            3 => 'Rejected',
        ];
    }

}
