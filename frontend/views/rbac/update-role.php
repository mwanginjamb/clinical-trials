<?php

/**
 * @var yii\web\View $this
 * @var frontend\models\RoleForm $model
 * @var yii\rbac\Permission[] $permissions
 * @var bool $isUpdate
 */

use common\library\FormUi;

$this->title = 'Edit role: ' . $model->originalName;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->originalName;
?>
<?= $this->render('_header', [
    'title' => 'Edit role: ' . $model->originalName,
    'subtitle' => 'Changes apply immediately to every user holding this role.',
    'actions' => FormUi::actionBtn('account_tree', ['manage-hierarchy', 'name' => $model->originalName], 'view', ['title' => 'Hierarchy']),
]) ?>
<?= $this->render('_role_form', ['model' => $model, 'permissions' => $permissions]) ?>
