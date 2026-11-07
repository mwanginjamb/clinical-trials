<?php

/**
 * Shared helpers for user tables: status badge + role chips.
 * Returns closures; include with: ['status' => $status, 'roles' => $roles] = require __DIR__ . '/_user_cells.php';
 */

use common\models\User;
use common\rbac\Rbac;
use yii\helpers\Html;

return [
    'statusLabels' => [
        User::STATUS_ACTIVE => 'Active',
        User::STATUS_INACTIVE => 'Unverified',
        User::STATUS_DELETED => 'Disabled',
    ],
    'status' => static function (int $status): string {
        [$label, $cls] = match ($status) {
            User::STATUS_ACTIVE => ['Active', 'bg-tertiary-container text-white'],
            User::STATUS_INACTIVE => ['Unverified', 'bg-[#fff3cd] text-[#7a5c00]'],
            User::STATUS_DELETED => ['Disabled', 'bg-error-container text-on-error-container'],
            default => ['Unknown', 'bg-surface-container text-on-surface-variant'],
        };
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ' . $cls . '">' . $label . '</span>';
    },
    'roles' => static function (array $roles): string {
        if (!$roles) {
            return '<span class="text-xs text-on-surface-variant italic">No role</span>';
        }
        $out = '';
        foreach ($roles as $r) {
            $cls = $r === Rbac::ROLE_ADMIN ? 'bg-primary text-white' : 'bg-primary-fixed text-on-primary-fixed';
            $out .= '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold ' . $cls . '">' . Html::encode($r) . '</span> ';
        }
        return '<div class="flex flex-wrap gap-1.5">' . $out . '</div>';
    },
];
