<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "funding".
 *
 * @property int $id
 * @property int|null $sponsor_name
 * @property float|null $Amount
 * @property int|null $country
 * @property int|null $funding_Sector
 * @property int|null $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $update_by
 *
 * @property ClinicalTrial $trial
 */
class Funding extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'funding';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sponsor_name', 'Amount', 'country', 'funding_Sector', 'trial_id', 'created_at', 'updated_at', 'created_by', 'update_by'], 'default', 'value' => null],
            [['sponsor_name', 'country', 'funding_Sector', 'trial_id', 'created_at', 'updated_at', 'created_by', 'update_by'], 'integer'],
            [['Amount'], 'number'],
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
            'sponsor_name' => Yii::t('app', 'Sponsor Name'),
            'Amount' => Yii::t('app', 'Amount'),
            'country' => Yii::t('app', 'Country'),
            'funding_Sector' => Yii::t('app', 'Funding Sector'),
            'trial_id' => Yii::t('app', 'Trial ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'update_by' => Yii::t('app', 'Update By'),
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
     * @return \frontend\models\query\FundingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\FundingQuery(get_called_class());
    }

}
