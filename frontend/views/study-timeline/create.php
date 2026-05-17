<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\StudyTimeline $model */

$this->title = Yii::t('app', 'Create Study Timeline');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Study Timelines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-timeline-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
