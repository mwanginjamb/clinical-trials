<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "study_results".
 *
 * @property int $id
 * @property int|null $permission_to_publish
 * @property string|null $summary_results
 * @property string|null $authority_committe_name
 * @property string|null $publisher
 * @property string|null $url_doi
 * @property int|null $publication_type
 * @property string|null $publication_title
 * @property int $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ClinicalTrial $trial
 */
class StudyResults extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_results';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permission_to_publish', 'summary_results', 'authority_committe_name', 'publisher', 'url_doi', 'publication_type', 'publication_title', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['permission_to_publish', 'publication_type', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['summary_results'], 'string'],
            [['trial_id'], 'required'],
            [['authority_committe_name', 'publisher'], 'string', 'max' => 255],
            [['url_doi', 'publication_title'], 'string', 'max' => 250],
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
            'permission_to_publish' => Yii::t('app', 'Permission To Publish'),
            'summary_results' => Yii::t('app', 'Summary Results'),
            'authority_committe_name' => Yii::t('app', 'Authority Committe Name'),
            'publisher' => Yii::t('app', 'Publisher'),
            'url_doi' => Yii::t('app', 'Url Doi'),
            'publication_type' => Yii::t('app', 'Publication Type'),
            'publication_title' => Yii::t('app', 'Publication Title'),
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
