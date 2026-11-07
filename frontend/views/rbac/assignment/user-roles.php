<?php

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array<int,string[]> $rolesByUser
 * @var yii\rbac\Role[] $allRoles
 * @var string|null $q
 */

use common\library\FormUi;
use yii\helpers\Html;

$this->title = 'User Role Assignment';
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['/rbac/index']];
$this->params['breadcrumbs'][] = $this->title;

['status' => $statusBadge, 'roles' => $roleChips] = require __DIR__ . '/../_user_cells.php';
?>

<?= $this->render('../_header', [
    'title' => 'User Role Assignment',
    'subtitle' => 'Escalate or change what each user can do. New users receive the researcher role after verifying their email.',
]) ?>

<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden">
    <?= Html::beginForm(['user-roles'], 'get', ['class' => 'flex flex-col sm:flex-row gap-2 px-6 py-[1.125rem] border-b border-outline-variant/15']) ?>
        <div class="relative flex-1 min-w-0">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <?= Html::textInput('q', $q, ['placeholder' => 'Search username or email…', 'class' => FormUi::gridSearchClass()]) ?>
        </div>
        <?= Html::submitButton('Search', ['class' => 'px-6 py-2 rounded-xl text-sm font-semibold bg-surface-container hover:bg-surface-container-high']) ?>
    <?= Html::endForm() ?>

    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[640px]">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="<?= FormUi::thClass() ?>">User</th>
                    <th class="<?= FormUi::thClass() ?>">Status</th>
                    <th class="<?= FormUi::thClass() ?>">Roles</th>
                    <th class="<?= FormUi::thClass(true) ?>">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php foreach ($dataProvider->getModels() as $user): ?>
                    <tr class="<?= FormUi::trClass() ?>">
                        <td class="px-6 py-[1.125rem]">
                            <p class="font-bold text-sm text-on-surface break-all"><?= Html::encode($user->username) ?>
                                <?php if ((int) $user->id === (int) Yii::$app->user->id): ?>
                                    <span class="text-[10px] font-semibold text-on-surface-variant">(you)</span>
                                <?php endif; ?>
                            </p>
                            <p class="text-xs text-on-surface-variant break-all"><?= Html::encode($user->email) ?></p>
                        </td>
                        <td class="px-6 py-[1.125rem]"><?= $statusBadge((int) $user->status) ?></td>
                        <td class="px-6 py-[1.125rem]"><?= $roleChips($rolesByUser[$user->id] ?? []) ?></td>
                        <td class="px-6 py-[1.125rem] text-right">
                            <?= Html::a(
                                '<span class="material-symbols-outlined text-[18px]">manage_accounts</span>Manage roles',
                                ['assign-role', 'userId' => $user->id],
                                ['class' => 'inline-flex items-center gap-1.5 px-[0.875rem] py-2 rounded-lg text-xs font-semibold text-primary hover:bg-primary-fixed/50 whitespace-nowrap']
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$dataProvider->getCount()): ?>
                    <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-on-surface-variant">No users match.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= $this->render('../_pager', ['dataProvider' => $dataProvider]) ?>
</div>
