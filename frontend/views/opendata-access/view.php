<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\OpendataAccess $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Opendata Accesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="opendata-access-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'allow_publishing',
            'repository_name',
            'study_identification_variable',
            'sensitivity_analysis_result:ntext',
            'effective_size_value',
            'adjustable_miltiple_comparison',
            'handling_missing_data',
            'document_path',
            'quality_assessment_variable',
            'risk_of_bias_assessment',
            'study_limitation:ntext',
            'funding_source',
            'potential_conflict_of_interest',
            'publication_bias_indicator',
            'heterogenity_measure',
            'confidential_interval',
            'trial_id',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ],
    ]) ?>

</div>
