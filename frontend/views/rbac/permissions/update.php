<?php

/**
 * @var yii\web\View $this
 * @var frontend\models\PermissionForm $model
 * @var array $parentOptions
 */

$this->title = 'Edit permission: ' . $model->originalName;
$this->params['breadcrumbs'][] = ['label' => 'Permissions', 'url' => ['permissions']];
$this->params['breadcrumbs'][] = $model->originalName;
?>
<?= $this->render('../_header', [
    'title' => 'Edit permission: ' . $model->originalName,
    'subtitle' => 'Changes apply immediately to every role and user that holds this permission.',
]) ?>
<?= $this->render('_form', ['model' => $model, 'parentOptions' => $parentOptions]) ?>
