<?php

namespace frontend\controllers;

use frontend\models\ClinicalTrial;
use frontend\models\ClinicalTrialSearch;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * ClinicalTrialController implements the CRUD actions for ClinicalTrial model.
 */
class ClinicalTrialController extends Controller
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
     * Lists all ClinicalTrial models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ClinicalTrialSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClinicalTrial model.
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
     * Creates a new ClinicalTrial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {

        $model = new ClinicalTrial();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // save model ID for wizard step
                Yii::$app->wizard->registerModel('clinical-trial', $model->id);
                // save trial ID session for utilization by rest of dependant models
                Yii::$app->session->set('clinical_trial_id', $model->id);
                return $this->redirect(Url::toRoute(['study-purpose/create', 'id' => $model->id]));
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ClinicalTrial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id = null, $trial_id = null)
    {
        $model = $this->findModel($id);
        
        // if model is null , find it by trial_id
        if (!$model && $trial_id) {
            $model = ClinicalTrial::find()->where(['id' => $trial_id])->one();
        }
        
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            
            // save model ID for wizard step
            Yii::$app->wizard->registerModel('clinical-trial', $model->id);
            Yii::$app->session->set('clinical_trial_id', $model->id);
            return $this->redirect(Url::toRoute(['study-purpose/update', 'trial_id' => $model->id]));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ClinicalTrial model.
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
     * Finds the ClinicalTrial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ClinicalTrial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClinicalTrial::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
