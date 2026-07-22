<?php

namespace frontend\controllers;

use frontend\models\Country;
use frontend\models\Region;
use frontend\models\StudyTimeline;
use frontend\models\StudyTimelineSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * StudyTimelineController implements the CRUD actions for StudyTimeline model.
 */
class StudyTimelineController extends Controller
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
     * Lists all StudyTimeline models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StudyTimelineSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudyTimeline model.
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
     * Creates a new StudyTimeline model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new StudyTimeline();
        $model->trial_id = Yii::$app->session->get('clinical_trial_id');

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // save model ID for wizard step
                Yii::$app->wizard->registerModel('study-timeline', $model->id);
                return $this->redirect(Yii::$app->urlManager->createUrl(['investigator-team/create']));
            }
        } else {
            $model->loadDefaultValues();
        }

        $countries = ArrayHelper::merge(
            ['other' => 'Other'],
            ArrayHelper::map(Country::find()->all(),'id','name'),
        );

         $regions = ArrayHelper::merge(
            ['other' => 'Other'],
            ArrayHelper::map(Region::find()->all(),'id','name'),
        );

        return $this->render('create', [
            'model' => $model,
            'countries' => $countries,
            'regions' => $regions,
        ]);
    }

    /**
     * Updates an existing StudyTimeline model.
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
            $model = StudyTimeline::findOne(['trial_id' => $trial_id]);
            if (!$model) {
                $model = new StudyTimeline();
                $model->trial_id = $trial_id;
            }
        }


        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->wizard->registerModel('study-timeline', $model->id);
            return $this->redirect(Yii::$app->urlManager->createUrl(['investigator-team/create', 'trial_id' => $model->trial_id]));
        }

         $countries = ArrayHelper::merge(
            ['other' => 'Other'],
            ArrayHelper::map(Country::find()->all(),'id','name'),
        );

         $regions = ArrayHelper::merge(
            ['other' => 'Other'],
            ArrayHelper::map(Region::find()->all(),'id','name'),
        );

        return $this->render('update', [
            'model' => $model,
            'countries' => $countries,
            'regions' => $regions,
        ]);
    }

    /**
     * Deletes an existing StudyTimeline model.
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
     * Finds the StudyTimeline model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return StudyTimeline the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudyTimeline::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
