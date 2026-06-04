<?php

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME') . ';port=' . env('DB_PORT'),
            'username' => env('DB_USER'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
        ],
        'wizard' => [
            'class' => \common\library\WizardComponent::class,
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            // send all mails to a file by default.
            'useFileTransport' => false,
            // You have to set
            //
            // 'useFileTransport' => false,
            //
            // and configure a transport for the mailer to send real emails.
            //
            // SMTP server example:
            'transport' => [
                'scheme' => 'smtp',
                'host' => env('SMTP_HOST'),
                'username' => env('SMTP_USERNAME'),
                'password' => env('SMTP_PASSWORD'),
                'port' => 465,
                // 'dsn' => 'native://default',
            ],
            //
            // DSN example:
            //    'transport' => [
            //        'dsn' => 'smtp://'.env('SMTP_USERNAME').':'.env('SMTP_PASSWORD').'@'.env('SMTP_HOST').':25',
            //   ],
            //
            // See: https://symfony.com/doc/current/mailer.html#using-built-in-transports
            // Or if you use a 3rd party service, see:
            // https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport
        ],
    ],
];
