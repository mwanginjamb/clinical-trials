<?php

/** @var yii\web\View $this */
/** @var frontend\models\Publisher $model */

$this->title = $model->publisher;
$this->params['breadcrumbs'][] = ['label' => 'Publishers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('../lookups/_header', [
    'title' => (string) $model->publisher,
    'subtitle' => 'Publisher #' . $model->id,
]) ?>
<?= $this->render('../lookups/_detail', [
    'model' => $model,
    'noun' => 'publisher',
    'name' => (string) $model->publisher,
    'fields' => [
        'Publisher' => $model->publisher,
    ],
]) ?>
