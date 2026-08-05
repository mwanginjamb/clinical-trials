<?php

namespace frontend\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Simple form model backing the upload widget on the import page.
 */
class TrialImportForm extends Model
{
    /** @var UploadedFile */
    public $file;

    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => 'xlsx, xls', 'maxSize' => 1024 * 1024 * 10], // 10MB
            // Mime Type Validator
            [
                'file',
                'file',
                'checkExtensionByMimeType' => true,
                'mimeTypes' => [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                ]
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Batch upload workbook (.xlsx)',
        ];
    }
}
