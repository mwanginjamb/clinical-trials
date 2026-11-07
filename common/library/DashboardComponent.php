<?php

namespace common\library;

use frontend\models\AreaOfSpecialization;
use frontend\models\ClinicalTrial;
use frontend\models\StudyPurpose;
use frontend\models\StudyTimeline;
use Yii;
use yii\base\Component;
use yii\db\Expression;
use yii\db\Query;

/**
 * Registry metrics for the dashboard (Yii::$app->dashboard).
 *
 * Everything is derived from ONE per-trial "facts" query (facts()), so every figure on the
 * dashboard uses the same definitions. Each metric carries a plain-language `definition`
 * that the view shows to readers (scientists, ethics review board) next to the number.
 *
 * Conventions
 *  - A trial's one-to-one sections (timeline, purpose, eligibility, ethics, open data) are read
 *    from their most recent row per trial.
 *  - Recruitment codes come from StudyTimeline::getRecruitmentStatus():
 *      0 Not yet recruiting, 1 Recruiting, 2 Enrolling by invitation, 3 Active, not recruiting,
 *      4 Completed, 5 Suspended, 6 Terminated, 7 Withdrawn, 8 Unknown status.
 *  - "Not recorded" means the wizard step holding that field has not been filled in.
 */
class DashboardComponent extends Component
{
    /** Look-ahead window for upcoming starts/ends, in days. */
    public int $horizonDays = 90;

    /** Recruitment codes that mean participants are being enrolled now. */
    public const RECRUITING = [1, 2];
    /** Recruiting, or active but no longer recruiting (study under way). */
    public const ACTIVE = [1, 2, 3];
    /** The study has ended one way or another. */
    public const CLOSED = [4, 5, 6, 7];
    public const COMPLETED = 4;

    public const REG_PENDING = 2;
    public const REG_REJECTED = 3;

    /** Wizard sections (child tables) used for record completeness, in wizard order. */
    public const SECTIONS = [
        'purpose' => ['study_purpose', 'Study purpose', 'study-purpose'],
        'eligibility' => ['study_population_eligibility', 'Population & eligibility', 'study-population-eligibility'],
        'timeline' => ['study_timeline', 'Timeline & location', 'study-timeline'],
        'investigators' => ['investigator_team', 'Investigator team', 'investigator-team'],
        'ethics' => ['ethical_approval', 'Ethics approval', 'ethical-approval'],
        'funding' => ['funding', 'Funding', 'funding'],
        'description' => ['study_description', 'Study description', 'study-description'],
        'intervention' => ['study_intervention', 'Interventions & outcomes', 'study-intervention'],
        'results' => ['study_results', 'Results & publication', 'study-results'],
        'opendata' => ['opendata_access', 'Open data access', 'opendata-access'],
    ];

    private ?array $facts = null;

    /* ═══════════════════════════════ Source data ═══════════════════════════════ */

