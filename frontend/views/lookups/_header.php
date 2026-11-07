<?php

/**
 * Page header + section tabs shared by every lookup screen (areas, countries, towns, publishers).
 *
 * @var yii\web\View $this
 * @var string $title
 * @var string|null $subtitle
 * @var string|null $actions  pre-rendered HTML (buttons) for the right side
 */

use yii\helpers\Html;

$tabs = [
    ['label' => 'Areas of Specialization', 'icon' => 'category', 'controller' => 'area-of-specialization'],
    ['label' => 'Countries', 'icon' => 'public', 'controller' => 'country'],
    ['label' => 'Towns', 'icon' => 'location_city', 'controller' => 'town'],
    ['label' => 'Publishers', 'icon' => 'menu_book', 'controller' => 'publisher'],
];
$current = Yii::$app->controller->id;
?>
<div class="mb-6 sm:mb-8 min-w-0">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-[1rem] mb-6">
        <div class="min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Lookups</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-headline text-on-surface tracking-tight break-words"><?= Html::encode($title) ?></h1>
            <?php if (!empty($subtitle)): ?>
                <p class="mt-1 text-sm text-on-surface-variant max-w-3xl"><?= Html::encode($subtitle) ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($actions)): ?>
            <div class="flex flex-wrap gap-2 shrink-0"><?= $actions ?></div>
        <?php endif; ?>
    </div>

    <nav aria-label="Lookup tables" class="flex gap-1 p-1.5 overflow-x-auto no-scrollbar rounded-xl bg-surface-container-low border border-outline-variant/15">
        <?php foreach ($tabs as $tab): $active = $tab['controller'] === $current; ?>
            <?= Html::a(
                '<span class="material-symbols-outlined text-[18px]">' . $tab['icon'] . '</span>' . Html::encode($tab['label']),
                ['/' . $tab['controller'] . '/index'],
                [
                    'class' => 'shrink-0 whitespace-nowrap inline-flex items-center gap-2 px-[0.875rem] py-2 rounded-lg text-xs transition-colors '
                        . ($active ? 'bg-primary-fixed text-on-primary-fixed font-semibold' : 'text-on-surface-variant font-medium hover:bg-surface-container'),
                    'aria-current' => $active ? 'page' : null,
                ]
            ) ?>
        <?php endforeach; ?>
    </nav>
</div>
