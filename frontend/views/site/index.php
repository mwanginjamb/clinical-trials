<?php

/**
 * Clinical Curator — Dashboard View
 *
 * @var \yii\web\View $this
 *
 * Rendered inside views/layouts/main.php → injected at $content.
 * Contains only the editorial page sections; chrome (sidebar, topbar,
 * FAB) lives in the layout.
 */

use yii\helpers\Html;

$this->title = 'SERCEA Research Repository — Clinical Curator';
?>

<!-- ════════════════════════════════════════════════════════════════════════════
     Page Title Section
     ════════════════════════════════════════════════════════════════════════════ -->
<section class="md:ml-12 text-left">
    <h2 class="font-headline text-3xl md:text-[2.5rem] font-extrabold tracking-tighter text-primary">
        Research Repository
    </h2>
    <p class="font-body text-on-surface-variant max-w-2xl mt-2 leading-relaxed text-sm md:text-base">
        Monitoring institutional clinical trial performance and investigator lifecycle
        across the enterprise ecosystem.
    </p>
</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     Metrics Bento Grid
     ════════════════════════════════════════════════════════════════════════════ -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

    <!-- Total Trials -->
    <div class="bg-surface-container-lowest p-5 md:p-6 rounded-xl
                shadow-[0_8px_24px_-4px_rgba(0,59,83,0.04)] group hover:shadow-lg
                transition-all border-b-2 border-transparent hover:border-primary">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2.5 md:p-3 bg-primary-fixed rounded-lg text-primary">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">
                    analytics
                </span>
            </div>
            <span class="text-[10px] md:text-xs font-bold text-tertiary uppercase tracking-tighter">
                +12% vs LY
            </span>
        </div>
        <p class="label-sm text-on-surface-variant font-medium uppercase tracking-widest
                  text-[9px] md:text-[10px] mb-1">Total Trials</p>
        <h3 class="text-2xl md:text-3xl font-headline font-bold text-on-surface">1,284</h3>
    </div>

    <!-- Active Trials -->
    <div class="bg-surface-container-lowest p-5 md:p-6 rounded-xl
                shadow-[0_8px_24px_-4px_rgba(0,59,83,0.04)] group hover:shadow-lg
                transition-all border-b-2 border-transparent hover:border-tertiary">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2.5 md:p-3 bg-tertiary-fixed rounded-lg text-tertiary">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">
                    monitoring
                </span>
            </div>
            <div class="flex -space-x-2">
                <div class="w-5 h-5 md:w-6 md:h-6 rounded-full border-2 border-white bg-slate-300"></div>
                <div class="w-5 h-5 md:w-6 md:h-6 rounded-full border-2 border-white bg-slate-400"></div>
            </div>
        </div>
        <p class="label-sm text-on-surface-variant font-medium uppercase tracking-widest
                  text-[9px] md:text-[10px] mb-1">Active Trials</p>
        <h3 class="text-2xl md:text-3xl font-headline font-bold text-on-surface">412</h3>
    </div>

    <!-- Pending Approvals -->
    <div class="bg-surface-container-lowest p-5 md:p-6 rounded-xl
                shadow-[0_8px_24px_-4px_rgba(0,59,83,0.04)] group hover:shadow-lg
                transition-all border-b-2 border-transparent hover:border-error">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2.5 md:p-3 bg-error-container rounded-lg text-error">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">
                    pending_actions
                </span>
            </div>
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full
                             bg-error opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-error"></span>
            </span>
        </div>
        <p class="label-sm text-on-surface-variant font-medium uppercase tracking-widest
                  text-[9px] md:text-[10px] mb-1">Pending Approvals</p>
        <h3 class="text-2xl md:text-3xl font-headline font-bold text-on-surface">18</h3>
    </div>

    <!-- Avg. Recruitment -->
    <div class="bg-surface-container-lowest p-5 md:p-6 rounded-xl
                shadow-[0_8px_24px_-4px_rgba(0,59,83,0.04)] group hover:shadow-lg transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2.5 md:p-3 bg-secondary-container rounded-lg text-secondary">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">
                    group_add
                </span>
            </div>
        </div>
        <p class="label-sm text-on-surface-variant font-medium uppercase tracking-widest
                  text-[9px] md:text-[10px] mb-1">Avg. Recruitment</p>
        <div class="flex items-center gap-2">
            <h3 class="text-2xl md:text-3xl font-headline font-bold text-on-surface">84%</h3>
            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-primary" style="width:84%"></div>
            </div>
        </div>
    </div>

