<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\OpendataAccessSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="opendata-access-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'allow_publishing') ?>

    <?= $form->field($model, 'repository_name') ?>

    <?= $form->field($model, 'study_identification_variable') ?>

    <?= $form->field($model, 'sensitivity_analysis_result') ?>

    <?php // echo $form->field($model, 'effective_size_value') ?>

    <?php // echo $form->field($model, 'adjustable_miltiple_comparison') ?>

    <?php // echo $form->field($model, 'handling_missing_data') ?>

    <?php // echo $form->field($model, 'document_path') ?>

    <?php // echo $form->field($model, 'quality_assessment_variable') ?>

    <?php // echo $form->field($model, 'risk_of_bias_assessment') ?>

    <?php // echo $form->field($model, 'study_limitation') ?>

    <?php // echo $form->field($model, 'funding_source') ?>

    <?php // echo $form->field($model, 'potential_conflict_of_interest') ?>

    <?php // echo $form->field($model, 'publication_bias_indicator') ?>

    <?php // echo $form->field($model, 'heterogenity_measure') ?>

    <?php // echo $form->field($model, 'confidential_interval') ?>

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
