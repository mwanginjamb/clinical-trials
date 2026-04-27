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
     Recent Trials Ledger
     ════════════════════════════════════════════════════════════════════════════ -->
<section class="space-y-4 md:space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
        <div>
            <h3 class="font-headline text-xl md:text-2xl font-bold text-primary">Recent Trials</h3>
            <p class="text-on-surface-variant text-xs md:text-sm">Last updated 42 minutes ago.</p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button class="flex-1 sm:flex-none px-4 py-2 bg-surface-container-low
                           text-on-surface-variant text-xs md:text-sm font-semibold rounded-lg
                           hover:bg-surface-container-high transition-colors">
                Export
            </button>
            <?= Html::a(
                'View All',
                ['/trials/index'],
                [
                    'class' => 'flex-1 sm:flex-none px-4 py-2 bg-primary text-white text-xs md:text-sm
                             font-semibold rounded-lg hover:opacity-90 transition-opacity'
                ]
            ) ?>
        </div>
    </div>

    <!-- Scrollable table wrapper -->
    <div class="bg-surface-container-lowest rounded-2xl overflow-hidden
                shadow-[0_24px_48px_-12px_rgba(0,59,83,0.06)]
                border border-outline-variant/10 overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px] lg:min-w-full">
            <thead class="bg-surface-container-low/50">
                <tr>
                    <th class="px-6 md:px-8 py-4 md:py-5 text-[9px] md:text-[10px] font-bold
                               uppercase tracking-widest text-on-surface-variant">
                        Protocol Title &amp; ID
                    </th>
                    <th class="px-4 py-4 md:py-5 text-[9px] md:text-[10px] font-bold uppercase
                               tracking-widest text-on-surface-variant text-center">
                        Status
                    </th>
                    <th class="px-4 py-4 md:py-5 text-[9px] md:text-[10px] font-bold uppercase
                               tracking-widest text-on-surface-variant text-center">
                        Phase
                    </th>
                    <th class="px-4 py-4 md:py-5 text-[9px] md:text-[10px] font-bold uppercase
                               tracking-widest text-on-surface-variant">
                        Primary Investigator
                    </th>
                    <th class="px-6 md:px-8 py-4 md:py-5 text-[9px] md:text-[10px] font-bold
                               uppercase tracking-widest text-on-surface-variant text-right">
                        Start Date
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/5">

                <?php
                /**
                 * In production, $trials would be passed from the controller:
                 *   return $this->render('index', ['trials' => $trials]);
                 *
                 * The static rows below represent the design-time placeholder data.
                 */
                $trials = [
                    [
                        'title' => 'AstraZeneca AZD1222 Clinical Efficacy',
                        'id' => 'NCT04516746',
                        'status' => 'Approved',
                        'statusClass' => 'bg-tertiary-container text-white',
                        'phase' => 'III',
                        'investigator' => 'Dr. Sarah Chen',
                        'avatarUrl' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCG4uoy9xTC0VHDY5FIDJqGU_ufe3JSId2mOm0g5H3Sy3aCgrbGpHTYQt8Pfbyfxp9gcAK9nAxqlkrtkT61HOetIOVtzo8XfOwDxtURoc4Zy3sGokt3J9CBjhnap58ak6-dZ5Z0r_NHig9jwkHlkaHfHAt7fb0Eza2K5IbDQsZYwAY8nscwgKJHSRO1E1HyT6u_eyJeRh4vajFgfo0w7NDe496VRsTSnc7egFYssrLLZ0wYn5rZPKZurmvIBUexFVTG2q-sALYMx6A',
                        'startDate' => 'Oct 12, 2023',
                    ],
                    [
                        'title' => 'Hyper-Targeted CAR-T Cell Therapy',
                        'id' => 'NCT05921832',
                        'status' => 'In Progress',
                        'statusClass' => 'bg-secondary-container text-on-secondary-container',
                        'phase' => 'II',
                        'investigator' => 'Dr. Marcus Thorne',
                        'avatarUrl' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDyglRLrHs9u_6LTauA3rLEJospzogzo-igDOBmwUAm9pTu5Ai8rlCVioeZyusgJeU7ay7b7wSNnGSfe-j7uYFS8H4KEQabkkn91MmixhrcZpfyj1p4ORprpt_KZYPGHr076fb4iPpm7ocA5vfbtKv_9vHd1Eb3Nx8o7skHz108xnrUXUwzhvtgRoUggNv6k_z1nhwGkRws2mZ5mLfdBcMtDwHKu17a4Y7jWSyu9bldfs7sglnI5Kzu2TdYNUfgEohNA__LUZmmRUU',
                        'startDate' => 'Jan 05, 2024',
                    ],
                ];
                ?>

                <?php foreach ($trials as $trial): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 md:px-8 py-5 md:py-6">
                            <div class="flex flex-col">
                                <span class="font-headline font-bold text-sm md:text-base text-on-surface
                                         group-hover:text-primary transition-colors">
                                    <?= Html::encode($trial['title']) ?>
                                </span>
                                <span class="text-[10px] md:text-xs text-on-surface-variant/70 font-mono">
                                    ID: <?= Html::encode($trial['id']) ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-5 md:py-6 text-center">
                            <span class="px-2 md:px-3 py-1 <?= $trial['statusClass'] ?>
                                     text-[9px] md:text-[10px] font-bold rounded-full uppercase tracking-tighter">
                                <?= Html::encode($trial['status']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-5 md:py-6 text-center">
                            <span class="font-headline font-extrabold text-primary/40 text-sm md:text-base">
                                <?= Html::encode($trial['phase']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-5 md:py-6">
                            <div class="flex items-center gap-2 md:gap-3">
                                <div class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-slate-200 overflow-hidden">
                                    <?= Html::img(
                                        $trial['avatarUrl'],
                                        ['alt' => 'Investigator', 'class' => 'w-full h-full object-cover']
                                    ) ?>
                                </div>
                                <span class="text-xs md:text-sm font-medium text-on-surface">
                                    <?= Html::encode($trial['investigator']) ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 md:px-8 py-5 md:py-6 text-right">
                            <span class="text-xs md:text-sm font-medium text-on-surface-variant">
                                <?= Html::encode($trial['startDate']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
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
                Automated compliance checks are currently monitoring 412 active protocols
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