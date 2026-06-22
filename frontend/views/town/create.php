<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\Town $model */

$this->title = Yii::t('app', 'Create Town');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Towns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="town-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'countries' => $countries
    ]) ?>

</div>