<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "investigator_team".
 *
 * @property int $id
 * @property int|null $role
 * @property string|null $institution
 * @property int|null $country
 * @property string|null $name
 * @property string|null $mobile_number
 * @property string|null $email_address
 * @property string|null $postal_address
 * @property int|null $city
 * @property int $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ClinicalTrial $trial
 */
class InvestigatorTeam extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'investigator_team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role', 'institution', 'country', 'name', 'mobile_number', 'email_address', 'postal_address', 'city', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['role', 'country', 'city', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['trial_id'], 'required'],
            [['institution'], 'string', 'max' => 255],
            [['name', 'mobile_number', 'email_address', 'postal_address'], 'string', 'max' => 150],
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
            'role' => Yii::t('app', 'Role'),
            'institution' => Yii::t('app', 'Institution'),
            'country' => Yii::t('app', 'Country'),
            'name' => Yii::t('app', 'Name'),
            'mobile_number' => Yii::t('app', 'Mobile Number'),
            'email_address' => Yii::t('app', 'Email Address'),
            'postal_address' => Yii::t('app', 'Postal Address'),
            'city' => Yii::t('app', 'City'),
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
     * @return \yii\db\ActiveQuery|\frontend\models\query\ClinicalTrialQuery
     */
    public function getTrial()
    {
        return $this->hasOne(ClinicalTrial::class, ['id' => 'trial_id']);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\query\InvestigatorTeamQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\InvestigatorTeamQuery(get_called_class());
    }

    // Get PI role options for dropdown
    public static function getRoleOptions()
    {
        return [
            1 => 'Principal Investigator',
            2 => 'Co-Principal Investigator',
            3 => 'Collaborator',
        ];
    }

    // get Country Options
    public function getCountryOptions()
    {
        return [
            1 => 'Kenya',
            2 => 'Uganda',
            3 => 'United Kingdom',
            4 => 'Canada',
            5 => 'Australia',
            6 => 'Germany',
            // Add more countries as needed
        ];
    }

    // Get City Options

    public function getCityOptions()
    {
        return [
            1 => 'Nairobi',
            2 => 'Kampala',
            3 => 'London',
            4 => 'Toronto',
            5 => 'Sydney',
            6 => 'Berlin',
            // Add more cities as needed
        ];
    }


}
