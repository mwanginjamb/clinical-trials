<?php

/**
 * Report for both steps: the validation run (dryRun, with a confirm button when something
 * is importable) and the real import.
 *
 * @var yii\web\View $this
 * @var frontend\services\TrialImportResult $result
 * @var string|null $token     set when the checked file can be confirmed
 * @var string $fileName
 */

use frontend\services\TrialImportResult as R;
use yii\helpers\Html;

$dry = $result->dryRun;
$ok = $result->okCount();
$errors = $result->count(R::STATUS_ERROR);
$skipped = $result->count(R::STATUS_SKIPPED);

$this->title = $dry ? 'Import check' : 'Import result';
$this->params['breadcrumbs'][] = ['label' => 'Bulk Trial Import', 'url' => ['import']];
$this->params['breadcrumbs'][] = $this->title;

$tile = static fn(string $icon, string $label, int $n, string $cls): string =>
    '<div class="min-w-0 flex items-center gap-[0.75rem] bg-surface-container-low rounded-2xl px-[1.125rem] py-[0.875rem]">'
    . '<span class="h-10 w-10 rounded-full flex items-center justify-center shrink-0 ' . $cls . '"><span class="material-symbols-outlined">' . $icon . '</span></span>'
    . '<div><p class="text-2xl font-black text-on-surface leading-none">' . $n . '</p>'
    . '<p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">' . $label . '</p></div></div>';

