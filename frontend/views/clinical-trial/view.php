<?php

use frontend\models\AreaOfSpecialization;
use frontend\models\Country;
use frontend\models\InvestigatorTeam;
use frontend\models\Region;
use frontend\models\StudyIntervention;
use frontend\models\StudyResults;
use frontend\models\Studytype;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var frontend\models\ClinicalTrial $model */

$this->title = $model->getDisplayTitle();
$this->params['breadcrumbs'][] = ['label' => 'Clinical Trials', 'url' => ['index']];
$this->params['breadcrumbs'][] = StringHelper::truncate($this->title, 40);

$fmt = Yii::$app->formatter;
$canEdit = Yii::$app->user->can('updateTrial', ['trial' => $model]);

/* ─────────────────────────────── View helpers ─────────────────────────────── */

/** True when a value should be treated as "not provided". */
$blank = static fn($v): bool => $v === null || $v === '' || (is_string($v) && trim($v) === '');

/** Label for a coded value; falls back to the raw value, or null when blank. */
$label = static fn(array $options, $v) => $blank($v) ? null : ($options[$v] ?? $v);

/** Name from a lookup table by id; tolerates legacy free-text values. */
$lookup = static function (string $class, string $attr, $id) use ($blank) {
    if ($blank($id)) {
        return null;
    }
    $row = is_numeric($id) ? $class::findOne((int) $id) : null;
    return $row->$attr ?? $id;
};

$yesNo = static fn($v) => $blank($v) ? null : ((int) $v ? 'Yes' : 'No');

/** Only allow http(s) links — blocks javascript:/data: URLs stored in free-text fields. */
$safeUrl = static function ($url) use ($blank): ?string {
    if ($blank($url)) {
        return null;
    }
    $url = trim($url);
    if (!preg_match('~^https?://~i', $url) && preg_match('~^(10\.\d{4,}/\S+)$~', $url)) {
        $url = 'https://doi.org/' . $url; // bare DOI
    }
    return preg_match('~^https?://~i', $url) ? $url : null;
};

/**
 * Renders one labelled value. $value is plain text (encoded here) unless $raw = true.
 * Long text keeps its line breaks and wraps instead of overflowing.
 */
$field = static function (string $name, $value, bool $raw = false, string $extra = '') use ($blank): string {
    $content = $blank($value)
        ? '<span class="text-on-surface-variant/60">—</span>'
        : ($raw ? $value : nl2br(Html::encode($value)));

    return '<div class="min-w-0 ' . $extra . '">'
        . '<dt class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">' . Html::encode($name) . '</dt>'
        . '<dd class="text-sm font-medium text-on-surface leading-relaxed break-words">' . $content . '</dd>'
        . '</div>';
};

/** Big number tile. */
$stat = static function (string $name, $value, string $suffix = '') use ($blank): string {
    $shown = $blank($value) ? '—' : Html::encode($value) . $suffix;
    return '<div class="min-w-0 bg-surface-container-lowest px-[1rem] py-[1rem] rounded-xl border border-outline-variant/15 text-center">'
        . '<p class="text-2xl font-black text-primary break-words">' . $shown . '</p>'
        . '<p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">' . Html::encode($name) . '</p>'
        . '</div>';
};

$badge = static fn(string $text, string $classes = 'bg-surface-container-high text-on-surface-variant'): string =>
    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ' . $classes . '">'
    . Html::encode($text) . '</span>';

/** Placeholder for a wizard step that has not been filled in yet. */
$empty = static fn(string $what): string =>
    '<p class="text-sm text-on-surface-variant bg-surface-container-low rounded-xl px-[1.25rem] py-[1rem]">No ' . Html::encode($what) . ' recorded yet.</p>';

/** Section heading with a number. */
$heading = static fn(string $num, string $title, string $icon): string =>
    '<h3 class="flex items-center gap-[0.75rem] text-lg sm:text-xl font-bold font-headline text-on-surface mb-6">'
    . '<span class="material-symbols-outlined text-primary">' . $icon . '</span>'
    . '<span><span class="text-on-surface-variant/70 mr-1">' . $num . '.</span>' . Html::encode($title) . '</span>'
    . '</h3>';

