<?php

/** @var yii\web\View $this */
/** @var frontend\models\Publisher $model */

$this->title = 'New publisher';
$this->params['breadcrumbs'][] = ['label' => 'Publishers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('../lookups/_header', [
    'title' => 'New publisher',
    'subtitle' => 'Journals and publishers offered when recording trial publications.',
]) ?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
