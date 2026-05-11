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
        [
            'label' => '4 . Study Timeline and Location',
            'controller' => 'study-timeline',
            'action' => 'create',
            'url' => ['/study-timeline/create'],
            'icon' => 'map-pin',
        ],
        [
            'label' => '5. Investigator Team',
            'controller' => 'investigator-team',
            'action' => 'create',
            'url' => ['/investigator-team/create'],
            'icon' => 'users',
        ],
        [
            'label' => '6. Ethics and Regulatory Approvals',
            'controller' => 'ethics-approval',
            'action' => 'create',
            'url' => ['/ethics-regulatory/create'],
            'icon' => 'shield-check',
        ],
        [
            'label' => '7. Funding and Sponsorship',
            'controller' => 'funding',
            'action' => 'create',
            'url' => ['/funding/create'],
            'icon' => 'share-nodes',
        ],
        [
            'label' => '8. Study Description (Lay and Scientific)',
            'controller' => 'study-description',
            'action' => 'create',
            'url' => ['/study-description/create'],
            'icon' => 'document-text',
        ],
        [
            'label' => '9. Study Interventions and Outcomes',
            'controller' => 'study-intervention',
            'action' => 'create',
            'url' => ['/study-intervention/create'],
            'icon' => 'pills',
        ],
        [
            'label' => '10. Study Result Pulication',
            'controller' => 'study-results',
            'action' => 'create',
            'url' => ['/study-results/create'],
            'icon' => 'file-text',
        ],
        [
            'label' => '11. Open Data Access and Sharing',
            'controller' => 'open-data',
            'action' => 'create',
            'url' => ['/open-data/create'],
            'icon' => 'database',
        ]

    ]
];
