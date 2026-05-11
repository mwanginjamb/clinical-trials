<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "ethical_approval".
 *
 * @property int $id
 * @property int|null $ethical_regulatory_body
 * @property int|null $approved_by_ethical_committee
 * @property string|null $document_number
 * @property string|null $document_path
 * @property int $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ClinicalTrial $trial
 */
class EthicalApproval extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ethical_approval';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ethical_regulatory_body', 'approved_by_ethical_committee', 'document_number', 'document_path', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['ethical_regulatory_body', 'approved_by_ethical_committee', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['trial_id'], 'required'],
            [['document_number', 'document_path'], 'string', 'max' => 255],
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
            'ethical_regulatory_body' => Yii::t('app', 'Ethical Regulatory Body'),
            'approved_by_ethical_committee' => Yii::t('app', 'Approved By Ethical Committee'),
            'document_number' => Yii::t('app', 'Document Number'),
            'document_path' => Yii::t('app', 'Document Path'),
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
