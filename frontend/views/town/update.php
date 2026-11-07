<?php

/** @var yii\web\View $this */
/** @var frontend\models\Town $model */
/** @var array $countries id => name */

$this->title = 'Edit town: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Towns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<?= $this->render('../lookups/_header', [
    'title' => 'Edit town',
    'subtitle' => $model->name,
]) ?>
<?= $this->render('_form', [
    'model' => $model,
    'countries' => $countries,
]) ?>
