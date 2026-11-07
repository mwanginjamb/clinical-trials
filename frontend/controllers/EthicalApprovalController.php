<?php

namespace frontend\controllers;

use frontend\models\EthicalApproval;
use frontend\models\EthicalApprovalDocument;
use frontend\models\EthicalApprovalSearch;
use Yii;
use yii\filters\VerbFilter;
use frontend\components\AuthenticatedController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * EthicalApprovalController implements the CRUD actions for EthicalApproval model.
 */
class EthicalApprovalController extends AuthenticatedController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'delete-document' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all EthicalApproval models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EthicalApprovalSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EthicalApproval model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EthicalApproval model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new EthicalApproval();
        $model->trial_id = Yii::$app->session->get('clinical_trial_id');
        // fetch model by trial_id - check if already exists
        $existingModel = EthicalApproval::find()->where(['trial_id' => $model->trial_id])->one();
        if ($existingModel) {
            // redirect to update action
            return $this->redirect(['update', 'id' => $existingModel->id]);
        }

        if ($this->request->isPost) {
            if ($response = $this->saveAndRedirect($model)) {
                return $response;
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EthicalApproval model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $trial_id = null)
    {
        $model = $this->findModel($id);

        // if model is null , find it by trial_id
        if (!$model && $trial_id) {
            $model = EthicalApproval::find()->where(['trial_id' => $trial_id])->one();
        }

        if ($this->request->isPost && ($response = $this->saveAndRedirect($model))) {
            return $response;
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EthicalApproval model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * GET: streams an approval document inline so the browser previews the PDF.
     * Allowed for users who can view trials, or who can edit this trial.
     * @param int $id document ID
     * @throws NotFoundHttpException|ForbiddenHttpException
     */
    public function actionDocument($id)
    {
        $document = $this->findDocument($id);
        $trial = $document->ethicalApproval->trial;
        $user = Yii::$app->user;
        if (!$user->can('viewTrial') && !$user->can('updateTrial', ['trial' => $trial])) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to view this document.'));
        }

        $path = $document->getFilePath();
        if (!is_file($path)) {
            throw new NotFoundHttpException(Yii::t('app', 'The document file is missing.'));
        }

        return Yii::$app->response->sendFile($path, $document->original_name, [
            'mimeType' => $document->mime_type,
            'inline' => true,
        ]);
    }

    /**
     * POST: deletes an approval document (record and file), then returns to the form.
     * Edit rights on the trial are enforced by the model.
     * @param int $id document ID
     */
    public function actionDeleteDocument($id)
    {
        $document = $this->findDocument($id);
        $approvalId = $document->ethical_approval_id;

        if ($document->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Document "{name}" deleted.', ['name' => $document->original_name]));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'The document could not be deleted.'));
        }

        return $this->redirect(['update', 'id' => $approvalId]);
    }

    /**
     * Loads the POSTed form and uploaded documents, saves both, and redirects:
     * "Save" reloads the form (so the uploaded documents are listed), "Save & Continue"
     * moves on to the next wizard step.
     *
     * @return \yii\web\Response|null null when nothing was loaded or validation failed
     */
    private function saveAndRedirect(EthicalApproval $model)
    {
        if (!$model->load($this->request->post())) {
            return null;
        }
        $model->documentFiles = UploadedFile::getInstances($model, 'documentFiles');
        $uploaded = count($model->documentFiles);

        if (!$model->save()) {
            return null;
        }
        Yii::$app->wizard->registerModel('ethical-approval', $model->id);

        $failed = $model->saveDocumentFiles();
        if ($failed > 0) {
            Yii::$app->session->setFlash('error', Yii::t('app', '{n, plural, =1{One document} other{# documents}} could not be stored. Please try uploading again.', ['n' => $failed]));
        } elseif ($uploaded > 0) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Ethical approval saved with {n, plural, =1{one new document} other{# new documents}}.', ['n' => $uploaded]));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Ethical approval saved.'));
        }

        if ($this->request->post('intent') === 'continue' && $failed === 0) {
            return $this->redirect(['funding/create', 'trial_id' => $model->trial_id]);
        }

        return $this->redirect(['update', 'id' => $model->id]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findDocument($id): EthicalApprovalDocument
    {
        if (($document = EthicalApprovalDocument::findOne(['id' => $id])) !== null) {
            return $document;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested document does not exist.'));
    }

    /**
     * Finds the EthicalApproval model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return EthicalApproval the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EthicalApproval::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
