<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\Funding $model */

$this->title = Yii::t('app', 'Create Funding');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fundings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="funding-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
        'countries' => $countries
    ]) ?>

</div>
