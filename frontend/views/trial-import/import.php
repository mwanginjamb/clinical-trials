<?php

/**
 * @var yii\web\View $this
 * @var frontend\models\TrialImportForm $form
 */

use frontend\services\TrialImportSchema;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Bulk Trial Import';
$this->params['breadcrumbs'][] = $this->title;

$steps = [
    ['download', 'Download the template', 'It lists the allowed values for coded columns from this system, so get a fresh copy when lookups change.'],
    ['edit_note', 'Fill it in', 'One row per trial on the Trials sheet; link purpose, eligibility and investigator rows with the same trial_ref. Red headers are required.'],
    ['fact_check', 'Upload to check', 'Nothing is saved yet. You get a report of what would import and what needs fixing.'],
    ['task_alt', 'Confirm', 'Import the valid trials. Rows with problems are left out; fix them and upload again.'],
];
?>
<div class="max-w-5xl min-w-0">
    <div class="mb-8">
        <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Administration</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold font-headline text-on-surface tracking-tight"><?= Html::encode($this->title) ?></h1>
        <p class="mt-1 text-sm text-on-surface-variant">Onboard many trials at once from the Excel template (up to <?= TrialImportSchema::MAX_TRIALS ?> per file).</p>
    </div>

    <ol class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-[0.75rem] mb-8">
        <?php foreach ($steps as $i => [$icon, $title, $text]): ?>
            <li class="min-w-0 bg-surface-container-low rounded-2xl px-[1.125rem] py-[1.125rem]">
                <div class="flex items-center gap-2 mb-2">
                    <span class="h-7 w-7 rounded-full bg-primary-container text-white text-xs font-bold flex items-center justify-center shrink-0"><?= $i + 1 ?></span>
                    <span class="material-symbols-outlined text-primary text-[20px]"><?= $icon ?></span>
                </div>
                <p class="text-sm font-bold text-on-surface"><?= Html::encode($title) ?></p>
                <p class="mt-1 text-xs text-on-surface-variant leading-relaxed"><?= Html::encode($text) ?></p>
            </li>
        <?php endforeach; ?>
    </ol>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-2 min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-6">
            <h2 class="font-headline font-bold text-on-surface mb-1">1. Template</h2>
            <p class="text-xs text-on-surface-variant mb-6">Instructions, dropdowns and an example row are included.</p>
            <?= Html::a(
                '<span class="material-symbols-outlined text-[20px]">download</span>Download template (.xlsx)',
                ['template'],
                ['class' => 'w-full inline-flex justify-center items-center gap-2 px-6 py-[0.75rem] rounded-xl text-sm font-bold text-primary bg-primary-fixed/40 hover:bg-primary-fixed/70 transition-colors', 'data-pjax' => 0]
            ) ?>
        </div>

        <div class="lg:col-span-3 min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-6">
            <h2 class="font-headline font-bold text-on-surface mb-1">2. Upload to check</h2>
            <p class="text-xs text-on-surface-variant mb-6">Nothing is saved until you confirm on the next screen.</p>

            <?php $activeForm = ActiveForm::begin([
                'action' => ['import'],
                'options' => ['enctype' => 'multipart/form-data'],
                'fieldConfig' => [
                    'errorOptions' => ['tag' => 'p', 'class' => 'text-xs text-error mt-2'],
                ],
            ]); ?>
                <?= $activeForm->field($form, 'file', ['template' => "{input}\n{error}"])->fileInput([
                    'accept' => '.xlsx,.xls',
                    'class' => 'block w-full text-sm text-on-surface-variant
                        file:mr-[0.75rem] file:px-[1.125rem] file:py-2.5 file:rounded-xl file:border-0
                        file:text-sm file:font-semibold file:bg-surface-container file:text-on-surface
                        hover:file:bg-surface-container-high cursor-pointer
                        border border-dashed border-outline-variant/50 rounded-2xl p-[0.75rem]',
                ]) ?>
                <?= Html::submitButton(
                    '<span class="material-symbols-outlined text-[20px]">fact_check</span>Upload and check',
                    ['class' => 'mt-6 w-full inline-flex justify-center items-center gap-2 px-6 py-[0.75rem] rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:opacity-90']
                ) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
