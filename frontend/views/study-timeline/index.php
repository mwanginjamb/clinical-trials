<?php

use frontend\models\StudyTimeline;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\StudyTimelineSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Study Timelines');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-timeline-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Study Timeline'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'study_duration',
            'study_site_location',
            'centre_postal_address',
            'anticipated_start_date',
            //'anticipated_end_date',
            //'recruitment_status',
            //'recruiting_country',
            //'centre_pysical_address',
            //'centre_region',
            //'trial_id',
            //'created_at',
            //'updated_at',
            //'updated_by',
            //'created_by',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, StudyTimeline $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
