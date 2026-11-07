<?php

/**
 * @var yii\web\View $this
 * @var yii\data\ArrayDataProvider $dataProvider
 */

use common\library\FormUi;
use yii\helpers\Html;

$this->title = 'Permissions';
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['/rbac/index']];
$this->params['breadcrumbs'][] = $this->title;

$chip = static fn(string $text, string $cls = 'bg-surface-container text-on-surface-variant'): string =>
    '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold ' . $cls . '">' . Html::encode($text) . '</span>';
?>

<?= $this->render('../_header', [
    'title' => 'Permissions',
    'subtitle' => 'Permissions are the actions the code checks. System permissions are used by the application and cannot be renamed or deleted.',
    'actions' => FormUi::ctaButton('New permission', 'add', ['create-permission']),
]) ?>

<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[720px]">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="<?= FormUi::thClass() ?>">Permission</th>
                    <th class="<?= FormUi::thClass() ?>">Granted to roles</th>
                    <th class="<?= FormUi::thClass() ?>">Included in</th>
                    <th class="<?= FormUi::thClass(true) ?>">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php foreach ($dataProvider->getModels() as $p): ?>
                    <tr class="<?= FormUi::trClass() ?> align-top">
                        <td class="px-6 py-[1.125rem] max-w-sm">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-headline font-bold text-primary text-sm break-all"><?= Html::encode($p['name']) ?></span>
                                <?php if ($p['system']): ?>
                                    <?= $chip('System', 'bg-secondary-container text-on-secondary-container') ?>
                                <?php endif; ?>
                                <?php if ($p['rule']): ?>
                                    <?= $chip('rule: ' . $p['rule'], 'bg-tertiary-fixed text-on-tertiary-fixed-variant') ?>
                                <?php endif; ?>
                            </div>
                            <p class="mt-1 text-xs text-on-surface-variant break-words"><?= Html::encode($p['description'] ?: '—') ?></p>
                        </td>
                        <td class="px-6 py-[1.125rem]">
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach ($p['roles'] as $r): ?>
                                    <?= $chip($r, 'bg-primary-fixed text-on-primary-fixed') ?>
                                <?php endforeach; ?>
                                <?php if (!$p['roles']): ?><span class="text-xs text-on-surface-variant">—</span><?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-[1.125rem]">
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach ($p['parentPermissions'] as $pp): ?>
                                    <?= $chip($pp) ?>
                                <?php endforeach; ?>
                                <?php if (!$p['parentPermissions']): ?><span class="text-xs text-on-surface-variant">—</span><?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-[1.125rem] text-right whitespace-nowrap">
                            <?= FormUi::actionBtn('edit_square', ['update-permission', 'name' => $p['name']], 'edit', ['title' => 'Edit permission']) ?>
                            <?= FormUi::actionBtn('account_tree', ['manage-hierarchy', 'name' => $p['name']], 'view', ['title' => 'Child permissions']) ?>
                            <?php if (!$p['system']): ?>
                                <?= FormUi::actionBtn('delete', ['delete-permission', 'name' => $p['name']], 'delete', [
                                    'title' => 'Delete permission',
                                    'data' => ['method' => 'post', 'confirm' => "Delete permission '{$p['name']}'? Roles granting it will lose it."],
                                ]) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $this->render('../_pager', ['dataProvider' => $dataProvider]) ?>
</div>
