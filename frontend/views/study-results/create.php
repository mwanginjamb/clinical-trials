<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\StudyResults $model */

$this->title = Yii::t('app', 'Create Study Results');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Study Results'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-results-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
