<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyTimeline $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="study-timeline-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'study_duration')->textInput() ?>

    <?= $form->field($model, 'study_site_location')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'centre_postal_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'anticipated_start_date')->textInput() ?>

    <?= $form->field($model, 'anticipated_end_date')->textInput() ?>

    <?= $form->field($model, 'recruitment_status')->textInput() ?>

    <?= $form->field($model, 'recruiting_country')->textInput() ?>

    <?= $form->field($model, 'centre_pysical_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'centre_region')->textInput() ?>

    <?= $form->field($model, 'trial_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
