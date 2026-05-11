<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\Funding $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="funding-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sponsor_name')->textInput() ?>

    <?= $form->field($model, 'Amount')->textInput() ?>

    <?= $form->field($model, 'country')->textInput() ?>

    <?= $form->field($model, 'funding_Sector')->textInput() ?>

    <?= $form->field($model, 'trial_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'update_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
