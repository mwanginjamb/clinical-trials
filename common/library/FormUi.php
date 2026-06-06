<?php
namespace common\library;

use yii\bootstrap5\Html;

/**
 * A generator class for form elements UI aspects
 * 
 * @author Francis Njambi
 * /**
 * FormUi
 *
 * Centralised Tailwind class and Yii2 ActiveForm / DataTable config factory.
 *
 * Split into two logical sections:
 *
 *   ① Form UI  — ActiveForm config, field templates, inputs, buttons, links
 *   ② Grid UI  — Table shell, th/td/tr classes, badges, chips, action buttons,
 *                stat chips, and CTA helpers for DataTables-powered views
 *
 * ┌──────────────────────────────────────────────────────────────────────┐
 * │  Design decisions                                                    │
 * │                                                                      │
 * │  • All methods are static — no instantiation needed.                 │
 * │  • Class strings use multi-line indentation for readability;         │
 * │    Tailwind's CDN JIT scans PHP strings so all classes resolve.      │
 * │  • badge() / chip() render full <span> HTML so views stay           │
 * │    expression-only: <?= FormUi::badge($label, $variant) ?>          │
 * │  • actionBtn() bakes in the delete confirmation + CSRF method        │
 * │    automatically when intent === 'delete'.                           │
 * └──────────────────────────────────────────────────────────────────────┘
 */

class FormUi
{
    public static function formConfig($id, $method = 'post'): array
    {
        return [
            'id' => $id,
            'method' => $method,
            'options' => [
                'class' => 'space-y-5 md:space-y-6',
            ],
            'fieldConfig' => self::fieldConfig()['base'],
            'enableClientValidation' => true,
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'validateOnBlur' => true,
        ];
    }

    public static function inputOptions(): array
    {
        // Shared Tailwind classes for every text-like control
        $baseControl = 'w-full bg-surface-container-low border-none border-b-2 border-transparent '
            . 'focus:border-primary focus:bg-surface-container-lowest focus:ring-0 '
            . 'transition-all text-sm py-3 px-4';

        // -----------------------------------------------------------------------
        // Single-line text input
        // -----------------------------------------------------------------------
        $text = [
            'class' => $baseControl,
        ];

        // -----------------------------------------------------------------------
        // Multi-line textarea  (set 'rows' per call to override)
        // -----------------------------------------------------------------------
        $textarea = [
            'class' => $baseControl . ' resize-none',
            'rows' => 3,
        ];

        // -----------------------------------------------------------------------
        // <select> / dropDownList
        // -----------------------------------------------------------------------
        $select = [
            'class' => $baseControl,
        ];

        // -----------------------------------------------------------------------
        // Toggle switch (checkbox rendered as an iOS-style pill)
        // Uses a custom template — see usage note below.
        //
        // In the view, wrap the field output in the toggle-row <div>
        // and suppress the auto-generated label (use 'label' => false).
        // -----------------------------------------------------------------------
        $toggle = [
            // The checkbox input itself is visually hidden; the pill is CSS-only.
            'class' => 'sr-only peer',
            'uncheck' => '0',
            // Yii2 wraps checkboxes in <label>; we handle layout externally.
            'labelOptions' => ['style' => 'display:none'],
        ];

        return [
            'text' => $text,
            'textarea' => $textarea,
            'select' => $select,
            'toggle' => $toggle,
        ];
    }


    /**
 * Holds the icon-side padding class computed by the last fieldConfig() call
 * so inputOptions() callers can retrieve it without repeating the logic.
 *
 * @internal  Set by fieldConfig(); read via iconInputClass().
 */
private static string $_iconInputPadding = '';

/**
 * Returns the input padding class that matches the last fieldConfig() icon call.
 *
 * Usage — pair with fieldConfig():
 *   $form->field($model, 'password', FormUi::fieldConfig('lock')['base'])
 *       ->passwordInput(['class' => FormUi::inputClass() . ' ' . FormUi::iconInputClass()])
 *
 * @return string  e.g. 'pr-11', 'pl-11', or '' when no icon was set.
 */
public static function iconInputClass(): string
{
    return self::$_iconInputPadding;
}

