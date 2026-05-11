<?php

use yii\helpers\Html;

$wizard = Yii::$app->wizard;

$steps = $wizard->getSteps();

$visibleSteps = $wizard->getVisibleSteps(4);

$activeIndex = $wizard->getActiveIndex();

$totalSteps = count($steps);

?>

<div class="mb-10">

    <!-- Header -->
    <div class="flex items-center justify-between mb-4">

        <div class="text-sm font-semibold text-primary">
            Step <?= $activeIndex + 1 ?>
            of
            <?= $totalSteps ?>
        </div>

        <div class="text-xs text-on-surface-variant">
            <?= $wizard->getProgressPercentage() ?>%
            Complete
        </div>

    </div>

    <!-- Progress Bar -->
    <div class="w-full h-2 bg-outline-variant rounded-full overflow-hidden mb-8">

        <div
            class="h-full bg-primary transition-all duration-500"
            style="width: <?= $wizard->getProgressPercentage() ?>%">
        </div>

    </div>

    <!-- Stepper -->
    <div
        class="flex items-center justify-between gap-3"
        role="navigation"
        aria-label="Form progress"
    >

        <?php foreach ($visibleSteps as $i => $step):

            $isCompleted = $wizard->isCompleted($i);

            $isActive = $wizard->isActive($i);

            $isUpcoming = $wizard->isUpcoming($i);

            $stepUrl = $wizard->getStepUrl($step, $i);

            $stepNumber = $i + 1;

            $isLastVisible = $i === array_key_last($visibleSteps);

        ?>

            <div class="flex items-center flex-1">

                <div class="flex flex-col items-center text-center min-w-[100px]">

                    <?php if ($isCompleted && $stepUrl): ?>

                        <?= Html::a(
                            '<span class="material-symbols-outlined text-sm"
                                style="font-variation-settings: \'FILL\' 1">
                                check
                            </span>',
                            $stepUrl,
                            [
                                'class' => '
                                    w-10 h-10 rounded-full
                                    bg-primary text-white
                                    flex items-center justify-center
                                    shadow-sm hover:scale-105
                                    transition-transform
                                ',
                            ]
                        ) ?>

                    <?php elseif ($isActive): ?>

                        <div
                            class="
                                w-12 h-12 rounded-2xl
                                bg-gradient-to-br
                                from-primary to-primary-container
                                text-white font-bold
                                flex items-center justify-center
                                shadow-lg
                            "
                            aria-current="step"
                        >
                            <?= $stepNumber ?>
                        </div>

                    <?php else: ?>

                        <div
                            class="
                                w-10 h-10 rounded-full
                                bg-outline-variant
                                text-on-surface
                                flex items-center justify-center
                                opacity-40
                            "
                        >
                            <?= $stepNumber ?>
                        </div>

                    <?php endif; ?>

                    <div
                        class="
                            mt-2 text-xs font-medium leading-tight
                            <?= $isActive
                                ? 'text-primary'
                                : 'text-on-surface-variant'
                            ?>
                        "
                    >
                        <?= Html::encode($step['label']) ?>
                    </div>

                </div>

                <?php if (!$isLastVisible): ?>

                    <div
                        class="
                            flex-1 h-[2px] mx-3 rounded-full
                            <?= $isCompleted
                                ? 'bg-primary'
                                : 'bg-outline-variant'
                            ?>
                        ">
                    </div>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

</div>