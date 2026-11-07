<?php

use common\library\FormUi;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\Town $model */
/** @var array $countries id => name */
?>
<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-8 max-w-4xl">
    <?php $form = ActiveForm::begin(FormUi::formConfig('town-form')); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
        <?= $form->field($model, 'name')->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true, 'autofocus' => true, 'placeholder' => 'e.g. Kisumu'])) ?>
        <?= $form->field($model, 'country_id')->dropDownList($countries, array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select country'])) ?>
        <?= $form->field($model, 'code')->textInput(array_merge(FormUi::inputOptions()['text'], ['type' => 'number', 'placeholder' => 'Numeric town code'])) ?>
    </div>

    <?= $this->render('../lookups/_form_actions', ['model' => $model, 'noun' => 'town']) ?>

    <?php ActiveForm::end(); ?>
</div>
