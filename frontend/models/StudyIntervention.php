<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "study_intervention".
 *
 * @property int $id
 * @property string|null $intervention_name
 * @property string|null $intervention_description
 * @property string|null $control_comparator
 * @property int|null $type_of_outcome
 * @property int $trial_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ClinicalTrial $trial
 */
class StudyIntervention extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_intervention';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['intervention_name', 'intervention_description', 'control_comparator', 'type_of_outcome', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['intervention_description'], 'string'],
            [['type_of_outcome', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['trial_id'], 'required'],
            [['intervention_name'], 'string', 'max' => 150],
            [['control_comparator'], 'string', 'max' => 255],
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
            'intervention_name' => Yii::t('app', 'Intervention Name'),
            'intervention_description' => Yii::t('app', 'Intervention Description'),
            'control_comparator' => Yii::t('app', 'Control Comparator'),
            'type_of_outcome' => Yii::t('app', 'Type Of Outcome'),
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
