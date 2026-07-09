<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\AreaOfSpecialization $model */

$this->title = Yii::t('app', 'Create Area Of Specialization');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Area Of Specializations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="area-of-specialization-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
