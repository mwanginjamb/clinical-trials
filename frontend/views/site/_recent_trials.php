<!-- Recent Trials Table Loop -->
<div
    class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-[0_24px_48px_-12px_rgba(0,59,83,0.06)] border border-outline-variant/10 overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[800px] lg:min-w-full">
        <thead class="bg-surface-container-low/50">
            <tr>
                <th
                    class="px-6 md:px-8 py-4 md:py-5 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                    Protocol Title &amp; ID
                </th>
                <th
                    class="px-4 py-4 md:py-5 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-on-surface-variant text-center">
                    Status
                </th>
                <th
                    class="px-4 py-4 md:py-5 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-on-surface-variant text-center">
                    Phase
                </th>
                <th
                    class="px-4 py-4 md:py-5 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                    Private Investigator
                </th>
                <th
                    class="px-6 md:px-8 py-4 md:py-5 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-on-surface-variant text-right">
                    Start Date
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/5">
            <?php foreach ($recentTrials as $trial):
                $pi = $trial->getPrimaryInvestigator();
                ?>
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <!-- Protocol Title & ID -->
                    <td class="px-6 md:px-8 py-5 md:py-6">
                        <div class="flex flex-col">
                            <span
                                class="font-headline font-bold text-sm md:text-base text-on-surface group-hover:text-primary transition-colors">
                                <?= htmlspecialchars($trial->getDisplayTitle()) ?>
                            </span>
                            <span class="text-[10px] md:text-xs text-on-surface-variant/70 font-mono">
                                ID: <?= htmlspecialchars($trial->getProtocolIdentifier()) ?>
                            </span>
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="px-4 py-5 md:py-6 text-center">
                        <?= $trial->getStatusBadge() ?>
                    </td>

                    <!-- Phase -->
                    <td class="px-4 py-5 md:py-6 text-center">
                        <span class="font-headline font-extrabold text-primary/40 text-sm md:text-base">
                            <?= $trial->getPhaseDisplay() ?>
                        </span>
                    </td>

                    <!-- Primary Investigator -->
                    <td class="px-4 py-5 md:py-6">
                        <div class="flex items-center gap-2 md:gap-3">
                            <div
                                class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-slate-200 overflow-hidden flex items-center justify-center text-slate-400">
                                <span class="material-symbols-outlined text-sm">person</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs md:text-sm font-medium text-on-surface">
                                    <?= htmlspecialchars($pi ? $pi->name : 'Not Assigned') ?>
                                </span>
                                <?php if ($pi && $pi->institution): ?>
                                    <span class="text-[10px] text-on-surface-variant/60">
                                        <?= htmlspecialchars($pi->institution) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    <!-- Start Date -->
                    <td class="px-6 md:px-8 py-5 md:py-6 text-right">
                        <span class="text-xs md:text-sm font-medium text-on-surface-variant">
                            <?= $trial->getFormattedStartDate() ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($recentTrials)): ?>
                <tr>
                    <td colspan="5" class="px-6 md:px-8 py-12 text-center text-on-surface-variant">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl">inbox</span>
                            <p class="text-sm">No recent trials found</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>