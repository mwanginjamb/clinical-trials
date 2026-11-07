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
 * @property EthicalApprovalDocument[] $documents
 */
class EthicalApproval extends TrialChildRecord
{
    /** @var \yii\web\UploadedFile[] approval documents submitted with the form (not a column) */
    public $documentFiles = [];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ethical_approval';
    }

    public function behaviors()
    {
        return  [
            \yii\behaviors\TimestampBehavior::class,
            \yii\behaviors\BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ethical_regulatory_body', 'approved_by_ethical_committee', 'document_number', 'document_path', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['ethical_regulatory_body', 'approved_by_ethical_committee', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['trial_id', 'ethical_regulatory_body', 'approved_by_ethical_committee'], 'required'],
            [['document_number', 'document_path'], 'string', 'max' => 255],
            [['trial_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClinicalTrial::class, 'targetAttribute' => ['trial_id' => 'id']],
            [['documentFiles'], 'file',
                'skipOnEmpty' => true,
                'extensions' => 'pdf',
                'mimeTypes' => 'application/pdf',
                'maxSize' => EthicalApprovalDocument::MAX_SIZE,
                'maxFiles' => EthicalApprovalDocument::MAX_FILES,
            ],
            [['documentFiles'], 'validateDocumentLimit', 'skipOnEmpty' => true],
        ];
    }

    /** Existing + newly uploaded documents may not exceed the per-approval limit. */
    public function validateDocumentLimit($attribute)
    {
        $existing = $this->isNewRecord ? 0 : (int) $this->getDocuments()->count();
        $remaining = EthicalApprovalDocument::MAX_FILES - $existing;
        $incoming = count((array) $this->$attribute);

        if ($incoming > $remaining) {
            $this->addError($attribute, $remaining > 0
                ? Yii::t('app', 'You can upload at most {n, plural, =1{one more document} other{# more documents}} (limit {max}).', ['n' => $remaining, 'max' => EthicalApprovalDocument::MAX_FILES])
                : Yii::t('app', 'The limit of {max} documents has been reached. Delete one to upload another.', ['max' => EthicalApprovalDocument::MAX_FILES]));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ethical_regulatory_body' => Yii::t('app', 'Ethical Regulatory Body'),
            'approved_by_ethical_committee' => Yii::t('app', 'Approved By Ethical Committee ?'),
            'document_number' => Yii::t('app', 'Document Number'),
            'document_path' => Yii::t('app', 'Document Path'),
            'documentFiles' => Yii::t('app', 'Approval Documents (PDF)'),
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

    /**
     * Gets query for [[Documents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(EthicalApprovalDocument::class, ['ethical_approval_id' => 'id'])
            ->orderBy(['id' => SORT_ASC]);
    }

    /**
     * Stores {@see $documentFiles} (already validated) against this saved approval.
     *
     * @return int number of documents that could NOT be stored
     */
    public function saveDocumentFiles(): int
    {
        $failed = 0;
        foreach ((array) $this->documentFiles as $file) {
            if (EthicalApprovalDocument::store($this, $file) === null) {
                $failed++;
            }
        }
        $this->documentFiles = [];
        return $failed;
    }

    /** Deletes documents through ActiveRecord so their files are removed too (the FK cascade would orphan them). */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        foreach ($this->documents as $document) {
            $document->delete();
        }
        return true;
    }

    // Get Ethical Regulatory Body Options
    public function getEthicalRegulatoryBodyOptions()
    {
        return [
            1 => 'Internal Review Board',
            2 => 'External Review Board',
            3 => 'Other',
        ];
    }

}