    /**
     * One row per trial with everything the dashboard needs. Memoised per request.
     *
     * @return array<int, array> keyed by trial id
     */
    public function facts(): array
    {
        if ($this->facts !== null) {
            return $this->facts;
        }

        // Latest row per trial for the one-to-one tables.
        $latest = fn(string $table, string $alias) =>
            "{$alias}.id = (SELECT MAX(x.id) FROM {{%{$table}}} x WHERE x.trial_id = t.id)";

        $select = [
            't.id', 't.scientific_title', 't.public_title', 't.scientific_acronym', 't.protocol_number',
            't.registration_status', 't.area_of_specialization', 't.created_at', 't.updated_at', 't.created_by',
            'recruitment_status' => 'tl.recruitment_status',
            'start_date' => 'tl.anticipated_start_date',
            'end_date' => 'tl.anticipated_end_date',
            'phase' => 'sp.phase_of_study',
            'target' => 'el.participant_target_number',
            'enrolled' => 'el.final_number_of_participants',
            'ethics_approved' => 'ea.approved_by_ethical_committee',
            'data_sharing' => 'od.allow_publishing',
            'results_summary' => 'sr.summary_results',
            'results_publication' => 'sr.publication_title',
        ];
        foreach (self::SECTIONS as $key => [$table]) {
            $select["has_{$key}"] = new Expression("EXISTS(SELECT 1 FROM {{%{$table}}} s WHERE s.trial_id = t.id)");
        }

        $rows = (new Query())
            ->select($select)
            ->from(['t' => '{{%clinical_trial}}'])
            ->leftJoin(['tl' => '{{%study_timeline}}'], $latest('study_timeline', 'tl'))
            ->leftJoin(['sp' => '{{%study_purpose}}'], $latest('study_purpose', 'sp'))
            ->leftJoin(['el' => '{{%study_population_eligibility}}'], $latest('study_population_eligibility', 'el'))
            ->leftJoin(['ea' => '{{%ethical_approval}}'], $latest('ethical_approval', 'ea'))
            ->leftJoin(['od' => '{{%opendata_access}}'], $latest('opendata_access', 'od'))
            ->leftJoin(['sr' => '{{%study_results}}'], $latest('study_results', 'sr'))
            ->orderBy(['t.id' => SORT_DESC])
            ->all();

        $facts = [];
        foreach ($rows as $r) {
            $r['title'] = $this->titleOf($r);
            $r['sections_done'] = 0;
            foreach (array_keys(self::SECTIONS) as $key) {
                $r["has_{$key}"] = (bool) $r["has_{$key}"];
                $r['sections_done'] += (int) $r["has_{$key}"];
            }
            foreach (['registration_status', 'recruitment_status', 'phase', 'target', 'enrolled', 'ethics_approved', 'data_sharing', 'area_of_specialization', 'created_by'] as $int) {
                $r[$int] = $r[$int] === null || $r[$int] === '' ? null : (int) $r[$int];
            }
            $facts[(int) $r['id']] = $r;
        }

        return $this->facts = $facts;
    }

    /** Back-compat: total number of trials. */
    public function getAllTrials(): int
    {
        return count($this->facts());
    }

    /* ═══════════════════════════════ Headline figures ═══════════════════════════════ */

    /**
     * KPI tiles. Each: key, label, value (raw), display, context, definition, icon,
     * and for ratios: numerator / denominator / percent.
     */
    public function kpis(): array
    {
        $facts = $this->facts();
        $total = count($facts);
        $since = time() - 30 * 86400;
        $new30 = count(array_filter($facts, fn($t) => (int) $t['created_at'] >= $since));

        $recruiting = count(array_filter($facts, fn($t) => in_array($t['recruitment_status'], self::RECRUITING, true)));

        $withTargets = array_filter($facts, fn($t) => $t['target'] !== null && $t['target'] > 0);
        $enrolled = array_sum(array_map(fn($t) => (int) $t['enrolled'], $withTargets));
        $target = array_sum(array_column($withTargets, 'target'));

        $ethicsApproved = count(array_filter($facts, fn($t) => $t['ethics_approved'] === 1));

        $withOpenData = array_filter($facts, fn($t) => $t['has_opendata']);
        $sharing = count(array_filter($withOpenData, fn($t) => $t['data_sharing'] === 1));

        return [
            [
                'key' => 'trials', 'icon' => 'clinical_notes', 'label' => 'Trials in the registry',
                'value' => $total, 'display' => number_format($total),
                'context' => $new30 ? "+{$new30} added in the last 30 days" : 'None added in the last 30 days',
                'definition' => 'Every trial record in the repository, whatever its status.',
            ],
            [
                'key' => 'recruiting', 'icon' => 'person_search', 'label' => 'Actively recruiting',
                'value' => $recruiting, 'display' => number_format($recruiting),
                'context' => $total ? $this->pct($recruiting, $total) . '% of trials' : 'No trials yet',
                'definition' => 'Recruitment status is "Recruiting" or "Enrolling by invitation".',
            ],
            $this->ratio('enrolment', 'groups', 'Participants enrolled', $enrolled, $target,
                count($withTargets) ? number_format($enrolled) . ' of ' . number_format($target) . ' target, across ' . count($withTargets) . ' trial' . (count($withTargets) === 1 ? '' : 's') : 'No enrolment targets recorded',
                'Sum of final number of participants over sum of target numbers, for trials that recorded a target.'),
            $this->ratio('ethics', 'verified', 'Ethics approval on record', $ethicsApproved, $total,
                $total ? "{$ethicsApproved} of {$total} trials" : 'No trials yet',
                'Trials whose ethics section records approval by an ethics committee.'),
            $this->ratio('sharing', 'lock_open', 'Open data sharing', $sharing, count($withOpenData),
                count($withOpenData) ? "{$sharing} of " . count($withOpenData) . ' trials with an open-data section' : 'No open-data sections recorded',
                'Trials that allow publishing their data, out of trials that completed the open-data section.'),
        ];
    }

