<?php

/**
 * @var yii\web\View $this
 * @var frontend\models\PermissionForm $model
 * @var array $parentOptions ['Roles' => [name => name], 'Permissions' => [...]]
 */

use common\library\FormUi;
use common\rbac\Rbac;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$auth = Yii::$app->authManager;
$isSystem = in_array($model->originalName, Rbac::SYSTEM_PERMISSIONS, true);

// name => description for the checkbox grid
$groups = [];
foreach ($parentOptions as $group => $names) {
    foreach ($names as $name) {
        $groups['Granted by ' . strtolower($group)][$name] = Rbac::item($name)->description ?? '';
    }
}
?>
<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-8 max-w-5xl">
    <?php $form = ActiveForm::begin(FormUi::formConfig('permission-form')); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?= $form->field($model, 'name')->textInput(array_merge(
            FormUi::inputOptions()['text'],
            ['readonly' => $isSystem, 'maxlength' => 64, 'autofocus' => !$isSystem]
        ))->hint($isSystem ? 'System permission: checked in code, so the name cannot change.' : $model->getAttributeHint('name')) ?>
        <?= $form->field($model, 'description')->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => 255])) ?>
    </div>

    <div>
        <p class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1"><?= $model->getAttributeLabel('parents') ?></p>
        <p class="text-xs text-on-surface-variant mb-[0.875rem]"><?= Html::encode($model->getAttributeHint('parents')) ?></p>
        <?= $this->render('../_checkbox_grid', [
            'name' => Html::getInputName($model, 'parents'),
            'groups' => $groups,
            'selected' => (array) $model->parents,
            'locked' => $model->originalName === Rbac::PERM_MANAGE_RBAC ? [Rbac::ROLE_ADMIN] : [],
        ]) ?>
        <?= Html::error($model, 'parents', ['class' => 'text-xs text-error mt-2']) ?>
    </div>

    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
        <?= Html::a('Cancel', ['permissions'], ['class' => 'inline-flex justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-on-surface-variant hover:bg-surface-container']) ?>
        <?= Html::submitButton(
            '<span class="material-symbols-outlined text-[18px]">save</span>' . ($model->scenario === 'create' ? 'Create permission' : 'Save changes'),
            ['class' => 'inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:opacity-90']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
