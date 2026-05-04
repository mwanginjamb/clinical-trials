<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPopulationEligibility $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="study-population-eligibility-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'health_condition_studied')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type_of_eligibility')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'participant_target_number')->textInput() ?>

    <?= $form->field($model, 'sample_size')->textInput() ?>

    <?= $form->field($model, 'final_number_of_participants')->textInput() ?>

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