    /* ═══════════════════════════════ Oversight ═══════════════════════════════ */

    /**
     * Items an ethics review board or administrator should follow up, most severe first.
     * Each: key, severity (critical|serious|warning|info), title, why, count, trials[] (id, title, detail).
     */
    public function attention(int $examples = 5): array
    {
        $facts = $this->facts();
        $today = date('Y-m-d');
        $recruit = $this->options('recruitment');
        $reg = ClinicalTrial::getRegistrationStatusOptions();

        $checks = [
            [
                'key' => 'recruiting-no-ethics', 'severity' => 'critical',
                'title' => 'Enrolling without recorded ethics approval',
                'why' => 'Recruiting or active, but the ethics section does not record committee approval.',
                'match' => fn($t) => in_array($t['recruitment_status'], self::ACTIVE, true) && $t['ethics_approved'] !== 1,
                'detail' => fn($t) => ($recruit[$t['recruitment_status']] ?? '') . ' · ethics: ' . ($t['has_ethics'] ? 'not approved' : 'section missing'),
            ],
            [
                'key' => 'overdue', 'severity' => 'serious',
                'title' => 'Past anticipated end date, still open',
                'why' => 'The anticipated end date has passed but the recruitment status is not Completed, Suspended, Terminated or Withdrawn.',
                'match' => fn($t) => $t['end_date'] && $t['end_date'] < $today && !in_array($t['recruitment_status'], self::CLOSED, true),
                'detail' => fn($t) => 'Ended ' . Yii::$app->formatter->asDate($t['end_date']) . ' · ' . ($recruit[$t['recruitment_status']] ?? 'status not recorded'),
            ],
            [
                'key' => 'registration', 'severity' => 'warning',
                'title' => 'Registration pending or rejected',
                'why' => 'The trial registration has not been accepted yet.',
                'match' => fn($t) => in_array($t['registration_status'], [self::REG_PENDING, self::REG_REJECTED], true),
                'detail' => fn($t) => $reg[$t['registration_status']] ?? '',
            ],
            [
                'key' => 'no-results', 'severity' => 'warning',
                'title' => 'Completed without results',
                'why' => 'Recruitment is Completed but no results summary or publication has been recorded.',
                'match' => fn($t) => $t['recruitment_status'] === self::COMPLETED && !$this->filled($t['results_summary']) && !$this->filled($t['results_publication']),
                'detail' => fn($t) => $t['end_date'] ? 'Ended ' . Yii::$app->formatter->asDate($t['end_date']) : 'End date not recorded',
            ],
            [
                'key' => 'over-target', 'severity' => 'info',
                'title' => 'Enrolment above target',
                'why' => 'More participants recorded than the target: check the numbers or the approved sample size.',
                'match' => fn($t) => $t['target'] && $t['enrolled'] !== null && $t['enrolled'] > $t['target'],
                'detail' => fn($t) => number_format($t['enrolled']) . ' enrolled vs target ' . number_format($t['target']),
            ],
        ];

        $items = [];
        foreach ($checks as $check) {
            $matched = array_filter($facts, $check['match']);
            $items[] = [
                'key' => $check['key'],
                'severity' => $check['severity'],
                'title' => $check['title'],
                'why' => $check['why'],
                'count' => count($matched),
                'trials' => array_map(
                    fn($t) => ['id' => (int) $t['id'], 'title' => $t['title'], 'detail' => ($check['detail'])($t)],
                    array_slice(array_values($matched), 0, $examples)
                ),
            ];
        }
        return $items;
    }

    /* ═══════════════════════════════ Portfolio ═══════════════════════════════ */