    /**
 * Field template config.
 *
 * @param string|null $icon      Material Symbol name e.g. 'lock', 'mail'.
 *                               Pass null (default) for no icon.
 * @param string      $position  'right' (default) | 'left'
 *
 * Usage (no icon — existing behaviour preserved):
 *   $form->field($model, 'name', FormUi::fieldConfig()['base'])
 *
 * Usage (with icon):
 *   $form->field($model, 'password', FormUi::fieldConfig('lock')['base'])
 *   $form->field($model, 'email',    FormUi::fieldConfig('mail', 'left')['base'])
 *
 * formConfig() still works unchanged — it calls fieldConfig() with no args.
 */
public static function fieldConfig(?string $icon = null, string $position = 'right'): array
{
    // ------------------------------------------------------------------
    // Icon injection — build the wrapper HTML if an icon was requested
    // ------------------------------------------------------------------
    $inputBlock = '{input}';

    if ($icon !== null) {
        $pos = match($position) {
            'left'  => ['wrapper' => 'absolute left-4 top-1/2 -translate-y-1/2',  'input' => 'pl-11'],
            default => ['wrapper' => 'absolute right-4 top-1/2 -translate-y-1/2', 'input' => 'pr-11'],
        };

        $iconHtml = Html::tag(
            'div',
            Html::tag('span', $icon, ['class' => 'material-symbols-outlined text-xl']),
            [
                'class' => trim(
                    $pos['wrapper'] . ' '
                    . 'text-outline opacity-40 '
                    . 'group-focus-within:text-primary group-focus-within:opacity-100 transition-all'
                ),
            ]
        );

        // Wrap {input} in a relative+group div so the icon is anchored
        // to the input and group-focus-within: variants fire correctly
        $inputBlock = Html::tag(
            'div',
            "{input}\n" . $iconHtml,
            ['class' => 'relative group']
        );

        // Stash the extra padding class so callers can retrieve it via
        // FormUi::iconInputClass($position) without re-deriving it
        self::$_iconInputPadding = $pos['input'];
    } else {
        self::$_iconInputPadding = '';
    }

    // ------------------------------------------------------------------
    // BASE field template
    // ------------------------------------------------------------------
    $baseField = [
        'template'     => "{label}\n{$inputBlock}\n{hint}\n{error}",
        'options'      => ['class' => 'space-y-2'],
        'labelOptions' => [
            'class' => 'block text-xs font-bold uppercase tracking-wider text-on-surface-variant',
        ],
        'errorOptions' => [
            'tag'   => 'p',
            'class' => 'text-xs text-error mt-1',
        ],
        'hintOptions'  => [
            'tag'   => 'p',
            'class' => 'text-xs text-on-surface-variant mt-1',
        ],
    ];

    // ------------------------------------------------------------------
    // VARIANT: no label (toggle rows, etc.)
    // ------------------------------------------------------------------
    $noLabelField = array_merge($baseField, [
        'template' => "{input}\n{error}",
        'options'  => ['class' => ''],
    ]);

    return [
        'base'    => $baseField,
        'noLabel' => $noLabelField,
    ];
}


    public static function buttonClass(): string
    {
        return '
           w-full bg-gradient-to-r from-primary to-primary-container text-white
                            rounded-xl py-3 px-4 font-bold flex items-center justify-center
                            gap-2 shadow-lg mb-6 active:scale-95 duration-200
        ';
    }

   


    /*
   |--------------------------------------------------------------------------
   | Checkbox Field Config
   |--------------------------------------------------------------------------
   */

    public static function checkboxFieldConfig(): array
    {
        return [
            'template' => "{input}\n{error}",

            'options' => [
                'class' => 'mb-0',
            ],

            'errorOptions' => [
                'class' => 'text-sm text-red-600 mt-2',
            ],
        ];
    }



    /*
    |--------------------------------------------------------------------------
    | Checkbox Input Config
    |--------------------------------------------------------------------------
    */

    public static function checkboxConfig(string $label): array
    {
        return [
            'label' => $label,

            'labelOptions' => [
                'class' => '
                    text-sm
                    text-on-surface-variant
                    select-none
                    cursor-pointer
                ',
            ],

            'class' => '
                w-4
                h-4
                rounded
                border-outline
                text-primary
                focus:ring-primary-container
                bg-surface-container-lowest
            ',

            'container' => [
                'class' => '
                    flex
                    items-center
                    gap-3
                ',
            ],
        ];
    }

 public static function linkClass($color = false, $underline = false): string
    {
        return '
            text-' . ($color ?: 'primary') . '
            font-bold
            text-sm
            hover:text-on-secondary-fixed-variant
            transition-colors
            ' . ($underline ? 'underline' : '') . '
        ';
    }

    /**
     * Link element generator
     * Usage:<?= AuthUi::link( 'Back to login',['site/login'],'block text-center mt-6') ?>
     */

    public static function link(
        string $label,
        array $url,
        string $extraClass = ''
    ): string {
        return Html::a($label, $url, [
            'class' => self::linkClass($extraClass),
        ]);
    }




