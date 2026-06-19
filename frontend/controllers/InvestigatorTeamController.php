<?php

namespace frontend\controllers;

use frontend\models\InvestigatorTeam;
use frontend\models\InvestigatorTeamSearch;
use Yii;
use yii\base\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * InvestigatorTeamController implements the CRUD actions for InvestigatorTeam model.
 */
class InvestigatorTeamController extends Controller
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
                        'save-member' => ['POST', 'PUT'], // Only allow POST and PUT for the saveMember action
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

    // Exclude 'save-member' from CSRF validation since it's an AJAX endpoint that may be called from JS without a CSRF token
    public function beforeAction($action)
    {
        if ($action->id === 'save-member') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all InvestigatorTeam models.
     *
     * @return string
     */
    public function actionIndex(int $trialId)
    {
        $searchModel = new InvestigatorTeamSearch();
        $params = $this->request->queryParams();
        $params['InvestigatorTeamSearch']['trial_id'] = $trialId;

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'trial_id' => $trialId
        ]);
    }

    /**
     * Displays a single InvestigatorTeam model.
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
     * Creates a new InvestigatorTeam model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($trial_id)
    {
        $model = new InvestigatorTeam();
        $searchModel = new InvestigatorTeamSearch();
        $params = $this->request->queryParams;

        if (!isset($params['InvestigatorTeamSearch'])) {
            $params['InvestigatorTeamSearch'] = [];
        }

        $params['InvestigatorTeamSearch']['trial_id'] = $trial_id;

        $dataProvider = $searchModel->search($params);

        // Load all records for pre-rendering into the table
        $members = $dataProvider->getModels();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // save model ID for wizard step
                Yii::$app->wizard->registerModel('investigator-team', $model->id);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'members' => $members,
        ]);
    }

    /**
     * Updates an existing InvestigatorTeam model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // save model ID for wizard step
            Yii::$app->wizard->registerModel('investigator-team', $model->id);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing InvestigatorTeam model.
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
     * Finds the InvestigatorTeam model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return InvestigatorTeam the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InvestigatorTeam::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    // save member data via AJAX for dynamic form
    public function actionSaveMember()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Only accept POST/PUT + XMLHttpRequest
        if (!Yii::$app->request->isPost && !Yii::$app->request->isPut || !Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('Invalid request.');
        }

        // Parse JSON body sent by fetch()
        $body = Json::decode(Yii::$app->request->rawBody, true);

        if (empty($body)) {
            return ['success' => false, 'message' => 'Empty request body.'];
        }

        $id = $body['id'] ?? null;

        // Load existing record (update) or create a new one (insert)
        if ($id) {
            $model = InvestigatorTeam::findOne($id);
            if (!$model) {
                return ['success' => false, 'message' => 'Record not found.'];
            }
        } else {
            $model = new InvestigatorTeam();
        }

        // Map payload fields onto the model — only assign safe/expected attributes
        $model->setAttributes([
            'role' => $body['role'] ?? null,
            'name' => $body['name'] ?? null,
            'institution' => $body['institution'] ?? null,
            'country' => $body['country'] ?? null,
            'city' => $body['city'] ?? null,
            'postal_address' => $body['postal_address'] ?? null,
            'email_address' => $body['email_address'] ?? null,
            'mobile_number' => $body['mobile_number'] ?? null,
            'trial_id' => Yii::$app->session->get('clinical_trial_id') ?? 0, // Fallback to session if not provided in payload
        ]);




        if ($model->save()) {
            return [
                'success' => true,
                'id' => $model->id,           // JS writes this back to data-member-id
                'message' => $id ? 'Member updated.' : 'Member saved.',
            ];
        }


        // Return validation errors so the front end can display them if needed
        return [
            'success' => false,
            'message' => 'Validation failed - ' . (Yii::$app->session->get('clinical_trial_id') ?? 'NS'),
            'errors' => $model->getFirstErrors(),

        ];


    }
}
