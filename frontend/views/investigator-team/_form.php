<?php

use common\library\FormUi;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\InvestigatorTeam $model */
/** @var yii\widgets\ActiveForm $form */

$steps = Yii::$app->params['steps'];
$actionId = Yii::$app->controller->action->id;
$totalSteps = count($steps);

$activeIndex = 0;

// show next/prev buttons based on current step index not more than 3 steps away from current step to prevent navigation to non-sequential steps
foreach ($steps as $i => $step) {
    if ($step['controller'] === Yii::$app->controller->id &&  in_array($step['action'],['create','update'])) {
        $activeIndex = $i;
        break;
    }
}

$stepNumber = str_pad($activeIndex + 1, 2, '0', STR_PAD_LEFT);
$prevStep = $activeIndex > 0 ? $steps[$activeIndex - 1] : null;
$nextStep = $activeIndex < $totalSteps - 1 ? $steps[$activeIndex + 1] : null;

?>

<div class="investigator-team-form">


    <header class="mb-12 border-l-4 border-primary pl-8">
        <span class="text-label-sm font-bold tracking-[0.1em] text-on-surface-variant uppercase">
            Step
            <?= $stepNumber ?> /
            <?= $totalSteps ?>
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-primary mt-2">
            Investigator Team
        </h1>
        <p class="text-on-surface-variant max-w-2xl mt-3 leading-relaxed">
            Define the details for the investigator team members.
        </p>
    </header>

    <?php /* ── Progress Tracker partial (active state auto-resolved internally) */ ?>
    <?= $this->render('../clinical-trial/_progress_tracker') ?>


    <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">

        <!-- Header -->
        <div class="p-8 border-b border-surface-container-high bg-surface-container-low">
            <div class="flex justify-between items-end">
                <div>
                    <h3 class="text-2xl font-bold text-primary mb-1">Collaborative Network</h3>
                    <p class="text-on-surface-variant text-sm">
                        Define roles and contact parameters for all trial personnel.
                    </p>
                </div>

                <button id="add-member"
                    class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:opacity-90 transition-opacity shadow-md">
                    <span class="material-symbols-outlined text-sm">person_add</span>
                    <span>Add Investigator</span>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                            Role</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                            Investigator Details</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                            Location</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                            Contact</th>
                        <th class="px-6 py-4 text-right"></th>
                    </tr>
                </thead>

                <!-- DATA ROWS -->
                <tbody id="team-body" class="divide-y divide-surface-container-high">

                    <?php foreach ($members as $i => $member): ?>
                        <tr data-member-id="<?= $member->id ?>">

                            <td class="px-6 py-6 align-top">
                                <select name="team[<?= $i ?>][role]"
                                    class="w-full bg-surface-container-low border-0 border-b-2 focus:border-primary rounded-lg text-sm py-2 px-3">
                                    <option value="">Select</option>
                                    <option value="1" <?= $member->role == 1 ? 'selected' : '' ?>>PI</option>
                                    <option value="2" <?= $member->role == 2 ? 'selected' : '' ?>>Co-PI</option>
                                    <option value="3" <?= $member->role == 3 ? 'selected' : '' ?>>Collaborator</option>
                                </select>
                            </td>

                            <td class="px-6 py-6">
                                <div class="space-y-3">
                                    <input name="team[<?= $i ?>][name]" placeholder="Full Name"
                                        value="<?= Html::encode($member->name) ?>"
                                        class="w-full bg-transparent border-0 border-b text-sm font-semibold p-0">
                                    <input name="team[<?= $i ?>][institution]" placeholder="Institution"
                                        value="<?= Html::encode($member->institution) ?>"
                                        class="w-full bg-transparent border-0 border-b text-xs p-0">
                                </div>
                            </td>

                            <td class="px-6 py-6">
                                <div class="space-y-3">
                                    <div class="flex gap-2">
                                        <input name="team[<?= $i ?>][country]" placeholder="Country"
                                            value="<?= Html::encode($member->countryModel?->name ?? '') ?>"
                                            class="w-1/2 bg-transparent border-0 border-b text-xs p-0">
                                        <input name="team[<?= $i ?>][city]" placeholder="City"
                                            value="<?= Html::encode($member->cityModel?->name ?? '') ?>"
                                            class="w-1/2 bg-transparent border-0 border-b text-xs p-0">
                                    </div>
                                    <input name="team[<?= $i ?>][postal_address]" placeholder="Postal Address"
                                        value="<?= Html::encode($member->postal_address) ?>"
                                        class="w-full bg-transparent border-0 border-b text-xs p-0">
                                </div>
                            </td>

                            <td class="px-6 py-6">
                                <div class="space-y-3">
                                    <input type="email" name="team[<?= $i ?>][email_address]" placeholder="Email"
                                        value="<?= Html::encode($member->email_address) ?>"
                                        class="w-full bg-transparent border-0 border-b text-xs p-0">
                                    <input type="tel" name="team[<?= $i ?>][mobile_number]" placeholder="Phone"
                                        value="<?= Html::encode($member->mobile_number) ?>"
                                        class="w-full bg-transparent border-0 border-b text-xs p-0">
                                </div>
                            </td>

                            <td class="px-6 py-6 text-right">

                                <button type="button" class="remove-row p-2 text-on-surface-variant hover:text-error">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </td>

                        </tr>
                    <?php endforeach; ?>


                </tbody>
            </table>
        </div>
    </div>

    <!-- ✅ ROW TEMPLATE (CLONE THIS VIA JS) -->

    <table class="hidden">
        <tbody>
            <tr id="row-template">
                <td class="px-6 py-6 align-top">
                    <?= Html::dropDownList(
                        'InvestigatorTeam[__INDEX__][role]',
                        null,
                        $model->getRoleOptions(),
                        array_merge(FormUi::inputOptions()['select'], ['prompt' => 'Select Role...'])
                    ) ?>
                </td>

                <td class="px-6 py-6">
                    <div class="space-y-3">
                        <?= Html::textInput(
                            'InvestigatorTeam[__INDEX__][name]',
                            null,
                            array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Full Name'])
                        ) ?>

                        <?= Html::textInput(
                            'InvestigatorTeam[__INDEX__][institution]',
                            null,
                            array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Institution'])
                        ) ?>
                    </div>
                </td>

                <td class="px-6 py-6">
                    <div class="space-y-3">
                        <div class="flex gap-2">
                            <?= Html::dropDownList(
                                'InvestigatorTeam[__INDEX__][country]',
                                null,
                                $model->countryOptions, // Assuming you have a method in your model that returns country options
                                array_merge(
                                    FormUi::inputOptions()['select'],
                                    [
                                        'prompt' => 'Select Country...',
                                        'onchange' => '$.post( "' . Yii::$app->urlManager->createUrl('investigator-team/towns-dd?id=') . '" + $(this).val(), function( data ) {
                                        $( "select.town-dd" ).html( data );
                                });
                                '
                                    ]
                                )
                            ) ?>

                            <?= Html::dropDownList(
                                'InvestigatorTeam[__INDEX__][city]',
                                null,
                                $model->cityOptions, // Assuming you have a method in your model that returns city options
                                array_merge(
                                    FormUi::inputOptions()['select'],
                                    [
                                        'prompt' => 'Select City...',
                                        'class' => 'town-dd'
                                    ]
                                )
                            ) ?>
                        </div>

                        <?= Html::textInput(
                            'InvestigatorTeam[__INDEX__][postal_address]',
                            null,
                            array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Postal Address'])
                        ) ?>
                    </div>
                </td>

                <td class="px-6 py-6">
                    <div class="space-y-3">
                        <?= Html::textInput(
                            'InvestigatorTeam[__INDEX__][email_address]',
                            null,
                            array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'e-mail Address', 'type' => 'email'])
                        ) ?>

                        <?= Html::textInput(
                            'InvestigatorTeam[__INDEX__][mobile_number]',
                            null,
                            array_merge(FormUi::inputOptions()['text'], ['placeholder' => 'Mobile Number'])
                        ) ?>
                    </div>
                </td>

                <td class="px-6 py-6 text-right">
                    <button type="button" class="remove-row p-2 text-on-surface-variant hover:text-error">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between my-6">
        <button
            class="flex items-center gap-2 px-8 py-3 text-primary font-bold hover:bg-primary-fixed/20 rounded-xl transition-all">
            <span class="material-symbols-outlined">arrow_back</span>
            <span>Previous Step</span>
        </button>
        <div class="flex items-center gap-4">

            <?= Html::a(
                '<span>Continue to Next Step</span><span class="material-symbols-outlined">arrow_forward</span>',
                $nextStep['createUrl'], // Replace with your actual route or URL
                [
                    'class' => 'inline-flex items-center gap-2 px-10 py-3 bg-gradient-to-r from-primary to-primary-container text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform'
                ]
            ) ?>
        </div>
    </div>
</div>




<?php

$this->registerJsFile('@web/js/investigator-team.js', ['position' => \yii\web\View::POS_END]);