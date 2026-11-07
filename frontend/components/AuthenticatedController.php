<?php

namespace frontend\components;

use frontend\models\ClinicalTrial;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Base controller for every page behind login.
 *
 * Access is deny-by-default: EVERY action requires an authenticated user, including
 * actions added later. Do not re-introduce an `'only' => [...]` allow-list in child
 * controllers — that pattern silently left new actions (e.g. AJAX endpoints) public.
 *
 * Set {@see $requiredPermission} to additionally gate the whole controller behind an
 * RBAC permission (e.g. 'manageLookups', 'importTrials').
 *
 * Child controllers should merge with parent::behaviors() so this filter is kept.
 */
abstract class AuthenticatedController extends Controller
{
    /** RBAC permission required for every action, or null for "any logged-in user". */
    protected ?string $requiredPermission = null;

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [$this->requiredPermission ?? '@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Throws 403 unless the current user may edit the given trial (its creator, or an admin).
     *
     * @throws ForbiddenHttpException
     */
    protected function requireTrialEditAccess(ClinicalTrial $trial): void
    {
        if (!Yii::$app->user->can('updateTrial', ['trial' => $trial])) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to modify this trial.'));
        }
    }
}
