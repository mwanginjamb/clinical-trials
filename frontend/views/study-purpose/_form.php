<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPurpose $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="study-purpose-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'study_purpose')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'study_objective')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'study_hypothesis')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type_of_study')->textInput() ?>

    <?= $form->field($model, 'intervention')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'control_group_name')->textInput() ?>

    <?= $form->field($model, 'design_control_group_presence')->textInput() ?>

    <?= $form->field($model, 'phase_of_study')->textInput() ?>

    <?= $form->field($model, 'randomization_method_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'masking_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'masking_status')->textInput() ?>

    <?= $form->field($model, 'trial_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
