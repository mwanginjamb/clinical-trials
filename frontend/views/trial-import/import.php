<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \frontend\models\TrialImportForm $form */

$this->title = 'Batch Import Trials';
?>
<h1>
    <?= Html::encode($this->title) ?>
</h1>

<p>
    <?= Html::a('Download the import template (.xlsx)', ['trial-import/template'], ['class' => 'btn btn-outline-primary']) ?>
</p>

<?php $activeForm = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<?= $activeForm->field($form, 'file')->fileInput() ?>

<?= Html::submitButton('Upload &amp; Import', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>