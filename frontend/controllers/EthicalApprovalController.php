<?php

namespace frontend\controllers;

use frontend\models\EthicalApproval;
use frontend\models\EthicalApprovalSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EthicalApprovalController implements the CRUD actions for EthicalApproval model.
 */
class EthicalApprovalController extends Controller
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
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['logout', 'signup', 'index', 'view', 'create', 'update', 'delete'],
                    'rules' => [
                        [
                            'actions' => ['signup'],
                            'allow' => true,
                            'roles' => ['?'],
                        ],
                        [
                            'actions' => ['logout', 'index', 'view', 'create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
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
            if ($model->load($this->request->post()) && $model->save()) {
                // save model ID for wizard step
                Yii::$app->wizard->registerModel('ethical-approval', $model->id);
                return $this->redirect(Yii::$app->urlManager->createUrl(['funding/create']));
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->wizard->registerModel('ethical-approval', $model->id);
            return $this->redirect(Yii::$app->urlManager->createUrl(['funding/create', 'trial_id' => $model->trial_id]));
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
