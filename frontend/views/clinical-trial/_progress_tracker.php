<?php
/**
 * views/trials/_progress_tracker.php
 *
 * Reusable multi-step progress tracker partial.
 * Resolves active/completed/upcoming state automatically from the current
 * Yii2 controller/action context — no variables need to be passed in.
 *
 * Usage (in any wizard step view):
 *   <?= $this->render('_progress_tracker') ?>
 *
 * The partial reads _steps_config.php from the same directory to stay
 * decoupled from individual step views.
 */

use yii\helpers\Html;
use yii\helpers\Url;

// ── Load step definitions ────────────────────────────────────────────────────
$steps = Yii::$app->params['steps'];

// ── Resolve active step from current controller/action context ───────────────
$currentController = Yii::$app->controller->id;
$currentAction = Yii::$app->controller->action->id;

$activeIndex = 0; // fallback to first step if no match found

foreach ($steps as $i => $step) {
    if ($step['controller'] === $currentController && $step['action'] === $currentAction) {
        $activeIndex = $i;
        break;
    }
}

$totalSteps = count($steps);
?>

<div class="mb-12 flex items-center justify-between max-w-4xl px-4" role="navigation" aria-label="Form progress">

    <?php foreach ($steps as $i => $step):

        $isCompleted = $i < $activeIndex;
        $isActive = $i === $activeIndex;
        $isUpcoming = $i > $activeIndex;
        $isLast = $i === $totalSteps - 1;
        $stepNumber = $i + 1;

        // Completed steps are clickable; upcoming steps are not
        $stepUrl = $isCompleted ? Url::to($step['url']) : null;

        ?>

        <?php /* ── Step pill ───────────────────────────────────────────── */ ?>
        <div class="flex items-center gap-3 <?= $isUpcoming ? 'opacity-40' : '' ?>">

            <?php if ($isCompleted): ?>

                <?= Html::a(
                    '<span class="material-symbols-outlined text-sm" style="font-variation-settings: \'FILL\' 1">check</span>',
                    $stepUrl,
                    [
                        'class' => 'w-7 h-7 rounded bg-primary text-white flex items-center
                                         justify-center text-xs font-bold
                                         hover:opacity-80 transition-opacity',
                        'title' => $step['label'],
                        'aria-label' => "Go back to step {$stepNumber}: {$step['label']}",
                    ]
                ) ?>

            <?php elseif ($isActive): ?>

                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-primary-container
                             text-white flex items-center justify-center text-sm font-bold shadow-lg"
                    aria-current="step" title="Current step: <?= Html::encode($step['label']) ?>">
                    <?= $stepNumber ?>
                </div>

            <?php else: ?>

                <div class="w-7 h-7 rounded bg-outline-variant text-on-surface flex items-center
                             justify-center text-xs font-bold" title="<?= Html::encode($step['label']) ?>">
                    <?= $stepNumber ?>
                </div>

            <?php endif; ?>

            <span
                class="text-xs font-bold tracking-wide <?= $isActive ? 'text-primary' : 'text-on-surface' ?> hidden sm:block">
                <?= Html::encode($step['label']) ?>
            </span>

        </div>

        <?php /* ── Connector line (skipped after last step) ─────────────── */ ?>
        <?php if (!$isLast): ?>
            <div class="flex-1 h-[2px] mx-3 <?= $isCompleted ? 'bg-primary opacity-30' : 'bg-outline-variant' ?>"></div>
        <?php endif; ?>

    <?php endforeach; ?>

</div>