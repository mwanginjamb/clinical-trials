<?php

/** @var yii\web\View $this */
/** @var frontend\models\Country $model */

$this->title = 'Edit country: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<?= $this->render('../lookups/_header', [
    'title' => 'Edit country',
    'subtitle' => $model->name,
]) ?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