$card = 'section-anchor scroll-mt-40 min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-8';

/* ─────────────────────────────── Data ─────────────────────────────── */

$statusLabel = $model::getRegistrationStatusOptions()[$model->registration_status] ?? 'Unknown';
$statusClasses = [
    'Registered' => 'bg-tertiary-container text-white',
    'Ongoing' => 'bg-secondary-container text-on-secondary-container',
    'Pending' => 'bg-primary-fixed text-on-primary-fixed',
    'Rejected' => 'bg-error-container text-on-error-container',
][$statusLabel] ?? 'bg-surface-container-high text-on-surface-variant';

$purpose = $model->purpose;
$eligibility = $model->studyPopulationEligibility;
$timeline = $model->timeline;
$approval = $model->ethicalApproval;
$fund = $model->funding;
$desc = $model->studyDescription;
$interv = $model->studyIntervention;
$result = $model->studyResults;
$stats = $model->opendataAccess;
$pi = $model->getPrimaryInvestigator();

$sections = [
    'trial-info' => 'Trial Info',
    'purpose-design' => 'Purpose & Design',
    'eligibility' => 'Eligibility',
    'timeline-location' => 'Timeline',
    'investigator-team' => 'Investigators',
    'regulatory' => 'Regulatory',
    'funding' => 'Funding',
    'abstracts' => 'Abstracts',
    'interventions' => 'Outcomes',
    'publication' => 'Publications',
    'stats' => 'Open Data',
];
?>

