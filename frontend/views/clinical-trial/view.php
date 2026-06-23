<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\ClinicalTrial $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clinical Trials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$map = [
    'approved' => ['bg-tertiary-container', 'text-on-tertiary-container', 'Approved'],
    'active' => ['bg-secondary-container', 'text-on-secondary-container', 'In Progress'],
    'on_hold' => ['bg-error-container', 'text-on-error-container', 'On Hold'],
    'pending' => ['bg-surface-container-high', 'text-on-surface-variant', 'Pending'],
    'rejected' => ['bg-error-container', 'text-on-error-container', 'Rejected'],
];
$key = strtolower($model::getRegistrationStatusOptions()[$model->registration_status] ?? 'Unknown');
[$bg, $fg, $status] = $map[$key] ?? ['bg-surface-container-high', 'text-on-surface-variant', ucfirst($key) ?: '—'];

?>

<div class="trial-view flex flex-col">
    <!-- Page Header & Floating Sub-Nav -->
    <div class="pt-10 pb-6 bg-surface-container-low px-4 sm:px-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div class="max-w-4xl">
                <div class="flex items-center gap-3 mb-3">
                    <span
                        class="px-3 py-1 <?= $bg . " " . $fg ?>  font-semibold text-[10px] tracking-widest uppercase rounded-full">
                        <?= $model::getRegistrationStatusOptions()[$model->registration_status] ?>
                    </span>
                    <span class="text-on-surface-variant font-label text-xs tracking-tighter">PROTOCOL ID:
                        <?= $model->protocol_number ?? '' ?></span>
                </div>
                <h2 class="text-3xl font-extrabold font-headline text-on-surface tracking-tight leading-tight">
                    <?= Html::encode($model->scientific_title ?? '') ?>
                </h2>
            </div>
            <div class="flex gap-3">
                <button
                    class="px-6 py-2.5 bg-surface-container-lowest text-primary font-semibold text-sm rounded-lg border border-outline-variant/15 hover:bg-white transition-all shadow-sm">Export
                    Protocol</button>
                <button
                    class="px-6 py-2.5 bg-gradient-to-r from-primary to-primary-container text-on-primary font-semibold text-sm rounded-lg shadow-md hover:opacity-90 transition-all">Submit
                    Revision</button>
            </div>
        </div>
        <!-- Sticky Sub-Navigation -->
        <nav
            class="sticky top-[64px] z-30 bg-surface-container-lowest/80 backdrop-blur-md flex items-center gap-1 p-1.5 rounded-xl border border-outline-variant/10 overflow-x-auto no-scrollbar shadow-sm">
            <a class="whitespace-nowrap px-4 py-2 text-xs font-semibold rounded-lg bg-primary-fixed text-on-primary-fixed"
                href="#trial-info">1. Trial Info</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#purpose-design">2. Purpose &amp; Design</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#eligibility">3. Eligibility</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#timeline-location">4. Timeline</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#investigator-team">5. Investigators</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#regulatory">6. Regulatory</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#funding">7. Funding</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#abstracts">8. Abstracts</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#interventions">9. Outcomes</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#publication">10. Publications</a>
            <a class="whitespace-nowrap px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors"
                href="#stats">11. Stats</a>
        </nav>
    </div>

    <!-- Content Canvas -->
    <div class="py-12 space-y-16 px-4 sm:px-10">

        <!-- Section 1: Trial Info -->
        <section class="section-anchor" id="trial-info">
            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-12 md:col-span-4">
                    <h3 class="text-xl font-bold font-headline mb-2">01. Trial Information</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Core identification and nomenclature
                        details for regulatory and public indexing.</p>
                </div>
                <div class="col-span-12 md:col-span-8 grid grid-cols-2 gap-y-10 gap-x-12">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-8 gap-x-12">
                        <div>
                            <label
                                class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Public
                                Title</label>
                            <p class="text-sm font-medium leading-relaxed"><?= Html::encode($model->public_title) ?></p>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Acronym</label>
                            <p class="text-sm font-medium"><?= Html::encode($model->scientific_acronym) ?></p>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Registry
                                ID</label>
                            <p class="text-sm font-medium"><?= Html::encode($model->registration_number) ?></p>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Secondary
                                IDs</label>
                            <p class="text-sm font-medium"><?= Html::encode($model->protocol_number) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 2: Purpose & Design -->
        <section class="section-anchor p-8 bg-surface-container-low rounded-2xl" id="purpose-design">
            <div class="mb-10">
                <h3 class="text-xl font-bold font-headline mb-4">02. Purpose &amp; Design</h3>
                <div class="h-1 w-12 bg-primary rounded-full"></div>
            </div>
            <?php $purpose = $model->purpose; ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="col-span-1 md:col-span-2">
                    <label
                        class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-3">Primary
                        Hypothesis</label>
                    <p class="text-base text-on-surface italic leading-relaxed">
                        <?= Html::encode($purpose->study_hypothesis ?? 'Not specified') ?>
                    </p>
                </div>
                <div class="space-y-6">
                    <div>
                        <label
                            class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Trial
                            Phase</label>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary scale-75">science</span>
                            <span class="font-bold text-sm"><?= Html::encode($purpose->phase_of_study ?? '—') ?></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Masking</label>
                            <p class="text-xs font-medium"><?= Html::encode($purpose->masking_description ?? '—') ?></p>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Randomization</label>
                            <p class="text-xs font-medium">
                                <?= Html::encode($purpose->randomization_method_name ?? '—') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: Eligibility -->
        <section class="section-anchor" id="eligibility">
            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-12 md:col-span-4">
                    <h3 class="text-xl font-bold font-headline mb-2">03. Eligibility Criteria</h3>
                    <?php $eligibility = $model->studyPopulationEligibility; ?>
                    <div class="flex items-center gap-4 mt-6">
                        <div
                            class="text-center bg-surface-container-lowest px-4 py-3 rounded-xl border border-outline-variant/10 shadow-sm">
                            <p class="text-2xl font-black text-primary">
                                <?= $eligibility->participant_target_number ?? '—' ?>
                            </p>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant">Target N</p>
                        </div>
                        <div
                            class="text-center bg-surface-container-lowest px-4 py-3 rounded-xl border border-outline-variant/10 shadow-sm">
                            <p class="text-2xl font-black text-on-surface">
                                <?= $eligibility->final_number_of_participants ?? '—' ?>
                            </p>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant">Enrolled</p>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-surface-container-lowest p-6 rounded-xl border-l-4 border-primary shadow-sm">
                        <h4
                            class="text-xs font-bold uppercase tracking-widest text-primary mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">add_circle</span> Inclusion
                        </h4>
                        <ul class="space-y-3">
                            <li class="text-xs leading-relaxed flex gap-2">
                                <span class="text-primary">•</span>
                                <?= Html::encode($eligibility->health_condition_studied ?? 'Not specified') ?>
                            </li>
                            <!-- Additional static bullet points can be replaced with dynamic data if available -->
                        </ul>
                    </div>
                    <div class="bg-surface-container-lowest p-6 rounded-xl border-l-4 border-error/40 shadow-sm">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-error mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">do_not_disturb_on</span> Exclusion
                        </h4>
                        <ul class="space-y-3">
                            <li class="text-xs leading-relaxed flex gap-2">
                                <span class="text-error">•</span>
                                <?= Html::encode($eligibility->type_of_eligibility ?? 'Not specified') ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 4 & 5: Timeline & Investigators -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <section class="section-anchor" id="timeline-location">
                <h3 class="text-xl font-bold font-headline mb-6">04. Timeline &amp; Locations</h3>
                <?php $timeline = $model->timeline; ?>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-lg">
                        <span class="text-xs font-semibold text-on-surface-variant">Study Duration</span>
                        <span class="text-sm font-bold"><?= $timeline->study_duration ?? '—' ?> Months</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 border border-outline-variant/15 rounded-lg">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase mb-1">Start Date</p>
                            <p class="text-sm font-bold">
                                <?= Yii::$app->formatter->asDate($timeline->anticipated_start_date ?? null) ?>
                            </p>
                        </div>
                        <div class="p-4 border border-outline-variant/15 rounded-lg">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase mb-1">Est. Completion</p>
                            <p class="text-sm font-bold">
                                <?= Yii::$app->formatter->asDate($timeline->anticipated_end_date ?? null) ?>
                            </p>
                        </div>
                    </div>
                    <div class="pt-4">
                        <label
                            class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-3">Primary
                            Site</label>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary">location_on</span>
                            <div>
                                <p class="text-sm font-bold"><?= Html::encode($timeline->study_site_location ?? '') ?>
                                </p>
                                <p class="text-xs text-on-surface-variant">
                                    <?= Html::encode($timeline->centre_pysical_address ?? '') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-anchor" id="investigator-team">
                <h3 class="text-xl font-bold font-headline mb-6">05. Investigator Team</h3>
                <div class="overflow-x-auto no-scrollbar rounded-xl border border-outline-variant/20">
                    <table class="w-full text-left min-w-[500px]">
                        <thead class="bg-surface-container-low">
                            <tr>
                                <th
                                    class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                                    Name</th>
                                <th
                                    class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                                    Role</th>
                                <th
                                    class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                                    Contact</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            <?php foreach ($model->investigators as $investigator): ?>
                                <tr class="hover:bg-surface-container-lowest transition-colors">
                                    <td class="px-4 py-4">
                                        <p class="text-sm font-bold"><?= Html::encode($investigator->name) ?></p>
                                        <p class="text-[10px] text-on-surface-variant">
                                            <?= Html::encode($investigator->institution) ?>
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 text-xs">
                                        <?= Html::encode(\frontend\models\InvestigatorTeam::getRoleOptions()[$investigator->role] ?? 'Unknown') ?>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-primary font-medium">
                                        <?= Html::encode($investigator->email_address) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($model->investigators)): ?>
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">No investigators assigned.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Section 6 & 7: Regulatory & Funding -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <section class="section-anchor" id="regulatory">
                <?php $approval = $model->ethicalApproval; ?>
                <div class="p-8 border border-primary/10 rounded-2xl bg-primary/[0.02]">
                    <h3 class="text-xl font-bold font-headline mb-6 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">verified</span>
                        06. Regulatory Approval
                    </h3>
                    <div class="space-y-6">
                        <div class="flex justify-between items-center pb-4 border-b border-outline-variant/10">
                            <span class="text-xs font-semibold text-on-surface-variant">IRB/EC Name</span>
                            <span
                                class="text-sm font-bold"><?= Html::encode($approval->ethical_regulatory_body ?? '—') ?></span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-outline-variant/10">
                            <span class="text-xs font-semibold text-on-surface-variant">Approval Date</span>
                            <span
                                class="text-sm font-bold"><?= Yii::$app->formatter->asDate($approval->created_at ?? null) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-semibold text-on-surface-variant">Status</span>
                            <span
                                class="px-2 py-0.5 bg-tertiary-container text-on-tertiary-fixed text-[10px] font-bold rounded"><?= ($approval->approved_by_ethical_committee ?? 0) ? 'ACTIVE' : 'PENDING' ?></span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-anchor" id="funding">
                <?php $fund = $model->funding; ?>
                <div class="p-8 border border-secondary-container rounded-2xl bg-secondary-container/5">
                    <h3 class="text-xl font-bold font-headline mb-6 flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary">payments</span>
                        07. Funding Details
                    </h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2 p-4 bg-white rounded-xl shadow-sm border border-outline-variant/10">
                            <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1">Primary
                                Sponsor</label>
                            <p class="text-lg font-bold text-primary"><?= Html::encode($fund->sponsor_name ?? '—') ?>
                            </p>
                            <p class="text-xs text-on-surface-variant">Sector:
                                <?= Html::encode($fund->funding_Sector ?? '—') ?>
                            </p>
                        </div>
                        <div class="p-4 border border-outline-variant/15 rounded-xl">
                            <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1">Grant
                                Amount</label>
                            <p class="text-sm font-bold">$<?= number_format($fund->Amount ?? 0, 2) ?> USD</p>
                        </div>
                        <div class="p-4 border border-outline-variant/15 rounded-xl">
                            <label class="block text-[10px] font-bold text-on-surface-variant uppercase mb-1">Fund
                                Code</label>
                            <p class="text-sm font-bold"><?= Html::encode($model->protocol_number) ?></p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Section 8: Abstracts -->
        <section class="section-anchor" id="abstracts">
            <?php $desc = $model->studyDescription; ?>
            <div class="bg-surface-container-low p-10 rounded-3xl">
                <h3 class="text-2xl font-bold font-headline mb-8">08. Abstracts</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div>
                        <h4
                            class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-4 border-b pb-2 border-outline-variant/20">
                            Scientific Summary</h4>
                        <p class="text-sm leading-relaxed text-on-surface font-light">
                            <?= Html::encode($desc->scientific_summary ?? 'Not available') ?>
                        </p>
                    </div>
                    <div>
                        <h4
                            class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-4 border-b pb-2 border-outline-variant/20">
                            Lay Summary</h4>
                        <p class="text-sm leading-relaxed text-on-surface font-light italic">
                            <?= Html::encode($desc->lay_summary ?? 'Not available') ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 9: Interventions & Outcomes -->
        <section class="section-anchor" id="interventions">
            <h3 class="text-xl font-bold font-headline mb-6">09. Interventions &amp; Outcomes</h3>
            <?php $interv = $model->studyIntervention; ?>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                <div class="md:col-span-4 space-y-6">
                    <div class="p-6 bg-primary-container text-white rounded-2xl">
                        <label class="text-[10px] font-bold uppercase opacity-70 block mb-2">Experimental
                            Intervention</label>
                        <p class="text-sm font-bold"><?= Html::encode($interv->intervention_name ?? '—') ?></p>
                        <p class="text-[10px] mt-1"><?= Html::encode($interv->intervention_description ?? '') ?></p>
                    </div>
                    <div class="p-6 bg-surface-container-highest rounded-2xl">
                        <label
                            class="text-[10px] font-bold uppercase text-on-surface-variant block mb-2">Comparator/Placebo</label>
                        <p class="text-sm font-bold"><?= Html::encode($interv->control_comparator ?? '—') ?></p>
                        <p class="text-[10px] mt-1 text-on-surface-variant">Oral, once daily for 28-day cycles</p>
                        <!-- static example -->
                    </div>
                </div>
                <div class="md:col-span-8 grid grid-cols-1 gap-6">
                    <div class="p-6 border border-outline-variant/20 rounded-2xl">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span
                                    class="px-2 py-0.5 bg-primary text-white text-[9px] font-bold rounded uppercase">Primary
                                    Outcome</span>
                                <h4 class="mt-2 text-sm font-bold">
                                    <?= Html::encode($interv->outcome_description ?? 'Not specified') ?>
                                </h4>
                            </div>
                            <span class="text-[10px] font-semibold text-on-surface-variant">TIME FRAME: VARIABLE</span>
                        </div>
                        <p class="text-xs text-on-surface-variant"><?= Html::encode($interv->type_of_outcome ?? '') ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 10: Publication Ledger -->
        <section class="section-anchor" id="publication">
            <h3 class="text-xl font-bold font-headline mb-6">10. Publication Ledger</h3>
            <?php $result = $model->studyResults; ?>
            <?php if ($result && is_object($model->studyResults)): ?>
                <div class="bg-surface-container-lowest border border-outline-variant/15 rounded-2xl overflow-hidden mb-6">
                    <div class="p-6 border-b border-outline-variant/10 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-surface-container flex items-center justify-center rounded-lg">
                                <span class="material-symbols-outlined text-on-surface-variant">menu_book</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-on-surface"><?= Html::encode($result->publication_title) ?>
                                </p>
                                <p class="text-[10px] text-on-surface-variant">Published:
                                    <?= Yii::$app->formatter->asDate($result->created_at) ?>
                                </p>
                            </div>
                        </div>
                        <?php if ($result->url_doi): ?>
                            <a class="text-primary text-xs font-bold flex items-center gap-1 hover:underline"
                                href="<?= Html::encode($result->url_doi) ?>" target="_blank">
                                DOI: <?= Html::encode($result->url_doi) ?> <span
                                    class="material-symbols-outlined text-xs">open_in_new</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <p class="text-xs font-semibold text-on-surface-variant mb-2 uppercase tracking-tighter">Results
                            Summary</p>
                        <p class="text-sm leading-relaxed"><?= Html::encode($result->summary_results) ?></p>
                    </div>
                </div>

            <?php else: ?>
                <div
                    class="bg-surface-container-lowest border border-outline-variant/15 rounded-2xl p-6 text-center text-gray-500">
                    No publications recorded.</div>
            <?php endif; ?>
        </section>

        <!-- Section 11: Data Access & Stats -->
        <section class="section-anchor pb-20" id="stats">
            <?php $stats = $model->opendataAccess; ?>
            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-12 md:col-span-4">
                    <h3 class="text-xl font-bold font-headline mb-2">11. Data Access &amp; Stats</h3>
                    <p class="text-sm text-on-surface-variant">Statistical sensitivity and heterogeneity metrics for
                        meta-analysis verification.</p>
                </div>
                <div class="col-span-12 md:col-span-8">
                    <div class="grid gap-6 grid-cols-1 sm:grid-cols-3">
                        <div class="bg-surface-container-low p-6 rounded-2xl text-center">
                            <p class="text-[10px] font-bold text-on-secondary-container uppercase mb-2">Sensitivity (P)
                            </p>
                            <p class="text-2xl font-black text-primary">
                                <?= Html::encode($stats->significant_p_value ?? '—') ?>
                            </p>
                            <span class="text-[9px] text-on-surface-variant">Confidence:
                                <?= $stats->confidential_interval ?? '—' ?>%</span>
                        </div>
                        <div class="bg-surface-container-low p-6 rounded-2xl text-center">
                            <p class="text-[10px] font-bold text-on-secondary-container uppercase mb-2">Heterogeneity
                                (I²)</p>
                            <p class="text-2xl font-black text-primary">
                                <?= Html::encode($stats->heterogenity_measure ?? '—') ?>%
                            </p>
                            <span
                                class="text-[9px] text-on-surface-variant"><?= Html::encode($stats->quality_assessment_variable ?? '') ?></span>
                        </div>
                        <div class="bg-surface-container-low p-6 rounded-2xl text-center">
                            <p class="text-[10px] font-bold text-on-secondary-container uppercase mb-2">Risk of Bias</p>
                            <p class="text-2xl font-black text-tertiary">
                                <?= Html::encode($stats->risk_of_bias_assessment ?? '—') ?>
                            </p>
                            <span
                                class="text-[9px] text-on-surface-variant"><?= Yii::$app->formatter->asDate($stats->updated_at ?? null) ?></span>
                        </div>
                    </div>
                    <div class="mt-8 p-6 bg-inverse-surface text-inverse-on-surface rounded-2xl flex items-start gap-4">
                        <span class="material-symbols-outlined text-inverse-primary">lock_open</span>
                        <div>
                            <h5 class="text-sm font-bold mb-1">Open Data Access Policy</h5>
                            <p class="text-xs opacity-70 leading-relaxed">
                                <?= Html::encode($stats->repository_name ?? '') ?> —
                                <?= Html::encode($stats->study_identification_variable ?? '') ?>
                                <?= ($stats->allow_publishing ?? 0) ? 'Data sharing allowed.' : 'Restricted access.' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

</div>