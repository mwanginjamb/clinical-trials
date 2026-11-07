<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;

/**
 * Base class for every table that hangs off a clinical trial via `trial_id`
 * (study_purpose, funding, investigator_team, ...).
 *
 * Guards writes at the model layer: in a web request, inserting, updating or deleting a
 * child row requires `updateTrial` on the parent trial (owner or admin). This holds no
 * matter which controller, AJAX endpoint or wizard step performs the write. Moving a
 * row to another trial requires access to BOTH the old and the new trial.
 *
 * Console commands are not guarded (no logged-in user).
 *
 * @property int|null $trial_id
 */
abstract class TrialChildRecord extends ActiveRecord
{
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->assertCanEditTrial($this->trial_id);
        if (!$insert && $this->isAttributeChanged('trial_id', false)) {
            $this->assertCanEditTrial($this->getOldAttribute('trial_id'));
        }

        return true;
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        $this->assertCanEditTrial($this->getOldAttribute('trial_id'));

        return true;
    }

    /**
     * @throws ForbiddenHttpException
     */
    protected function assertCanEditTrial($trialId): void
    {
        if (!Yii::$app instanceof \yii\web\Application) {
            return;
        }
        if ($trialId === null || $trialId === '') {
            return; // nothing to own yet; required/exist validators handle a missing trial
        }

        $trial = ClinicalTrial::findOne($trialId);
        if ($trial === null) {
            return; // the `exist` validator reports unknown trials
        }

        if (!Yii::$app->user->can('updateTrial', ['trial' => $trial])) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to modify this trial.'));
        }
    }
}