    /**
     * Counts per category for one dimension, largest first (phase keeps its natural order).
     * "Not recorded" is always last. Rows: label, count, share (0-100).
     *
     * @param string $dimension registration | recruitment | phase | area
     */
    public function distribution(string $dimension, int $maxRows = 8): array
    {
        $field = ['registration' => 'registration_status', 'recruitment' => 'recruitment_status', 'phase' => 'phase', 'area' => 'area_of_specialization'][$dimension];
        $labels = $this->options($dimension);
        $facts = $this->facts();
        $total = count($facts);

        $counts = [];
        $missing = 0;
        foreach ($facts as $t) {
            $code = $t[$field];
            if ($code === null) {
                $missing++;
                continue;
            }
            $label = $labels[$code] ?? "Code {$code}";
            $counts[$label] = ($counts[$label] ?? 0) + 1;
        }

        if ($dimension === 'phase') {
            $ordered = [];
            foreach ($labels as $label) {
                if (isset($counts[$label])) {
                    $ordered[$label] = $counts[$label];
                }
            }
            $counts = $ordered + $counts;
        } else {
            arsort($counts);
        }

        // Fold a long tail into "Other" rather than inventing more rows.
        if (count($counts) > $maxRows) {
            $head = array_slice($counts, 0, $maxRows - 1, true);
            $head['Other'] = array_sum(array_slice($counts, $maxRows - 1));
            $counts = $head;
        }

        $rows = [];
        foreach ($counts as $label => $n) {
            $rows[] = ['label' => (string) $label, 'count' => $n, 'share' => $this->pct($n, $total), 'missing' => false];
        }
        if ($missing) {
            $rows[] = ['label' => 'Not recorded', 'count' => $missing, 'share' => $this->pct($missing, $total), 'missing' => true];
        }
        return $rows;
    }

    /** Trials by anticipated start year, continuous range (gaps shown as 0). [year => count] */
    public function startsByYear(int $maxYears = 12): array
    {
        $years = [];
        foreach ($this->facts() as $t) {
            if ($t['start_date']) {
                $y = (int) substr($t['start_date'], 0, 4);
                $years[$y] = ($years[$y] ?? 0) + 1;
            }
        }
        if (!$years) {
            return [];
        }
        $last = max(array_keys($years));
        $first = max(min(array_keys($years)), $last - $maxYears + 1);
        $series = [];
        for ($y = $first; $y <= $last; $y++) {
            $series[$y] = $years[$y] ?? 0;
        }
        return $series;
    }

    /** How many trials have no start date (shown under the timeline chart). */
    public function startsNotRecorded(): int
    {
        return count(array_filter($this->facts(), fn($t) => !$t['start_date']));
    }

    /**
     * Enrolment against target for active trials, least progress first (where help is needed).
     * Rows: id, title, enrolled, target, percent, status.
     */
    public function recruitmentProgress(int $limit = 8): array
    {
        $recruit = $this->options('recruitment');
        $rows = [];
        foreach ($this->facts() as $t) {
            if (!in_array($t['recruitment_status'], self::ACTIVE, true) || !$t['target']) {
                continue;
            }
            $enrolled = (int) $t['enrolled'];
            $rows[] = [
                'id' => (int) $t['id'], 'title' => $t['title'],
                'enrolled' => $enrolled, 'target' => $t['target'],
                'percent' => $this->pct($enrolled, $t['target']),
                'status' => $recruit[$t['recruitment_status']] ?? '',
            ];
        }
        usort($rows, fn($a, $b) => $a['percent'] <=> $b['percent']);
        return array_slice($rows, 0, $limit);
    }

    /** Anticipated starts and ends within the look-ahead window. ['starting' => [...], 'ending' => [...]] */
    public function milestones(int $limit = 6): array
    {
        $today = date('Y-m-d');
        $until = date('Y-m-d', strtotime("+{$this->horizonDays} days"));
        $out = ['starting' => [], 'ending' => []];
        foreach ($this->facts() as $t) {
            if ($t['start_date'] && $t['start_date'] >= $today && $t['start_date'] <= $until) {
                $out['starting'][] = ['id' => (int) $t['id'], 'title' => $t['title'], 'date' => $t['start_date']];
            }
            if ($t['end_date'] && $t['end_date'] >= $today && $t['end_date'] <= $until && !in_array($t['recruitment_status'], self::CLOSED, true)) {
                $out['ending'][] = ['id' => (int) $t['id'], 'title' => $t['title'], 'date' => $t['end_date']];
            }
        }
        foreach ($out as &$list) {
            usort($list, fn($a, $b) => strcmp($a['date'], $b['date']));
            $list = array_slice($list, 0, $limit);
        }
        return $out;
    }

