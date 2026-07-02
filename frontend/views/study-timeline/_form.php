<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;

/** @var yii\web\View $this */
/** @var frontend\models\StudyTimeline $model */
/** @var yii\widgets\ActiveForm $form */

$steps = Yii::$app->params['steps'];
$actionId = Yii::$app->controller->action->id;
$totalSteps = count($steps);

$activeIndex = 0;

// show next/prev buttons based on current step index not more than 3 steps away from current step to prevent navigation to non-sequential steps
foreach ($steps as $i => $step) {
    if ($step['controller'] === Yii::$app->controller->id &&  in_array($step['action'],['create','update'])) {
        $activeIndex = $i;
        break;
    }
}

$stepNumber = str_pad($activeIndex + 1, 2, '0', STR_PAD_LEFT);
$prevStep = $activeIndex > 0 ? $steps[$activeIndex - 1] : null;
$nextStep = $activeIndex < $totalSteps - 1 ? $steps[$activeIndex + 1] : null;
?>

<div class="study-timeline-form">

    <header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Study Timeline
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Define the timeline for the study including start and end dates, recruitment status, and country
            information.
        </p>
    </header>

    <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('../clinical-trial/_progress_tracker') ?>

    <?php $form = ActiveForm::begin(FormUi::formConfig($model->formName())); ?>


    <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-primary border-b border-surface-container pb-4">
            Study Timeline
        </h2>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'study_duration', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Enter study duration...', 'type' => 'number'])) ?>

        <?= $form->field($model, 'study_site_location', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true])) ?>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'centre_postal_address', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true])) ?>
        <?= $form->field($model, 'anticipated_start_date', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['type' => 'date'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'anticipated_end_date', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['type' => 'date'])) ?>
        <?= $form->field($model, 'recruitment_status', FormUi::fieldConfig()['base'])->dropDownList($model->recruitmentStatus, array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select recruitment status...'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'recruiting_country', FormUi::fieldConfig()['base'])->dropDownList($model->recruitingCountry, array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select recruiting country...'])) ?>
        <?= $form->field($model, 'centre_pysical_address', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true, 'placeholder' => 'Enter centre physical address...'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'centre_region', FormUi::fieldConfig()['base'])->dropDownList($model->centreRegion, array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select centre region...'])) ?>
    </div>

    <?= $form->field($model, 'trial_id')->hiddenInput(['readonly' => true])->label(false) ?>

    </div>

    <!-- <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save and Continue'), ['class' => FormUi::buttonClass()]) ?>
    </div> -->

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