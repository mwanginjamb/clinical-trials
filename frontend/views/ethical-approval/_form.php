<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;
use frontend\models\EthicalApprovalDocument;

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

$documents = $model->isNewRecord ? [] : $model->documents;
$maxFiles = EthicalApprovalDocument::MAX_FILES;
$remaining = max(0, $maxFiles - count($documents));
$maxSizeLabel = Yii::$app->formatter->asShortSize(EthicalApprovalDocument::MAX_SIZE, 0);
// Delete links post through this separate form: inside the ActiveForm, yii.js would
// otherwise reuse (and resubmit) the whole approval form.
$docActionFormId = 'ethical-approval-document-action';
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

    <?php $form = ActiveForm::begin(array_merge_recursive(
        FormUi::formConfig($model->formName()),
        ['options' => ['enctype' => 'multipart/form-data']]
    )); ?>



     <div class="bg-surface-container-lowest p-6 sm:p-10 rounded-xl shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-primary border-b border-surface-container pb-4">
            Ethical Approval Information
        </h2>


     <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'ethical_regulatory_body', FormUi::fieldConfig()['base'])->dropDownList($model->ethicalRegulatoryBodyOptions,array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select ethical regulatory body'])) ?>
        <?= $form->field($model, 'approved_by_ethical_committee', FormUi::checkboxFieldConfig())->checkbox(array_merge(FormUi::checkboxConfig('Approved by ethical committee ?'), ['label' => 'Approved by ethical committee ?'])) ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?= $form->field($model, 'document_number', FormUi::fieldConfig()['base'])->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true, 'placeholder' => 'Enter the document number'])) ?>
        <div>
            <?= $form->field($model, 'documentFiles[]', FormUi::fieldConfig()['base'])
                ->fileInput(array_merge(FormUi::inputOptions()['text'], [
                    'multiple' => true,
                    'accept' => 'application/pdf,.pdf',
                    'disabled' => $remaining === 0,
                    'data-remaining' => $remaining,
                ]))
                ->label($model->getAttributeLabel('documentFiles')) ?>
            <p class="text-xs text-on-surface-variant mt-2">
                <?= $remaining > 0
                    ? Yii::t('app', 'PDF only, up to {size} each. You can add {n, plural, =1{one more document} other{# more documents}} (maximum {max}).', ['size' => $maxSizeLabel, 'n' => $remaining, 'max' => $maxFiles])
                    : Yii::t('app', 'The limit of {max} documents has been reached. Delete one below to upload another.', ['max' => $maxFiles]) ?>
            </p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <?= $form->field($model, 'trial_id')->textInput(array_merge(FormUi::inputOptions()['text'], ['readonly' => true])) ?>
    </div>

    <!-- Uploaded approval documents -->
    <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-surface-container pb-3">
            <h3 class="text-base font-bold text-primary"><?= Yii::t('app', 'Uploaded Approval Documents') ?></h3>
            <span class="text-xs font-semibold text-on-surface-variant"><?= count($documents) ?> / <?= $maxFiles ?></span>
        </div>

        <?php if (empty($documents)): ?>
            <p class="text-sm text-on-surface-variant py-4">
                <?= Yii::t('app', 'No documents uploaded yet. Choose up to {max} PDF files above and click Save.', ['max' => $maxFiles]) ?>
            </p>
        <?php else: ?>
            <div class="overflow-x-auto rounded-xl border border-surface-container">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low">
                            <th class="<?= FormUi::thClass() ?>">#</th>
                            <th class="<?= FormUi::thClass() ?>"><?= Yii::t('app', 'File Name') ?></th>
                            <th class="<?= FormUi::thClass() ?>"><?= Yii::t('app', 'Size') ?></th>
                            <th class="<?= FormUi::thClass() ?>"><?= Yii::t('app', 'Uploaded On') ?></th>
                            <th class="<?= FormUi::thClass(true) ?>"><?= Yii::t('app', 'Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        <?php foreach ($documents as $i => $document): ?>
                            <tr class="<?= FormUi::trClass() ?>">
                                <td class="<?= FormUi::tdClass('muted') ?>"><?= $i + 1 ?></td>
                                <td class="<?= FormUi::tdClass('primary') ?> break-all">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="material-symbols-outlined text-error text-[20px]">picture_as_pdf</span>
                                        <?= Html::encode($document->original_name) ?>
                                    </span>
                                </td>
                                <td class="<?= FormUi::tdClass('muted') ?>"><?= Html::encode($document->getSizeLabel()) ?></td>
                                <td class="<?= FormUi::tdClass('muted') ?>">
                                    <?= $document->created_at ? Yii::$app->formatter->asDatetime($document->created_at, 'medium') : '—' ?>
                                </td>
                                <td class="px-6 py-5 text-right whitespace-nowrap">
                                    <?= FormUi::actionBtn('visibility', ['document', 'id' => $document->id], 'view', [
                                        'title' => Yii::t('app', 'Preview'),
                                        'aria-label' => Yii::t('app', 'Preview {name}', ['name' => $document->original_name]),
                                        'target' => '_blank',
                                        'rel' => 'noopener',
                                    ]) ?>
                                    <?= FormUi::actionBtn('delete', ['delete-document', 'id' => $document->id], 'delete', [
                                        'title' => Yii::t('app', 'Delete'),
                                        'aria-label' => Yii::t('app', 'Delete {name}', ['name' => $document->original_name]),
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Delete "{name}"? This cannot be undone.', ['name' => $document->original_name]),
                                            'method' => 'post',
                                            'form' => $docActionFormId,
                                        ],
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    </div>

    <div class="flex flex-col sm:flex-row gap-4 my-6">
        <?= Html::submitButton(
            '<span class="material-symbols-outlined">save</span><span>' . Yii::t('app', 'Save') . '</span>',
            [
                'name' => 'intent',
                'value' => 'save',
                'class' => 'flex-1 inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl font-bold '
                    . 'text-primary bg-surface-container-low border border-primary/20 hover:bg-primary-fixed/30 transition-all',
            ]
        ) ?>
        <?= Html::submitButton(
            '<span>' . Yii::t('app', 'Save & Continue') . '</span><span class="material-symbols-outlined">arrow_forward</span>',
            [
                'name' => 'intent',
                'value' => 'continue',
                'class' => 'flex-1 inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl font-bold '
                    . 'bg-gradient-to-r from-primary to-primary-container text-white shadow-lg shadow-primary/20 '
                    . 'hover:scale-[1.01] transition-transform',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?= Html::beginForm('', 'post', ['id' => $docActionFormId, 'class' => 'hidden']) . Html::endForm() ?>

</div>

<?php
$fileInputId = Html::getInputId($model, 'documentFiles');
$this->registerJs(<<<JS
(function () {
    // Server-side validation enforces the limit too; this just warns before the upload.
    var input = document.getElementById('{$fileInputId}');
    if (!input) { return; }
    input.addEventListener('change', function () {
        var remaining = parseInt(input.getAttribute('data-remaining'), 10) || 0;
        var tooMany = input.files.length > remaining;
        input.setCustomValidity(tooMany
            ? 'You can upload at most ' + remaining + ' more document' + (remaining === 1 ? '' : 's') + '.'
            : '');
        if (tooMany) { input.reportValidity(); }
    });
})();
JS);
