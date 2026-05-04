<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPurpose $model */

$this->title = 'Update Study Purpose: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Study Purposes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="study-purpose-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
