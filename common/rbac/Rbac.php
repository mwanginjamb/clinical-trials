<?php

namespace common\rbac;

use Yii;
use yii\db\Query;
use yii\rbac\Item;

/**
 * RBAC names used by code, plus helpers that keep the system usable.
 *
 * Roles
 *   admin       – full access; manages RBAC (manageRbac)
 *   researcher  – default role, given to every user once their account is validated
 *
 * System items
 *   A role or permission that code depends on (a can() check, an AccessControl rule, a
 *   business rule) is flagged in the database: auth_item.data = ['system' => true].
 *   The RBAC screens let admins edit its description and grants but never rename or
 *   delete it, because that would silently break access checks.
 *
 *   Items created in the UI are never system items and need no code changes.
 *
 *   When code starts depending on a NEW item, create it in a migration and flag it:
 *       $perm = $auth->createPermission('exportTrials');
 *       Rbac::markSystem($perm);
 *       $auth->add($perm);
 *   (Optionally add a constant below so code can reference it without typos.)
 */
final class Rbac
{
    /** Key in auth_item.data that marks an item the code depends on. */
    public const SYSTEM_FLAG = 'system';

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

    /**
     * Whether a role/permission is flagged as a system item (see class docs).
     * Accepts the item itself or its name; unknown names are not system items.
     */
    public static function isSystemItem(Item|string|null $item): bool
    {
        if (is_string($item)) {
            $item = self::item($item);
        }
        return $item !== null
            && is_array($item->data)
            && !empty($item->data[self::SYSTEM_FLAG]);
    }

    /**
     * Flags an item as a system item. Sets the in-memory data only: call before
     * $auth->add($item), or follow with $auth->update($item->name, $item).
     * Intended for migrations, not the UI.
     */
    public static function markSystem(Item $item): Item
    {
        $item->data = array_merge(is_array($item->data) ? $item->data : [], [self::SYSTEM_FLAG => true]);
        return $item;
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
    public static function item(string $name): ?Item
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
