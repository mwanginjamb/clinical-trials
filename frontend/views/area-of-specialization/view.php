<?php

/** @var yii\web\View $this */
/** @var frontend\models\AreaOfSpecialization $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Areas of Specialization', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('../lookups/_header', [
    'title' => (string) $model->title,
    'subtitle' => 'Area of specialization #' . $model->id,
]) ?>
<?= $this->render('../lookups/_detail', [
    'model' => $model,
    'noun' => 'area of specialization',
    'name' => (string) $model->title,
    'fields' => [
        'Title' => $model->title,
        'Description' => $model->description,
    ],
]) ?>
