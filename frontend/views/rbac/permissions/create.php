<?php

/**
 * @var yii\web\View $this
 * @var frontend\models\PermissionForm $model
 * @var array $parentOptions
 */

$this->title = 'New permission';
$this->params['breadcrumbs'][] = ['label' => 'Permissions', 'url' => ['permissions']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('../_header', [
    'title' => 'New permission',
    'subtitle' => 'A new permission has no effect until code checks it with Yii::$app->user->can(). Grant it to one or more roles below.',
]) ?>
<?= $this->render('_form', ['model' => $model, 'parentOptions' => $parentOptions]) ?>