    /* ═══════════════════════════════ Record completeness ═══════════════════════════════ */

    /**
     * Completeness of trial records (10 wizard sections after the trial details).
     *
     * @param int|null $userId limit to trials created by this user; null = all trials
     * @return array{total:int, complete:int, average:int, trials: array<int, array>}  trials least complete first
     */
    public function completeness(?int $userId = null, int $limit = 6): array
    {
        $sectionCount = count(self::SECTIONS);
        $facts = array_filter($this->facts(), fn($t) => $userId === null || $t['created_by'] === $userId);

        $trials = [];
        foreach ($facts as $t) {
            $missing = [];
            $firstMissingRoute = null;
            foreach (self::SECTIONS as $key => [, $label, $controller]) {
                if (!$t["has_{$key}"]) {
                    $missing[] = $label;
                    $firstMissingRoute ??= $controller;
                }
            }
            $trials[] = [
                'id' => (int) $t['id'], 'title' => $t['title'],
                'done' => $t['sections_done'], 'of' => $sectionCount,
                'percent' => $this->pct($t['sections_done'], $sectionCount),
                'missing' => $missing,
                'updated_at' => $t['updated_at'],
            ];
        }
        usort($trials, fn($a, $b) => [$a['done'], -$a['id']] <=> [$b['done'], -$b['id']]);

        return [
            'total' => count($trials),
            'complete' => count(array_filter($trials, fn($t) => $t['done'] === $sectionCount)),
            'average' => $trials ? (int) round(array_sum(array_column($trials, 'percent')) / count($trials)) : 0,
            'trials' => array_slice(array_values(array_filter($trials, fn($t) => $t['done'] < $sectionCount)), 0, $limit),
        ];
    }

    /** Most recently registered trials, with the relations the table shows. */
    public function recentTrials(int $limit = 5): array
    {
        return ClinicalTrial::find()
            ->with(['investigators', 'timeline', 'purpose'])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /* ═══════════════════════════════ Helpers ═══════════════════════════════ */

    /** Code => label for a dimension, from the same lists the wizard uses. */
    public function options(string $dimension): array
    {
        static $cache = [];
        return $cache[$dimension] ??= match ($dimension) {
            'registration' => ClinicalTrial::getRegistrationStatusOptions(),
            'recruitment' => (new StudyTimeline())->getRecruitmentStatus(),
            'phase' => (new StudyPurpose())->getPhaseOfStudy(),
            'area' => \yii\helpers\ArrayHelper::map(AreaOfSpecialization::find()->asArray()->all(), 'id', 'title'),
        };
    }

    private function ratio(string $key, string $icon, string $label, int $num, int $den, string $context, string $definition): array
    {
        $percent = $den > 0 ? $this->pct($num, $den) : null;
        return [
            'key' => $key, 'icon' => $icon, 'label' => $label,
            'value' => $percent, 'display' => $percent === null ? '—' : $percent . '%',
            'numerator' => $num, 'denominator' => $den, 'percent' => $percent,
            'context' => $context, 'definition' => $definition,
        ];
    }

    private function pct(int|float $part, int|float $whole): int
    {
        return $whole > 0 ? (int) round($part * 100 / $whole) : 0;
    }

    private function filled($v): bool
    {
        return $v !== null && trim((string) $v) !== '';
    }

    private function titleOf(array $r): string
    {
        foreach (['scientific_title', 'public_title', 'scientific_acronym'] as $f) {
            if ($this->filled($r[$f] ?? null) && $r[$f] !== 'N/A') {
                return trim($r[$f]);
            }
        }
        return 'Untitled trial #' . $r['id'];
    }
}
