<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyIntervention $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="study-intervention-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'intervention_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'intervention_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'control_comparator')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type_of_outcome')->textInput() ?>

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
