<?php

use common\library\FormUi;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPurpose $model */
/** @var yii\widgets\ActiveForm $form */

$steps = Yii::$app->params['steps'];
$actionId = Yii::$app->controller->action->id;
$totalSteps = count($steps);

$activeIndex = 0;

// show next/prev buttons based on current step index not more than 3 steps away from current step to prevent navigation to non-sequential steps
foreach ($steps as $i => $step) {
    if ($step['action'] === $actionId) {
        $activeIndex = $i;
        break;
    }
}

$stepNumber = str_pad($activeIndex + 1, 2, '0', STR_PAD_LEFT);
$prevStep = $activeIndex > 0 ? $steps[$activeIndex - 1] : null;
$nextStep = $activeIndex < $totalSteps - 1 ? $steps[$activeIndex + 1] : null;
?>

<div class="study-purpose-form">

    <?php /* ── Editorial Header ────────────────────────────────────────────── */ ?>

    <header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Study Purpose and Design
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Define the scientific intent and methodological framework for the upcoming
            clinical investigation. This data ensures protocol compliance and regulatory alignment.
        </p>
    </header>

    <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('../clinical-trial/_progress_tracker') ?>


    <?php $form = ActiveForm::begin(FormUi::formConfig('clinical-trial-form')); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'study_purpose', FormUi::fieldConfig()['base'])->textarea(array_merge(FormUi::inputOptions()['textarea'], ['rows' => 3])) ?>

        <?= $form->field($model, 'study_objective', FormUi::fieldConfig()['base'])->textarea(array_merge(FormUi::inputOptions()['textarea'], ['rows' => 3])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'study_hypothesis', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter study hypothesis...']))->hint($model->getAttributeHint('study_hypothesis')) ?>

        <?= $form->field($model, 'type_of_study', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter type of study...']))->hint($model->getAttributeHint('type_of_study')) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <?= $form->field($model, 'intervention', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter intervention...']))->hint($model->getAttributeHint('intervention')) ?>

        <?= $form->field($model, 'control_group_name', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter control group name...']))->hint($model->getAttributeHint('control_group_name')) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <?= $form->field($model, 'design_control_group_presence', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter design control group presence...']))->hint($model->getAttributeHint('design_control_group_presence')) ?>

        <?= $form->field($model, 'phase_of_study', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter phase of study...']))->hint($model->getAttributeHint('phase_of_study')) ?>

    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <?= $form->field($model, 'randomization_method_name', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter randomization method name...']))->hint($model->getAttributeHint('randomization_method_name')) ?>

        <?= $form->field($model, 'masking_description', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter masking description...']))->hint($model->getAttributeHint('masking_description')) ?>

    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <?= $form->field($model, 'masking_status', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter masking status...']))->hint($model->getAttributeHint('masking_status')) ?>

        <?= $form->field($model, 'trial_id', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['readonly' => true]))->hint($model->getAttributeHint('trial_id')) ?>

    </div>






    <!-- ═══════════════════════════════════════════════════════
         Form Actions
     ═══════════════════════════════════════════════════════ -->
    <div class="flex items-center justify-end gap-6 pt-6 border-t border-outline-variant/20">

        <?= Html::submitButton(
            'Save and Continue',
            [
                'class' => FormUi::buttonClass(),
            ]
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>