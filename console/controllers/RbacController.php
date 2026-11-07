<?php

namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Manage user roles.
 *
 *   php yii rbac/assign <username> <role>   e.g. php yii rbac/assign jdoe admin
 *   php yii rbac/revoke <username> <role>
 *   php yii rbac/list
 */
class RbacController extends Controller
{
    public function actionAssign(string $username, string $roleName): int
    {
        [$user, $role] = $this->resolve($username, $roleName);
        if ($user === null) {
            return ExitCode::DATAERR;
        }

        $auth = Yii::$app->authManager;
        if ($auth->getAssignment($role->name, $user->id) !== null) {
            $this->stdout("{$username} already has role '{$roleName}'.\n");
            return ExitCode::OK;
        }
        $auth->assign($role, $user->id);
        $this->stdout("Assigned '{$roleName}' to {$username}.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    public function actionRevoke(string $username, string $roleName): int
    {
        [$user, $role] = $this->resolve($username, $roleName);
        if ($user === null) {
            return ExitCode::DATAERR;
        }

        Yii::$app->authManager->revoke($role, $user->id);
        $this->stdout("Revoked '{$roleName}' from {$username}.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    public function actionList(): int
    {
        $auth = Yii::$app->authManager;
        foreach (User::find()->orderBy('username')->all() as $user) {
            $roles = implode(', ', array_keys($auth->getRolesByUser($user->id))) ?: '(none)';
            $this->stdout(str_pad($user->username, 30) . $roles . "\n");
        }

        return ExitCode::OK;
    }

    private function resolve(string $username, string $roleName): array
    {
        $user = User::findOne(['username' => $username]);
        if ($user === null) {
            $this->stderr("User '{$username}' not found.\n", Console::FG_RED);
            return [null, null];
        }
        $role = Yii::$app->authManager->getRole($roleName);
        if ($role === null) {
            $this->stderr("Role '{$roleName}' not found.\n", Console::FG_RED);
            return [null, null];
        }

        return [$user, $role];
    }
}
