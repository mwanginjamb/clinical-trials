<?php

/** @var yii\web\View $this */
/** @var frontend\models\Town $model */
/** @var array $countries id => name */

$this->title = 'New town';
$this->params['breadcrumbs'][] = ['label' => 'Towns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('../lookups/_header', [
    'title' => 'New town',
    'subtitle' => 'Towns and cities, each linked to a country.',
]) ?>
<?= $this->render('_form', [
    'model' => $model,
    'countries' => $countries,
]) ?>
