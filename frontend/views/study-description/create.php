<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\StudyDescription $model */

$this->title = Yii::t('app', 'Create Study Description');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Study Descriptions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-description-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
