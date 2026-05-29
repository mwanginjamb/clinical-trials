<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use common\library\FormUi;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>



<div class="bg-surface-container-lowest/80 backdrop-blur-xl rounded-xl p-10
            shadow-[0_32px_64px_-12px_rgba(0,59,83,0.12)] border border-outline-variant/10">


    <div class="mb-8">
        <h2 class="text-xl font-bold text-on-surface mb-2 text-center">Create Access Credentials</h2>
        <p class="text-on-surface-variant text-sm">
            An Institutional username is preferred. Password must be at least 8 characters.
        </p>
    </div>

    <?php $form = ActiveForm::begin(FormUi::formConfig('form-signup')); ?>

    <?= $form->field($model, 'username', FormUi::fieldConfig('account_circle')['base'])->textInput(array_merge(FormUi::inputOptions()['text'])) ?>

    <?= $form->field($model, 'email', FormUi::fieldConfig('mail')['base'])->textInput(array_merge(FormUi::inputOptions()['text'])) ?>

    <?= $form->field($model, 'password', FormUi::fieldConfig('lock')['base'])->passwordInput(array_merge(FormUi::inputOptions()['text'])) ?>

    <?= $form->field($model, 'confirmPassword', FormUi::fieldConfig('lock')['base'])->passwordInput(array_merge(FormUi::inputOptions()['text']))->label('Confirm Password') ?>

    <?= FormUi::submitButton('Signup', 'arrow_forward') ?>

    <?php ActiveForm::end(); ?>

    <!-- Add a divider -->
    <div class="my-6 flex items-center gap-4">
        <div class="h-[1px] flex-1 bg-outline-variant/30"></div>
        <span class="text-sm text-on-surface-variant">or</span>
        <div class="h-[1px] flex-1 bg-outline-variant/30"></div>
    </div>

    <!-- Back to login page -->

    <p class="mt-4 text-center text-xs text-on-surface-variant">
        Already have an account?
        <?= FormUi::link('Login', ['/site/login'], FormUi::linkClass()) ?>
    </p>

</div>