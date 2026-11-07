<?php

use common\library\FormUi;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\Publisher $model */
?>
<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-8 max-w-4xl">
    <?php $form = ActiveForm::begin(FormUi::formConfig('publisher-form')); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
        <?= $form->field($model, 'publisher')->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true, 'autofocus' => true, 'placeholder' => 'e.g. The Lancet'])) ?>
    </div>

    <?= $this->render('../lookups/_form_actions', ['model' => $model, 'noun' => 'publisher']) ?>

    <?php ActiveForm::end(); ?>
</div>
