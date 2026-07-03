<?php

namespace frontend\models;

use frontend\models\Publisher;
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


    public $other_publisher_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_results';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            'blameable' => [
                'class' => \yii\behaviors\BlameableBehavior::class,
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
            [['permission_to_publish', 'summary_results', 'authority_committe_name', 'url_doi', 'publication_type', 'publication_title', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['permission_to_publish', 'publication_type', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['summary_results'], 'string'],
            [['trial_id'], 'required'],
            [['authority_committe_name', 'publisher'], 'string', 'max' => 255],
            [['url_doi', 'publication_title'], 'string', 'max' => 250],
            [['url_doi'], 'url'],
            [['trial_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClinicalTrial::class, 'targetAttribute' => ['trial_id' => 'id']],
            ['publisher', 'safe'],
            ['other_publisher_name', 'string', 'max' => 250],
            [
                'other_publisher_name',
                'required',
                'when' => function ($model) {
                    return $model->publisher === 'other';
                },
                'whenClient' => "function (attribute, value) {
                return $('#studyresults-publisher').val() === 'other';
            }"
            ],
            [
                'publisher',
                'exist',
                'skipOnError' => true,
                'targetClass' => Publisher::class,
                'targetAttribute' => ['publisher' => 'publisher'],
                'message' => 'The selected publisher does not exist in the database.',
                'when' => function ($model) {
                    return $model->publisher !== 'other'; // Only validate existence if publisher is not 'other'
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
            'permission_to_publish' => Yii::t('app', 'Permission To Publish'),
            'summary_results' => Yii::t('app', 'Summary Results'),
            'authority_committe_name' => Yii::t('app', 'Authority Committe Name'),
            'publisher' => Yii::t('app', 'Publisher'),
            'other_publisher_name' => Yii::t('app', 'Other Publisher Name'),
            'url_doi' => Yii::t('app', 'Url / DOI'),
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
     * @return \yii\db\ActiveQuery|\frontend\models\query\ClinicalTrialQuery
     */
    public function getTrial()
    {
        return $this->hasOne(ClinicalTrial::class, ['id' => 'trial_id']);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\query\StudyResultsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\StudyResultsQuery(get_called_class());
    }

    // publisher options - major journals and publishers
    /*
        *@todo: fetch from database include a flag indicating whether the publisher is a major journal or a
        standard publisher
    */
    public static function getPublisherOptions()
    {
        return [
            1 => 'The Lancet',
            2 => 'New England Journal of Medicine',
            3 => 'JAMA',
            4 => 'BMJ',
            5 => 'The BMJ',
            6 => 'The New England Journal of Medicine',
            7 => 'The Lancet',
            8 => 'The BMJ',
            9 => 'JAMA',
            10 => 'The Lancet',
            11 => 'The BMJ',
            12 => 'JAMA',
            13 => 'Nature',
            14 => 'Science',
            15 => 'The New England Journal of Medicine',
            16 => 'The Lancet',
            17 => 'The BMJ',
            18 => 'JAMA',
            19 => 'Elsevier',
            20 => 'Springer',
            21 => 'Wiley',
            22 => 'Sage',
            23 => 'Taylor & Francis',
            24 => 'Oxford University Press',
            25 => 'Cambridge University Press',
            26 => 'Blackwell',
            27 => 'Karger',
            28 => 'S. Karger',
            29 => 'Karger Publishers',
            30 => 'S. Karger Publishers',
            31 => 'Other',
        ];
    }

    // options for publication types
    public static function getPublicationTypeOptions()
    {
        return [
            1 => 'Journal Article',
            2 => 'Conference Paper',
            3 => 'Book Chapter',
            4 => 'Report',
            5 => 'Other',
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->publisher == 'other' && !empty($this->other_publisher_name)) {
                // check wheather the other publisher already exists in the database
                $existingPublisher = Publisher::find()->where(['publisher' => $this->other_publisher_name])->one();
                // create the publisher if it doesn't exist
                if (!$existingPublisher || $existingPublisher === null) {
                    $publisher = new Publisher();
                    $publisher->publisher = $this->other_publisher_name;
                    if (!$publisher->save()) {
                        return false; // stop saving if the publisher could not be saved
                    }
                }
                // set publisher to the value of other_publisher_name for saving in study_results table
                $this->publisher = $this->other_publisher_name;
            }
            return true;
        }
        return false;
    }

}
