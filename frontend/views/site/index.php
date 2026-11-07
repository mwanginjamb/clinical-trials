<?php

/**
 * Registry dashboard for research scientists and the ethics review board.
 * Every figure comes from common\library\DashboardComponent; nothing here is hard-coded.
 *
 * @var yii\web\View $this
 * @var common\library\DashboardComponent $dash
 */

use common\rbac\Rbac;
use yii\helpers\Html;

$this->title = 'Registry dashboard';

$user = Yii::$app->user;
$fmt = Yii::$app->formatter;
$kpis = $dash->kpis();
$total = $kpis[0]['value'];
$attention = $dash->attention();
$milestones = $dash->milestones();
$progress = $dash->recruitmentProgress();
$years = $dash->startsByYear();
$myRecords = $dash->completeness((int) $user->id);
$scopeIsMine = $myRecords['total'] > 0;
$records = $scopeIsMine ? $myRecords : $dash->completeness();

// Status presentation: always icon + label, never colour alone. Reserved for oversight items.
$severity = [
    'critical' => ['Critical', 'report', 'bg-error-container text-on-error-container', 'border-l-error'],
    'serious' => ['Serious', 'warning', 'bg-[#ffdbc8] text-[#6e2c00]', 'border-l-[#b4530a]'],
    'warning' => ['Follow up', 'flag', 'bg-[#fff3cd] text-[#7a5c00]', 'border-l-[#c99a00]'],
    'info' => ['Check data', 'info', 'bg-surface-container-high text-on-surface-variant', 'border-l-outline-variant'],
];
$severityRank = ['critical' => 0, 'serious' => 1, 'warning' => 2, 'info' => 3];
usort($attention, fn($a, $b) => [$a['count'] === 0, $severityRank[$a['severity']]] <=> [$b['count'] === 0, $severityRank[$b['severity']]]);
$openItems = array_sum(array_map(fn($a) => $a['count'] > 0 ? 1 : 0, $attention));

$card = 'min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm';
$h2 = 'font-headline text-lg font-bold text-on-surface';
$sub = 'text-xs text-on-surface-variant';

/** Meter: same-hue track, value bar anchored left. */
$meter = static fn(int $percent, string $label, string $bar = 'bg-primary-container'): string =>
    '<div class="h-2 w-full rounded-full bg-primary-fixed/50 overflow-hidden" role="meter" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . $percent . '" aria-label="' . Html::encode($label) . '">'
    . '<div class="h-full rounded-full ' . $bar . '" style="width:' . min(100, max($percent > 0 ? 2 : 0, $percent)) . '%"></div></div>';

/**
 * Ranked horizontal bars, one hue; "Not recorded" in de-emphasis grey.
 * Direct labels (count + share), per-bar tooltip, and a table view.
 */
$bars = static function (string $id, string $title, string $caption, array $rows) use ($card, $h2, $sub): string {
    ob_start(); ?>
    <section class="<?= $card ?> p-[1.25rem] sm:p-6" aria-labelledby="<?= $id ?>-title">
        <h3 id="<?= $id ?>-title" class="<?= $h2 ?>"><?= Html::encode($title) ?></h3>
        <p class="<?= $sub ?> mb-[1.125rem]"><?= Html::encode($caption) ?></p>
        <?php if (!$rows): ?>
            <p class="text-sm text-on-surface-variant">No trials yet.</p>
        <?php else:
            $max = max(array_column($rows, 'count')); ?>
            <ul class="space-y-2.5">
                <?php foreach ($rows as $r): ?>
                    <li class="grid grid-cols-[minmax(0,9rem)_1fr_auto] sm:grid-cols-[minmax(0,11rem)_1fr_auto] items-center gap-x-[0.75rem]"
                        title="<?= Html::encode("{$r['label']}: {$r['count']} trial" . ($r['count'] === 1 ? '' : 's') . " ({$r['share']}%)") ?>">
                        <span class="text-xs text-on-surface truncate <?= $r['missing'] ? 'italic text-on-surface-variant' : '' ?>"><?= Html::encode($r['label']) ?></span>
                        <span class="h-[0.875rem] rounded-r bg-surface-container-low">
                            <span class="block h-full rounded-r <?= $r['missing'] ? 'bg-outline-variant' : 'bg-primary-container' ?>"
                                style="width:<?= max(2, round($r['count'] * 100 / $max)) ?>%"></span>
                        </span>
                        <span class="text-xs tabular-nums text-on-surface whitespace-nowrap"><b><?= $r['count'] ?></b> <span class="text-on-surface-variant"><?= $r['share'] ?>%</span></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <details class="mt-[1.125rem] text-xs">
                <summary class="cursor-pointer text-primary font-semibold">Show as table</summary>
                <table class="mt-2 w-full text-left">
                    <thead><tr class="text-on-surface-variant"><th class="py-1 font-semibold">Category</th><th class="py-1 font-semibold text-right">Trials</th><th class="py-1 font-semibold text-right">Share</th></tr></thead>
                    <tbody>
                        <?php foreach ($rows as $r): ?>
                            <tr class="border-t border-outline-variant/20"><td class="py-1"><?= Html::encode($r['label']) ?></td><td class="py-1 text-right tabular-nums"><?= $r['count'] ?></td><td class="py-1 text-right tabular-nums"><?= $r['share'] ?>%</td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </details>
        <?php endif; ?>
    </section>
    <?php return ob_get_clean();
};
?>

