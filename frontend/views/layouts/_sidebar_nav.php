<?php

/**
 * Primary sidebar menu, shared by every layout with a sidebar (main.php, dashboard.php).
 *
 * Item keys:
 *   label, icon (Material Symbol), url (route array)
 *   visible  bool (default true) — hide items the user can't use
 *   active   optional list of "controller" or "controller/action" ids that mark the item active
 *   group + children — renders an accordion; a group is open when one of its children is active
 *
 * @var yii\web\View $this
 */

use common\rbac\Rbac;
use yii\helpers\Html;

$user = Yii::$app->user;
$controllerId = Yii::$app->controller->id;
$route = $controllerId . '/' . Yii::$app->controller->action->id;

$items = [
    ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => ['/site/index'], 'active' => ['site/index']],
    ['label' => 'Clinical Trials', 'icon' => 'clinical_notes', 'url' => ['/clinical-trial/index'], 'active' => ['clinical-trial']],
    [
        'label' => 'Bulk Trial Import', 'icon' => 'upload', 'url' => ['/trial-import/import'],
        'active' => ['trial-import'], 'visible' => $user->can(Rbac::PERM_IMPORT_TRIALS),
    ],

    // ── Nested group: 'children' and no 'url' ──
    [
        'label' => 'Lookups',
        'icon' => 'list_alt',
        'group' => true,
        'visible' => $user->can(Rbac::PERM_MANAGE_LOOKUPS),
        'children' => [
            ['label' => 'Areas of Specialization', 'url' => ['/area-of-specialization/index'], 'icon' => 'category', 'active' => ['area-of-specialization']],
            ['label' => 'Countries', 'url' => ['/country/index'], 'icon' => 'public', 'active' => ['country']],
            ['label' => 'Towns', 'url' => ['/town/index'], 'icon' => 'location_city', 'active' => ['town']],
            ['label' => 'Publishers', 'url' => ['/publisher/index'], 'icon' => 'menu_book', 'active' => ['publisher']],
        ],
    ],
    [
        'label' => 'RBAC Mgt',
        'icon' => 'admin_panel_settings',
        'group' => true,
        'visible' => $user->can(Rbac::PERM_MANAGE_RBAC),
        'children' => [
            ['label' => 'User Role Assignment', 'url' => ['/rbac/user-roles'], 'icon' => 'manage_accounts', 'active' => ['rbac/user-roles', 'rbac/assign-role']],
            ['label' => 'Roles', 'url' => ['/rbac/index'], 'icon' => 'security_update_good', 'active' => ['rbac/index', 'rbac/create-role', 'rbac/update-role', 'rbac/manage-hierarchy']],
            ['label' => 'Permissions', 'url' => ['/rbac/permissions'], 'icon' => 'security', 'active' => ['rbac/permissions', 'rbac/create-permission', 'rbac/update-permission']],
            ['label' => 'System Users', 'url' => ['/rbac/users'], 'icon' => 'group', 'active' => ['rbac/users']],
        ],
    ],
];

$isActive = static function (array $item) use ($controllerId, $route): bool {
    foreach ($item['active'] ?? [] as $pattern) {
        if ($pattern === $route || $pattern === $controllerId) {
            return true;
        }
    }
    return false;
};

$base = 'flex items-center gap-3 px-4 py-3 rounded-lg font-headline text-sm transition-all';
$on = 'bg-[#c4e7ff] text-[#001e2c] font-bold';
$off = 'text-slate-600 hover:bg-slate-200 font-medium';
$icon = static fn(string $name, bool $filled = false, string $size = ''): string =>
    '<span class="material-symbols-outlined shrink-0 ' . $size . '"' . ($filled ? ' style="font-variation-settings:\'FILL\' 1;"' : '') . '>' . $name . '</span>';
?>
<nav class="flex-1 px-4 space-y-1" aria-label="Main">
    <?php foreach ($items as $item):
        if (($item['visible'] ?? true) === false) {
            continue;
        }

        if (!empty($item['group'])):
            $children = array_filter($item['children'], fn($c) => ($c['visible'] ?? true) !== false);
            if (!$children) {
                continue;
            }
            $groupActive = (bool) array_filter($children, $isActive); ?>
            <details class="group/nav" <?= $groupActive ? 'open' : '' ?>>
                <summary class="<?= $base ?> cursor-pointer list-none select-none [&::-webkit-details-marker]:hidden <?= $groupActive ? 'text-[#001e2c] font-bold' : $off ?>">
                    <?= $icon($item['icon'], $groupActive) ?>
                    <span class="flex-1 min-w-0 truncate"><?= Html::encode($item['label']) ?></span>
                    <span class="material-symbols-outlined text-[18px] transition-transform group-open/nav:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 ml-[1.375rem] pl-2 border-l border-slate-300/70 space-y-1">
                    <?php foreach ($children as $child):
                        $active = $isActive($child); ?>
                        <?= Html::a(
                            $icon($child['icon'], $active, 'text-[20px]') . '<span class="min-w-0 truncate">' . Html::encode($child['label']) . '</span>',
                            $child['url'],
                            [
                                'class' => 'flex items-center gap-2.5 px-[0.875rem] py-2 rounded-lg font-headline text-[13px] transition-all '
                                    . ($active ? $on : $off),
                                'aria-current' => $active ? 'page' : null,
                            ]
                        ) ?>
                    <?php endforeach; ?>
                </div>
            </details>
        <?php else:
            $active = $isActive($item); ?>
            <?= Html::a(
                $icon($item['icon'], $active) . '<span class="min-w-0 truncate">' . Html::encode($item['label']) . '</span>',
                $item['url'],
                ['class' => $base . ' ' . ($active ? $on : $off), 'aria-current' => $active ? 'page' : null]
            ) ?>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
