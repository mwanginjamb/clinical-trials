<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;

/** @var yii\web\View $this */
/** @var frontend\models\StudyResults $model */
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

<div class="study-results-form">

<header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Study Results
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Define the study results information.
        </p>
    </header>

    <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('../clinical-trial/_progress_tracker') ?>

    <?php $form = ActiveForm::begin(FormUi::formConfig('study-results-form')); ?>


     <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-primary border-b border-surface-container pb-4">
            Study Results Information
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'permission_to_publish',FormUi::fieldConfig()['noLabel'])->checkbox(array_merge(FormUi::checkboxConfig('Permission to Publish ?'), ['label' => 'Permission to Publish'])) ?>

        <?= $form->field($model, 'summary_results',FormUi::fieldConfig()['base'])->textarea(array_merge(FormUi::inputOptions()['textarea'], ['maxlength' => true])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'authority_committe_name',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true])) ?>

    <?= $form->field($model, 'publisher',FormUi::fieldConfig()['base'])->dropDownList(\frontend\models\StudyResults::getPublisherOptions(), array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select Publisher'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'url_doi',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['type' => 'url'])) ?>

    <?= $form->field($model, 'publication_type',FormUi::fieldConfig()['base'])->dropDownList(\frontend\models\StudyResults::getPublicationTypeOptions(), array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select Publication Type'])) ?>
    </div>

     <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'publication_title',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true])) ?>

    <?= $form->field($model, 'trial_id',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['readonly' => true])) ?>
    </div>
   
    </div>

     <!-- ═══════════════════════════════════════════════════════
         Form Actions
     ═══════════════════════════════════════════════════════ -->
    <div class="flex items-center justify-end gap-6 pt-6 border-t border-outline-variant/20">

        <?php Html::a(
            'Cancel',
            ['attachee/create'],   // adjust route as needed
            ['class' => 'px-8 py-3 font-semibold text-on-surface-variant hover:text-primary transition-colors']
        ) ?>

        <?= Html::submitButton(
            'Save and Continue',
            [
                'class' => FormUi::buttonClass(),
            ]
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
