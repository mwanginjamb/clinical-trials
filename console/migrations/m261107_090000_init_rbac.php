<?php

use common\rbac\TrialOwnerRule;
use yii\db\Migration;
use yii\db\Query;

/**
 * Seeds the RBAC hierarchy.
 *
 *   researcher: viewTrial, createTrial, updateOwnTrial (-> updateTrial, only for trials they created)
 *   admin:      researcher + updateTrial, deleteTrial, importTrials, manageLookups
 *
 * Every existing user is assigned `researcher`. Promote admins with:
 *   php yii rbac/assign <username> admin
 *
 * Requires Yii's RBAC tables (@yii/rbac/migrations), which run first because of their older timestamps.
 */
class m261107_090000_init_rbac extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $ownerRule = new TrialOwnerRule();
        $auth->add($ownerRule);

        $viewTrial = $this->permission('viewTrial', 'View trials in the internal registry');
        $createTrial = $this->permission('createTrial', 'Register a new trial');
        $updateTrial = $this->permission('updateTrial', 'Edit any trial and its wizard steps');
        $deleteTrial = $this->permission('deleteTrial', 'Delete a trial and all its child records');
        $importTrials = $this->permission('importTrials', 'Bulk import trials from the Excel template');
        $manageLookups = $this->permission('manageLookups', 'Manage lookup tables (countries, towns, publishers, specializations)');

        $updateOwnTrial = $this->permission('updateOwnTrial', 'Edit trials the user created', $ownerRule->name);
        $auth->addChild($updateOwnTrial, $updateTrial);

        $researcher = $auth->createRole('researcher');
        $researcher->description = 'Registers trials and edits their own';
        $auth->add($researcher);
        $auth->addChild($researcher, $viewTrial);
        $auth->addChild($researcher, $createTrial);
        $auth->addChild($researcher, $updateOwnTrial);

        $admin = $auth->createRole('admin');
        $admin->description = 'Full access, including delete, bulk import and lookup tables';
        $auth->add($admin);
        $auth->addChild($admin, $researcher);
        $auth->addChild($admin, $updateTrial);
        $auth->addChild($admin, $deleteTrial);
        $auth->addChild($admin, $importTrials);
        $auth->addChild($admin, $manageLookups);

        foreach ((new Query())->select('id')->from('{{%user}}')->column() as $userId) {
            $auth->assign($researcher, $userId);
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        foreach (['admin', 'researcher'] as $role) {
            if ($item = $auth->getRole($role)) {
                $auth->remove($item);
            }
        }
        foreach (['viewTrial', 'createTrial', 'updateTrial', 'updateOwnTrial', 'deleteTrial', 'importTrials', 'manageLookups'] as $name) {
            if ($item = $auth->getPermission($name)) {
                $auth->remove($item);
            }
        }
        if ($rule = $auth->getRule('isTrialOwner')) {
            $auth->remove($rule);
        }
    }

    private function permission(string $name, string $description, ?string $ruleName = null): \yii\rbac\Permission
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission($name);
        $permission->description = $description;
        $permission->ruleName = $ruleName;
        $auth->add($permission);

        return $permission;
    }
}
