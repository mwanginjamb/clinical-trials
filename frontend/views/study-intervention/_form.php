<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;
use frontend\models\StudyIntervention;

/** @var yii\web\View $this */
/** @var frontend\models\StudyIntervention $model */
/** @var yii\widgets\ActiveForm $form */

$steps = Yii::$app->params['steps'];
$actionId = Yii::$app->controller->action->id;
$totalSteps = count($steps);

$activeIndex = 0;
foreach ($steps as $i => $step) {
    if ($step['controller'] === Yii::$app->controller->id && in_array($step['action'], ['create', 'update'])) {
        $activeIndex = $i;
        break;
    }
}

$stepNumber = str_pad($activeIndex + 1, 2, '0', STR_PAD_LEFT);
$prevStep = $activeIndex > 0 ? $steps[$activeIndex - 1] : null;
$nextStep = $activeIndex < $totalSteps - 1 ? $steps[$activeIndex + 1] : null;

?>

<div class="study-intervention-form">

        <?php /* ── Editorial Header ────────────────────────────────────────────── */ ?>
    <header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Study Intervention
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Provide details about the interventions being studied.
        </p>
    </header>


    <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('/clinical-trial/_progress_tracker') ?>

    <?php $form = ActiveForm::begin(FormUi::formConfig('study-intervention-form')); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger alert-dismissible fade show', 'role' => 'alert']) ?>

    <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-primary border-b border-surface-container pb-4">
            Study Intervention
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'intervention_name', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true])) ?>

            <?= $form->field($model, 'intervention_description', FormUi::fieldConfig()['base'])->textarea(array_merge(FormUi::inputOptions()['textarea'], ['rows' => 6])) ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'control_comparator', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true])) ?>

            <?= $form->field($model, 'type_of_outcome', FormUi::fieldConfig()['base'])->dropDownList(StudyIntervention::getTypeOfOutcomeOptions(), array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select type of outcome'])) ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'outcome_description', FormUi::fieldConfig()['base'])->textarea(array_merge(FormUi::inputOptions()['textarea'], ['rows' => 6])) ?>
            <?= $form->field($model, 'trial_id', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true, 'readonly' => true])) ?>
        </div>
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