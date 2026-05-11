<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\OpendataAccess $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="opendata-access-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'allow_publishing')->textInput() ?>

    <?= $form->field($model, 'repository_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'study_identification_variable')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sensitivity_analysis_result')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'effective_size_value')->textInput() ?>

    <?= $form->field($model, 'adjustable_miltiple_comparison')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'handling_missing_data')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'document_path')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quality_assessment_variable')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'risk_of_bias_assessment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'study_limitation')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'funding_source')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'potential_conflict_of_interest')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publication_bias_indicator')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'heterogenity_measure')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'confidential_interval')->textInput() ?>

    <?= $form->field($model, 'trial_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
