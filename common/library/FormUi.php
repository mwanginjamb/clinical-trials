<?php
namespace common\library;

/**
 * A generator class for form elements UI aspects
 * 
 * @author Francis Njambi
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
     * Field template Config
     * Usage: Shared field config — preserves the "bottom-border only" input aesthetic
     * Example: <?= $form->field($model, 'username', FormUi::fieldTemplateConfig("{label}\n{input}\n{error}"))->textInput(['class' => FormUi::inputClass()]) ?>
     */

    public static function fieldConfig(): array
    {

        // ---------------------------------------------------------------------------
        // BASE field template (wraps label + input + hint + error)
        // ---------------------------------------------------------------------------
        $baseField = [
            'template' => "{label}\n{input}\n{hint}\n{error}",
            'options' => ['class' => 'space-y-2'],          // <div> wrapper
            'labelOptions' => [
                'class' => 'block text-xs font-bold uppercase tracking-wider text-on-surface-variant',
            ],
            'errorOptions' => [
                'tag' => 'p',
                'class' => 'text-xs text-error mt-1',
            ],
            'hintOptions' => [
                'tag' => 'p',
                'class' => 'text-xs text-on-surface-variant mt-1',
            ],
        ];
        // ---------------------------------------------------------------------------
        // VARIANT: no label (for toggle rows that have their own inline label)
        // ---------------------------------------------------------------------------
        $noLabelField = array_merge($baseField, [
            'template' => "{input}\n{error}",
            'options' => ['class' => ''],
        ]);


        return [
            'base' => $baseField,
            'noLabel' => $noLabelField,
        ];
    }


    public static function buttonClass(): string
    {
        return '
            w-full
            py-4
            bg-gradient-to-r
            from-primary
            to-primary-container
            text-on-primary
            font-headline
            font-bold
            text-lg
            rounded-lg
            shadow-lg
            hover:shadow-xl
            transition-all
            active:scale-[0.98]
            flex
            items-center
            justify-center
            gap-3
        ';
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





}