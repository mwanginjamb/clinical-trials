<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "study_description".
 *
 * @property int $id
 * @property string|null $study_website
 * @property string|null $lay_summary
 * @property string|null $scientific_summary
 * @property int $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ClinicalTrial $trial
 */
class StudyDescription extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_description';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            'blameable' => [
                'class' => 'yii\behaviors\BlameableBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['study_website', 'lay_summary', 'scientific_summary', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['lay_summary', 'scientific_summary'], 'string'],
            [['trial_id'], 'required'],
            [['trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['study_website'], 'string', 'max' => 255],
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
            'study_website' => Yii::t('app', 'Study Website'),
            'lay_summary' => Yii::t('app', 'Lay Summary'),
            'scientific_summary' => Yii::t('app', 'Scientific Summary'),
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
