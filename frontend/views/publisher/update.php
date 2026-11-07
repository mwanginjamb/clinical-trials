<?php

/** @var yii\web\View $this */
/** @var frontend\models\Publisher $model */

$this->title = 'Edit publisher: ' . $model->publisher;
$this->params['breadcrumbs'][] = ['label' => 'Publishers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->publisher, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<?= $this->render('../lookups/_header', [
    'title' => 'Edit publisher',
    'subtitle' => $model->publisher,
]) ?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
