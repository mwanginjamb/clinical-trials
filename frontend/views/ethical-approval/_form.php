<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\EthicalApproval $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="ethical-approval-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ethical_regulatory_body')->textInput() ?>

    <?= $form->field($model, 'approved_by_ethical_committee')->textInput() ?>

    <?= $form->field($model, 'document_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'document_path')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trial_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
