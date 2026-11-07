<?php

use common\library\FormUi;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\AreaOfSpecialization $model */
?>
<div class="min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-[1.25rem] sm:p-8 max-w-4xl">
    <?php $form = ActiveForm::begin(FormUi::formConfig('area-of-specialization-form')); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
        <?= $form->field($model, 'title')->textInput(array_merge(FormUi::inputOptions()['text'], ['maxlength' => true, 'autofocus' => true, 'placeholder' => 'e.g. Infectious diseases'])) ?>
        <div class="md:col-span-2">
            <?= $form->field($model, 'description')->textarea(array_merge(FormUi::inputOptions()['text'], ['rows' => 5, 'placeholder' => 'Optional notes on what this area covers'])) ?>
        </div>
    </div>

    <?= $this->render('../lookups/_form_actions', ['model' => $model, 'noun' => 'area of specialization']) ?>

    <?php ActiveForm::end(); ?>
</div>
