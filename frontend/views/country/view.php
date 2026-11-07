<?php

/** @var yii\web\View $this */
/** @var frontend\models\Country $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('../lookups/_header', [
    'title' => (string) $model->name,
    'subtitle' => 'Country #' . $model->id,
]) ?>
<?= $this->render('../lookups/_detail', [
    'model' => $model,
    'noun' => 'country',
    'name' => (string) $model->name,
    'confirm' => 'Delete country "' . $model->name . '" and its ' . $model->getTowns()->count() . ' town(s)? This cannot be undone.',
    'fields' => [
        'Name' => $model->name,
        'Code' => $model->code,
        'Towns' => (string) $model->getTowns()->count(),
    ],
]) ?>
