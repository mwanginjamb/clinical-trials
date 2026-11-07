<?php

/**
 * Responsive grid of checkboxes with a description under each label.
 *
 * Always submits the field: an empty hidden value is sent first, so unticking every box
 * posts "" (→ []) instead of omitting the field and keeping the old values.
 *
 * @var yii\web\View $this
 * @var string $name      input name WITHOUT [], e.g. "RoleForm[permissions]"
 * @var array  $groups    ['Group title' => ['value' => 'description', ...], ...]
 * @var array  $selected  values currently ticked
 * @var array  $locked    values that must stay ticked (rendered disabled, still submitted)
 * @var string $emptyText
 */

use yii\helpers\Html;

$locked = $locked ?? [];
$emptyText = $emptyText ?? 'Nothing to choose from yet.';
?>
<?= Html::hiddenInput($name, '') ?>
<?php $any = false;
foreach ($groups as $groupTitle => $items):
    if (!$items) {
        continue;
    }
    $any = true; ?>
    <?php if (count($groups) > 1): ?>
        <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mt-6 mb-2 first:mt-0"><?= Html::encode($groupTitle) ?></p>
    <?php endif; ?>
    <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-2">
        <?php foreach ($items as $value => $description):
            $isLocked = in_array($value, $locked, true);
            $checked = $isLocked || in_array($value, (array) $selected, true);
            $id = 'cb-' . md5($name . $value); ?>
            <label for="<?= $id ?>"
                class="min-w-0 flex items-start gap-2.5 px-[0.875rem] py-2.5 rounded-xl border transition-colors
                    <?= $checked ? 'border-primary/40 bg-primary-fixed/30' : 'border-outline-variant/20 bg-surface-container-lowest hover:bg-surface-container-low' ?>
                    <?= $isLocked ? 'cursor-not-allowed opacity-80' : 'cursor-pointer' ?>">
                <input type="checkbox" id="<?= $id ?>" value="<?= Html::encode($value) ?>"
                    <?= $isLocked ? 'disabled' : 'name="' . Html::encode($name) . '[]"' ?>
                    <?= $checked ? 'checked' : '' ?>
                    class="mt-0.5 w-4 h-4 rounded border-outline text-primary focus:ring-primary-container shrink-0">
                <?php if ($isLocked): ?>
                    <?= Html::hiddenInput($name . '[]', $value) ?>
                <?php endif; ?>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-on-surface break-words">
                        <?= Html::encode($value) ?>
                        <?php if ($isLocked): ?><span class="material-symbols-outlined text-[14px] align-middle text-on-surface-variant" title="Required">lock</span><?php endif; ?>
                    </span>
                    <?php if ($description): ?>
                        <span class="block text-xs text-on-surface-variant leading-snug break-words"><?= Html::encode($description) ?></span>
                    <?php endif; ?>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
<?php if (!$any): ?>
    <p class="text-sm text-on-surface-variant"><?= Html::encode($emptyText) ?></p>
<?php endif; ?>
