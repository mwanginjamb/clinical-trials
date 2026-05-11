<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\StudyTimelineSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="study-timeline-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'study_duration') ?>

    <?= $form->field($model, 'study_site_location') ?>

    <?= $form->field($model, 'centre_postal_address') ?>

    <?= $form->field($model, 'anticipated_start_date') ?>

    <?php // echo $form->field($model, 'anticipated_end_date') ?>

    <?php // echo $form->field($model, 'recruitment_status') ?>

    <?php // echo $form->field($model, 'recruiting_country') ?>

    <?php // echo $form->field($model, 'centre_pysical_address') ?>

    <?php // echo $form->field($model, 'centre_region') ?>

    <?php // echo $form->field($model, 'trial_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
