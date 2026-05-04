<?php

return [
    'adminEmail' => 'admin@example.com',
    /* * Single source of truth for the Clinical Trial registration wizard steps.
     * Consumed by:
     *   - _progress_tracker.php  (renders the step pills)
     *   - TrialsController       (next/prev routing helpers)
     *
     * Each entry maps a step to the exact controller/action pair that owns it,
     * so active-state detection is automatic via Yii::$app->controller context.
     *
     * 'controller' matches Yii::$app->controller->id   (e.g. 'trials')
     * 'action'     matches Yii::$app->controller->action->id (e.g. 'step1')
     */
    'steps' => [
        [
            'label' => 'Trial Details',
            'controller' => 'clinical-trial',
            'action' => 'create',
            'url' => ['/clinical-trial/create'],
            'icon' => 'check',
        ],
        [
            'label' => 'Study Purpose',
            'controller' => 'study-purpose',
            'action' => 'create',
            'url' => ['/study-purpose/create'],
            'icon' => 'science',
        ],
        [
            'label' => 'Study Population Eligibility',
            'controller' => 'study-population-eligibility',
            'action' => 'create',
            'url' => ['/study-population-eligibility/create'],
            'icon' => 'checklist',
        ],

    ]
];
