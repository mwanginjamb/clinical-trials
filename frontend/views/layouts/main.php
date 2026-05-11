<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\DashAsset;

DashAsset::register($this);

$controllerId = Yii::$app->controller->id;
$actionId = Yii::$app->controller->action->id;

$this->registerCssFile('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap', ['depends' => []]);
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap', ['depends' => []]);


/*
 * Nav helper: returns the CSS classes for a sidebar nav link.
 * Active when the current controller matches $ctrl (and optionally $action).
 */
$navClass = function (string $ctrl, string $action = '') use ($controllerId, $actionId): string {
    $isActive = $controllerId === $ctrl && ($action === '' || $actionId === $action);
    return $isActive
        ? 'flex items-center gap-3 px-4 py-3 bg-[#c4e7ff] text-[#001e2c] rounded-lg font-headline text-sm font-bold'
        : 'flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-200 transition-all rounded-lg font-headline text-sm font-medium';
};

$topNavClass = function (string $ctrl) use ($controllerId): string {
    $isActive = $controllerId === $ctrl;
    return $isActive
        ? 'text-[#005470] font-bold border-b-2 border-[#005470] font-headline text-sm transition-colors py-1'
        : 'text-slate-500 dark:text-slate-400 hover:text-[#005470] font-headline text-sm font-medium transition-colors';
};

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html class="light" lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= Html::encode($this->title ?? 'Clinical Curator') ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body class="bg-surface font-body text-on-surface">
    <?php $this->beginBody() ?>

    <!-- ═══════════════════════════════════════════════════════
     TOP NAV BAR
════════════════════════════════════════════════════════ -->
    <header
        class="bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-md docked full-width top-0 sticky z-50 shadow-[0_12px_32px_-4px_rgba(0,59,83,0.08)]">
        <div class="flex justify-between items-center px-8 py-3 w-full">

            <!-- Brand + Top Nav Links -->
            <div class="flex items-center gap-8">
                <?= Html::a(
                    '<span class="text-xl font-bold tracking-tight text-[#005470] dark:text-cyan-500 font-headline">Clinical Curator</span>',
                    Url::to(['/site/index']),
                    ['class' => 'no-underline']
                ) ?>

                <nav class="hidden md:flex gap-6 items-center">
                    <?= Html::a('Dashboard', Url::to(['/dashboard/index']), ['class' => $topNavClass('dashboard')]) ?>
                    <?= Html::a('Clinical Trials', Url::to(['/trials/index']), ['class' => $topNavClass('trials')]) ?>
                    <?= Html::a('Investigator Registry', Url::to(['/investigators/index']), ['class' => $topNavClass('investigators')]) ?>
                </nav>
            </div>

            <!-- Right-Side Actions -->
            <div class="flex items-center gap-4">
                <button class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-[#005470]">notifications</span>
                </button>
                <?= Html::a(
                    '<span class="material-symbols-outlined text-[#005470]">settings</span>',
                    Url::to(['/settings/index']),
                    ['class' => 'p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors']
                ) ?>
                <div class="h-8 w-8 rounded-full overflow-hidden border border-outline-variant/30">
                    <?= Html::img(
                        Yii::$app->user->identity->avatarUrl ?? Url::to('@web/img/avatar-placeholder.png'),
                        [
                            'alt' => 'Researcher Profile',
                            'class' => 'w-full h-full object-cover',
                        ]
                    ) ?>
                </div>
            </div>

        </div>
    </header>

    <div class="flex">

        <!-- ═══════════════════════════════════════════════════════
         SIDE NAV BAR
    ════════════════════════════════════════════════════════ -->
        <aside class="h-screen w-64 fixed left-0 top-0 bg-slate-100 dark:bg-slate-900 z-40 hidden lg:block">
            <div class="flex flex-col h-full py-6 space-y-2">

                <!-- Brand Mark -->
                <div class="px-6 mb-8">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-white">
                            <span class="material-symbols-outlined"
                                style="font-variation-settings: 'FILL' 1;">science</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-[#005470] font-headline leading-tight">The Curator</h2>
                            <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Enterprise
                                Research</p>
                        </div>
                    </div>
                </div>

                <!-- Primary Nav -->
                <nav class="flex-1 px-4 space-y-1">
                    <?= Html::a(
                        '<span class="material-symbols-outlined">dashboard</span> Dashboard',
                        Url::to(['/dashboard/index']),
                        ['class' => $navClass('dashboard')]
                    ) ?>
                    <?= Html::a(
                        '<span class="material-symbols-outlined" style="font-variation-settings: \'FILL\' 1;">clinical_notes</span> Clinical Trials',
                        Url::to(['/trials/index']),
                        ['class' => $navClass('trials')]
                    ) ?>
                    <?= Html::a(
                        '<span class="material-symbols-outlined">group</span> Investigator Registry',
                        Url::to(['/investigators/index']),
                        ['class' => $navClass('investigators')]
                    ) ?>
                    <?= Html::a(
                        '<span class="material-symbols-outlined">query_stats</span> Analytics',
                        Url::to(['/analytics/index']),
                        ['class' => $navClass('analytics')]
                    ) ?>
                    <?= Html::a(
                        '<span class="material-symbols-outlined">description</span> Protocols',
                        Url::to(['/protocols/index']),
                        ['class' => $navClass('protocols')]
                    ) ?>
                </nav>

                <!-- CTA Button -->
                <div class="px-6 pt-4 border-t border-slate-200/50">
                    <?= Html::a(
                        'New Trial',
                        Url::to(['/trials/create']),
                        ['class' => 'block w-full py-3 bg-primary text-white rounded-xl font-headline text-sm font-bold shadow-md hover:opacity-90 transition-opacity text-center']
                    ) ?>
                </div>

                <!-- Secondary Nav (Bottom) -->
                <div class="px-4 mt-auto">
                    <?= Html::a(
                        '<span class="material-symbols-outlined">help_center</span> Support',
                        Url::to(['/site/support']),
                        ['class' => $navClass('site', 'support')]
                    ) ?>
                    <?= Html::a(
                        '<span class="material-symbols-outlined">logout</span> Sign Out',
                        Url::to(['/site/logout']),
                        [
                            'class' => $navClass('site', 'logout'),
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to sign out?',
                        ]
                    ) ?>
                </div>

            </div>
        </aside>

        <!-- ═══════════════════════════════════════════════════════
         MAIN CONTENT AREA
    ════════════════════════════════════════════════════════ -->
        <main class="flex-1 lg:ml-64 p-8 bg-surface">
            <?= $this->render('_bread_crumbs') ?>
            <?= $this->render('_flash_alerts') ?>
            <?= $content ?>
        </main>

    </div>

    <!-- ═══════════════════════════════════════════════════════
     FIXED EDITORIAL SUPPORT FAB
════════════════════════════════════════════════════════ -->
    <div class="fixed bottom-8 right-8 z-50">
        <?= Html::a(
            '<span class="material-symbols-outlined">contact_support</span>',
            Url::to(['/site/support']),
            ['class' => 'w-14 h-14 bg-primary text-white rounded-2xl editorial-shadow flex items-center justify-center hover:scale-110 active:scale-95 transition-all']
        ) ?>
    </div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>