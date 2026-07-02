<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\StudyDescription $model */

/*$this->title = Yii::t('app', 'Update Study Description: {name}', [
    'name' => $model->id,
]);*/
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Study Descriptions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="study-description-update">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
