<?php

/**
 * Cancel + submit buttons at the bottom of a lookup form.
 *
 * @var yii\web\View $this
 * @var yii\db\ActiveRecord $model
 * @var string $noun  e.g. 'country'
 */

use yii\helpers\Html;

$cancelUrl = $model->isNewRecord ? ['index'] : ['view', 'id' => $model->getPrimaryKey()];
?>
<div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2 border-t border-outline-variant/15">
    <?= Html::a('Cancel', $cancelUrl, ['class' => 'inline-flex justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-on-surface-variant hover:bg-surface-container']) ?>
    <?= Html::submitButton(
        '<span class="material-symbols-outlined text-[18px]">save</span>' . Html::encode($model->isNewRecord ? 'Create ' . $noun : 'Save changes'),
        ['class' => 'inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:opacity-90']
    ) ?>
</div>
