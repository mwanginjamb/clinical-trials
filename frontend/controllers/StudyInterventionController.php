<?php

namespace frontend\controllers;

use frontend\models\StudyIntervention;
use frontend\models\StudyInterventionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * StudyInterventionController implements the CRUD actions for StudyIntervention model.
 */
class StudyInterventionController extends Controller
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
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all StudyIntervention models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StudyInterventionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudyIntervention model.
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
     * Creates a new StudyIntervention model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new StudyIntervention();
        $model->trial_id = Yii::$app->request->get('trial_id') ?? Yii::$app->session->get('clinical_trial_id');

        //find model by trial_id, it it exists, redirect to update
        $existingModel = StudyIntervention::find()->where(['trial_id' => $model->trial_id])->one();
        if ($existingModel) {
            return $this->redirect(['update', 'id' => $existingModel->id]);
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // save model ID for wizard step
                Yii::$app->wizard->registerModel('study-intervention', $model->id);
                return $this->redirect(Yii::$app->urlManager->createUrl(['study-results/create', 'trial_id' => $model->trial_id]));
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StudyIntervention model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id = null, $trial_id = null)
    {
        $model = $this->findModel($id);
        if ($model === null && $trial_id !== null) {
            $model = StudyIntervention::find()->where(['trial_id' => $trial_id])->one();
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // save model ID for wizard step
            Yii::$app->wizard->registerModel('study-intervention', $model->id);
            return $this->redirect(Yii::$app->urlManager->createUrl(['study-results/create', 'trial_id' => $model->trial_id]));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing StudyIntervention model.
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
     * Finds the StudyIntervention model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return StudyIntervention the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudyIntervention::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
