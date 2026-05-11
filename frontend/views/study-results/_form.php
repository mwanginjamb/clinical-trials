<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyResults $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="study-results-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'permission_to_publish')->textInput() ?>

    <?= $form->field($model, 'summary_results')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'authority_committe_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publisher')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url_doi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publication_type')->textInput() ?>

    <?= $form->field($model, 'publication_title')->textInput(['maxlength' => true]) ?>

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
