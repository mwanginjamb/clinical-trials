<?php

/**
 * Page header + RBAC section tabs shared by every RBAC screen.
 *
 * @var yii\web\View $this
 * @var string $title
 * @var string|null $subtitle
 * @var string|null $actions  pre-rendered HTML (buttons) for the right side
 */

use yii\helpers\Html;

$tabs = [
    ['label' => 'User Roles', 'icon' => 'manage_accounts', 'route' => 'rbac/user-roles', 'match' => ['user-roles', 'assign-role']],
    ['label' => 'Roles', 'icon' => 'security_update_good', 'route' => 'rbac/index', 'match' => ['index', 'create-role', 'update-role']],
    ['label' => 'Permissions', 'icon' => 'security', 'route' => 'rbac/permissions', 'match' => ['permissions', 'create-permission', 'update-permission']],
    ['label' => 'System Users', 'icon' => 'group', 'route' => 'rbac/users', 'match' => ['users']],
];
$action = Yii::$app->controller->action->id;
?>
<div class="mb-6 sm:mb-8 min-w-0">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-[1rem] mb-6">
        <div class="min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Access control</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-headline text-on-surface tracking-tight break-words"><?= Html::encode($title) ?></h1>
            <?php if (!empty($subtitle)): ?>
                <p class="mt-1 text-sm text-on-surface-variant max-w-3xl"><?= Html::encode($subtitle) ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($actions)): ?>
            <div class="flex flex-wrap gap-2 shrink-0"><?= $actions ?></div>
        <?php endif; ?>
    </div>

    <nav aria-label="RBAC sections" class="flex gap-1 p-1.5 overflow-x-auto no-scrollbar rounded-xl bg-surface-container-low border border-outline-variant/15">
        <?php foreach ($tabs as $tab):
            $active = in_array($action, $tab['match'], true)
                || ($action === 'manage-hierarchy' && $tab['route'] === 'rbac/index'); ?>
            <?= Html::a(
                '<span class="material-symbols-outlined text-[18px]">' . $tab['icon'] . '</span>' . Html::encode($tab['label']),
                ['/' . $tab['route']],
                ['class' => 'shrink-0 whitespace-nowrap inline-flex items-center gap-2 px-[0.875rem] py-2 rounded-lg text-xs transition-colors '
                    . ($active ? 'bg-primary-fixed text-on-primary-fixed font-semibold' : 'text-on-surface-variant font-medium hover:bg-surface-container')]
            ) ?>
        <?php endforeach; ?>
    </nav>
</div>
