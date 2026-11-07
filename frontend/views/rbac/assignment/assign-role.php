<?php

/**
 * @var yii\web\View $this
 * @var frontend\models\UserRoleForm $model
 * @var common\models\User $user
 * @var yii\rbac\Role[] $allRoles
 */

use common\rbac\Rbac;
use yii\helpers\Html;

$this->title = 'Roles for ' . $user->username;
$this->params['breadcrumbs'][] = ['label' => 'User Role Assignment', 'url' => ['user-roles']];
$this->params['breadcrumbs'][] = $user->username;

['status' => $statusBadge] = require __DIR__ . '/../_user_cells.php';

ksort($allRoles);
$options = array_map(fn($r) => $r->description, $allRoles);
$isSelf = (int) $user->id === (int) Yii::$app->user->id;
$auth = Yii::$app->authManager;
?>
<?= $this->render('../_header', [
    'title' => 'Roles for ' . $user->username,
    'subtitle' => 'Ticked roles are assigned; everything else is removed when you save.',
]) ?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 max-w-6xl">
    <aside class="min-w-0 bg-surface-container-low rounded-2xl p-[1.25rem] sm:p-6 h-fit">
        <div class="flex items-center gap-[0.875rem]">
            <div class="h-12 w-12 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-lg shrink-0">
                <?= Html::encode(mb_strtoupper(mb_substr($user->username, 0, 1))) ?>
            </div>
            <div class="min-w-0">
                <p class="font-bold text-on-surface break-all"><?= Html::encode($user->username) ?><?= $isSelf ? ' <span class="text-xs font-semibold text-on-surface-variant">(you)</span>' : '' ?></p>
                <p class="text-xs text-on-surface-variant break-all"><?= Html::encode($user->email) ?></p>
            </div>
        </div>
        <dl class="mt-6 space-y-[0.875rem] text-sm">
            <div class="flex justify-between gap-2"><dt class="text-on-surface-variant">Status</dt><dd><?= $statusBadge((int) $user->status) ?></dd></div>
            <div class="flex justify-between gap-2"><dt class="text-on-surface-variant">Joined</dt><dd class="font-semibold"><?= Yii::$app->formatter->asDate($user->created_at) ?></dd></div>
            <div class="flex justify-between gap-2"><dt class="text-on-surface-variant">Effective permissions</dt><dd class="font-semibold"><?= count($auth->getPermissionsByUser($user->id)) ?></dd></div>
        </dl>
        <?php if ((int) $user->status !== common\models\User::STATUS_ACTIVE): ?>
            <p class="mt-6 text-xs text-on-surface-variant bg-[#fff3cd] rounded-lg px-[0.875rem] py-2.5">
                This account is not active, so it cannot sign in regardless of its roles. Activate it under System Users.
            </p>
        <?php endif; ?>
    </aside>

    <div class="xl:col-span-2 min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-8">
        <?= Html::beginForm(['assign-role', 'userId' => $user->id], 'post') ?>
            <?= $this->render('../_checkbox_grid', [
                'name' => Html::getInputName($model, 'roles'),
                'groups' => ['Roles' => $options],
                'selected' => (array) $model->roles,
                // An admin can't remove their own admin role from here.
                'locked' => $isSelf && in_array(Rbac::ROLE_ADMIN, (array) $model->roles, true) ? [Rbac::ROLE_ADMIN] : [],
            ]) ?>
            <?= Html::error($model, 'roles', ['class' => 'text-sm text-error mt-[0.875rem]']) ?>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-8">
                <?= Html::a('Cancel', ['user-roles'], ['class' => 'inline-flex justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-on-surface-variant hover:bg-surface-container']) ?>
                <?= Html::submitButton(
                    '<span class="material-symbols-outlined text-[18px]">save</span>Save roles',
                    ['class' => 'inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:opacity-90']
                ) ?>
            </div>
        <?= Html::endForm() ?>
    </div>
</div>
