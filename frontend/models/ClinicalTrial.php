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
 * @property int|null $area_of_specialization
 * @property int|null $specialization_sub_section
 *
 * @property StudyPopulationEligibility[] $studyPopulationEligibilities
 * @property StudyPurpose[] $studyPurposes
 */
class ClinicalTrial extends \yii\db\ActiveRecord
{

    public $other_area_of_specialization;
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
            // [['scientific_title', 'protocol_number', 'registration_status'], 'required', 'message' => 'This field is required.'],
            ['scientific_title', 'required', 'message' => 'Scientific title is required.'],
            ['protocol_number', 'required', 'message' => 'Protocol number is required.'],
            ['registration_status', 'required', 'message' => 'Registration status is required.'],
            [['specialization_sub_section'], 'integer'],
            ['area_of_specialization', 'safe'],
            ['other_area_of_specialization', 'string', 'max' => 255],
            [
                'other_area_of_specialization',
                'required',
                'when' => function ($model) {
                    return $model->area_of_specialization === 'other';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#clinicaltrial-area_of_specialization').val() === 'other';
                }",
                'message' => 'Please specify the other area of specialization.'
            ],
            [
                'area_of_specialization',
                'exist',
                'skipOnError' => true,
                'targetClass' => AreaOfSpecialization::class,
                'targetAttribute' => ['area_of_specialization' => 'id'],
                'message' => 'Please select a valid area of specialization.',
                'when' => function ($model) {
                    return $model->area_of_specialization !== 'other'; // exclude validation when "other" is selected
                },
            ],

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
            'area_of_specialization' => Yii::t('app', 'Broad Study Area of Study'),
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

    // options for clinical trial area of specialization
    public static function getAreaOfSpecializationOptions(): array
    {
        return [
            1 => 'Internal Medicine',
            2 => 'Surgery',
            3 => 'Pediatrics',
            4 => 'Obstetrics and Gynecology',
            5 => 'Psychiatry',
            6 => 'Dermatology',
            7 => 'Ophthalmology',
            8 => 'Otolaryngology',
            9 => 'Orthopedics',
            10 => 'Urology',
            11 => 'Cardiology',
            12 => 'Neurology',
            13 => 'Radiology',
            14 => 'Pathology',
            15 => 'Anesthesiology',
            16 => 'Emergency Medicine',
            17 => 'Family Medicine',
            18 => 'Public Health',
            19 => 'Other',
        ];
    }

    // Get Timelines
    public function getTimeline()
    {
        return $this->hasOne(StudyTimeline::class, ['trial_id' => 'id']);
    }

    // Get Investigators
    public function getInvestigators()
    {
        return $this->hasMany(InvestigatorTeam::class, ['trial_id' => 'id']);
    }

    //get Study Purpose
    public function getPurpose()
    {
        return $this->hasOne(StudyPurpose::class, ['trial_id' => 'id']);
    }

    // get population Eligibility
    public function getStudyPopulationEligibility()
    {
        return $this->hasOne(StudyPopulationEligibility::class, ['trial_id' => 'id']);
    }

    // Get Ethical Approval
    public function getEthicalApproval()
    {
        return $this->hasOne(EthicalApproval::class, ['trial_id' => 'id']);
    }

    // Get Funding
    public function getFunding()
    {
        return $this->hasOne(Funding::class, ['trial_id' => 'id']);
    }

    // get study description
    public function getStudyDescription()
    {
        return $this->hasOne(StudyDescription::class, ['trial_id' => 'id']);
    }

    // Get Study Intervention
    public function getStudyIntervention()
    {
        return $this->hasOne(StudyIntervention::class, ['trial_id' => 'id']);
    }

    // Get Study Results
    public function getStudyResults()
    {
        return $this->hasOne(StudyResults::class, ['trial_id' => 'id']);
    }

    // Get Open Data Access
    public function getOpendataAccess()
    {
        return $this->hasOne(OpendataAccess::class, ['trial_id' => 'id']);
    }




    /*
     * Markup helper functions for grid
     */

    // Add status badge helper
    public function getStatusBadge()
    {
        $map = [
            1 => ['label' => 'Draft', 'class' => 'bg-slate-200 text-slate-600'],
            2 => ['label' => 'Approved', 'class' => 'bg-tertiary-container text-white'],
            3 => ['label' => 'In Progress', 'class' => 'bg-secondary-container text-on-secondary-container'],
            4 => ['label' => 'Completed', 'class' => 'bg-primary-container text-white'],
        ];

        $status = $map[$this->registration_status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-200 text-slate-600'];

        return '<span class="px-2 md:px-3 py-1 ' . $status['class'] . ' text-[9px] md:text-[10px] font-bold rounded-full uppercase tracking-tighter">' . $status['label'] . '</span>';
    }

    // Get display title (prioritize scientific, then public, then acronym)
    public function getDisplayTitle()
    {
        if (!empty($this->scientific_title) && $this->scientific_title !== 'N/A') {
            return $this->scientific_title;
        }
        if (!empty($this->public_title) && $this->public_title !== 'N/A') {
            return $this->public_title;
        }
        if (!empty($this->scientific_acronym)) {
            return $this->scientific_acronym;
        }
        return 'Untitled Trial';
    }

    // Get protocol identifier
    public function getProtocolIdentifier()
    {
        return !empty($this->protocol_number)
            ? $this->protocol_number
            : ($this->registration_number ?? 'N/A');
    }

    // Get phase display
    public function getPhaseDisplay()
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
        $phase = $this->purpose->phase_of_study ?? null;
        return $map[$phase] ?? 'N/A';
    }

    // Get primary investigator
    public function getPrimaryInvestigator()
    {
        foreach ($this->investigators as $investigator) {
            if ($investigator->role == 1) {
                return $investigator;
            }
        }
        return null;
    }

    // Get formatted start date
    public function getFormattedStartDate()
    {
        if ($this->timeline && $this->timeline->anticipated_start_date) {
            return Yii::$app->formatter->asDate($this->timeline->anticipated_start_date, 'php:M d, Y');
        }
        return 'TBD';
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // If area_of_specialization is 'other', set it to null and save the other_area_of_specialization
            if ($this->area_of_specialization === 'other' && !empty($this->other_area_of_specialization)) {
                $this->area_of_specialization = null;
                // Check if the other_area_of_specialization already exists in the database
                $existingSpecialization = AreaOfSpecialization::find()->where(['title' => $this->other_area_of_specialization])->one();
                // Create the specialization if it doesn't exist
                if (!$existingSpecialization) {
                    $specialization = new AreaOfSpecialization();
                    $specialization->title = $this->other_area_of_specialization;
                    if (!$specialization->save()) {
                        return false; // stop saving if the specialization could not be saved
                    }
                }
                // Set area_of_specialization to the value of other_area_of_specialization for saving in clinicaltrial table
                $this->area_of_specialization = $specialization->id;

            }
            return true;
        }
        return false;
    }

}
