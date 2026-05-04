<?php

use frontend\models\StudyPurpose;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPurposeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Study Purposes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-purpose-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Study Purpose', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'study_purpose:ntext',
            'study_objective:ntext',
            'study_hypothesis',
            'type_of_study',
            //'intervention',
            //'control_group_name',
            //'design_control_group_presence',
            //'phase_of_study',
            //'randomization_method_name',
            //'masking_description',
            //'masking_status',
            //'trial_id',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, StudyPurpose $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
