<?php

namespace frontend\controllers;

use frontend\models\StudyDescription;
use frontend\models\StudyDescriptionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * StudyDescriptionController implements the CRUD actions for StudyDescription model.
 */
class StudyDescriptionController extends Controller
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
     * Lists all StudyDescription models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StudyDescriptionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudyDescription model.
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
     * Creates a new StudyDescription model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new StudyDescription();
        $model->trial_id = Yii::$app->session->get('clinical_trial_id');

       // search model by trial_id, if it exists redirect to update action
        $studyDescription = StudyDescription::find()->where(['trial_id' => $model->trial_id])->one();
        if ($studyDescription) {
            $model->id = $studyDescription->id;
            return $this->redirect(['update', 'id' => $studyDescription->id]);
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // save model ID for wizard step
                Yii::$app->wizard->registerModel('study-description', $model->id);
                return $this->redirect(Yii::$app->urlManager->createUrl(['study-intervention/create']));
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StudyDescription model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $trial_id = null)
    {
        $model = $this->findModel($id);
        // if $model is null and trial_id isset find model by  trial_id
        if(!$model && $trial_id) {
            $model = StudyDescription::find()->where(['trial_id' => $trial_id])->one();
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // save model ID for wizard step
            Yii::$app->wizard->registerModel('study-description', $model->id);
            return $this->redirect(Yii::$app->urlManager->createUrl(['study-intervention/create']));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing StudyDescription model.
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
     * Finds the StudyDescription model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return StudyDescription the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudyDescription::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
