<?php

/** @var yii\web\View $this */
/** @var frontend\models\AreaOfSpecialization $model */

$this->title = 'Edit area of specialization: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Areas of Specialization', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<?= $this->render('../lookups/_header', [
    'title' => 'Edit area of specialization',
    'subtitle' => $model->title,
]) ?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
