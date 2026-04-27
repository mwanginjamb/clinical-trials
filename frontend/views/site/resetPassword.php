<?php

/**
 * Clinical Curator — Login View
 *
 * @var \yii\web\View  $this
 * @var app\models\LoginForm $model
 *
 * Controller: SiteController::actionLogin()
 * Layout:     views/layouts/guest.php  (set via $this->layout or controller property)
 *
 * Usage in SiteController:
 *   public $layout = 'guest';   // applies to all actions, or…
 *   public function actionLogin() {
 *       $this->layout = 'guest';
 *       …
 *   }
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\library\FormUi;

$this->title = 'Reset Password | Clinical Curator';
$this->params['brandSubtitle'] = 'Institutional Research Portal';
?>

<!-- ════════════════════════════════════════════════════════════════════════════
     Login Card
     ════════════════════════════════════════════════════════════════════════════ -->
<div class="bg-surface-container-lowest/80 backdrop-blur-xl rounded-xl p-10
            shadow-[0_32px_64px_-12px_rgba(0,59,83,0.12)] border border-outline-variant/10">

    <div class="mb-8">
        <h2 class="text-xl font-bold text-on-surface mb-2">Secure Access</h2>
        <p class="text-on-surface-variant text-sm">
            Please Enter your preferred Password.
        </p>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'reset-password-form',
        'method' => 'post',
        'options' => ['class' => 'space-y-6'],
        // Suppress ActiveForm's default Bootstrap field layout
        'fieldConfig' => [
            'template' => '{label}{input}{error}',
            'labelOptions' => [
                'class' => 'block font-label text-[0.6875rem] font-bold uppercase
                            tracking-wider text-on-surface-variant ml-1',
            ],
            'errorOptions' => [
                'class' => 'text-xs text-error mt-1 ml-1',
                'tag' => 'p',
            ],
            'options' => ['class' => 'space-y-1'],
        ],
        'enableClientValidation' => true,
        'validateOnBlur' => true,
    ]); ?>


    <!-- ── Password ───────────────────────────────────────────────────────── -->
    <div class="space-y-1">
        <div class="flex justify-between items-center ml-1">
            <label class="block font-label text-[0.6875rem] font-bold uppercase tracking-wider
                          text-on-surface-variant" for="loginform-password">
                Password
            </label>
            <?= Html::a(
                'Forgot Password?',
                ['/site/request-password-reset'],
                [
                    'class' => 'text-[0.6875rem] font-bold uppercase tracking-wider text-primary
                             hover:text-primary-container transition-colors'
                ]
            ) ?>
        </div>
        <div class="group relative">
            <?= $form->field($model, 'password', ['options' => ['class' => '']])->passwordInput([
                'placeholder' => '••••••••••••',
                'class' => 'w-full h-12 bg-surface-container-low border-b-2
                                  border-transparent focus:border-primary focus:ring-0
                                  focus:bg-surface-container-lowest transition-all px-4
                                  text-on-surface rounded-t-lg',
            ])->label(false) ?>
            <div class="absolute right-4 top-3 text-outline opacity-40
                        group-focus-within:text-primary group-focus-within:opacity-100 transition-all">
                <span class="material-symbols-outlined text-xl">lock</span>
            </div>
        </div>
        <div class="group relative">
            <?= $form->field($model, 'confirm_password', ['options' => ['class' => '']])->passwordInput([
                'placeholder' => '••••••••••••',
                'autocomplete' => "off",
                'class' => 'w-full h-12 bg-surface-container-low border-b-2
                                  border-transparent focus:border-primary focus:ring-0
                                  focus:bg-surface-container-lowest transition-all px-4
                                  text-on-surface rounded-t-lg',
            ])->label(false) ?>
            <div class="absolute right-4 top-3 text-outline opacity-40
                        group-focus-within:text-primary group-focus-within:opacity-100 transition-all">
                <span class="material-symbols-outlined text-xl">lock</span>
            </div>
        </div>
    </div>


    <!-- ── Submit ─────────────────────────────────────────────────────────── -->
    <?= Html::submitButton(
        '<span>Set Password</span>
         <span class="material-symbols-outlined text-xl">arrow_forward</span>',
        [
            'class' => 'editorial-gradient w-full py-4 rounded-xl text-on-primary font-bold
                        tracking-tight text-lg shadow-[0_8px_20px_-4px_rgba(0,59,83,0.3)]
                        hover:scale-[1.02] active:scale-[0.98] transition-all duration-200
                        flex items-center justify-center gap-2',
        ]
    ) ?>

    <?php ActiveForm::end(); ?>

    <!-- ── Back to login ───────────────────────────────────────────────────── -->
    <p class="mt-6 text-center text-xs text-on-surface-variant">
        <?= Html::a(
            '← Back to Sign In',
            ['/site/login'],
            ['class' => 'font-bold text-primary hover:text-primary-container transition-colors']
        ) ?>
    </p>

    <!-- ── Divider ─────────────────────────────────────────────────────────── -->
    <div class="mt-10 flex items-center gap-4">
        <div class="h-[1px] flex-1 bg-outline-variant/30"></div>
        <span class="text-[0.6875rem] font-bold text-outline uppercase tracking-widest">
            Verification
        </span>
        <div class="h-[1px] flex-1 bg-outline-variant/30"></div>
    </div>

    <!-- ── HIPAA / compliance notice ──────────────────────────────────────── -->
    <div class="mt-6 flex items-start gap-3 p-4 rounded-lg bg-secondary-container/30">
        <span class="material-symbols-outlined text-secondary text-xl">verified_user</span>
        <p class="text-xs leading-relaxed text-on-secondary-container">
            This system is for authorized clinical personnel only. All access is logged
            and subject to HIPAA and institutional compliance audits.
        </p>
    </div>

</div>