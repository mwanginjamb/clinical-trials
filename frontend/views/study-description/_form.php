<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;

/** @var yii\web\View $this */
/** @var frontend\models\StudyDescription $model */
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

<div class="study-description-form">


<?php /* ── Editorial Header ────────────────────────────────────────────── */ ?>
    <header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Study Description
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Provide a comprehensive overview of the study's objectives, methodology, and expected outcomes.
        </p>
    </header>


     <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('/clinical-trial/_progress_tracker') ?>

    <?php $form = ActiveForm::begin(FormUi::formConfig('study-description-form')); ?>

    <?php
    /* -------------------------------------------------------------------
       SECTION 1 – Study Description
       ------------------------------------------------------------------- */
    ?>

    <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-primary border-b border-surface-container pb-4">
            Study Description
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'study_website',FormUi::fieldConfig()['base'])->textInput(array_merge(['maxlength' => true,'type' => 'url','placeholder' => 'https://example.com'], FormUi::inputOptions()['text']))->hint('The URL of the study website') ?>

            <?= $form->field($model, 'lay_summary',FormUi::fieldConfig()['base'])->textarea(array_merge(['rows' => 6], FormUi::inputOptions()['textarea']))->hint('A simplified explanation of the study for the general public') ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'scientific_summary',FormUi::fieldConfig()['base'])->textarea(array_merge(['rows' => 6], FormUi::inputOptions()['textarea'])) ?>

            <?= $form->field($model, 'trial_id')->hiddenInput()->label(false) ?>
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
