<?php

namespace frontend\models;

use common\rbac\Rbac;
use Yii;
use yii\base\Model;

/**
 * Replaces the set of roles assigned to one user.
 * Guards against locking everyone out: an admin cannot drop their own admin role, and
 * the last remaining admin cannot be demoted.
 */
class UserRoleForm extends Model
{
    public $userId;
    /** @var string[] */
    public $roles = [];

    public function rules()
    {
        return [
            [['roles'], 'default', 'value' => []],
            [['roles'], 'each', 'rule' => ['in', 'range' => array_keys(Yii::$app->authManager->getRoles())]],
            [['roles'], 'validateAdminKept'],
        ];
    }

    public function validateAdminKept($attribute)
    {
        $keepsAdmin = in_array(Rbac::ROLE_ADMIN, (array) $this->$attribute, true);
        if ($keepsAdmin) {
            return;
        }

        $admins = Rbac::adminIds();
        $isAdmin = in_array((int) $this->userId, $admins, true);
        if (!$isAdmin) {
            return;
        }
        if ((int) $this->userId === (int) Yii::$app->user->id) {
            $this->addError($attribute, 'You cannot remove your own admin role. Ask another admin to do it.');
        } elseif (count($admins) <= 1) {
            $this->addError($attribute, 'This is the only admin. Promote another user to admin first.');
        }
    }

    public function attributeLabels()
    {
        return ['roles' => 'Roles'];
    }
}