</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     Recent Trial Entries (5)
     ════════════════════════════════════════════════════════════════════════════ -->
<section class="space-y-4 md:space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
        <div>
            <h3 class="font-headline text-xl md:text-2xl font-bold text-primary">Recent Trials</h3>
            <!-- <p class="text-on-surface-variant text-xs md:text-sm">Last updated 42 minutes ago.</p> -->
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            
            <?= Html::a(
                'View All',
                ['clinical-trial/index'],
                [
                    'class' => 'flex-1 sm:flex-none px-4 py-2 bg-primary text-white text-xs md:text-sm
                             font-semibold rounded-lg hover:opacity-90 transition-opacity'
                ]
            ) ?>
        </div>
    </div>

    <!-- Scrollable table wrapper : render the table here -->

    <?= $this->render('_recent_trials', ['recentTrials' => $recentTrials]) ?>

</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     Bottom Editorial Section — Insights & Integrity Cards
     ════════════════════════════════════════════════════════════════════════════ -->
<section class="editorial-grid">

    <!-- Feature insight card -->
    <div class="col-span-12 md:col-span-7 bg-primary text-white p-8 md:p-10 rounded-3xl
                relative overflow-hidden group">
        <div class="relative z-10">
            <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px]
                         font-bold uppercase tracking-widest mb-4 md:mb-6 inline-block">
                Institutional Focus
            </span>
            <h3 class="text-2xl md:text-4xl font-headline font-bold mb-4 leading-tight">
                Diversity and Inclusion in Global Oncology Trials
            </h3>
            <p class="text-on-primary-container text-base md:text-lg mb-6 md:mb-8
                       leading-relaxed max-w-xl">
                Our recent metadata audit shows a 24% increase in representative cohort
                participation in Stage III trials since the implementation of the Curator protocol.
            </p>
            <button class="bg-white text-primary px-6 md:px-8 py-2 md:py-3 rounded-full
                           font-bold hover:bg-primary-fixed transition-colors text-sm md:text-base">
                Read Full Insight
            </button>
        </div>
        <!-- Decorative background image -->
        <div class="absolute right-0 top-0 w-1/3 h-full opacity-20 group-hover:opacity-30
                    transition-opacity hidden sm:block">
            <?= Html::img(
                'https://lh3.googleusercontent.com/aida-public/AB6AXuDzbcrfhuUJ7XRWWtPERg8KTeXvT0lyHuZLw3DMQfTQF-9VRgmdy9d5KTxd05cwnHiy3o1uss_mwXINXjR4aMzF4alY3NGIo9EKp4ldmE6bKSWhzW4mUBx3bkG504OPCczl08u0nC6uW9-UJiudLKaTohFW5vCjb_xf4HV-PGqV1LiIYZiqapNcivqMMT_5e1XRSPyueRxbBMlyxwOZ6xx4aNgWuUMFWBHGW1EKHhnEwPjx_Kb8M9cpV_N7IEbN4XIxb4cEjHOFcX0',
                ['alt' => 'Abstract Science', 'class' => 'w-full h-full object-cover']
            ) ?>
        </div>
    </div>

    <!-- Protocol Integrity card -->
    <div class="col-span-12 md:col-span-5 bg-surface-container-low p-8 md:p-10 rounded-3xl
                border border-outline-variant/10 flex flex-col justify-between space-y-6">
        <div>
            <h4 class="font-headline text-lg md:text-xl font-bold text-primary mb-2">
                Protocol Integrity
            </h4>
            <p class="text-on-surface-variant text-sm mb-4 md:mb-6">
                Automated compliance checks are currently monitoring <?= Yii::$app->dashboard->allTrials ?> active protocols
                for data drift.
            </p>
        </div>
        <div class="space-y-3 md:space-y-4">
            <div class="p-3 md:p-4 bg-surface-container-lowest rounded-xl
                        flex items-center justify-between">
                <span class="text-xs md:text-sm font-medium">Compliance Rate</span>
                <span class="font-bold text-tertiary">99.2%</span>
            </div>
            <div class="p-3 md:p-4 bg-surface-container-lowest rounded-xl
                        flex items-center justify-between">
                <span class="text-xs md:text-sm font-medium">Audit Velocity</span>
                <span class="font-bold text-primary">High</span>
            </div>
        </div>
    </div>

</section>