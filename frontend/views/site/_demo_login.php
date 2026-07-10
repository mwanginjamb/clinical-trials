<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Login';
?>

<div class="mb-8">
    <h2 class="text-xl font-bold text-gray-900">
        Secure Access
    </h2>

    <p class="text-sm text-gray-500 mt-1">
        Enter your institutional credentials to access the repository.
    </p>
</div>

<?php $form = ActiveForm::begin([
    'options' => [
        'class' => 'space-y-6'
    ]
]); ?>

<div>
    <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider mb-1">
        Scientist Username
    </label>

    <?= $form->field($model, 'username', [
        'template' => '{input}{error}',
    ])->textInput([
                'class' => 'block w-full px-4 py-3 bg-brand-light border-transparent
                    focus:ring-2 focus:ring-brand-accent focus:bg-white
                    rounded-lg text-sm',
                'placeholder' => 'Enter username',
                'autofocus' => true,
                'autocomplete' => 'off',
            ])->label(false) ?>
</div>

<div>
    <div class="flex justify-between items-end mb-1">
        <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-wider">
            Password
        </label>

        <?= Html::a(
            'Forgot Password?',
            ['site/request-password-reset'],
            [
                'class' => 'text-[10px] font-extrabold text-brand-accent uppercase tracking-wider hover:underline',

            ]
        ) ?>
    </div>

    <?= $form->field($model, 'password', [
        'template' => '{input}{error}',
    ])->passwordInput([
                'class' => 'block w-full px-4 py-3 bg-brand-light border-transparent
                    focus:ring-2 focus:ring-brand-accent focus:bg-white
                    rounded-lg text-sm',
                'placeholder' => 'Enter password',
                'autocomplete' => 'off',
            ])->label(false) ?>
</div>

<div>
    <?= $form->field($model, 'rememberMe')->checkbox([
        'class' => 'h-4 w-4 text-brand-accent rounded'
    ]) ?>
</div>

<div class="pt-2">
    <?= Html::submitButton(
        'Sign In',
        [
            'class' => 'w-full py-4 px-4 rounded-lg text-base font-bold
                        text-white bg-brand-primary
                        hover:bg-brand-primary/90 transition'
        ]
    ) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="mt-8 text-center space-y-3">
    <p class="text-xs text-gray-500">
        Didn't receive the verification email?
        <?= Html::a(
            'Resend Email',
            ['site/resend-verification-email'],
            ['class' => 'font-bold text-brand-accent hover:underline']
        ) ?>
    </p>

    <p class="text-xs text-gray-500">
        Don't have an account?
        <?= Html::a(
            'Register',
            ['site/signup'],
            ['class' => 'font-bold text-brand-accent hover:underline']
        ) ?>
    </p>
</div>

<div class="mt-10 pt-6 border-t border-gray-100">

    <div class="flex justify-center mb-4">
        <span class="px-3 bg-white text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em]">
            Verification
        </span>
    </div>

    <div class="bg-gray-50 rounded-lg p-4 flex items-start space-x-3">

        <svg class="h-4 w-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>

        <p class="text-[11px] leading-relaxed text-gray-500">
            This system is for authorized personnel only. All access is logged
            and subject to institutional compliance audits.
        </p>

    </div>

</div>