<?php

/** @var yii\web\View $this */
/** @var frontend\models\AreaOfSpecialization $model */

$this->title = 'New area of specialization';
$this->params['breadcrumbs'][] = ['label' => 'Areas of Specialization', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('../lookups/_header', [
    'title' => 'New area of specialization',
    'subtitle' => 'Specialities used to classify trials.',
]) ?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
