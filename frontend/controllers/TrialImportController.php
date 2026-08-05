<?php

namespace frontend\controllers;

use frontend\models\TrialImportForm;
use frontend\services\TrialBatchImporter;
use frontend\services\TrialTemplateBuilder;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;

class TrialImportController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // authenticated users only; tighten to a specific role/permission as needed
                    ],
                ],
            ],
        ];
    }

    /**
     * GET /trial-import/template
     * Streams the blank workbook the user fills in.
     */
    public function actionTemplate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        (new TrialTemplateBuilder())->outputAsDownload();
        Yii::$app->end();
    }

    /**
     * GET  /trial-import/import  -> shows the upload form
     * POST /trial-import/import  -> runs the batch import and shows a report
     */
    public function actionImport()
    {
        $form = new TrialImportForm();

        if (Yii::$app->request->isPost) {
            $form->file = UploadedFile::getInstance($form, 'file');

            if ($form->validate()) {
                $result = (new TrialBatchImporter())->import($form->file);

                Yii::$app->session->setFlash(
                    $result->hasErrors() ? 'warning' : 'success',
                    "{$result->successCount()} trial(s) imported, {$result->errorCount()} failed."
                );

                return $this->render('import-result', [
                    'result' => $result,
                ]);
            }
        }

        return $this->render('import', [
            'form' => $form,
        ]);
    }
}
