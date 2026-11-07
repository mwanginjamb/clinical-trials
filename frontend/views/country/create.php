<?php

/** @var yii\web\View $this */
/** @var frontend\models\Country $model */

$this->title = 'New country';
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('../lookups/_header', [
    'title' => 'New country',
    'subtitle' => 'Countries offered in trial, investigator and funding forms.',
]) ?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
