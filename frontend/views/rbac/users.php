<?php

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array<int,string[]> $rolesByUser
 * @var string|null $q
 * @var string|null $status
 */

use common\library\FormUi;
use common\models\User;
use yii\helpers\Html;

$this->title = 'System Users';
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['/rbac/index']];
$this->params['breadcrumbs'][] = $this->title;

['statusLabels' => $statusLabels, 'status' => $statusBadge, 'roles' => $roleChips] = require __DIR__ . '/_user_cells.php';
$me = (int) Yii::$app->user->id;
$btn = 'inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap';
?>

<?= $this->render('_header', [
    'title' => 'System Users',
    'subtitle' => 'Everyone with an account. Activating an unverified account also gives it the researcher role, just like email verification.',
]) ?>

<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden">
    <?= Html::beginForm(['users'], 'get', ['class' => 'flex flex-col md:flex-row gap-2 px-6 py-[1.125rem] border-b border-outline-variant/15']) ?>
        <div class="relative flex-1 min-w-0">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <?= Html::textInput('q', $q, ['placeholder' => 'Search username or email…', 'class' => FormUi::gridSearchClass()]) ?>
        </div>
        <?= Html::dropDownList('status', $status, $statusLabels, [
            'prompt' => 'Any status',
            'class' => 'bg-surface-container border border-surface-variant rounded-xl py-2 pl-[0.875rem] pr-10 text-sm',
        ]) ?>
        <?= Html::submitButton('Filter', ['class' => 'px-6 py-2 rounded-xl text-sm font-semibold bg-surface-container hover:bg-surface-container-high']) ?>
    <?= Html::endForm() ?>

    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[760px]">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="<?= FormUi::thClass() ?>">User</th>
                    <th class="<?= FormUi::thClass() ?>">Status</th>
                    <th class="<?= FormUi::thClass() ?>">Roles</th>
                    <th class="<?= FormUi::thClass() ?>">Joined</th>
                    <th class="<?= FormUi::thClass(true) ?>">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php foreach ($dataProvider->getModels() as $user):
                    $isMe = (int) $user->id === $me; ?>
                    <tr class="<?= FormUi::trClass() ?>">
                        <td class="px-6 py-[1.125rem]">
                            <p class="font-bold text-sm text-on-surface break-all"><?= Html::encode($user->username) ?>
                                <?= $isMe ? '<span class="text-[10px] font-semibold text-on-surface-variant">(you)</span>' : '' ?></p>
                            <p class="text-xs text-on-surface-variant break-all"><?= Html::encode($user->email) ?></p>
                        </td>
                        <td class="px-6 py-[1.125rem]"><?= $statusBadge((int) $user->status) ?></td>
                        <td class="px-6 py-[1.125rem]"><?= $roleChips($rolesByUser[$user->id] ?? []) ?></td>
                        <td class="<?= FormUi::tdClass('muted') ?>"><?= Yii::$app->formatter->asDate($user->created_at) ?></td>
                        <td class="px-6 py-[1.125rem] text-right">
                            <div class="inline-flex flex-wrap justify-end gap-1">
                                <?= Html::a('<span class="material-symbols-outlined text-[16px]">manage_accounts</span>Roles',
                                    ['assign-role', 'userId' => $user->id],
                                    ['class' => $btn . ' text-primary hover:bg-primary-fixed/50']) ?>
                                <?php if (!$isMe && (int) $user->status !== User::STATUS_ACTIVE): ?>
                                    <?= Html::a('<span class="material-symbols-outlined text-[16px]">check_circle</span>Activate',
                                        ['set-user-status', 'id' => $user->id, 'status' => User::STATUS_ACTIVE],
                                        ['class' => $btn . ' text-tertiary hover:bg-tertiary-container/10',
                                            'data' => ['method' => 'post', 'confirm' => "Activate {$user->username}? They will be able to sign in as a researcher."]]) ?>
                                <?php elseif (!$isMe): ?>
                                    <?= Html::a('<span class="material-symbols-outlined text-[16px]">block</span>Disable',
                                        ['set-user-status', 'id' => $user->id, 'status' => User::STATUS_DELETED],
                                        ['class' => $btn . ' text-error hover:bg-error-container',
                                            'data' => ['method' => 'post', 'confirm' => "Disable {$user->username}? They will no longer be able to sign in."]]) ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$dataProvider->getCount()): ?>
                    <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-on-surface-variant">No users match.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= $this->render('_pager', ['dataProvider' => $dataProvider]) ?>
</div>
