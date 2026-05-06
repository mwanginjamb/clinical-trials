<?php

use common\library\FormUi;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPopulationEligibility $model */
/** @var yii\widgets\ActiveForm $form */

$steps = Yii::$app->params['steps'];
$actionId = Yii::$app->controller->action->id;
$totalSteps = count($steps);

$activeIndex = 0;
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

<div class="study-population-eligibility-form">


    <?php /* ── Editorial Header ────────────────────────────────────────────── */ ?>

    <header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Study Population and Eligibility Criteria
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Define the characteristics of the study population, including inclusion and exclusion criteria. This data
            ensures appropriate participant selection and regulatory compliance.
        </p>
    </header>

    <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('../clinical-trial/_progress_tracker') ?>

    <?php $form = ActiveForm::begin(FormUi::formConfig($model->formName())); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'health_condition_studied', FormUi::fieldConfig()['base'])->textarea(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter health condition studied...']))->hint($model->getAttributeHint('health_condition_studied')) ?>

        <?= $form->field($model, 'type_of_eligibility', FormUi::fieldConfig()['base'])->dropDownList($model->typeOfEligibilityOptions, array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select type of eligibility...']))->hint($model->getAttributeHint('type_of_eligibility')) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'participant_target_number', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter participant target number...', 'type' => 'number']))->hint($model->getAttributeHint('participant_target_number')) ?>

        <?= $form->field($model, 'sample_size', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter sample size...', 'type' => 'number']))->hint($model->getAttributeHint('sample_size')) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'final_number_of_participants')->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter final number of participants...', 'type' => 'number'])) ?>

    </div>

    <?= $form->field($model, 'trial_id')->hiddenInput()->label(false) ?>



    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => FormUi::buttonClass()]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>