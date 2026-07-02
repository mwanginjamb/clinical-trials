<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * DashAsset — Clinical Curator
 *
 * CSS pipeline
 * ------------
 * During development, run `npm run dev` from the project root to watch and
 * recompile `web/css/src/app.css` → `web/css/app.css` via the Tailwind CLI.
 * For production, run `npm run build` to produce a minified output.
 *
 * External fonts / icons (Google Fonts & Material Symbols) are registered
 * directly inside the main layout via `$this->registerCssFile()` so they
 * load in <head> before any local CSS, avoiding a flash of unstyled text.
 */
class DashAsset extends AssetBundle
{
    /** @var string Resolved at runtime to the `web/` directory absolute path. */
    public $basePath = '@webroot';

    /** @var string Resolved at runtime to the root-relative web URL. */
    public $baseUrl = '@web';

    /**
     * Local stylesheets.
     * `app.css` is the Tailwind CLI compiled output — commit this file so the
     * app works without a Node build step in production deploys.
     */
    public $css = [
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;family=Manrope:wght@400;500;600;700;800&amp;display=swap',
        'css/tailwind.css',
    ];

    /**
     * Local scripts.
     * Loaded with `YII_DEBUG` position awareness; defaults to
     * `POS_END` (before </body>) for performance.
     */
    public $js = [
        'js/app.js',
        '//cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js'
    ];

    /** @var array Yii asset dependencies (none required at this time). */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}