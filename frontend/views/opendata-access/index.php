<?php

use frontend\models\OpendataAccess;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\OpendataAccessSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Opendata Accesses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="opendata-access-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Opendata Access'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'allow_publishing',
            'repository_name',
            'study_identification_variable',
            'sensitivity_analysis_result:ntext',
            //'effective_size_value',
            //'adjustable_miltiple_comparison',
            //'handling_missing_data',
            //'document_path',
            //'quality_assessment_variable',
            //'risk_of_bias_assessment',
            //'study_limitation:ntext',
            //'funding_source',
            //'potential_conflict_of_interest',
            //'publication_bias_indicator',
            //'heterogenity_measure',
            //'confidential_interval',
            //'trial_id',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, OpendataAccess $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
