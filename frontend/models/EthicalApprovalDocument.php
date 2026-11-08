<?php

namespace frontend\models;

use common\library\FileStorage;
use Yii;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * An approval document (PDF) attached to an ethical approval.
 *
 * The file is stored privately under {@see storageDir()} with a random name; the original
 * file name is kept only for display/download. Deleting the record deletes the file.
 *
 * Writes are guarded like {@see TrialChildRecord}: in a web request the user must be
 * allowed to edit the parent trial.
 *
 * @property int $id
 * @property int $ethical_approval_id
 * @property string $original_name
 * @property string $stored_name
 * @property string $mime_type
 * @property int $size
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property EthicalApproval $ethicalApproval
 * @property-read string $filePath
 */
class EthicalApprovalDocument extends ActiveRecord
{
    /** Maximum number of documents per ethical approval. */
    public const MAX_FILES = 3;

    /** Maximum size of a single document, in bytes. */
    public const MAX_SIZE = 10 * 1024 * 1024;

    public static function tableName()
    {
        return 'ethical_approval_document';
    }

    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
            \yii\behaviors\BlameableBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['ethical_approval_id', 'original_name', 'stored_name', 'mime_type', 'size'], 'required'],
            [['ethical_approval_id', 'size'], 'integer'],
            [['original_name'], 'string', 'max' => 255],
            [['stored_name'], 'string', 'max' => 64],
            [['mime_type'], 'string', 'max' => 100],
            [['ethical_approval_id'], 'exist', 'skipOnError' => true, 'targetClass' => EthicalApproval::class, 'targetAttribute' => ['ethical_approval_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'original_name' => Yii::t('app', 'File Name'),
            'size' => Yii::t('app', 'Size'),
            'created_at' => Yii::t('app', 'Uploaded On'),
        ];
    }

    public function getEthicalApproval()
    {
        return $this->hasOne(EthicalApproval::class, ['id' => 'ethical_approval_id']);
    }

    public static function storageDir(int $approvalId): string
    {
        return Yii::getAlias('@uploads/ethical-approvals/' . $approvalId);
    }

    public function getFilePath(): string
    {
        return self::storageDir((int) $this->ethical_approval_id) . DIRECTORY_SEPARATOR . $this->stored_name;
    }

    /**
     * Stores an uploaded (already validated) file and saves its record.
     *
     * @return self|null the saved document, or null if the file or record could not be saved
     */
    public static function store(EthicalApproval $approval, UploadedFile $file): ?self
    {
        $doc = new self([
            'ethical_approval_id' => $approval->id,
            'original_name' => mb_substr($file->name, 0, 255),
            'stored_name' => Yii::$app->security->generateRandomString(32) . '.pdf',
            'mime_type' => 'application/pdf',
            'size' => (int) $file->size,
        ]);

        // creates @uploads/ethical-approvals/{id}/ (and any missing parent) on first use
        if (!FileStorage::saveUploadedFile($file, $doc->getFilePath())) {
            return null;
        }
        if (!$doc->save()) {
            @unlink($doc->getFilePath());
            return null;
        }

        return $doc;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->assertCanEditTrial();
        return true;
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        $this->assertCanEditTrial();
        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $path = $this->getFilePath();
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /** Human-readable file size, e.g. "1.4 MB". */
    public function getSizeLabel(): string
    {
        return Yii::$app->formatter->asShortSize($this->size, 1);
    }

    /**
     * @throws ForbiddenHttpException
     */
    protected function assertCanEditTrial(): void
    {
        if (!Yii::$app instanceof \yii\web\Application) {
            return;
        }
        $trial = $this->ethicalApproval->trial ?? null;
        if ($trial !== null && !Yii::$app->user->can('updateTrial', ['trial' => $trial])) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to modify this trial.'));
        }
    }
}
