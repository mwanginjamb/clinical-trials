<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyPurposeSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="study-purpose-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'study_purpose') ?>

    <?= $form->field($model, 'study_objective') ?>

    <?= $form->field($model, 'study_hypothesis') ?>

    <?= $form->field($model, 'type_of_study') ?>

    <?php // echo $form->field($model, 'intervention') ?>

    <?php // echo $form->field($model, 'control_group_name') ?>

    <?php // echo $form->field($model, 'design_control_group_presence') ?>

    <?php // echo $form->field($model, 'phase_of_study') ?>

    <?php // echo $form->field($model, 'randomization_method_name') ?>

    <?php // echo $form->field($model, 'masking_description') ?>

    <?php // echo $form->field($model, 'masking_status') ?>

    <?php // echo $form->field($model, 'trial_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
