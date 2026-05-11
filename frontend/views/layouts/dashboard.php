<?php

/**
 * Clinical Curator — Main Layout
 *
 * @var \yii\web\View $this
 * @var string        $content Rendered view output injected by Yii.
 */

use frontend\assets\DashAsset;
use yii\helpers\Html;



// ── External fonts & icon font ────────────────────────────────────────────────
// Registered here (not in AppAsset) so they resolve to CDN URLs and arrive in
// <head> via $this->head() before any local CSS — avoids FOUT.


$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap']);
$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap']);

DashAsset::register($this);


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

<body class="bg-background text-on-surface font-body selection:bg-primary-fixed selection:text-on-primary-fixed">
    <?php $this->beginBody() ?>

    <!-- ════════════════════════════════════════════════════════════════════════════
     Mobile Navigation Drawer — Overlay backdrop
     ════════════════════════════════════════════════════════════════════════════ -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] hidden opacity-0 lg:hidden"
        id="mobile-menu-overlay"></div>

    <!-- ════════════════════════════════════════════════════════════════════════════
     SideNavBar (Desktop persistent · Mobile drawer)
     ════════════════════════════════════════════════════════════════════════════ -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-slate-100 dark:bg-slate-900
           flex flex-col py-6 space-y-2 font-manrope text-sm font-medium
           tracking-wide z-[70] -translate-x-full lg:translate-x-0" id="mobile-sidebar">
        <!-- Brand / Close row -->
        <div class="px-6 mb-8 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary-container
                        flex items-center justify-center text-white">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">
                        clinical_notes
                    </span>
                </div>
                <div>
                    <h1 class="text-lg font-black text-[#005470]">The Curator</h1>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500">Enterprise Research</p>
                </div>
            </div>
            <button class="lg:hidden p-2 text-slate-600" onclick="toggleMobileMenu()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Primary navigation -->
        <nav class="flex-1 px-2 space-y-1">
            <?php
            $navItems = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'dashboard',
                    'filled' => true,
                    'url' => ['/dashboard/index'],
                    'active' => true,
                ],
                [
                    'label' => 'Clinical Trials',
                    'icon' => 'clinical_notes',
                    'filled' => false,
                    'url' => ['/trials/index'],
                    'active' => false,
                ],
                [
                    'label' => 'Investigator Registry',
                    'icon' => 'group',
                    'filled' => false,
                    'url' => ['/investigators/index'],
                    'active' => false,
                ],
                [
                    'label' => 'Analytics',
                    'icon' => 'query_stats',
                    'filled' => false,
                    'url' => ['/analytics/index'],
                    'active' => false,
                ],
                [
                    'label' => 'Protocols',
                    'icon' => 'description',
                    'filled' => false,
                    'url' => ['/protocols/index'],
                    'active' => false,
                ],
            ];

            foreach ($navItems as $item):
                $activeClass = $item['active']
                    ? 'bg-[#c4e7ff] dark:bg-[#003b53] text-[#001e2c] dark:text-[#c4e7ff]'
                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800';
                $fillSetting = $item['filled']
                    ? "font-variation-settings:'FILL' 1;"
                    : '';
                ?>
                <?= Html::a(
                    '<span class="material-symbols-outlined" style="' . $fillSetting . '">'
                    . Html::encode($item['icon']) . '</span>'
                    . '<span>' . Html::encode($item['label']) . '</span>',
                    $item['url'],
                    ['class' => $activeClass . ' rounded-lg mx-2 flex items-center gap-3 px-4 py-3 transition-all']
                ) ?>
            <?php endforeach; ?>
        </nav>

        <!-- Bottom utility area -->
        <div class="mt-auto px-4 space-y-1">
            <?= Html::a(
                '<span class="material-symbols-outlined">add</span><span>New Trial</span>',
                ['/trials/create'],
                [
                    'class' => 'w-full bg-gradient-to-r from-primary to-primary-container text-white
                            rounded-xl py-3 px-4 font-bold flex items-center justify-center
                            gap-2 shadow-lg mb-6 active:scale-95 duration-200',
                ]
            ) ?>
            <?= Html::a(
                '<span class="material-symbols-outlined">help_center</span><span>Support</span>',
                ['/site/contact'],
                [
                    'class' => 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800
                         rounded-lg flex items-center gap-3 px-4 py-3 transition-all'
                ]
            ) ?>
            <?= Html::a(
                '<span class="material-symbols-outlined">logout</span><span>Sign Out</span>',
                ['/site/logout'],
                [
                    'class' => 'text-slate-600 dark:text-slate-400 hover:bg-slate-200
                                   dark:hover:bg-slate-800 rounded-lg flex items-center gap-3
                                   px-4 py-3 transition-all',
                    'data-method' => 'post',
                ]
            ) ?>
        </div>
    </aside>

    <!-- ════════════════════════════════════════════════════════════════════════════
     Main Content Canvas
     ════════════════════════════════════════════════════════════════════════════ -->
    <main class="lg:ml-64 min-h-screen bg-surface">

        <!-- ── TopAppBar ──────────────────────────────────────────────────────── -->
        <header class="docked full-width top-0 sticky z-30 bg-slate-50/80 dark:bg-slate-950/80
                   backdrop-blur-md flex justify-between items-center px-4 md:px-8 py-3 w-full
                   shadow-[0_12px_32px_-4px_rgba(0,59,83,0.08)]">
            <div class="flex items-center gap-4 md:gap-8">
                <!-- Hamburger (mobile only) -->
                <button class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-slate-100 transition-colors"
                    onclick="toggleMobileMenu()">
                    <span class="material-symbols-outlined text-primary">menu</span>
                </button>

                <span class="font-manrope antialiased text-lg md:text-xl font-bold tracking-tight
                         text-[#005470] dark:text-cyan-500 truncate">
                    Clinical Curator
                </span>

                <!-- Search (desktop only) -->
                <div class="relative group hidden md:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2
                             text-slate-400 group-focus-within:text-primary transition-colors">
                        search
                    </span>
                    <input class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-full
                           w-48 lg:w-80 focus:ring-2 focus:ring-primary-fixed text-sm transition-all"
                        placeholder="Search trials…" type="text" />
                </div>
            </div>

            <div class="flex items-center gap-2 md:gap-4">
                <!-- Notifications -->
                <button class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800
                           transition-colors relative">
                    <span class="material-symbols-outlined text-slate-600">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full
                             border-2 border-white"></span>
                </button>

                <!-- Settings (≥ sm) -->
                <button class="hidden sm:flex p-2 rounded-full hover:bg-slate-100
                           dark:hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined text-slate-600">settings</span>
                </button>

                <!-- Avatar -->
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-slate-200 overflow-hidden
                        border-2 border-white shadow-sm">
                    <?= Html::img(
                        'https://lh3.googleusercontent.com/aida-public/AB6AXuDbUjMISHuEoOLTVh20Srs2iirQxVpUBoLOTFiMqjyExCT2bVw3mHLN-Xyw0EPZGG5_o5JfXQuU_90vapGKEo4w8gr_Le7YeJv7ZLDnIJOLfxolvK7OgFhbnrgayjlZUhubQWd1BVZkug5MBT3Scl9F5-EOjpryHCC7TX-0wNS9GFO_3uGbMe9qbX_Lm37mJRVWqV6DIN3YPuLN_Wr_pa3C3xrXBG9Mx07QpoWyhJT3Y2uNfEhdQBW3BPg5qk0t95t2YsM408yjP68',
                        ['alt' => 'Researcher Profile', 'class' => 'w-full h-full object-cover']
                    ) ?>
                </div>
            </div>
        </header>

        <!-- ── Page view content injected here ──────────────────────────────── -->
        <div class="p-6 md:p-12 max-w-7xl mx-auto space-y-8 md:space-y-12">
            <?= $content ?>
        </div>

    </main>

    <!-- ════════════════════════════════════════════════════════════════════════════
     Contextual FAB
     ════════════════════════════════════════════════════════════════════════════ -->
    <button class="fixed bottom-6 right-6 md:bottom-10 md:right-10 w-14 h-14 md:w-16 md:h-16
               bg-primary text-white rounded-2xl shadow-2xl flex items-center justify-center
               hover:scale-110 active:scale-95 transition-all z-50">
        <span class="material-symbols-outlined text-2xl md:text-3xl">add_chart</span>
    </button>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>