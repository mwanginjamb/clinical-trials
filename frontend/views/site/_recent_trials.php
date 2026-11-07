<?php

/**
 * Most recently registered trials: a table from md up, stacked cards on phones.
 * Spacing uses values Bootstrap's !important utilities don't override.
 *
 * @var yii\web\View $this
 * @var frontend\models\ClinicalTrial[] $recentTrials
 */

use yii\helpers\Html;

$th = 'px-[1.125rem] py-[0.75rem] text-[10px] font-bold uppercase tracking-widest text-on-surface-variant';
?>
<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden">
    <?php if (empty($recentTrials)): ?>
        <div class="px-6 py-10 text-center text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl">inbox</span>
            <p class="text-sm mt-1">No trials registered yet.</p>
        </div>
    <?php else: ?>
        <table class="w-full text-left hidden md:table">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="<?= $th ?>">Trial &amp; protocol no.</th>
                    <th class="<?= $th ?>">Registration</th>
                    <th class="<?= $th ?>">Phase</th>
                    <th class="<?= $th ?>">Principal investigator</th>
                    <th class="<?= $th ?> text-right">Anticipated start</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php foreach ($recentTrials as $trial):
                    $pi = $trial->getPrimaryInvestigator(); ?>
                    <tr class="hover:bg-surface-container-low/60 transition-colors">
                        <td class="px-[1.125rem] py-[0.875rem] max-w-md">
                            <?= Html::a(Html::encode($trial->getDisplayTitle()), ['/clinical-trial/view', 'id' => $trial->id],
                                ['class' => 'block text-sm font-semibold text-on-surface hover:text-primary break-words']) ?>
                            <span class="text-[11px] text-on-surface-variant font-mono"><?= Html::encode($trial->protocol_number ?: '—') ?></span>
                        </td>
                        <td class="px-[1.125rem] py-[0.875rem]"><?= $trial->getStatusBadge() ?></td>
                        <td class="px-[1.125rem] py-[0.875rem] text-sm text-on-surface whitespace-nowrap"><?= Html::encode($trial->getPhaseDisplay()) ?></td>
                        <td class="px-[1.125rem] py-[0.875rem] text-sm">
                            <?php if ($pi): ?>
                                <span class="block text-on-surface"><?= Html::encode($pi->name) ?></span>
                                <?php if ($pi->institution): ?><span class="block text-[11px] text-on-surface-variant"><?= Html::encode($pi->institution) ?></span><?php endif; ?>
                            <?php else: ?>
                                <span class="text-on-surface-variant italic">Not recorded</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-[1.125rem] py-[0.875rem] text-sm text-on-surface-variant text-right whitespace-nowrap tabular-nums"><?= Html::encode($trial->getFormattedStartDate()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <ul class="md:hidden divide-y divide-outline-variant/10">
            <?php foreach ($recentTrials as $trial):
                $pi = $trial->getPrimaryInvestigator(); ?>
                <li class="px-[1.125rem] py-[0.875rem]">
                    <div class="flex items-start justify-between gap-[0.75rem]">
                        <?= Html::a(Html::encode($trial->getDisplayTitle()), ['/clinical-trial/view', 'id' => $trial->id],
                            ['class' => 'min-w-0 text-sm font-semibold text-on-surface hover:text-primary break-words']) ?>
                        <span class="shrink-0"><?= $trial->getStatusBadge() ?></span>
                    </div>
                    <p class="mt-1 text-[11px] text-on-surface-variant">
                        <?= Html::encode(implode(' · ', array_filter([
                            $trial->protocol_number,
                            $trial->getPhaseDisplay() !== '—' ? $trial->getPhaseDisplay() : null,
                            $pi->name ?? null,
                            'Starts ' . $trial->getFormattedStartDate(),
                        ]))) ?>
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
