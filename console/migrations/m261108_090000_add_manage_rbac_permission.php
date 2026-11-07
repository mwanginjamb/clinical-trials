<?php

use yii\db\Migration;

/**
 * Adds `manageRbac` (access to the RBAC administration screens) and grants it to `admin`.
 * Idempotent, so it is safe on environments where it was added by hand.
 */
class m261108_090000_add_manage_rbac_permission extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $permission = $auth->getPermission('manageRbac');
        if ($permission === null) {
            $permission = $auth->createPermission('manageRbac');
            $permission->description = 'Manage roles, permissions, user role assignments and system users';
            $auth->add($permission);
        }

        $admin = $auth->getRole('admin');
        if ($admin !== null && !$auth->hasChild($admin, $permission)) {
            $auth->addChild($admin, $permission);
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        if ($permission = $auth->getPermission('manageRbac')) {
            $auth->remove($permission);
        }
    }
}