     /* ══════════════════════════════════════════════════════════════════════
     │  ②  GRID / DATATABLE UI
     ╚══════════════════════════════════════════════════════════════════════ */

    /*
    |--------------------------------------------------------------------------
    | Grid container shell
    |--------------------------------------------------------------------------
    */

    /**
     * Outer wrapper class for the DataTables grid panel.
     * Provides the card surface, rounded corners, shadow, and border.
     *
     * Usage:
     *   <div class="<?= FormUi::gridContainerClass() ?>">
     */
    public static function gridContainerClass(): string
    {
        return 'bg-surface-container-low rounded-2xl overflow-hidden shadow-sm border border-surface-variant/10';
    }

    /**
     * Toolbar row above the table (export button slot + search input).
     *
     * Usage:
     *   <div class="<?= FormUi::gridToolbarClass() ?>">
     */
    public static function gridToolbarClass(): string
    {
        return 'px-4 md:px-6 pt-5 pb-3 '
             . 'flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 '
             . 'border-b border-surface-variant/20';
    }

    /**
     * Tailwind classes for the search <input> inside the toolbar.
     * Assumes a Material Symbol icon is absolutely positioned to its left;
     * the view must supply the wrapper div and the icon span.
     *
     * Usage:
     *   <input class="<?= FormUi::gridSearchClass() ?>" ... />
     */
    public static function gridSearchClass(): string
    {
        return 'w-full bg-surface-container border border-surface-variant rounded-xl '
             . 'py-2 pl-10 pr-4 text-sm '
             . 'focus:ring-2 focus:ring-primary/30 focus:border-primary/50 '
             . 'outline-none transition';
    }

    /**
     * Grid footer bar (DataTables info text + pagination row).
     *
     * Usage:
     *   <div class="<?= FormUi::gridFooterClass() ?>">
     */
    public static function gridFooterClass(): string
    {
        return 'bg-surface-container px-4 md:px-6 py-4 '
             . 'flex flex-col sm:flex-row items-center justify-between gap-4 '
             . 'border-t border-surface-variant/20';
    }


    /*
    |--------------------------------------------------------------------------
    | Table cell classes
    |--------------------------------------------------------------------------
    */

    /**
     * <th> header cell classes.
     *
     * @param bool $rightAlign  Pass true for the Actions column.
     *
     * Usage:
     *   <th class="<?= FormUi::thClass() ?>">Full Name</th>
     *   <th class="<?= FormUi::thClass(true) ?>">Actions</th>
     */
    public static function thClass(bool $rightAlign = false): string
    {
        return trim(
            'px-6 py-4 text-[11px] font-bold text-on-surface-variant '
          . 'uppercase tracking-widest whitespace-nowrap '
          . ($rightAlign ? 'text-right' : '')
        );
    }

    /**
     * <tr> body row classes — hover highlight via Tailwind group.
     *
     * Usage:
     *   <tr class="<?= FormUi::trClass() ?>">
     */
    public static function trClass(): string
    {
        return 'bg-surface-container-lowest hover:bg-surface-tint/[0.03] transition-colors group';
    }

