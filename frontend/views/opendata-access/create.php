<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\OpendataAccess $model */

$this->title = Yii::t('app', 'Create Opendata Access');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Opendata Accesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="opendata-access-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
