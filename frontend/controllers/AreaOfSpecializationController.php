<?php

namespace frontend\controllers;

use Yii;
use frontend\models\AreaOfSpecialization;
use frontend\models\AreaOfSpecializationSearch;
use frontend\components\AuthenticatedController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AreaOfSpecializationController implements the CRUD actions for AreaOfSpecialization model.
 */
class AreaOfSpecializationController extends AuthenticatedController
{
    protected ?string $requiredPermission = 'manageLookups';

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
     * Lists all AreaOfSpecialization models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AreaOfSpecializationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AreaOfSpecialization model.
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
     * Creates a new AreaOfSpecialization model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new AreaOfSpecialization();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Area of specialization "' . $model->title . '" created.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AreaOfSpecialization model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Area of specialization "' . $model->title . '" updated.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AreaOfSpecialization model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        try {
            if ($model->delete()) {
                Yii::$app->session->setFlash('success', 'Area of specialization "' . $model->title . '" deleted.');
            }
        } catch (\yii\db\IntegrityException $e) {
            Yii::$app->session->setFlash('error', 'Area of specialization "' . $model->title . '" is still in use and cannot be deleted.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the AreaOfSpecialization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return AreaOfSpecialization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AreaOfSpecialization::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
