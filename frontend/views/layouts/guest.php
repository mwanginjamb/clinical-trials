<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?= Html::csrfMetaTags() ?>

    <title>
        <?= Html::encode($this->title) ?> | <?= Html::encode(Yii::$app->name) ?>
    </title>
    <!-- Favicons all common sizes -->
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-180x180.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-16x16.png">
    <link rel="mask-icon" href="/icons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#ffffff">



    <!-- Web App Manifest -->
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#ffffff" />







    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background:
                linear-gradient(rgba(255, 255, 255, .85),
                    rgba(255, 255, 255, .85)),
                url('<?= Yii::getAlias('@web') . '/images/screen.png' ?>');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>

    <?php $this->head() ?>
</head>

<body class="h-full antialiased text-gray-900">
    <?php $this->beginBody() ?>

    <main class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 overflow-y-auto">

        <!-- Brand Section -->
        <header class="w-full max-w-md text-center mb-8">

            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-xl shadow-sm border border-gray-100 mb-6 mx-auto">

                <!-- Replace with KEMRI Logo -->
                <img src="<?= Yii::getAlias('@web') . '/icons/icon-512x512.png' ?>" alt="KEMRI Logo"
                    class="w-12 h-12 object-contain">

            </div>

            <h1 class="text-2xl font-extrabold text-brand-accent tracking-tight leading-tight">
                KEMRI Clinical Trials Management System
            </h1>

            <p class="mt-2 text-xs font-bold uppercase tracking-widest text-gray-500">
                Institutional Research Portal
            </p>

        </header>

        <!-- Auth Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

            <div class="p-8 sm:p-10">
                <?= $content ?>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-8 text-center">
            <p class="text-[10px] text-gray-400 font-medium">
                ©
                <?= date('Y') ?> KEMRI Clinical Trials Management. All Rights Reserved.
            </p>
        </footer>

    </main>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>