<?php

/**
 * @var yii\web\View $this
 * @var yii\rbac\Item $item
 * @var bool $isRole
 * @var yii\rbac\Item[] $allItems       candidate children (self excluded)
 * @var yii\rbac\Item[] $currentChildren
 * @var array $backRoute
 */

use common\rbac\Rbac;
use yii\helpers\Html;
use yii\rbac\Role;

$kind = $isRole ? 'role' : 'permission';
$this->title = 'Hierarchy: ' . $item->name;
$this->params['breadcrumbs'][] = ['label' => $isRole ? 'Roles' : 'Permissions', 'url' => $backRoute];
$this->params['breadcrumbs'][] = $item->name;

$roles = [];
$perms = [];
foreach ($allItems as $name => $candidate) {
    if ($candidate instanceof Role) {
        $roles[$name] = $candidate->description;
    } else {
        $perms[$name] = $candidate->description;
    }
}
ksort($roles);
ksort($perms);
$groups = $isRole ? ['Inherit roles' => $roles, 'Grant permissions' => $perms] : ['Include permissions' => $perms];
?>
<?= $this->render('_header', [
    'title' => 'Hierarchy: ' . $item->name,
    'subtitle' => $isRole
        ? "Everything ticked is granted to holders of the '{$item->name}' role. Ticking a role makes this role inherit all of its permissions."
        : "Holders of '{$item->name}' automatically get every permission ticked here.",
]) ?>

<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-8 max-w-5xl">
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-fixed text-on-primary-fixed"><?= $kind ?></span>
        <span class="font-headline font-bold text-on-surface"><?= Html::encode($item->name) ?></span>
        <?php if ($item->description): ?>
            <span class="text-sm text-on-surface-variant">— <?= Html::encode($item->description) ?></span>
        <?php endif; ?>
    </div>

    <?= Html::beginForm(['manage-hierarchy', 'name' => $item->name], 'post') ?>
        <?= $this->render('_checkbox_grid', [
            'name' => 'children',
            'groups' => $groups,
            'selected' => array_keys($currentChildren),
            'locked' => $item->name === Rbac::ROLE_ADMIN ? [Rbac::PERM_MANAGE_RBAC] : [],
            'emptyText' => 'There are no other items to add.',
        ]) ?>

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-8">
            <?= Html::a('Cancel', $backRoute, ['class' => 'inline-flex justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-on-surface-variant hover:bg-surface-container']) ?>
            <?= Html::submitButton(
                '<span class="material-symbols-outlined text-[18px]">save</span>Save hierarchy',
                ['class' => 'inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:opacity-90']
            ) ?>
        </div>
    <?= Html::endForm() ?>
</div>
