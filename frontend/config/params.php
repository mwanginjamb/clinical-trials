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
            'createUrl' => ['/clinical-trial/create'],
            'updateUrl' => ['/clinical-trial/update'],
            'icon' => 'check',
        ],
        [
            'label' => 'Study Purpose',
            'controller' => 'study-purpose',
            'action' => 'create',
            'createUrl' => ['/study-purpose/create'],
            'updateUrl' => ['/study-purpose/update'],
            'icon' => 'science',
        ],
        [
            'label' => 'Study Population Eligibility',
            'controller' => 'study-population-eligibility',
            'action' => 'create',
            'createUrl' => ['/study-population-eligibility/create'],
            'updateUrl' => ['/study-population-eligibility/update'],
            'icon' => 'checklist',
        ],
        [
            'label' => '4 . Study Timeline and Location',
            'controller' => 'study-timeline',
            'action' => 'create',
            'createUrl' => ['/study-timeline/create'],
            'updateUrl' => ['/study-timeline/update'],
            'icon' => 'map-pin',
        ],
        [
            'label' => '5. Investigator Team',
            'controller' => 'investigator-team',
            'action' => 'create',
            'createUrl' => ['/investigator-team/create'],
            'updateUrl' => ['/investigator-team/update'],
            'icon' => 'users',
        ],
        [
            'label' => '6. Ethics and Regulatory Approvals',
            'controller' => 'ethical-approval',
            'action' => 'create',
            'createUrl' => ['/ethical-approval/create'],
            'updateUrl' => ['/ethical-approval/update'],
            'icon' => 'shield-check',
        ],
        [
            'label' => '7. Funding and Sponsorship',
            'controller' => 'funding',
            'action' => 'create',
            'createUrl' => ['/funding/create'],
            'updateUrl' => ['/funding/update'],
            'icon' => 'share-nodes',
        ],
        [
            'label' => '8. Study Description (Lay and Scientific)',
            'controller' => 'study-description',
            'action' => 'create',
            'createUrl' => ['/study-description/create'],
            'updateUrl' => ['/study-description/update'],
            'icon' => 'document-text',
        ],
        [
            'label' => '9. Study Interventions and Outcomes',
            'controller' => 'study-intervention',
            'action' => 'create',
            'createUrl' => ['/study-intervention/create'],
            'updateUrl' => ['/study-intervention/update'],
            'icon' => 'pills',
        ],
        [
            'label' => '10. Study Result Pulication',
            'controller' => 'study-results',
            'action' => 'create',
            'createUrl' => ['/study-results/create'],
            'updateUrl' => ['/study-results/update'],
            'icon' => 'file-text',
        ],
        [
            'label' => '11. Open Data Access and Sharing',
            'controller' => 'opendata-access',
            'action' => 'create',
            'createUrl' => ['/opendata-access/create'],
            'updateUrl' => ['/opendata-access/update'],
            'icon' => 'database',
        ]

    ]
];
