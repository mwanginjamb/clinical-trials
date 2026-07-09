<?php

use common\library\FormUi;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\InvestigatorTeam $model */
/** @var yii\widgets\ActiveForm $form */

$steps = Yii::$app->params['steps'];
$actionId = Yii::$app->controller->action->id;
$totalSteps = count($steps);

$activeIndex = 0;

// show next/prev buttons based on current step index not more than 3 steps away from current step to prevent navigation to non-sequential steps
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

<div class="trials-form">
    <?php /* ── Editorial Header ────────────────────────────────────────────── */ ?>
    <header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Trial General Details
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Define the clinical trial general details.
        </p>
    </header>

    <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('_progress_tracker') ?>


    <?php $form = ActiveForm::begin(FormUi::formConfig('clinical-trial-form')); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger alert-dismissible fade show', 'role' => 'alert']) ?>
    <?php
    /* -------------------------------------------------------------------
       SECTION 1 – Scientific Rationale
       ------------------------------------------------------------------- */
    ?>

    <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-primary border-b border-surface-container pb-4">
            Scientific Rationale
        </h2>

        <!-- area of specialization field spanning 2 columns -->
        <div class="grid grid-cols-1 gap-10">
            <?= $form->field($model, 'area_of_specialization', FormUi::fieldConfig()['base'])
                ->dropDownList(
                    $studyAreas,
                    array_merge(FormUi::inputOptions()['select'], [
                        'prompt' => 'Select an area of specialization...',
                    ])
                )
                ->hint($model->getAttributeHint('area_of_specialization'))
                ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10" id="other_area_of_specialization"
            style="<?= $model->area_of_specialization === 'other' ? '' : 'display:none' ?>">
            <?= $form->field($model, 'other_area_of_specialization', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true])) ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'scientific_title', FormUi::fieldConfig()['base'])
                ->textarea(array_merge(FormUi::inputOptions()['textarea'], [
                    'rows' => 3,
                    'placeholder' => 'Describe the therapeutic or scientific problem this study intends to solve...',
                ]))
                ->hint($model->getAttributeHint('scientific_title'))
                ?>

            <?= $form->field($model, 'public_title', FormUi::fieldConfig()['base'])
                ->textarea(array_merge(FormUi::inputOptions()['textarea'], [
                    'rows' => 3,
                    'placeholder' => 'Provide a layperson-friendly summary of the study’s purpose and significance...',
                ]))
                ->hint($model->getAttributeHint('public_title'))
                ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'protocol_version', FormUi::fieldConfig()['base'])
                ->textInput(array_merge(FormUi::inputOptions()['text'], [
                    'placeholder' => 'Enter the protocol version...',
                ]))
                ->hint($model->getAttributeHint('protocol_version'))
                ?>

            <?= $form->field($model, 'registration_status', FormUi::fieldConfig()['base'])
                ->dropDownList($model->registrationStatusOptions, array_merge(FormUi::inputOptions()['select'], [
                    'prompt' => 'Select registration status...',
                ]))
                ->hint($model->getAttributeHint('registration_status'))
                ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?= $form->field($model, 'protocol_number', FormUi::fieldConfig()['base'])
                ->textInput(array_merge(FormUi::inputOptions()['text'], [
                    'placeholder' => 'Enter the protocol number...',
                ]))
                ->hint($model->getAttributeHint('protocol_number'))
                ?>

            <?= $form->field($model, 'registration_number', FormUi::fieldConfig()['base'])
                ->textInput(array_merge(FormUi::inputOptions()['text'], [
                    'placeholder' => 'Enter the registration number...',
                ]))
                ->hint($model->getAttributeHint('registration_number'))
                ?>
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


    <?php ActiveForm::end() ?>
</div>

<?php

$script = <<<JS
    function toggleOtherInstitution() {
        const institution = $('#clinicaltrial-area_of_specialization').val();

        if (institution === 'other') {
            $('#other_area_of_specialization').slideDown();
        } else {
            $('#other_area_of_specialization').slideUp();
            $('#clinicaltrial-other_area_of_specialization').val('');
        }
    }

    toggleOtherInstitution();

    $('#clinicaltrial-area_of_specialization').on('change', function () {
        toggleOtherInstitution();
    });
JS;
$this->registerJs($script);
?>