<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPopulationEligibility $model */

$this->title = 'Create Study Population Eligibility';
$this->params['breadcrumbs'][] = ['label' => 'Study Population Eligibilities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-population-eligibility-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
