<?php

/**
 * Clinical Curator — Guest Layout
 *
 * Used for all unauthenticated views: login, register, forgot-password, etc.
 * No sidebar, no topbar — full-screen centred card over a decorative background.
 *
 * @var \yii\web\View $this
 * @var string        $content Rendered view output injected by Yii.
 */

use frontend\assets\DashAsset;
use yii\helpers\Html;

DashAsset::register($this);

$this->registerCssFile(
    'https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800'
    . '&family=Inter:wght@400;500;600&display=swap',
    ['rel' => 'stylesheet']
);
$this->registerCssFile(
    'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined'
    . ':wght,FILL@100..700,0..1&display=swap',
    ['rel' => 'stylesheet']
);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        <?= Html::encode($this->title) ?>
    </title>
    <?php $this->head() ?>
</head>

<body class="bg-background min-h-screen flex items-center justify-center overflow-hidden">
    <?php $this->beginBody() ?>

    <!-- ════════════════════════════════════════════════════════════════════════════
     Decorative background layer
     ════════════════════════════════════════════════════════════════════════════ -->
    <div class="fixed inset-0 z-0">
        <?= Html::img(
            'https://lh3.googleusercontent.com/aida-public/AB6AXuAF9qoRSY9fpKyShvEfVp4HLlRDvmB98He-uwIoGqK_WY1SE8aMdxWo7YP5TCrLh3fXlooNN5XppY-aJ3xsrV1LDTkj8EAQ_tznRBjbG2VQ7YU1GnVCwe0q1mLT47WAsQ_OFc4Bh5MOoS3GFgbkWwADB_labyDXahPudgFlMoAJa89-mHCmMcvNvEgvFTueORbEpYzT8OgTmoS8O8yGoIfhlSX_r8CwGxMP6fHNebK0fVyGUw_x29R7z9GgZqtq-pgGjzHhs8GSsM4',
            [
                'alt' => '',
                'class' => 'w-full h-full object-cover opacity-20 filter grayscale contrast-125',
            ]
        ) ?>
        <div class="absolute inset-0 bg-gradient-to-tr from-surface via-surface/80 to-transparent"></div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════════
     Guest content — view-specific card/form rendered here
     ════════════════════════════════════════════════════════════════════════════ -->
    <main class="relative z-10 w-full max-w-[580px] px-6 ">

        <!-- Shared branding anchor (views may override $this->params['brandSubtitle']) -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center p-4 mb-6 rounded-xl
                    bg-surface-container-lowest
                    shadow-[0_12px_32px_-4px_rgba(0,59,83,0.08)]">
                <span class="material-symbols-outlined text-primary text-4xl">clinical_notes</span>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-primary leading-tight">
                Clinical Curator
            </h1>
            <p class="text-on-surface-variant font-label text-sm uppercase tracking-widest mt-2">
                <?= Html::encode($this->params['brandSubtitle'] ?? 'Institutional Research Portal') ?>
            </p>
        </div>

        <?= $content ?>

        <!-- Shared footer -->
        <footer class="mt-8 text-center">
            <p class="text-xs text-on-surface-variant opacity-60">
                &copy;
                <?= date('Y') ?> Clinical Curator Enterprise. Precision Data Infrastructure.
            </p>
        </footer>

    </main>

    <!-- ════════════════════════════════════════════════════════════════════════════
     Ambient decorative blurs (cosmetic only)
     ════════════════════════════════════════════════════════════════════════════ -->
    <div class="fixed top-12 left-12 w-64 h-64 bg-primary-container/5
            rounded-full blur-[100px] pointer-events-none"></div>
    <div class="fixed bottom-12 right-12 w-96 h-96 bg-tertiary-container/5
            rounded-full blur-[120px] pointer-events-none"></div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>