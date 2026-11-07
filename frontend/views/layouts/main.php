<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\DashAsset;

DashAsset::register($this);

$controllerId = \Yii::$app->controller->id;
$actionId = \Yii::$app->controller->action->id;

$this->registerCssFile('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap', ['depends' => []]);
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap', ['depends' => []]);

$userAvatar = "https://placehold.co/150/cccccc/FFFFFF.webp/?text=" . (Yii::$app->user->identity->username ?? 'User');
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
    <title><?= Html::encode($this->title ?? Yii::$app->name) ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body class="bg-surface font-body text-on-surface">
    <?php $this->beginBody() ?>

    <!-- ═══════════════════════════════════════════════════════
     TOP NAV BAR
════════════════════════════════════════════════════════ -->
    <header
        class="h-16 bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-md top-0 sticky z-50 shadow-[0_12px_32px_-4px_rgba(0,59,83,0.08)] print:hidden">
        <div class="flex justify-between items-center h-full px-[1rem] sm:px-8 w-full gap-[1rem]">

            <!-- Brand + Top Nav Links -->
            <div class="flex items-center gap-3 sm:gap-8 min-w-0">
                <!-- Mobile menu toggle (sidebar is hidden below lg) -->
                <button type="button" id="sidebar-toggle" aria-controls="app-sidebar" aria-expanded="false"
                    class="lg:hidden p-2 -ml-2 rounded-lg text-[#005470] hover:bg-slate-200 transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                    <span class="sr-only">Open menu</span>
                </button>
                <?= Html::a(
                    '<span class="text-lg sm:text-xl font-bold tracking-tight text-[#005470] dark:text-cyan-500 font-headline truncate">' . Html::encode(Yii::$app->name) . '</span>',
                    Url::to(['/site/index']),
                    ['class' => 'no-underline min-w-0 truncate']
                ) ?>

                <nav class="hidden md:flex gap-6 items-center shrink-0">
                    <?= Html::a('Dashboard', Url::to(['/site/index']), ['class' => $topNavClass('dashboard')]) ?>
                    <?= Html::a('Clinical Trials', Url::to(['/clinical-trial/index']), ['class' => $topNavClass('clinical-trial')]) ?>
                    <?php if (Yii::$app->user->can('importTrials')): ?>
                        <?= Html::a('Bulk Trials Import ', Url::to(['/trial-import/import']), ['class' => $topNavClass('trial-import')]) ?>
                    <?php endif; ?>

                </nav>
            </div>

            <!-- Right-Side Actions -->
            <div class="flex items-center gap-1 sm:gap-4 shrink-0">
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
                        Yii::$app->user->identity->avatarUrl ?? Url::to($userAvatar),
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
        <!-- Backdrop for the mobile drawer -->
        <div id="sidebar-backdrop" class="fixed inset-0 top-16 z-30 bg-slate-900/50 hidden lg:hidden print:hidden"></div>

        <!-- Sits BELOW the sticky header (top-16), so nothing is hidden behind it.
             Below lg it is an off-canvas drawer toggled by #sidebar-toggle. -->
        <aside id="app-sidebar"
            class="w-64 fixed left-0 top-16 h-[calc(100vh-4rem)] overflow-y-auto bg-slate-100 dark:bg-slate-900 z-40
                   -translate-x-full lg:translate-x-0 transition-transform duration-200 print:hidden">
            <div class="flex flex-col min-h-full py-6 space-y-2">

                <!-- Primary Nav (shared with dashboard.php) -->
                <?= $this->render('_sidebar_nav') ?>

                <!-- CTA Button -->
                <?php if (Yii::$app->user->can('createTrial')): ?>
                <div class="px-6 pt-4 border-t border-slate-200/50">
                    <?= Html::a(
                        '<span class="material-symbols-outlined text-[18px]">add</span> New Trial',
                        ['/clinical-trial/create'],
                        [
                            'class' => 'w-full mt-4 bg-primary text-white p-2.5 rounded-xl font-semibold text-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2'
                        ]
                    ) ?>
                </div>
                <?php endif; ?>

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
        <!-- min-w-0 lets this flex child shrink below its content width; without it, any wide
             child (e.g. a nowrap tab strip or table) widens the whole page and causes horizontal scroll. -->
        <main class="flex-1 min-w-0 lg:ml-64 px-[1rem] py-6 sm:px-6 lg:p-8 bg-surface print:ml-0 print:p-0">
            <div class="print:hidden">
                <?= $this->render('_bread_crumbs') ?>
            </div>
            <?= $this->render('_flash_alerts') ?>
            <?= $content ?>
        </main>

    </div>

    <!-- ═══════════════════════════════════════════════════════
     FIXED EDITORIAL SUPPORT FAB
════════════════════════════════════════════════════════ -->
    <div class="fixed bottom-4 right-4 sm:bottom-8 sm:right-8 z-40 print:hidden">
        <?= Html::a(
            '<span class="material-symbols-outlined">contact_support</span>',
            Url::to(['/site/support']),
            [
                'class' => 'w-12 h-12 sm:w-14 sm:h-14 bg-primary text-white rounded-2xl shadow-lg flex items-center justify-center hover:scale-110 active:scale-95 transition-all',
                'title' => 'Support',
                'aria-label' => 'Support',
            ]
        ) ?>
    </div>

    <?php $this->registerJs(<<<'JS'
(function () {
    var btn = document.getElementById('sidebar-toggle');
    var aside = document.getElementById('app-sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    if (!btn || !aside || !backdrop) return;
    function setOpen(open) {
        aside.classList.toggle('-translate-x-full', !open);
        backdrop.classList.toggle('hidden', !open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    btn.addEventListener('click', function () { setOpen(aside.classList.contains('-translate-x-full')); });
    backdrop.addEventListener('click', function () { setOpen(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
    window.matchMedia('(min-width: 1024px)').addEventListener('change', function () { setOpen(false); });
})();
JS); ?>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>