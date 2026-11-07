<?php

/**
 * Read-only card for a lookup record: its fields, audit trail and edit/delete buttons.
 *
 * @var yii\web\View $this
 * @var yii\db\ActiveRecord $model  must have created_at/updated_at/created_by/updated_by
 * @var array $fields   label => value; values are plain text unless listed in $rawFields
 * @var string[] $rawFields labels whose value is already-safe HTML
 * @var string $noun    e.g. 'country'
 * @var string $name    record's display name (for the delete prompt)
 * @var string|null $confirm custom delete prompt (e.g. to warn about cascades)
 */

use common\models\User;
use yii\helpers\Html;

$rawFields = $rawFields ?? [];
$fmt = Yii::$app->formatter;

$when = static function ($ts) use ($fmt): ?string {
    if ($ts === null || $ts === '' || !is_numeric($ts)) {
        return null;
    }
    return $fmt->asDatetime((int) $ts, 'medium');
};
$who = static function ($userId): ?string {
    if (!$userId) {
        return null;
    }
    return User::find()->select('username')->where(['id' => $userId])->scalar() ?: '#' . $userId;
};

$value = static function ($v, bool $raw = false): string {
    if ($v === null || $v === '' || (is_string($v) && trim($v) === '')) {
        return '<span class="text-on-surface-variant/60">—</span>';
    }
    return $raw ? (string) $v : nl2br(Html::encode($v));
};

$audit = [
    'Created' => trim(($when($model->created_at) ?? '') . ($who($model->created_by) ? ' by ' . $who($model->created_by) : '')),
    'Last updated' => trim(($when($model->updated_at) ?? '') . ($who($model->updated_by) ? ' by ' . $who($model->updated_by) : '')),
];
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-6xl">
    <section class="lg:col-span-2 min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-8">
        <h2 class="text-sm font-bold uppercase tracking-widest text-primary mb-6">Details</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
            <?php foreach ($fields as $label => $v): ?>
                <div class="min-w-0 <?= is_string($v) && mb_strlen($v) > 80 ? 'sm:col-span-2' : '' ?>">
                    <dt class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5"><?= Html::encode($label) ?></dt>
                    <dd class="text-sm font-medium text-on-surface leading-relaxed break-words"><?= $value($v, in_array($label, $rawFields, true)) ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
    </section>

    <aside class="min-w-0 space-y-6">
        <section class="bg-surface-container-low rounded-2xl border border-outline-variant/15 p-[1.25rem] sm:p-6">
            <h2 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-[1rem]">Record history</h2>
            <dl class="space-y-[1rem]">
                <?php foreach ($audit as $label => $v): ?>
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1"><?= $label ?></dt>
                        <dd class="text-sm text-on-surface"><?= $value($v) ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </section>

        <div class="flex flex-col gap-2">
            <?= Html::a(
                '<span class="material-symbols-outlined text-[18px]">edit_square</span>Edit ' . Html::encode($noun),
                ['update', 'id' => $model->getPrimaryKey()],
                ['class' => 'inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:opacity-90']
            ) ?>
            <?= Html::a(
                '<span class="material-symbols-outlined text-[18px]">delete</span>Delete',
                ['delete', 'id' => $model->getPrimaryKey()],
                [
                    'class' => 'inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-error hover:bg-error-container/40 transition-colors',
                    'data' => [
                        'confirm' => $confirm ?? "Delete {$noun} \"{$name}\"? This cannot be undone.",
                        'method' => 'post',
                    ],
                ]
            ) ?>
            <?= Html::a(
                '<span class="material-symbols-outlined text-[18px]">arrow_back</span>Back to list',
                ['index'],
                ['class' => 'inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-on-surface-variant hover:bg-surface-container']
            ) ?>
        </div>
    </aside>
</div>
