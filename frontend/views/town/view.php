<?php

/** @var yii\web\View $this */
/** @var frontend\models\Town $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Towns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('../lookups/_header', [
    'title' => (string) $model->name,
    'subtitle' => 'Town #' . $model->id,
]) ?>
<?= $this->render('../lookups/_detail', [
    'model' => $model,
    'noun' => 'town',
    'name' => (string) $model->name,
    'fields' => [
        'Name' => $model->name,
        'Country' => $model->country->name ?? null,
        'Code' => $model->code,
    ],
]) ?>
