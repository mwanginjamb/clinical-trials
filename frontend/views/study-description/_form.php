<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyDescription $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="study-description-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'study_website')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lay_summary')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'scientific_summary')->textarea(['rows' => 6]) ?>

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
