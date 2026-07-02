<?php

use common\library\CurrencyHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;

/** @var yii\web\View $this */
/** @var frontend\models\Funding $model */
/** @var yii\widgets\ActiveForm $form */

$wizard = Yii::$app->wizard;
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

<div class="funding-form">

 <header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Funding
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Define the funding information for the study.
        </p>
    </header>

     <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('../clinical-trial/_progress_tracker') ?>

    <?php $form = ActiveForm::begin(FormUi::formConfig('funding-form')); ?>


    
    <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-primary border-b border-surface-container pb-4">
            Funding Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'sponsor_name',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'],['placeholder' => 'Enter sponsor name'])) ?>

            <?= $form->field($model, 'Amount',FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'],['placeholder' => 'Enter amount','type' => 'number'])) ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'country',FormUi::fieldConfig()['base'])->dropDownList($countries,array_merge(FormUi::inputOptions()['select'],['prompt' => 'Select country ...'])) ?>

            <?= $form->field($model, 'funding_Sector',FormUi::fieldConfig()['base'])->dropDownList($model->fundingSectors,array_merge(FormUi::inputOptions()['select'],['prompt' => 'Select funding sector ...'])) ?>
        </div>

         <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

          <?= $form->field($model, 'currency',FormUi::fieldConfig()['base'])->dropDownList(CurrencyHelper::getSymbolList(),array_merge(FormUi::inputOptions()['select'],['prompt' => 'Select Currency ...'])) ?>

         </div>



        <?= $form->field($model, 'trial_id')->hiddenInput()->label(false) ?>

    

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


<?php

$script = <<<JS
 $('select').select2();
JS;

// is->registerJs($script);