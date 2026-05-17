<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\ClinicalTrial $model */

$this->title = 'Update Clinical Trial: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clinical Trials', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="clinical-trial-update">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
