<?php

namespace frontend\controllers;

use frontend\models\StudyPopulationEligibility;
use frontend\models\StudyPopulationEligibilitySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * StudyPopulationEligibilityController implements the CRUD actions for StudyPopulationEligibility model.
 */
class StudyPopulationEligibilityController extends Controller
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
     * Lists all StudyPopulationEligibility models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StudyPopulationEligibilitySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudyPopulationEligibility model.
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
     * Creates a new StudyPopulationEligibility model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new StudyPopulationEligibility();
        $model->trial_id = Yii::$app->session->get('clinical_trial_id');

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // save model ID for wizard step
                Yii::$app->wizard->registerModel('study-population-eligibility', $model->id);
                return $this->redirect(Yii::$app->urlManager->createUrl(['study-timeline/create']));
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StudyPopulationEligibility model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id = null, $trial_id = null)
    {
        if ($id) {
            $model = $this->findModel($id);
        } elseif ($trial_id) { // Find model by trial_id
            $model = $this->findModelByTrialId($trial_id);
        }
        $model->trial_id = Yii::$app->session->get('clinical_trial_id');
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->wizard->registerModel('study-population-eligibility', $model->id);
            return $this->redirect(Yii::$app->urlManager->createUrl(['study-timeline/update', 'trial_id' => $model->trial_id]));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing StudyPopulationEligibility model.
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
     * Finds the StudyPopulationEligibility model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return StudyPopulationEligibility the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudyPopulationEligibility::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the StudyPopulationEligibility model based on trial_id.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $trial_id ID
     * @return StudyPopulationEligibility the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByTrialId($trial_id)
    {
        if (($model = StudyPopulationEligibility::findOne(['trial_id' => $trial_id])) !== null) {
            return $model;
        }

        return new StudyPopulationEligibility();
    }
}
