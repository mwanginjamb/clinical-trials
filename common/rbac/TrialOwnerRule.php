<?php

namespace common\rbac;

use yii\rbac\Rule;

/**
 * Passes when the current user created the trial passed as `$params['trial']`.
 *
 * Usage: Yii::$app->user->can('updateTrial', ['trial' => $trial])
 * (researchers reach `updateTrial` only through `updateOwnTrial`, which carries this rule).
 */
class TrialOwnerRule extends Rule
{
    public $name = 'isTrialOwner';

    public function execute($user, $item, $params)
    {
        $trial = $params['trial'] ?? null;

        return $trial !== null
            && $trial->created_by !== null
            && (int) $trial->created_by === (int) $user;
    }
}