<div class="min-w-0 space-y-8">

    <!-- ═════════════ Header ═════════════ -->
    <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-[1rem]">
        <div class="min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">SERCEA clinical trials registry</p>
            <h1 class="font-headline text-2xl sm:text-3xl font-extrabold tracking-tight text-on-surface">Registry dashboard</h1>
            <p class="mt-1 text-sm text-on-surface-variant max-w-3xl">
                Live figures from <?= number_format($total) ?> trial record<?= $total === 1 ? '' : 's' ?>, as of <?= $fmt->asDatetime(time(), 'medium') ?>.
                Figures only count what has been entered; sections not yet completed show as "not recorded".
            </p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <?php if ($user->can(Rbac::PERM_CREATE_TRIAL)): ?>
                <?= Html::a('<span class="material-symbols-outlined text-[18px]">add</span>Register a trial', ['/clinical-trial/create'],
                    ['class' => 'inline-flex items-center gap-2 px-[1.125rem] py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:opacity-90']) ?>
            <?php endif; ?>
            <?php if ($user->can(Rbac::PERM_IMPORT_TRIALS)): ?>
                <?= Html::a('<span class="material-symbols-outlined text-[18px]">upload</span>Bulk import', ['/trial-import/import'],
                    ['class' => 'inline-flex items-center gap-2 px-[1.125rem] py-2.5 rounded-xl text-sm font-bold text-primary bg-primary-fixed/40 hover:bg-primary-fixed/70']) ?>
            <?php endif; ?>
        </div>
    </header>

    <!-- ═════════════ KPI row ═════════════ -->
    <section aria-label="Key figures" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-[0.75rem]">
        <?php foreach ($kpis as $k): ?>
            <div class="<?= $card ?> px-[1.125rem] py-[1.125rem] flex flex-col">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant"><?= Html::encode($k['label']) ?></p>
                    <span class="material-symbols-outlined text-[20px] text-primary-container shrink-0" aria-hidden="true"><?= $k['icon'] ?></span>
                </div>
                <p class="mt-2 font-headline text-3xl font-extrabold tabular-nums text-on-surface"><?= Html::encode($k['display']) ?></p>
                <?php if (array_key_exists('percent', $k) && $k['percent'] !== null): ?>
                    <div class="mt-2"><?= $meter($k['percent'], $k['label']) ?></div>
                <?php endif; ?>
                <p class="mt-2 text-xs text-on-surface-variant"><?= Html::encode($k['context']) ?></p>
                <p class="mt-auto pt-2 text-[11px] text-on-surface-variant/80 leading-snug">
                    <span class="material-symbols-outlined text-[13px] align-[-2px]" aria-hidden="true">info</span>
                    <?= Html::encode($k['definition']) ?>
                </p>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- ═════════════ Oversight ═════════════ -->
    <section class="<?= $card ?> overflow-hidden" aria-labelledby="attention-title">
        <div class="px-[1.25rem] sm:px-6 pt-[1.25rem] pb-[0.875rem] flex flex-wrap items-end justify-between gap-2 border-b border-outline-variant/15">
            <div>
                <h2 id="attention-title" class="<?= $h2 ?>">Needs attention</h2>
                <p class="<?= $sub ?>">Oversight checks run on every trial record. Each shows why it matters and which trials are affected.</p>
            </div>
            <p class="text-xs font-semibold <?= $openItems ? 'text-on-surface' : 'text-tertiary' ?>">
                <span class="material-symbols-outlined text-[16px] align-[-3px]"><?= $openItems ? 'pending_actions' : 'task_alt' ?></span>
                <?= $openItems ? "{$openItems} of " . count($attention) . ' checks need follow-up' : 'All checks clear' ?>
            </p>
        </div>
        <ul class="divide-y divide-outline-variant/10">
            <?php foreach ($attention as $a):
                [$sevLabel, $sevIcon, $sevClass, $sevBorder] = $severity[$a['severity']];
                $clear = $a['count'] === 0; ?>
                <li class="border-l-4 <?= $clear ? 'border-l-transparent' : $sevBorder ?>">
                    <details class="group" <?= !$clear && $a['severity'] === 'critical' ? 'open' : '' ?>>
                        <summary class="list-none [&::-webkit-details-marker]:hidden <?= $clear ? 'cursor-default' : 'cursor-pointer hover:bg-surface-container-low/60' ?> px-[1.25rem] sm:px-6 py-[0.875rem] grid grid-cols-[1fr_auto] sm:grid-cols-[7.5rem_1fr_auto] items-center gap-x-[0.875rem] gap-y-1.5">
                            <?php if ($clear): ?>
                                <span class="col-span-2 sm:col-span-1 justify-self-start inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-surface-container text-tertiary whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[14px]">check</span>Clear</span>
                            <?php else: ?>
                                <span class="col-span-2 sm:col-span-1 justify-self-start inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider whitespace-nowrap <?= $sevClass ?>">
                                    <span class="material-symbols-outlined text-[14px]"><?= $sevIcon ?></span><?= $sevLabel ?></span>
                            <?php endif; ?>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold <?= $clear ? 'text-on-surface-variant' : 'text-on-surface' ?>"><?= Html::encode($a['title']) ?></span>
                                <span class="block text-xs text-on-surface-variant"><?= Html::encode($a['why']) ?></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="font-headline text-xl font-extrabold tabular-nums <?= $clear ? 'text-on-surface-variant/50' : 'text-on-surface' ?>"><?= $a['count'] ?></span>
                                <?php if (!$clear): ?><span class="material-symbols-outlined text-[18px] text-on-surface-variant transition-transform group-open:rotate-180">expand_more</span><?php endif; ?>
                            </span>
                        </summary>
                        <?php if (!$clear): ?>
                            <ul class="px-[1.25rem] sm:px-6 pb-[0.875rem] pl-[1.25rem] sm:pl-[10.375rem] space-y-1.5">
                                <?php foreach ($a['trials'] as $t): ?>
                                    <li class="text-xs flex flex-wrap gap-x-2">
                                        <?= Html::a(Html::encode($t['title']), ['/clinical-trial/view', 'id' => $t['id']], ['class' => 'font-semibold text-primary hover:underline break-words']) ?>
                                        <span class="text-on-surface-variant"><?= Html::encode($t['detail']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if ($a['count'] > count($a['trials'])): ?>
                                    <li class="text-xs text-on-surface-variant">and <?= $a['count'] - count($a['trials']) ?> more</li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </details>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <!-- ═════════════ Portfolio ═════════════ -->
    <div>
        <h2 class="<?= $h2 ?> mb-[0.75rem]">Portfolio</h2>
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-[1rem]">
            <?= $bars('reg', 'Registration status', 'Where each trial stands with registration.', $dash->distribution('registration')) ?>
            <?= $bars('rec', 'Recruitment status', 'From the Timeline step.', $dash->distribution('recruitment')) ?>
            <?= $bars('phase', 'Phase of study', 'From the Study purpose step, in phase order.', $dash->distribution('phase')) ?>
            <?= $bars('area', 'Area of study', 'Broad area of specialization; smaller areas grouped as Other.', $dash->distribution('area')) ?>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-[1rem]">
        <!-- Starts per year: column chart, one hue -->
        <section class="xl:col-span-3 <?= $card ?> p-[1.25rem] sm:p-6" aria-labelledby="years-title">
            <h3 id="years-title" class="<?= $h2 ?>">Trials by anticipated start year</h3>
            <p class="<?= $sub ?> mb-[1.125rem]">
                <?= $years ? 'Number of trials per start year.' : '' ?>
                <?php if ($n = $dash->startsNotRecorded()): ?><?= $n ?> trial<?= $n === 1 ? ' has' : 's have' ?> no start date recorded.<?php endif; ?>
            </p>
            <?php if (!$years): ?>
                <p class="text-sm text-on-surface-variant">No start dates recorded yet.</p>
            <?php else:
                $maxY = max($years) ?: 1; ?>
                <div class="flex items-end gap-1.5 h-40 border-b border-outline-variant/40" role="img"
                    aria-label="<?= Html::encode(implode(', ', array_map(fn($y, $n) => "$y: $n", array_keys($years), $years))) ?>">
                    <?php foreach ($years as $year => $n): ?>
                        <div class="flex-1 min-w-0 h-full flex flex-col justify-end items-center" title="<?= "{$year}: {$n} trial" . ($n === 1 ? '' : 's') ?>">
                            <?php if ($n): ?><span class="text-[11px] tabular-nums text-on-surface mb-0.5"><?= $n ?></span><?php endif; ?>
                            <div class="w-full max-w-[2.5rem] rounded-t bg-primary-container" style="height:<?= round($n * 100 / $maxY) ?>%"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="flex gap-1.5 mt-1">
                    <?php foreach (array_keys($years) as $year): ?>
                        <span class="flex-1 min-w-0 text-center text-[10px] text-on-surface-variant tabular-nums truncate"><?= $year ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Upcoming milestones -->
        <section class="xl:col-span-2 <?= $card ?> p-[1.25rem] sm:p-6" aria-labelledby="ms-title">
            <h3 id="ms-title" class="<?= $h2 ?>">Next <?= $dash->horizonDays ?> days</h3>
            <p class="<?= $sub ?> mb-[1.125rem]">Anticipated starts and ends of open trials.</p>
            <?php foreach (['starting' => ['play_circle', 'Starting'], 'ending' => ['flag_circle', 'Ending']] as $key => [$icon, $label]): ?>
                <p class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5 mt-[0.875rem] first:mt-0">
                    <span class="material-symbols-outlined text-[15px] align-[-3px]"><?= $icon ?></span> <?= $label ?></p>
                <?php if (!$milestones[$key]): ?>
                    <p class="text-xs text-on-surface-variant">None in this window.</p>
                <?php else: ?>
                    <ul class="space-y-1.5">
                        <?php foreach ($milestones[$key] as $m): ?>
                            <li class="flex items-baseline justify-between gap-[0.75rem] text-xs">
                                <?= Html::a(Html::encode($m['title']), ['/clinical-trial/view', 'id' => $m['id']], ['class' => 'min-w-0 truncate font-semibold text-primary hover:underline']) ?>
                                <span class="shrink-0 tabular-nums text-on-surface-variant"><?= $fmt->asDate($m['date']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endforeach; ?>
        </section>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-[1rem]">
        <!-- Enrolment progress -->
        <section class="<?= $card ?> p-[1.25rem] sm:p-6" aria-labelledby="enrol-title">
            <h3 id="enrol-title" class="<?= $h2 ?>">Enrolment against target</h3>
            <p class="<?= $sub ?> mb-[1.125rem]">Active trials with a target, slowest first.</p>
            <?php if (!$progress): ?>
                <p class="text-sm text-on-surface-variant">No active trials have recorded an enrolment target.</p>
            <?php else: ?>
                <ul class="space-y-[0.875rem]">
                    <?php foreach ($progress as $p): ?>
                        <li>
                            <div class="flex items-baseline justify-between gap-[0.75rem] mb-1">
                                <?= Html::a(Html::encode($p['title']), ['/clinical-trial/view', 'id' => $p['id']], ['class' => 'min-w-0 truncate text-xs font-semibold text-primary hover:underline']) ?>
                                <span class="shrink-0 text-xs tabular-nums text-on-surface"><b><?= number_format($p['enrolled']) ?></b> / <?= number_format($p['target']) ?> · <?= $p['percent'] ?>%</span>
                            </div>
                            <?= $meter($p['percent'], "{$p['title']} enrolment") ?>
                            <p class="mt-0.5 text-[11px] text-on-surface-variant"><?= Html::encode($p['status']) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <!-- Record completeness -->
        <section class="<?= $card ?> p-[1.25rem] sm:p-6" aria-labelledby="rec-title">
            <div class="flex flex-wrap items-end justify-between gap-2 mb-[1.125rem]">
                <div>
                    <h3 id="rec-title" class="<?= $h2 ?>"><?= $scopeIsMine ? 'Your trial records' : 'Record completeness' ?></h3>
                    <p class="<?= $sub ?>">
                        <?= $records['total'] ?> record<?= $records['total'] === 1 ? '' : 's' ?> · <?= $records['complete'] ?> complete ·
                        average <?= $records['average'] ?>% of the <?= count($dash::SECTIONS) ?> sections after trial details
                    </p>
                </div>
            </div>
            <?php if (!$records['trials']): ?>
                <p class="text-sm text-on-surface-variant"><?= $records['total'] ? 'Every record is complete.' : 'No trial records yet.' ?></p>
            <?php else: ?>
                <ul class="space-y-[0.875rem]">
                    <?php foreach ($records['trials'] as $r):
                        $canEdit = $user->can(Rbac::PERM_UPDATE_TRIAL, ['trial' => \frontend\models\ClinicalTrial::findOne($r['id'])]); ?>
                        <li>
                            <div class="flex items-baseline justify-between gap-[0.75rem] mb-1">
                                <?= Html::a(Html::encode($r['title']), ['/clinical-trial/view', 'id' => $r['id']], ['class' => 'min-w-0 truncate text-xs font-semibold text-primary hover:underline']) ?>
                                <span class="shrink-0 text-xs tabular-nums text-on-surface"><b><?= $r['done'] ?></b> of <?= $r['of'] ?></span>
                            </div>
                            <?= $meter($r['percent'], "{$r['title']} completeness") ?>
                            <div class="mt-1 flex flex-wrap items-start justify-between gap-x-[0.75rem]">
                                <p class="min-w-0 text-[11px] text-on-surface-variant">Missing: <?= Html::encode(implode(', ', array_slice($r['missing'], 0, 4)) . (count($r['missing']) > 4 ? ' +' . (count($r['missing']) - 4) . ' more' : '')) ?></p>
                                <?php if ($canEdit): ?>
                                    <?= Html::a('Continue <span class="material-symbols-outlined text-[14px] align-[-3px]">arrow_forward</span>', ['/clinical-trial/update', 'id' => $r['id']], ['class' => 'shrink-0 text-[11px] font-bold text-primary hover:underline']) ?>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>

    <!-- ═════════════ Recent trials ═════════════ -->
    <section aria-labelledby="recent-title">
        <div class="flex items-end justify-between gap-[0.75rem] mb-[0.75rem]">
            <div>
                <h2 id="recent-title" class="<?= $h2 ?>">Recently registered</h2>
                <p class="<?= $sub ?>">The five newest trial records.</p>
            </div>
            <?= Html::a('All trials <span class="material-symbols-outlined text-[16px] align-[-3px]">arrow_forward</span>', ['/clinical-trial/index'],
                ['class' => 'text-sm font-bold text-primary hover:underline whitespace-nowrap']) ?>
        </div>
        <?= $this->render('_recent_trials', ['recentTrials' => $dash->recentTrials()]) ?>
    </section>
</div>
