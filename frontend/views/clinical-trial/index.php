<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\widgets\ActiveForm;
use frontend\models\ClinicalTrial;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClinicalTrialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trials Library';
?>

<style>
    /* ── Card-row table trick ───────────────────────────────────────────── */
    .trials-table {
        border-collapse: separate;
        border-spacing: 0 0.75rem;
        width: 100%;
    }

    /* Round the outer edges of every body row */
    .trials-table tbody tr td:first-child {
        border-radius: 1rem 0 0 1rem;
        padding-left: 2rem;
    }

    .trials-table tbody tr td:last-child {
        border-radius: 0 1rem 1rem 0;
        padding-right: 2rem;
    }

    /* Alternating card backgrounds */
    .trials-table tbody tr.row-odd td {
        background-color: #ffffff;
    }

    .trials-table tbody tr.row-even td {
        background-color: #f3f4f5;
    }

    /* Hover elevation */
    .trials-table tbody tr:hover td {
        box-shadow: 0 20px 25px -5px rgb(0 59 83 / 0.06);
    }

    /* Suppress Yii2 default table borders */
    .trials-table th,
    .trials-table td {
        border: none !important;
    }

    /* ── Pager ─────────────────────────────────────────────────────────── */
    .trials-pager {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .trials-pager li a,
    .trials-pager li span {
        width: 2.5rem;
        height: 2.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #40484d;
        text-decoration: none;
        transition: background-color 0.15s;
    }

    .trials-pager li a:hover {
        background-color: #e7e8e9;
    }

    .trials-pager li.active a {
        background-color: #003b53;
        color: #ffffff;
        font-weight: 700;
        pointer-events: none;
    }

    .trials-pager li.disabled a,
    .trials-pager li.disabled span {
        opacity: 0.35;
        pointer-events: none;
    }

    /* Prev / Next get icon-button look */
    .trials-pager li:first-child a,
    .trials-pager li:last-child a {
        width: auto;
        padding: 0.25rem 0.5rem;
        color: #70787d;
    }

    /* ── Filter form inputs: remove browser default outline & style ──────── */
    .trials-search-form input,
    .trials-search-form select {
        outline: none;
        box-shadow: none;
    }

    .trials-search-form input:focus,
    .trials-search-form select:focus {
        box-shadow: 0 0 0 2px rgb(0 59 83 / 0.2);
    }
</style>

<div class="max-w-7xl mx-auto">

    <!-- ── Page Header ──────────────────────────────────────────────── -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2">Trials Library</h1>
            <p class="text-on-surface-variant max-w-2xl" style="font-family:'Inter',sans-serif;">
                Manage and monitor multi-center clinical trials through a unified repository for
                research protocols, patient recruitment, and phase monitoring.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <?= Html::a(
                '<span class="material-symbols-outlined text-lg">download</span> Export CSV',
                ['export'],
                [
                    'encode' => false,
                    'class' => 'flex items-center gap-2 px-4 py-2.5 bg-surface-container-high '
                        . 'text-on-surface-variant rounded-xl font-medium text-sm '
                        . 'hover:bg-surface-container-highest transition-colors',
                ]
            ) ?>
            <?= Html::a(
                '<span class="material-symbols-outlined text-lg">add_circle</span> New Trial',
                ['create'],
                [
                    'encode' => false,
                    'class' => 'flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary '
                        . 'rounded-xl font-semibold text-sm hover:opacity-90 transition-opacity',
                ]
            ) ?>
        </div>
    </div>

    <!-- ── Search / Filter Bar ──────────────────────────────────────── -->
    <?php
    $form = ActiveForm::begin([
        'id' => 'search-form',
        'method' => 'get',
        'action' => ['index'],
        'options' => [
            'class' => 'trials-search-form bg-surface-container-low rounded-2xl '
                . 'p-6 mb-8 flex flex-wrap items-end gap-6',
            'data-pjax' => '',
        ],
    ]);
    ?>

    <div class="flex-1 min-w-[220px]">
        <label class="block text-[10px] font-bold text-outline uppercase tracking-wider mb-2 px-1">
            Scientific Title
        </label>
        <?= Html::activeTextInput($searchModel, 'scientific_title', [
            'class' => 'w-full bg-surface-container-lowest border border-outline-variant '
                . 'rounded-xl text-sm py-2.5 px-4',
            'placeholder' => 'Search by title…',
        ]) ?>
    </div>

    <div class="flex-1 min-w-[160px]">
        <label class="block text-[10px] font-bold text-outline uppercase tracking-wider mb-2 px-1">
            Acronym
        </label>
        <?= Html::activeTextInput($searchModel, 'scientific_acronym', [
            'class' => 'w-full bg-surface-container-lowest border border-outline-variant '
                . 'rounded-xl text-sm py-2.5 px-4',
            'placeholder' => 'e.g. N-Ab-401',
        ]) ?>
    </div>

    <div class="flex-1 min-w-[160px]">
        <label class="block text-[10px] font-bold text-outline uppercase tracking-wider mb-2 px-1">
            Status
        </label>
        <?= Html::activeDropDownList(
            $searchModel,
            'registration_status',
            [
                '' => 'All Status',
                'approved' => 'Approved',
                'active' => 'In Progress',
                'on_hold' => 'On Hold',
                'pending' => 'Pending',
            ],
            [
                'class' => 'w-full bg-surface-container-lowest border border-outline-variant '
                    . 'rounded-xl text-sm py-2.5 px-4',
            ]
        ) ?>
    </div>

    <div class="flex items-center gap-2 self-end">
        <?= Html::submitButton(
            '<span class="material-symbols-outlined text-lg">search</span> Search',
            [
                'encode' => false,
                'class' => 'flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary '
                    . 'rounded-xl font-semibold text-sm hover:opacity-90 transition-opacity',
            ]
        ) ?>
        <?= Html::a(
            '<span class="material-symbols-outlined">refresh</span>',
            ['index'],
            [
                'encode' => false,
                'title' => 'Reset filters',
                'class' => 'p-2.5 bg-surface-container-highest text-on-surface rounded-xl '
                    . 'hover:bg-outline-variant/30 transition-colors flex items-center justify-center',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <!-- ── GridView ─────────────────────────────────────────────────── -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,  // Filtering is handled by the styled form above.
        'tableOptions' => ['class' => 'trials-table'],

        /* Header row */
        'headerRowOptions' => ['class' => ''],

        /* Alternating row classes */
        'rowOptions' => function ($model, $key, $index, $grid) {
        $parity = ($index % 2 === 0) ? 'row-odd' : 'row-even';
        return ['class' => "$parity transition-all group cursor-default"];
    },

        /* Summary + pager wrapper */
        'layout' => implode("\n", [
            '<div class="space-y-1">{items}</div>',
            '<div class="mt-10 flex flex-col md:flex-row items-center justify-between',
            '     gap-6 border-t border-slate-200 pt-8">',
            '  <div class="text-sm text-on-surface-variant">{summary}</div>',
            '  <div>{pager}</div>',
            '</div>',
        ]),

        'summaryOptions' => ['tag' => 'span'],
        'summary' => 'Showing <span class="font-bold text-on-surface">{begin}–{end}</span> '
            . 'of <span class="font-bold text-on-surface">{totalCount}</span> entries',

        /* Custom pager */
        'pager' => [
            'options' => ['class' => 'trials-pager'],
            'prevPageLabel' => '<span class="material-symbols-outlined">chevron_left</span>',
            'nextPageLabel' => '<span class="material-symbols-outlined">chevron_right</span>',
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'disabledPageCssClass' => 'disabled',
        ],

        /* ── Columns ─────────────────────────────────────────────── */
        'columns' => [

            // ① Protocol & ID (scientific_title + acronym + public_title)
            [
                'attribute' => 'scientific_title',
                'label' => 'Protocol & ID',
                'format' => 'raw',
                'headerOptions' => [
                    'class' => 'text-[10px] font-bold text-outline uppercase tracking-widest '
                        . 'pb-3 text-left pl-8',
                    'style' => "font-family:'Inter',sans-serif;",
                ],
                'contentOptions' => ['class' => 'py-5 align-middle'],
                'value' => function ($model) {
                /* Icon chip */
                $icon = Html::tag(
                    'div',
                    Html::tag('span', 'biotech', ['class' => 'material-symbols-outlined']),
                    [
                        'class' => 'w-12 h-12 bg-secondary-container rounded-xl flex items-center '
                            . 'justify-center text-primary-container shrink-0'
                    ]
                );

                /* Acronym badge + separator + public title */
                $badge = Html::tag(
                    'span',
                    Html::encode($model->scientific_acronym ?: ('ID: ' . $model->id)),
                    ['class' => 'font-mono bg-surface-container-low px-1.5 py-0.5 rounded text-xs']
                );
                $dot = Html::tag('span', '', ['class' => 'w-1 h-1 rounded-full bg-outline-variant inline-block']);
                $sub = Html::tag('span', Html::encode($model->public_title ?? ''), []);
                $meta = Html::tag(
                    'div',
                    $badge . $dot . $sub,
                    ['class' => 'text-xs text-on-surface-variant flex items-center gap-2 mt-1']
                );

                /* Main heading */
                $heading = Html::tag(
                    'h3',
                    Html::encode($model->scientific_title),
                    [
                        'class' => 'font-bold text-on-surface group-hover:text-primary '
                            . 'transition-colors text-sm leading-snug',
                        'style' => "font-family:'Manrope',sans-serif;"
                    ]
                );

                $info = Html::tag('div', $heading . $meta);

                return Html::tag('div', $icon . $info, ['class' => 'flex items-start gap-4 w-full']);
            },
            ],

            // Registration Status
            [
                'attribute' => 'registration_status',
                'label' => 'Status',
                'format' => 'raw',
                'headerOptions' => [
                    'class' => 'text-[10px] font-bold text-outline uppercase tracking-widest '
                        . 'pb-3 text-center',
                    'style' => "font-family:'Inter',sans-serif;",
                ],
                'contentOptions' => ['class' => 'py-5 text-center align-middle'],
                'value' => function ($model) {
                $map = [
                    'approved' => ['bg-tertiary-container', 'text-on-tertiary-container', 'Approved'],
                    'active' => ['bg-secondary-container', 'text-on-secondary-container', 'In Progress'],
                    'on_hold' => ['bg-error-container', 'text-on-error-container', 'On Hold'],
                    'pending' => ['bg-surface-container-high', 'text-on-surface-variant', 'Pending'],
                ];
                $key = strtolower($model->registration_status ?? '');
                [$bg, $fg, $label] = $map[$key]
                    ?? ['bg-surface-container-high', 'text-on-surface-variant', ucfirst($key) ?: '—'];

                return Html::tag(
                    'span',
                    $label,
                    [
                        'class' => "inline-flex px-3 py-1 $bg $fg rounded-full "
                            . 'text-[10px] font-bold uppercase tracking-wider'
                    ]
                );
            },
            ],

            // ③ Protocol Version
            [
                'attribute' => 'protocol_version',
                'label' => 'Version',
                'format' => 'raw',
                'headerOptions' => [
                    'class' => 'text-[10px] font-bold text-outline uppercase tracking-widest '
                        . 'pb-3 text-center',
                    'style' => "font-family:'Inter',sans-serif;",
                ],
                'contentOptions' => ['class' => 'py-5 text-center align-middle'],
                'value' => function ($model) {
                return Html::tag(
                    'span',
                    Html::encode($model->protocol_version ?? '—'),
                    [
                        'class' => 'text-sm font-semibold text-primary',
                        'style' => "font-family:'Manrope',sans-serif;"
                    ]
                );
            },
            ],

            // ④ Actions (view · edit · delete)
            [
                'class' => ActionColumn::class,
                'header' => 'Actions',
                'headerOptions' => [
                    'class' => 'text-[10px] font-bold text-outline uppercase tracking-widest '
                        . 'pb-3 text-right',
                    'style' => "font-family:'Inter',sans-serif;",
                ],
                'contentOptions' => ['class' => 'py-5 align-middle'],
                'urlCreator' => function ($action, ClinicalTrial $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->id]);
            },
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                    return Html::a(
                        '<span class="material-symbols-outlined" style="font-size:18px;">visibility</span>',
                        $url,
                        [
                            'title' => 'View',
                            'encode' => false,
                            'class' => 'inline-flex items-center justify-center w-8 h-8 rounded-lg '
                                . 'hover:bg-surface-container-high text-on-surface-variant '
                                . 'transition-colors',
                        ]
                    );
                },
                    'update' => function ($url, $model, $key) {
                    return Html::a(
                        '<span class="material-symbols-outlined" style="font-size:18px;">edit</span>',
                        $url,
                        [
                            'title' => 'Edit',
                            'encode' => false,
                            'class' => 'inline-flex items-center justify-center w-8 h-8 rounded-lg '
                                . 'hover:bg-primary-fixed text-primary transition-colors',
                        ]
                    );
                },
                    'delete' => function ($url, $model, $key) {
                    return Html::a(
                        '<span class="material-symbols-outlined" style="font-size:18px;">delete</span>',
                        $url,
                        [
                            'title' => 'Delete',
                            'encode' => false,
                            'class' => 'inline-flex items-center justify-center w-8 h-8 rounded-lg '
                                . 'hover:bg-error-container text-error transition-colors',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this clinical trial? This action cannot be undone.',
                                'method' => 'post',
                            ],
                        ]
                    );
                },
                ],
            ],
        ],
    ]); ?>

</div>