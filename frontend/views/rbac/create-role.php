<?php

/**
 * @var yii\web\View $this
 * @var frontend\models\RoleForm $model
 * @var yii\rbac\Permission[] $permissions
 */

$this->title = 'New role';
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_header', [
    'title' => 'New role',
    'subtitle' => 'Name the role and pick the permissions it grants. To make it inherit another role, use Hierarchy after saving.',
]) ?>
<?= $this->render('_role_form', ['model' => $model, 'permissions' => $permissions]) ?>