<div class="trial-view min-w-0 max-w-7xl mx-auto">

    <!-- ═════════════ Header ═════════════ -->
    <header class="bg-surface-container-low rounded-2xl px-[1.25rem] sm:px-8 pt-6 sm:pt-8 pb-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 mb-[0.75rem]">
                    <?= $badge($statusLabel, $statusClasses) ?>
                    <?php if (!$blank($model->protocol_number)): ?>
                        <span class="text-xs text-on-surface-variant">Protocol
                            <span class="font-semibold text-on-surface"><?= Html::encode($model->protocol_number) ?></span></span>
                    <?php endif; ?>
                    <?php if (!$blank($model->registration_number)): ?>
                        <span class="text-xs text-on-surface-variant">Registry
                            <span class="font-semibold text-on-surface"><?= Html::encode($model->registration_number) ?></span></span>
                    <?php endif; ?>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold font-headline text-on-surface tracking-tight leading-tight break-words">
                    <?= Html::encode($this->title) ?>
                    <?php if (!$blank($model->scientific_acronym)): ?>
                        <span class="text-on-surface-variant font-semibold">(<?= Html::encode($model->scientific_acronym) ?>)</span>
                    <?php endif; ?>
                </h1>
                <?php if (!$blank($model->public_title) && $model->public_title !== $this->title): ?>
                    <p class="mt-2 text-sm sm:text-base text-on-surface-variant break-words"><?= Html::encode($model->public_title) ?></p>
                <?php endif; ?>

                <!-- Key facts -->
                <dl class="mt-[1.25rem] grid grid-cols-2 sm:flex sm:flex-wrap gap-x-8 gap-y-3 text-xs">
                    <div class="min-w-0">
                        <dt class="text-on-surface-variant">Phase</dt>
                        <dd class="font-bold text-on-surface"><?= Html::encode($label($purpose ? $purpose->getPhaseOfStudy() : [], $purpose->phase_of_study ?? null) ?? '—') ?></dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-on-surface-variant">Principal investigator</dt>
                        <dd class="font-bold text-on-surface break-words"><?= Html::encode($pi->name ?? '—') ?></dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-on-surface-variant">Start</dt>
                        <dd class="font-bold text-on-surface"><?= Html::encode($model->getFormattedStartDate()) ?></dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-on-surface-variant">Last updated</dt>
                        <dd class="font-bold text-on-surface"><?= $model->updated_at ? $fmt->asDate($model->updated_at) : '—' ?></dd>
                    </div>
                </dl>
            </div>

            <div class="flex flex-wrap gap-2 shrink-0 print:hidden">
                <?php if ($canEdit): ?>
                    <?= Html::a(
                        '<span class="material-symbols-outlined text-[18px]">edit</span> Edit trial',
                        ['update', 'id' => $model->id],
                        ['class' => 'inline-flex items-center gap-2 px-[1rem] py-2.5 bg-gradient-to-r from-primary to-primary-container text-on-primary font-semibold text-sm rounded-lg shadow-md hover:opacity-90 transition-opacity']
                    ) ?>
                <?php endif; ?>
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-[1rem] py-2.5 bg-surface-container-lowest text-primary font-semibold text-sm rounded-lg border border-outline-variant/30 hover:bg-white transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">print</span> Print
                </button>
            </div>
        </div>
    </header>

    <!-- ═════════════ Sticky section nav ═════════════ -->
    <nav id="trial-section-nav" aria-label="Trial sections"
        class="sticky top-16 z-30 mb-8 bg-surface/90 backdrop-blur-md rounded-xl border border-outline-variant/15 shadow-sm print:hidden">
        <div class="flex gap-1 p-1.5 overflow-x-auto no-scrollbar">
            <?php $i = 0;
            foreach ($sections as $anchor => $name):
                $i++; ?>
                <a href="#<?= $anchor ?>" data-section-link="<?= $anchor ?>"
                    class="shrink-0 whitespace-nowrap px-[0.75rem] py-2 text-xs font-medium rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">
                    <?= $i ?>. <?= Html::encode($name) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <div class="space-y-6 sm:space-y-8 pb-24">

        <!-- 1. Trial info -->
        <section id="trial-info" class="<?= $card ?>">
            <?= $heading('01', 'Trial Information', 'badge') ?>
            <dl class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-6">
                <?= $field('Scientific title', $model->scientific_title, false, 'sm:col-span-2 xl:col-span-3') ?>
                <?= $field('Lay title', $model->public_title, false, 'sm:col-span-2 xl:col-span-3') ?>
                <?= $field('Acronym', $model->scientific_acronym) ?>
                <?= $field('Protocol number', $model->protocol_number) ?>
                <?= $field('Protocol version', $model->protocol_version) ?>
                <?= $field('Registration status', $statusLabel) ?>
                <?= $field('Registration number', $model->registration_number) ?>
                <?= $field('Area of study', $lookup(AreaOfSpecialization::class, 'title', $model->area_of_specialization)) ?>
            </dl>
        </section>

        <!-- 2. Purpose & design -->
        <section id="purpose-design" class="<?= $card ?>">
            <?= $heading('02', 'Purpose & Design', 'science') ?>
            <?php if ($purpose): ?>
                <dl class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
                    <?= $field('Study purpose', $purpose->study_purpose) ?>
                    <?= $field('Objective', $purpose->study_objective) ?>
                    <?php if (!$blank($purpose->study_hypothesis)): ?>
                        <div class="min-w-0 lg:col-span-2 border-l-4 border-primary bg-primary/[0.03] rounded-r-xl px-[1.25rem] py-[1rem]">
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Hypothesis</dt>
                            <dd class="text-base italic text-on-surface leading-relaxed break-words"><?= nl2br(Html::encode($purpose->study_hypothesis)) ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>
                <dl class="mt-6 pt-6 border-t border-outline-variant/15 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-x-8 gap-y-6">
                    <?= $field('Type of study', $lookup(Studytype::class, 'name', $purpose->type_of_study)) ?>
                    <?= $field('Phase', $label($purpose->getPhaseOfStudy(), $purpose->phase_of_study)) ?>
                    <?= $field('Intervention', $purpose->intervention) ?>
                    <?= $field('Control group', $yesNo($purpose->design_control_group_presence)) ?>
                    <?= $field('Control group name', $purpose->control_group_name) ?>
                    <?= $field('Randomization', $purpose->randomization_method_name) ?>
                    <?= $field('Masking', $yesNo($purpose->masking_status)) ?>
                    <?= $field('Masking details', $purpose->masking_description) ?>
                </dl>
            <?php else: ?>
                <?= $empty('purpose & design details') ?>
            <?php endif; ?>
        </section>

        <!-- 3. Eligibility -->
        <section id="eligibility" class="<?= $card ?>">
            <?= $heading('03', 'Population & Eligibility', 'groups') ?>
            <?php if ($eligibility): ?>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <dl class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 content-start">
                        <?= $field('Health condition studied', $eligibility->health_condition_studied, false, 'sm:col-span-2') ?>
                        <?= $field('Eligibility type', $label($eligibility->getTypeOfEligibilityOptions(), $eligibility->type_of_eligibility)) ?>
                    </dl>
                    <div class="lg:col-span-5 grid grid-cols-3 gap-[0.75rem] content-start">
                        <?= $stat('Target', $eligibility->participant_target_number) ?>
                        <?= $stat('Sample size', $eligibility->sample_size) ?>
                        <?= $stat('Enrolled', $eligibility->final_number_of_participants) ?>
                    </div>
                </div>
            <?php else: ?>
                <?= $empty('eligibility details') ?>
            <?php endif; ?>
        </section>

        <!-- 4 + 5. Timeline & investigators -->
        <div class="grid grid-cols-1 2xl:grid-cols-2 gap-6 sm:gap-8">
            <section id="timeline-location" class="<?= $card ?>">
                <?= $heading('04', 'Timeline & Locations', 'event') ?>
                <?php if ($timeline): ?>
                    <div class="grid grid-cols-2 gap-[0.75rem] mb-6">
                        <div class="min-w-0 p-[1rem] rounded-xl bg-surface-container-low">
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant mb-1">Start</p>
                            <p class="text-sm font-bold"><?= $timeline->anticipated_start_date ? $fmt->asDate($timeline->anticipated_start_date) : '—' ?></p>
                        </div>
                        <div class="min-w-0 p-[1rem] rounded-xl bg-surface-container-low">
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant mb-1">Est. completion</p>
                            <p class="text-sm font-bold"><?= $timeline->anticipated_end_date ? $fmt->asDate($timeline->anticipated_end_date) : '—' ?></p>
                        </div>
                    </div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                        <?= $field('Duration', $blank($timeline->study_duration) ? null : $timeline->study_duration . ' months') ?>
                        <?= $field('Recruitment status', $label($timeline->getRecruitmentStatus(), $timeline->recruitment_status)) ?>
                        <?= $field('Recruiting country', $lookup(Country::class, 'name', $timeline->recruiting_country)) ?>
                        <?= $field('Centre / region', $lookup(Region::class, 'name', $timeline->centre_region)) ?>
                        <?= $field('Study site', $timeline->study_site_location, false, 'sm:col-span-2') ?>
                        <?= $field('Physical address', $timeline->centre_pysical_address) ?>
                        <?= $field('Postal address', $timeline->centre_postal_address) ?>
                    </dl>
                <?php else: ?>
                    <?= $empty('timeline') ?>
                <?php endif; ?>
            </section>

            <section id="investigator-team" class="<?= $card ?>">
                <?= $heading('05', 'Investigator Team', 'diversity_3') ?>
                <?php if ($model->investigators): ?>
                    <ul class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-1 gap-[0.75rem]">
                        <?php foreach ($model->investigators as $inv): ?>
                            <li class="min-w-0 p-[1rem] rounded-xl border border-outline-variant/15 bg-surface">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <p class="min-w-0 text-sm font-bold text-on-surface break-words"><?= Html::encode($inv->name) ?></p>
                                    <?= $badge(
                                        InvestigatorTeam::getRoleOptions()[$inv->role] ?? 'Member',
                                        (int) $inv->role === 1 ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-surface-container-high text-on-surface-variant'
                                    ) ?>
                                </div>
                                <?php
                                $place = implode(', ', array_filter([$inv->institution, $inv->cityModel->name ?? null, $inv->countryModel->name ?? null]));
                                ?>
                                <?php if ($place !== ''): ?>
                                    <p class="mt-1 text-xs text-on-surface-variant break-words"><?= Html::encode($place) ?></p>
                                <?php endif; ?>
                                <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap gap-x-4 gap-y-1 text-xs">
                                    <?php if (!$blank($inv->email_address)): ?>
                                        <?= Html::mailto(
                                            '<span class="material-symbols-outlined text-[14px]">mail</span> ' . Html::encode($inv->email_address),
                                            $inv->email_address,
                                            ['class' => 'inline-flex items-center gap-1 text-primary font-medium break-all hover:underline']
                                        ) ?>
                                    <?php endif; ?>
                                    <?php if (!$blank($inv->mobile_number)): ?>
                                        <span class="inline-flex items-center gap-1 text-on-surface-variant">
                                            <span class="material-symbols-outlined text-[14px]">call</span><?= Html::encode($inv->mobile_number) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <?= $empty('investigators') ?>
                <?php endif; ?>
            </section>
        </div>

        <!-- 6 + 7. Regulatory & funding -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 sm:gap-8">
            <section id="regulatory" class="<?= $card ?>">
                <?= $heading('06', 'Ethics & Regulatory Approval', 'verified') ?>
                <?php if ($approval): ?>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                        <?= $field('Regulatory body', $label($approval->getEthicalRegulatoryBodyOptions(), $approval->ethical_regulatory_body)) ?>
                        <?= $field(
                            'Ethics committee approval',
                            $blank($approval->approved_by_ethical_committee) ? null
                            : ((int) $approval->approved_by_ethical_committee
                                ? $badge('Approved', 'bg-tertiary-container text-white')
                                : $badge('Pending', 'bg-surface-container-high text-on-surface-variant')),
                            true
                        ) ?>
                        <?= $field('Approval document no.', $approval->document_number) ?>
                        <?= $field('Recorded on', $approval->created_at ? $fmt->asDate($approval->created_at) : null) ?>
                    </dl>
                <?php else: ?>
                    <?= $empty('ethics approval') ?>
                <?php endif; ?>
            </section>

            <section id="funding" class="<?= $card ?>">
                <?= $heading('07', 'Funding & Sponsorship', 'payments') ?>
                <?php if ($fund): ?>
                    <div class="p-[1rem] sm:p-5 rounded-xl bg-surface-container-low mb-6 min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Primary sponsor</p>
                        <p class="text-lg font-bold text-primary break-words"><?= Html::encode($fund->sponsor_name ?: '—') ?></p>
                    </div>
                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-6">
                        <?= $field(
                            'Amount',
                            $blank($fund->Amount) ? null : trim(($fund->currency ?? '') . ' ' . number_format((float) $fund->Amount, 2))
                        ) ?>
                        <?= $field('Sector', $label($fund->getFundingSectors(), $fund->funding_Sector)) ?>
                        <?= $field('Country', $lookup(Country::class, 'name', $fund->country)) ?>
                    </dl>
                <?php else: ?>
                    <?= $empty('funding details') ?>
                <?php endif; ?>
            </section>
        </div>

        <!-- 8. Abstracts -->
        <section id="abstracts" class="<?= $card ?>">
            <?= $heading('08', 'Study Description', 'description') ?>
            <?php if ($desc): ?>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <article class="min-w-0">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-[0.75rem] pb-2 border-b border-outline-variant/20">Scientific summary</h4>
                        <p class="text-sm leading-relaxed text-on-surface break-words"><?= $blank($desc->scientific_summary) ? '—' : nl2br(Html::encode($desc->scientific_summary)) ?></p>
                    </article>
                    <article class="min-w-0">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-[0.75rem] pb-2 border-b border-outline-variant/20">Lay summary</h4>
                        <p class="text-sm leading-relaxed text-on-surface break-words"><?= $blank($desc->lay_summary) ? '—' : nl2br(Html::encode($desc->lay_summary)) ?></p>
                    </article>
                </div>
                <?php if ($website = $safeUrl($desc->study_website)): ?>
                    <p class="mt-6 text-xs min-w-0">
                        <span class="text-on-surface-variant">Study website:</span>
                        <?= Html::a(Html::encode($website), $website, ['target' => '_blank', 'rel' => 'noopener noreferrer', 'class' => 'text-primary font-medium break-all hover:underline']) ?>
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <?= $empty('study description') ?>
            <?php endif; ?>
        </section>

        <!-- 9. Interventions & outcomes -->
        <section id="interventions" class="<?= $card ?>">
            <?= $heading('09', 'Interventions & Outcomes', 'medication') ?>
            <?php if ($interv): ?>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-[1rem] content-start">
                        <div class="min-w-0 p-[1.25rem] bg-primary-container text-white rounded-2xl">
                            <p class="text-[10px] font-bold uppercase opacity-70 mb-2">Intervention</p>
                            <p class="text-sm font-bold break-words"><?= Html::encode($interv->intervention_name ?: '—') ?></p>
                            <?php if (!$blank($interv->intervention_description)): ?>
                                <p class="text-xs mt-2 opacity-90 leading-relaxed break-words"><?= nl2br(Html::encode($interv->intervention_description)) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0 p-[1.25rem] bg-surface-container-high rounded-2xl">
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant mb-2">Comparator / placebo</p>
                            <p class="text-sm font-bold break-words"><?= Html::encode($interv->control_comparator ?: '—') ?></p>
                        </div>
                    </div>
                    <div class="lg:col-span-7 min-w-0 p-[1.25rem] sm:p-6 border border-outline-variant/20 rounded-2xl">
                        <?php $outcomeType = $label(StudyIntervention::getTypeOfOutcomeOptions(), $interv->type_of_outcome); ?>
                        <?= $badge(($outcomeType ?? 'Unspecified') . ' outcome', 'bg-primary text-white') ?>
                        <p class="mt-[0.75rem] text-sm leading-relaxed text-on-surface break-words">
                            <?= $blank($interv->outcome_description) ? '<span class="text-on-surface-variant">No outcome description provided.</span>' : nl2br(Html::encode($interv->outcome_description)) ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <?= $empty('interventions') ?>
            <?php endif; ?>
        </section>

        <!-- 10. Publication -->
        <section id="publication" class="<?= $card ?>">
            <?= $heading('10', 'Results & Publication', 'menu_book') ?>
            <?php if ($result): ?>
                <div class="flex flex-col sm:flex-row sm:items-start gap-[1rem] pb-6 mb-6 border-b border-outline-variant/15">
                    <div class="h-10 w-10 shrink-0 bg-surface-container flex items-center justify-center rounded-lg">
                        <span class="material-symbols-outlined text-on-surface-variant">article</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-on-surface break-words"><?= Html::encode($result->publication_title ?: 'Untitled publication') ?></p>
                        <p class="mt-1 text-xs text-on-surface-variant">
                            <?= Html::encode(implode(' · ', array_filter([
                                $label(StudyResults::getPublicationTypeOptions(), $result->publication_type),
                                $result->publisher,
                            ]))) ?>
                        </p>
                        <?php if ($doi = $safeUrl($result->url_doi)): ?>
                            <?= Html::a(
                                Html::encode($result->url_doi) . ' <span class="material-symbols-outlined text-[14px] align-middle">open_in_new</span>',
                                $doi,
                                ['target' => '_blank', 'rel' => 'noopener noreferrer', 'class' => 'mt-2 inline-block text-xs text-primary font-semibold break-all hover:underline']
                            ) ?>
                        <?php elseif (!$blank($result->url_doi)): ?>
                            <p class="mt-2 text-xs text-on-surface-variant break-all"><?= Html::encode($result->url_doi) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                    <?= $field('Results summary', $result->summary_results, false, 'sm:col-span-2') ?>
                    <?= $field('Permission to publish', $yesNo($result->permission_to_publish)) ?>
                    <?= $field('Authorising committee', $result->authority_committe_name) ?>
                </dl>
            <?php else: ?>
                <?= $empty('publications') ?>
            <?php endif; ?>
        </section>

        <!-- 11. Open data & stats -->
        <section id="stats" class="<?= $card ?>">
            <?= $heading('11', 'Open Data Access & Statistics', 'query_stats') ?>
            <?php if ($stats): ?>
                <div class="flex items-start gap-[1rem] p-[1.25rem] rounded-2xl mb-6 <?= (int) $stats->allow_publishing ? 'bg-tertiary-container/10 border border-tertiary-container/30' : 'bg-surface-container-low' ?>">
                    <span class="material-symbols-outlined <?= (int) $stats->allow_publishing ? 'text-tertiary' : 'text-on-surface-variant' ?>">
                        <?= (int) $stats->allow_publishing ? 'lock_open' : 'lock' ?>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold"><?= (int) $stats->allow_publishing ? 'Data sharing allowed' : 'Restricted access' ?></p>
                        <p class="text-xs text-on-surface-variant break-words">
                            <?= Html::encode(implode(' — ', array_filter([$stats->repository_name, $stats->study_identification_variable]))) ?: 'No repository specified.' ?>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-[0.75rem] mb-8">
                    <?= $stat('p-value', $stats->significant_p_value) ?>
                    <?= $stat('Confidence interval', $stats->confidential_interval, '%') ?>
                    <?= $stat('Heterogeneity (I²)', $stats->heterogenity_measure, is_numeric($stats->heterogenity_measure) ? '%' : '') ?>
                    <?= $stat('Effect size', $stats->effective_size_value) ?>
                    <?= $stat('Risk of bias', $stats->risk_of_bias_assessment) ?>
                </div>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <?= $field('Statistical method', $stats->statistical_method_used) ?>
                    <?= $field('Sensitivity analysis', $stats->sensitivity_analysis_result) ?>
                    <?= $field('Multiple-comparison adjustment', $stats->adjustable_miltiple_comparison) ?>
                    <?= $field('Handling of missing data', $stats->handling_missing_data) ?>
                    <?= $field('Quality assessment', $stats->quality_assessment_variable) ?>
                    <?= $field('Publication bias', $stats->publication_bias_indicator) ?>
                    <?= $field('Study limitations', $stats->study_limitation) ?>
                    <?= $field('Conflicts of interest', $stats->potential_conflict_of_interest) ?>
                </dl>
            <?php else: ?>
                <?= $empty('open data access details') ?>
            <?php endif; ?>
        </section>

    </div>
</div>

<?php
// Highlight the section-nav link for the section currently in view, and keep it scrolled into view.
$this->registerJs(<<<'JS'
(function () {
    var nav = document.getElementById('trial-section-nav');
    if (!nav || !('IntersectionObserver' in window)) return;
    var links = {};
    nav.querySelectorAll('[data-section-link]').forEach(function (a) { links[a.dataset.sectionLink] = a; });
    var active = ['bg-primary-fixed', 'text-on-primary-fixed', 'font-semibold'];
    function activate(id) {
        Object.keys(links).forEach(function (k) {
            var on = k === id;
            active.forEach(function (c) { links[k].classList.toggle(c, on); });
            links[k].classList.toggle('text-on-surface-variant', !on);
            if (on) links[k].scrollIntoView({ block: 'nearest', inline: 'nearest' });
        });
    }
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) { if (e.isIntersecting) activate(e.target.id); });
    }, { rootMargin: '-35% 0px -60% 0px' });
    document.querySelectorAll('.trial-view section[id]').forEach(function (s) { observer.observe(s); });
    activate('trial-info');
})();
JS);
?>
