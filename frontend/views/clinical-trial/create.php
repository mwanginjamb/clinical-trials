<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\ClinicalTrial $model */

$this->title = 'Create Clinical Trial';
$this->params['breadcrumbs'][] = ['label' => 'Clinical Trials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clinical-trial-create">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>