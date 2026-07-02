<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;

/** @var yii\web\View $this */
/** @var frontend\models\EthicalApproval $model */
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

<div class="ethical-approval-form">

 <header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Ethical Approval
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Define the ethical approval information for the study.
        </p>
    </header>

     <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('../clinical-trial/_progress_tracker') ?>

    <?php $form = ActiveForm::begin(FormUi::formConfig($model->formName())); ?>



     <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-primary border-b border-surface-container pb-4">
            Ethical Approval Information
        </h2>


     <div class="grid grid-cols-1 md:grid-cols-2 gap-10">   
        <?= $form->field($model, 'ethical_regulatory_body', FormUi::fieldConfig()['base'])->dropDownList($model->ethicalRegulatoryBodyOptions,array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select ethical regulatory body'])) ?>
        <?= $form->field($model, 'approved_by_ethical_committee', FormUi::checkboxFieldConfig())->checkbox(array_merge(FormUi::checkboxConfig('Approved by ethical committee ?'), ['label' => 'Approved by ethical committee ?'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'document_number', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true, 'placeholder' => 'Enter the document number'])) ?>
        <?= $form->field($model, 'document_path', FormUi::fieldConfig()['base'])->fileInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true, 'placeholder' => 'Enter the document path'])) ?>

        
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'trial_id')->textInput(array_merge(FormUi::inputOptions()['text'], ['readonly' => true])) ?>
    </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => FormUi::buttonClass()]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
