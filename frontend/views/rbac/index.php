<?php

/**
 * @var yii\web\View $this
 * @var yii\data\ArrayDataProvider $dataProvider
 */

use common\library\FormUi;
use yii\helpers\Html;

$this->title = 'Roles';
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['/rbac/index']];
$this->params['breadcrumbs'][] = $this->title;

$chip = static fn(string $text, string $cls = 'bg-surface-container text-on-surface-variant'): string =>
    '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold ' . $cls . '">' . Html::encode($text) . '</span>';
?>

<?= $this->render('_header', [
    'title' => 'Roles',
    'subtitle' => 'A role bundles permissions. Users get the researcher role once their account is validated; admins escalate from here.',
    'actions' => FormUi::ctaButton('New role', 'add', ['create-role']),
]) ?>

<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[720px]">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="<?= FormUi::thClass() ?>">Role</th>
                    <th class="<?= FormUi::thClass() ?>">Grants</th>
                    <th class="<?= FormUi::thClass() ?> text-center">Effective perms</th>
                    <th class="<?= FormUi::thClass() ?> text-center">Users</th>
                    <th class="<?= FormUi::thClass(true) ?>">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php foreach ($dataProvider->getModels() as $role): ?>
                    <tr class="<?= FormUi::trClass() ?> align-top">
                        <td class="px-6 py-[1.125rem] max-w-xs">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-headline font-bold text-primary text-sm"><?= Html::encode($role['name']) ?></span>
                                <?php if ($role['system']): ?>
                                    <?= $chip('System', 'bg-secondary-container text-on-secondary-container') ?>
                                <?php endif; ?>
                            </div>
                            <p class="mt-1 text-xs text-on-surface-variant break-words"><?= Html::encode($role['description'] ?: '—') ?></p>
                        </td>
                        <td class="px-6 py-[1.125rem]">
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach ($role['childRoles'] as $child): ?>
                                    <?= $chip('inherits ' . $child, 'bg-primary-fixed text-on-primary-fixed') ?>
                                <?php endforeach; ?>
                                <?php foreach ($role['permissions'] as $perm): ?>
                                    <?= $chip($perm) ?>
                                <?php endforeach; ?>
                                <?php if (!$role['childRoles'] && !$role['permissions']): ?>
                                    <span class="text-xs text-on-surface-variant">No permissions</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-[1.125rem] text-center text-sm font-bold text-on-surface"><?= $role['effectiveCount'] ?></td>
                        <td class="px-6 py-[1.125rem] text-center text-sm font-bold text-on-surface"><?= $role['userCount'] ?></td>
                        <td class="px-6 py-[1.125rem] text-right whitespace-nowrap">
                            <?= FormUi::actionBtn('edit_square', ['update-role', 'name' => $role['name']], 'edit', ['title' => 'Edit role']) ?>
                            <?= FormUi::actionBtn('account_tree', ['manage-hierarchy', 'name' => $role['name']], 'view', ['title' => 'Hierarchy (child roles & permissions)']) ?>
                            <?php if (!$role['system']): ?>
                                <?= FormUi::actionBtn('delete', ['delete-role', 'name' => $role['name']], 'delete', [
                                    'title' => 'Delete role',
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => "Delete role '{$role['name']}'? It will be removed from {$role['userCount']} user(s).",
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $this->render('_pager', ['dataProvider' => $dataProvider]) ?>
</div>
