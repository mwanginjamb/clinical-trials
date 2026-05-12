<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPopulationEligibility $model */

$this->title = 'Update Study Population Eligibility: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Study Population Eligibilities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="study-population-eligibility-update">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
