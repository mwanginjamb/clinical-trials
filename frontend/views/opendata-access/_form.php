<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;

/** @var yii\web\View $this */
/** @var frontend\models\OpendataAccess $model */
/** @var yii\widgets\ActiveForm $form */

$steps = Yii::$app->params['steps'];
$actionId = Yii::$app->controller->action->id;
$totalSteps = count($steps);
$activeIndex = 0;

// show next/prev buttons based on current step index not more than 3 steps away from current step to prevent navigation to non-sequential steps
foreach ($steps as $i => $step) {
    if ($step['controller'] === Yii::$app->controller->id && $step['action'] === Yii::$app->controller->action->id) {
        $activeIndex = $i;
        break;
    }
}

$stepNumber = str_pad($activeIndex + 1, 2, '0', STR_PAD_LEFT);
$prevStep = $activeIndex > 0 ? $steps[$activeIndex - 1] : null;
$nextStep = $activeIndex < $totalSteps - 1 ? $steps[$activeIndex + 1] : null;
?>

<div class="opendata-access-form">

<header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Open Data Access
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Define the open data access information for the study.
        </p>
    </header>

     <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('../clinical-trial/_progress_tracker') ?>

    <?php $form = ActiveForm::begin(FormUi::formConfig('opendata-access-form')); ?>

   <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-primary border-b border-surface-container pb-4">
            Open Data Access
        </h2>
        
        
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'allow_publishing',FormUi::fieldConfig()['base'])->dropDownList([1 => 'Yes', 0 => 'No'],array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select'])) ?>
            <?= $form->field($model, 'sensitivity_analysis_result',FormUi::fieldConfig()['base'])->textarea(array_merge(FormUi::inputOptions()['textarea'], ['rows' => 6])) ?>
    </div>
    

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'study_identification_variable',FormUi::fieldConfig()['base'])->dropDownList(\frontend\models\OpendataAccess::getStudyIdentificationVariableOptions(),array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select ...'])) ?>
        
        <?= $form->field($model, 'significant_p_value',FormUi::fieldConfig()['base'])->dropDownList(\frontend\models\OpendataAccess::getSignificantPValueOptions(),array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select ...'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'effective_size_value',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter effective size value','type' => 'number'])) ?>

    <?= $form->field($model, 'adjustable_miltiple_comparison',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter adjustable multiple comparison'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'handling_missing_data',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter handling missing data'])) ?>

    <?php $form->field($model, 'document_path',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter document path'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'quality_assessment_variable',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter quality assessment variable'])) ?>

    <?= $form->field($model, 'risk_of_bias_assessment',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter risk of bias assessment'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'study_limitation',FormUi::fieldConfig()['base'])->textarea(array_merge(FormUi::inputOptions()['textarea'], ['rows' => 6])) ?>

    <?php $form->field($model, 'funding_source',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter funding source'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'potential_conflict_of_interest',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter potential conflict of interest'])) ?>

    <?= $form->field($model, 'publication_bias_indicator',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter publication bias indicator'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'heterogenity_measure',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter heterogenity measure'])) ?>

    <?= $form->field($model, 'confidential_interval',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => '0.05', 'type' => 'number'])) ?>

</div>
    <?= $form->field($model, 'trial_id',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['readonly' => true])) ?>


    </div>

   <div class="flex items-center justify-end gap-6 pt-6 border-t border-outline-variant/20">

        <?= Html::submitButton(
            'Complete Adding Trial Data',
            [
                'class' => FormUi::buttonClass(),
            ]
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
