<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyResultsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="study-results-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'permission_to_publish') ?>

    <?= $form->field($model, 'summary_results') ?>

    <?= $form->field($model, 'authority_committe_name') ?>

    <?= $form->field($model, 'publisher') ?>

    <?php // echo $form->field($model, 'url_doi') ?>

    <?php // echo $form->field($model, 'publication_type') ?>

    <?php // echo $form->field($model, 'publication_title') ?>

    <?php // echo $form->field($model, 'trial_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
