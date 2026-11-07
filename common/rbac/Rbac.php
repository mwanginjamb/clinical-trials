<?php

namespace common\rbac;

use Yii;
use yii\db\Query;

/**
 * Single source of truth for RBAC names and the rules that keep the system usable.
 *
 * Roles
 *   admin       – full access; manages RBAC (manageRbac)
 *   researcher  – default role, given to every user once their account is validated
 *
 * "System" items are referenced from code (controllers, views, rules). They can be
 * edited (descriptions, grants) from the RBAC screens but never renamed or deleted,
 * because that would silently break access checks.
 */
final class Rbac
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_RESEARCHER = 'researcher';

    /** Role every validated account receives. Admins escalate from here. */
    public const DEFAULT_ROLE = self::ROLE_RESEARCHER;

    public const PERM_VIEW_TRIAL = 'viewTrial';
    public const PERM_CREATE_TRIAL = 'createTrial';
    public const PERM_UPDATE_TRIAL = 'updateTrial';
    public const PERM_UPDATE_OWN_TRIAL = 'updateOwnTrial';
    public const PERM_DELETE_TRIAL = 'deleteTrial';
    public const PERM_IMPORT_TRIALS = 'importTrials';
    public const PERM_MANAGE_LOOKUPS = 'manageLookups';
    public const PERM_MANAGE_RBAC = 'manageRbac';

    public const SYSTEM_ROLES = [self::ROLE_ADMIN, self::ROLE_RESEARCHER];

    public const SYSTEM_PERMISSIONS = [
        self::PERM_VIEW_TRIAL,
        self::PERM_CREATE_TRIAL,
        self::PERM_UPDATE_TRIAL,
        self::PERM_UPDATE_OWN_TRIAL,
        self::PERM_DELETE_TRIAL,
        self::PERM_IMPORT_TRIALS,
        self::PERM_MANAGE_LOOKUPS,
        self::PERM_MANAGE_RBAC,
    ];

    public static function isSystemItem(string $name): bool
    {
        return in_array($name, self::SYSTEM_ROLES, true) || in_array($name, self::SYSTEM_PERMISSIONS, true);
    }

    /**
     * Gives the default role to a user who has no role yet. Safe to call repeatedly.
     * Called once an account is validated (email verified, or activated by an admin).
     */
    public static function assignDefaultRole(int $userId): bool
    {
        $auth = Yii::$app->authManager;
        if ($auth->getRolesByUser($userId) !== []) {
            return false;
        }
        $role = $auth->getRole(self::DEFAULT_ROLE);
        if ($role === null) {
            Yii::warning('Default role "' . self::DEFAULT_ROLE . '" missing; run migrations.', __METHOD__);
            return false;
        }
        $auth->assign($role, $userId);

        return true;
    }

    /** A role or permission by name (DbManager::getItem() is protected). */
    public static function item(string $name): ?\yii\rbac\Item
    {
        $auth = Yii::$app->authManager;
        return $auth->getRole($name) ?? $auth->getPermission($name);
    }

    /**
     * Names of every item (role or permission) that directly has $name as a child.
     * DbManager has no public getParents(), so read auth_item_child directly.
     *
     * @return string[]
     */
    public static function parentsOf(string $name): array
    {
        /** @var \yii\rbac\DbManager $auth */
        $auth = Yii::$app->authManager;

        return (new Query())
            ->select('parent')
            ->from($auth->itemChildTable)
            ->where(['child' => $name])
            ->orderBy('parent')
            ->column($auth->db);
    }

    /** Ids of users holding the admin role. */
    public static function adminIds(): array
    {
        return array_map('intval', Yii::$app->authManager->getUserIdsByRole(self::ROLE_ADMIN));
    }
}
