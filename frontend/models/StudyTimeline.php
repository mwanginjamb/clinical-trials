<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "study_timeline".
 *
 * @property int $id
 * @property int|null $study_duration
 * @property string|null $study_site_location
 * @property string|null $centre_postal_address
 * @property string|null $anticipated_start_date
 * @property string|null $anticipated_end_date
 * @property int|null $recruitment_status
 * @property int|null $recruiting_country
 * @property string|null $centre_pysical_address
 * @property int|null $centre_region
 * @property int $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $created_by
 *
 * @property ClinicalTrial $trial
 */
class StudyTimeline extends \yii\db\ActiveRecord
{

    public $other_country;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_timeline';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['study_duration', 'study_site_location', 'centre_postal_address', 'anticipated_start_date', 'anticipated_end_date', 'recruitment_status', 'recruiting_country', 'centre_pysical_address', 'centre_region', 'created_at', 'updated_at', 'updated_by', 'created_by'], 'default', 'value' => null],
            [['study_duration', 'recruitment_status', 'centre_region', 'trial_id', 'created_at', 'updated_at', 'updated_by', 'created_by'], 'integer'],
            [['anticipated_start_date', 'anticipated_end_date'], 'safe'],
            [['trial_id'], 'required'],
            [['study_site_location', 'centre_postal_address', 'centre_pysical_address'], 'string', 'max' => 255],
            [['trial_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClinicalTrial::class, 'targetAttribute' => ['trial_id' => 'id']],
            ['recruiting_country', 'safe'],
            ['other_country', 'string', 'max' => 200],
            [
                'other_country',
                'required',
                'message' => 'Specify Other Country Option for Preferred Participating Country',
                'when' => function ($model) {
                    return $model->recruiting_country === 'other';
                },
                'whenClient' => "function (attribute, value) {
                return $('#studytimeline-recruiting_country').val() === 'other';
            }"
            ],
            [
                'recruiting_country',
                'exist',
                'skipOnError' => true,
                'targetClass' => Country::class,
                'targetAttribute' => ['recruiting_country' => 'id'],
                'message' => 'The selected country does not exist in the database.',
                'when' => function ($model) {
                    return $model->recruiting_country !== 'other'; // Only validate existence if publisher is not 'other'
                },
            ]

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'study_duration' => Yii::t('app', 'Study Duration (Years)'),
            'study_site_location' => Yii::t('app', 'Study Site Location'),
            'centre_postal_address' => Yii::t('app', 'Centre Postal Address'),
            'anticipated_start_date' => Yii::t('app', 'Anticipated Start Date'),
            'anticipated_end_date' => Yii::t('app', 'Anticipated End Date'),
            'recruitment_status' => Yii::t('app', 'Recruitment Status'),
            'recruiting_country' => Yii::t('app', 'Participating Country'),
            'centre_pysical_address' => Yii::t('app', 'Centre Pysical Address'),
            'centre_region' => Yii::t('app', 'Centre Region'),
            'trial_id' => Yii::t('app', 'Trial ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_by' => Yii::t('app', 'Created By'),
        ];
    }

    /**
     * Gets query for [[Trial]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\query\ClinicalTrialQuery
     */
    public function getTrial()
    {
        return $this->hasOne(ClinicalTrial::class, ['id' => 'trial_id']);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\query\StudyTimelineQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\StudyTimelineQuery(get_called_class());
    }

    // recruitment status options for dropdown
    public function getRecruitmentStatus()
    {
        return [
            0 => 'Not yet recruiting',
            1 => 'Recruiting',
            2 => 'Enrolling by invitation',
            3 => 'Active, not recruiting',
            4 => 'Completed',
            5 => 'Suspended',
            6 => 'Terminated',
            7 => 'Withdrawn',
            8 => 'Unknown status',
        ];
    }

    // Centres / Regions Options
    public function getCentreRegion()
    {
        return [
            1 => 'Nairobi (HQ)',
            2 => 'Busia',
            3 => 'Kwale',
            4 => 'Mombasa',
            5 => 'Kilifi Region',
            6 => 'Kisumu',
            7 => 'Siaya',
            8 => 'Kirinyaga',
        ];
    }

    public function beforeSave($insert)
    {
        $country = new Country();
        if (parent::beforeSave($insert)) {
            if ($this->recruiting_country == 'other' && !empty($this->other_country)) {
                // check wheather the other publisher already exists in the database
                $existingCountry = Country::find()->where(['name' => $this->other_country])->one();
                // create the publisher if it doesn't exist
                if (!$existingCountry || $existingCountry === null) {
                    $country->name = $this->other_country;
                    if (!$country->save()) {
                        return false; // stop saving if the publisher could not be saved
                    }
                }
                // set country to the value of other_country for saving in country table
                $this->recruiting_country = $country->id;
            }
            return true;
        }
        return false;
    }



}
