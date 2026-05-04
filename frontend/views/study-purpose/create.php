<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPurpose $model */

$this->title = 'Create Study Purpose';
$this->params['breadcrumbs'][] = ['label' => 'Study Purposes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-purpose-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
