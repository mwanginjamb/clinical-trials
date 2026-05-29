<?php

return [
    'adminEmail' => env('SMTP_USERNAME'),
    'supportEmail' => env('SMTP_USERNAME'),
    'senderEmail' => env('SMTP_USERNAME'),
    'senderName' => 'KEMRI Trials Mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'appName' => env('APP_NAME', 'KEMRI Clinical Trials Management Portal'),
    'appTitle' => env('APP_TITLE', 'Clinical Trials Management System'),
];
