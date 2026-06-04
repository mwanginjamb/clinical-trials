<?php

namespace frontend\controllers;

use frontend\models\StudyPurpose;
use frontend\models\StudyPurposeSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * StudyPurposeController implements the CRUD actions for StudyPurpose model.
 */
class StudyPurposeController extends Controller
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
     * Lists all StudyPurpose models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StudyPurposeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudyPurpose model.
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
     * Creates a new StudyPurpose model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new StudyPurpose();
        $model->trial_id = Yii::$app->session->get('clinical_trial_id');

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // save model ID for wizard step
                Yii::$app->wizard->registerModel('study-purpose', $model->id);
                return $this->redirect(Url::toRoute(['study-population-eligibility/create']));
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StudyPurpose model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id = null, $trial_id = null)
    {
        if ($id) {
            $model = $this->findModel($id);
        } elseif ($trial_id) {
            $model = $this->findModelByTrialId($trial_id);
        }
        $model->trial_id = Yii::$app->session->get('clinical_trial_id');
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->wizard->registerModel('study-purpose', $model->id);
            return $this->redirect(Url::toRoute(['study-population-eligibility/update', 'trial_id' => $model->trial_id]));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing StudyPurpose model.
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
     * Finds the StudyPurpose model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return StudyPurpose the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudyPurpose::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the StudyPurpose model based on trial_id.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $trial_id ID
     * @return StudyPurpose the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByTrialId($trial_id)
    {
        if (($model = StudyPurpose::findOne(['trial_id' => $trial_id])) !== null) {
            return $model;
        }

        return new StudyPurpose();
    }
}