    /**
     * <td> body cell classes — three visual variants.
     *
     * @param string $variant
     *   'primary' — bold, brand-coloured; use for IDs / key identifiers
     *   'muted'   — subdued on-surface-variant; use for dates and meta
     *   'default' — standard body text
     *
     * Usage:
     *   <td class="<?= FormUi::tdClass('primary') ?>">PT-00042</td>
     *   <td class="<?= FormUi::tdClass('muted')   ?>">12 Jan 2024</td>
     *   <td class="<?= FormUi::tdClass()          ?>">Nairobi</td>
     */
    public static function tdClass(string $variant = 'default'): string
    {
        return 'px-6 py-5 ' . match($variant) {
            'primary' => 'font-headline font-bold text-primary text-sm tracking-tight',
            'muted'   => 'text-sm text-on-surface-variant font-medium whitespace-nowrap',
            default   => 'text-sm text-on-surface',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Badges and chips
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a compact pill badge <span> — for status, category, or enum values.
     *
     * @param string $label    Display text (HTML-escaped internally).
     * @param string $variant  'secondary' | 'tertiary' | 'error' | 'warning' | 'default'
     *
     * Colour map (MD3 tokens):
     *   secondary → bg-secondary-container / text-on-secondary-container  (blue-teal)
     *   tertiary  → bg-tertiary-fixed / text-on-tertiary-fixed-variant     (warm sand)
     *   error     → bg-error-container / text-on-error-container           (red)
     *   warning   → amber tones (custom; no MD3 token in this palette)
     *   default   → bg-surface-container / text-on-surface-variant        (neutral)
     *
     * Usage:
     *   <?= FormUi::badge('Stage IV', 'error') ?>
     *   <?= FormUi::badge('African',  'secondary') ?>
     */
    public static function badge(string $label, string $variant = 'default'): string
    {
        $colors = match($variant) {
            'secondary' => 'bg-secondary-container text-on-secondary-container',
            'tertiary'  => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
            'error'     => 'bg-error-container text-on-error-container',
            'warning'   => 'bg-[#fff3cd] text-[#7a5c00]',
            default     => 'bg-surface-container text-on-surface-variant',
        };

        return Html::tag('span', Html::encode($label), [
            'class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {$colors}",
        ]);
    }

    /**
     * Renders a wider pill chip — for taxonomy / category labels (e.g. ICD codes,
     * ethnic group, place names). Slightly more padding than badge().
     *
     * @param string $label
     * @param string $variant  Same variants as badge().
     *
     * Usage:
     *   <?= FormUi::chip('C50.9 — Breast, NOS') ?>
     *   <?= FormUi::chip('Nairobi', 'secondary') ?>
     */
    public static function chip(string $label, string $variant = 'default'): string
    {
        $colors = match($variant) {
            'secondary' => 'bg-secondary-container text-on-secondary-container',
            'tertiary'  => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
            'error'     => 'bg-error-container text-on-error-container',
            'warning'   => 'bg-[#fff3cd] text-[#7a5c00]',
            default     => 'bg-surface-container text-primary',
        };

        return Html::tag('span', Html::encode($label), [
            'class' => "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {$colors}",
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Action buttons (grid row: view / edit / delete)
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a single icon action button for a grid row.
     *
     * Bakes in:
     *   • Hover colour + background per intent
     *   • CSRF method:post + JS confirm dialog when intent === 'delete'
     *
     * @param string $icon    Material Symbols icon name, e.g. 'visibility'.
     * @param array  $url     Yii2 URL array, e.g. ['view', 'id' => $model->id].
     * @param string $intent  'view' | 'edit' | 'delete'
     * @param array  $options Extra Html::a options (merged after defaults;
     *                        use to override title, add data-* attrs, etc.).
     *
     * Usage:
     *   <?= FormUi::actionBtn('visibility', ['view',   'id' => $m->id], 'view')   ?>
     *   <?= FormUi::actionBtn('edit_square',['update', 'id' => $m->id], 'edit')   ?>
     *   <?= FormUi::actionBtn('delete',     ['delete', 'id' => $m->id], 'delete') ?>
     */
    public static function actionBtn(
        string $icon,
        array  $url,
        string $intent  = 'view',
        array  $options = []
    ): string {
        $intentClass = match($intent) {
            'edit'   => 'p-2 text-outline hover:text-secondary hover:bg-surface-container rounded-lg transition-all',
            'delete' => 'p-2 text-outline hover:text-error hover:bg-error-container rounded-lg transition-all',
            default  => 'p-2 text-outline hover:text-primary hover:bg-surface-container rounded-lg transition-all',
        };

        $iconSpan = Html::tag('span', $icon, ['class' => 'material-symbols-outlined text-[20px]']);

        $defaults = ['class' => $intentClass, 'encode' => false];

        if ($intent === 'delete') {
            $defaults['data'] = [
                'confirm' => 'Are you sure you want to delete this record?',
                'method'  => 'post',
            ];
        }

        return Html::a($iconSpan, $url, array_merge($defaults, $options));
    }

    /**
     * Renders a condensed (smaller icon) action button for mobile card views.
     * Same signature as actionBtn(); icon renders at text-sm instead of text-[20px].
     *
     * Usage:
     *   <?= FormUi::actionBtnSm('visibility', ['view', 'id' => $m->id]) ?>
     */
    public static function actionBtnSm(
        string $icon,
        array  $url,
        string $intent  = 'view',
        array  $options = []
    ): string {
        $intentClass = match($intent) {
            'edit'   => 'p-2 text-outline hover:text-secondary',
            'delete' => 'p-2 text-outline hover:text-error',
            default  => 'p-2 text-outline hover:text-primary',
        };

        $iconSpan = Html::tag('span', $icon, ['class' => 'material-symbols-outlined text-sm']);
        $defaults = ['class' => $intentClass, 'encode' => false];

        if ($intent === 'delete') {
            $defaults['data'] = [
                'confirm' => 'Are you sure you want to delete this record?',
                'method'  => 'post',
            ];
        }

        return Html::a($iconSpan, $url, array_merge($defaults, $options));
    }


    /*
    |--------------------------------------------------------------------------
    | CTA page-header button ("New Patient", "New Abstract", etc.)
    |--------------------------------------------------------------------------
    */

    /**
     * Renders the gradient "New …" button used in page headers.
     *
     * @param string $label  Visible text, e.g. 'New Patient'.
     * @param string $icon   Material Symbol name, e.g. 'add', 'person_add'.
     * @param array  $url    Yii2 URL array.
     *
     * Usage:
     *   <?= FormUi::ctaButton('New Patient', 'person_add', ['patient/create']) ?>
     */
    public static function ctaButton(string $label, string $icon, array $url): string
    {
        $inner = Html::tag('span', $icon,         ['class' => 'material-symbols-outlined'])
               . Html::tag('span', Html::encode($label));

        return Html::a($inner, $url, [
            'class'  => 'inline-flex items-center justify-center gap-2 px-6 py-3.5 '
                      . 'bg-gradient-to-br from-primary to-primary-container text-white '
                      . 'rounded-xl font-bold shadow-[0_12px_32px_rgba(0,26,72,0.12)] '
                      . 'hover:scale-[1.02] transition-all active:scale-95 w-full sm:w-auto',
            'encode' => false,
        ]);
    }

    // Submit button

    public static function submitButton(string $label, string $icon): string
    {
        $inner = Html::tag('span', $icon, ['class' => 'material-symbols-outlined'])
               . Html::tag('span', Html::encode($label));

        return Html::submitButton($inner, [
            'class' => 'editorial-gradient w-full py-4 rounded-xl text-on-primary font-bold
                        tracking-tight text-lg shadow-[0_8px_20px_-4px_rgba(0,59,83,0.3)]
                        hover:scale-[1.02] active:scale-[0.98] transition-all duration-200
                        flex items-center justify-center gap-2',
            'encode' => false,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Stat chip — summary metric cards above the grid
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a stat chip card (icon circle + label + value).
     *
     * @param string $icon       Material Symbol name, e.g. 'group'.
     * @param string $label      Small uppercase descriptor, e.g. 'Total Patients'.
     * @param string $value      Display value, e.g. '12,842'.
     * @param string $iconBg     Tailwind bg class for the icon circle.
     * @param string $iconColor  Tailwind text class for the icon.
     *
     * Usage:
     *   <?= FormUi::statChip('group', 'Total Patients', number_format($count)) ?>
     *   <?= FormUi::statChip(
     *         'pending_actions', 'Pending Review', '48',
     *         'bg-tertiary-fixed', 'text-on-tertiary-fixed-variant'
     *       ) ?>
     */
    public static function statChip(
        string $icon,
        string $label,
        string $value,
        string $iconBg    = 'bg-secondary-container',
        string $iconColor = 'text-on-secondary-container'
    ): string {
        $circle = Html::tag(
            'div',
            Html::tag('span', $icon, ['class' => 'material-symbols-outlined']),
            ['class' => "h-10 w-10 rounded-full {$iconBg} flex items-center justify-center {$iconColor} shrink-0"]
        );

        $text = Html::tag('div',
            Html::tag('p', Html::encode($label),
                ['class' => 'text-[10px] uppercase font-bold text-outline-variant tracking-wider'])
          . Html::tag('p', Html::encode($value),
                ['class' => 'text-xl font-headline font-bold text-primary']),
        []);

        return Html::tag('div', $circle . $text, [
            'class' => 'bg-surface-container-low p-4 rounded-xl flex items-center gap-4 border border-surface-variant/10',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Breadcrumb helper
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a minimal breadcrumb trail.
     *
     * @param array $crumbs  [ 'Database', 'Patients' ] — last item is styled as active.
     *
     * Usage:
     *   <?= FormUi::breadcrumb(['Database', 'Patients']) ?>
     */
    public static function breadcrumb(array $crumbs): string
    {
        $parts = [];
        $last  = array_pop($crumbs);

        foreach ($crumbs as $crumb) {
            $parts[] = Html::tag('span', Html::encode($crumb));
            $parts[] = Html::tag('span', 'chevron_right', [
                'class' => 'material-symbols-outlined text-[10px]',
            ]);
        }

        $parts[] = Html::tag('span', Html::encode($last), ['class' => 'text-primary']);

        return Html::tag('nav', implode('', $parts), [
            'class' => 'flex items-center gap-2 text-xs font-label font-medium text-outline mb-3 uppercase tracking-widest',
        ]);
    }





}