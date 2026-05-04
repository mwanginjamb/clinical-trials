<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPurpose $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Study Purposes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="study-purpose-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'study_purpose:ntext',
            'study_objective:ntext',
            'study_hypothesis',
            'type_of_study',
            'intervention',
            'control_group_name',
            'design_control_group_presence',
            'phase_of_study',
            'randomization_method_name',
            'masking_description',
            'masking_status',
            'trial_id',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ],
    ]) ?>

</div>
