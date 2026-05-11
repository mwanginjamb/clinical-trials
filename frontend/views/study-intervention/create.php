<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\StudyIntervention $model */

$this->title = Yii::t('app', 'Create Study Intervention');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Study Interventions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-intervention-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