$badge = [
    R::STATUS_OK => [$dry ? 'Ready' : 'Imported', 'bg-tertiary-container text-white'],
    R::STATUS_ERROR => ['Needs fixing', 'bg-error-container text-on-error-container'],
    R::STATUS_SKIPPED => ['Skipped', 'bg-[#fff3cd] text-[#7a5c00]'],
];
$issueStyle = [
    'fatal' => ['error', 'bg-error-container/60 text-on-error-container'],
    'error' => ['error', 'bg-error-container/40 text-on-error-container'],
    'warning' => ['warning', 'bg-[#fff3cd] text-[#7a5c00]'],
    'info' => ['info', 'bg-surface-container text-on-surface-variant'],
];
$btn = 'inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold';
?>
<div class="min-w-0 max-w-6xl">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-[1rem] mb-6">
        <div class="min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Bulk Trial Import</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-headline text-on-surface tracking-tight"><?= Html::encode($this->title) ?></h1>
            <p class="mt-1 text-sm text-on-surface-variant break-all">
                <span class="material-symbols-outlined text-[16px] align-middle">description</span>
                <?= Html::encode($fileName) ?>
                <?= $dry ? ' · checked, nothing has been saved yet' : '' ?>
            </p>
        </div>

        <div class="flex flex-wrap gap-2 shrink-0">
            <?php if ($token !== null): ?>
                <?= Html::beginForm(['discard'], 'post', ['class' => 'contents']) ?>
                    <?= Html::hiddenInput('token', $token) ?>
                    <?= Html::submitButton('Cancel', ['class' => $btn . ' text-on-surface-variant hover:bg-surface-container']) ?>
                <?= Html::endForm() ?>
                <?= Html::beginForm(['confirm'], 'post', ['class' => 'contents']) ?>
                    <?= Html::hiddenInput('token', $token) ?>
                    <?= Html::submitButton(
                        '<span class="material-symbols-outlined text-[20px]">task_alt</span>Import ' . $ok . ' valid trial' . ($ok === 1 ? '' : 's'),
                        [
                            'class' => $btn . ' text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:opacity-90',
                            'data-confirm' => ($errors || $skipped)
                                ? "Import the {$ok} valid trial(s)? The " . ($errors + $skipped) . ' other(s) will be left out.'
                                : null,
                        ]
                    ) ?>
                <?= Html::endForm() ?>
            <?php endif; ?>
            <?= Html::a(
                '<span class="material-symbols-outlined text-[20px]">upload_file</span>' . ($dry && ($errors || !$ok) ? 'Upload corrected file' : 'Upload another file'),
                ['import'],
                ['class' => $btn . ' text-primary bg-primary-fixed/40 hover:bg-primary-fixed/70']
            ) ?>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-[0.75rem] mb-6">
        <?= $tile('task_alt', $dry ? 'Ready to import' : 'Imported', $ok, 'bg-tertiary-container text-white') ?>
        <?= $tile('error', 'Need fixing', $errors, 'bg-error-container text-on-error-container') ?>
        <?= $tile('content_copy', 'Skipped (duplicates)', $skipped, 'bg-[#fff3cd] text-[#7a5c00]') ?>
    </div>

    <?php if ($dry && $ok && ($errors || $skipped)): ?>
        <p class="mb-6 text-sm text-on-surface-variant bg-surface-container-low rounded-xl px-[1.125rem] py-[0.75rem]">
            You can import the <?= $ok ?> ready trial<?= $ok === 1 ? '' : 's' ?> now and upload the rest later once fixed:
            trials already imported will then be skipped as duplicates, so nothing is created twice.
        </p>
    <?php endif; ?>

    <?php if ($result->getFileIssues()): ?>
        <section class="mb-6 min-w-0">
            <h2 class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">File</h2>
            <ul class="space-y-2">
                <?php foreach ($result->getFileIssues() as $issue):
                    [$icon, $cls] = $issueStyle[$issue['level']] ?? $issueStyle['error']; ?>
                    <li class="flex items-start gap-2 rounded-xl px-[0.875rem] py-2.5 text-sm <?= $cls ?>">
                        <span class="material-symbols-outlined text-[18px] shrink-0 mt-px"><?= $icon ?></span>
                        <span class="min-w-0 break-words"><?= Html::encode($issue['message']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if ($result->getTrials()): ?>
        <section class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden">
            <ul class="divide-y divide-outline-variant/10">
                <?php foreach ($result->getTrials() as $t):
                    [$label, $cls] = $badge[$t['status']]; ?>
                    <li class="px-[1.125rem] sm:px-6 py-[1.125rem]">
                        <div class="flex flex-wrap items-center gap-x-[0.75rem] gap-y-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $cls ?>"><?= $label ?></span>
                            <span class="text-xs font-bold text-primary"><?= Html::encode($t['ref']) ?></span>
                            <span class="text-xs text-on-surface-variant">Trials row <?= $t['row'] ?></span>
                            <?php if ($t['trialId']): ?>
                                <?= Html::a('Open trial #' . $t['trialId'] . ' <span class="material-symbols-outlined text-[14px] align-middle">open_in_new</span>',
                                    ['clinical-trial/view', 'id' => $t['trialId']],
                                    ['class' => 'ml-auto text-xs font-semibold text-primary hover:underline']) ?>
                            <?php endif; ?>
                        </div>
                        <p class="mt-1 text-sm font-semibold text-on-surface break-words"><?= Html::encode($t['title'] ?: '(no scientific title)') ?></p>

                        <?php if ($t['messages']): ?>
                            <ul class="mt-2 space-y-1">
                                <?php foreach ($t['messages'] as $m): ?>
                                    <li class="flex items-start gap-1.5 text-xs <?= $t['status'] === R::STATUS_ERROR ? 'text-error' : 'text-on-surface-variant' ?>">
                                        <span class="material-symbols-outlined text-[14px] shrink-0 mt-px"><?= $t['status'] === R::STATUS_ERROR ? 'close' : 'info' ?></span>
                                        <span class="min-w-0 break-words"><?= Html::encode($m) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php elseif ($t['children']): ?>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <?php foreach ($t['children'] as $section => $n): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-surface-container text-on-surface-variant">
                                        <?= Html::encode($section) ?><?= $n > 1 ? ' ×' . $n : '' ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
</div>
