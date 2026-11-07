<?php

namespace frontend\controllers;

use common\models\User;
use common\rbac\Rbac;
use frontend\components\AuthenticatedController;
use frontend\models\PermissionForm;
use frontend\models\RoleForm;
use frontend\models\UserRoleForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\NotFoundHttpException;

/**
 * RBAC administration: roles, permissions, hierarchy, user-role assignment, system users.
 *
 * Only users with `manageRbac` (granted to the admin role) can reach any action.
 * System roles/permissions (see common\rbac\Rbac) can be edited but not renamed or deleted,
 * and the admin role always keeps `manageRbac` so the screens can't lock themselves out.
 */
class RbacController extends AuthenticatedController
{
    protected ?string $requiredPermission = Rbac::PERM_MANAGE_RBAC;

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-role' => ['POST'],
                    'delete-permission' => ['POST'],
                    'set-user-status' => ['POST'],
                ],
            ],
        ]);
    }

    private function auth(): \yii\rbac\DbManager
    {
        return Yii::$app->authManager;
    }

    /* ═════════════════════════════════ Roles ═════════════════════════════════ */

    public function actionIndex()
    {
        $auth = $this->auth();
        $rows = [];
        foreach ($auth->getRoles() as $role) {
            $children = $auth->getChildren($role->name);
            $rows[] = [
                'name' => $role->name,
                'description' => $role->description,
                'permissions' => array_keys(array_filter($children, fn($c) => $c instanceof Permission)),
                'childRoles' => array_keys(array_filter($children, fn($c) => $c instanceof Role)),
                'effectiveCount' => count($auth->getPermissionsByRole($role->name)),
                'userCount' => count($auth->getUserIdsByRole($role->name)),
                'system' => in_array($role->name, Rbac::SYSTEM_ROLES, true),
            ];
        }

        return $this->render('index', [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $rows,
                'key' => 'name',
                'sort' => ['attributes' => ['name', 'userCount'], 'defaultOrder' => ['name' => SORT_ASC]],
                'pagination' => ['pageSize' => 20],
            ]),
        ]);
    }

    public function actionCreateRole()
    {
        $auth = $this->auth();
        $model = new RoleForm(['scenario' => RoleForm::SCENARIO_CREATE]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = $auth->db->beginTransaction();
            try {
                $role = $auth->createRole($model->role_name);
                $role->description = $model->description;
                $auth->add($role);
                $this->syncPermissionChildren($role, (array) $model->permissions);
                $transaction->commit();

                Yii::$app->session->setFlash('success', "Role '{$role->name}' created.");
                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Could not create role: ' . $e->getMessage());
            }
        }

        return $this->render('create-role', [
            'model' => $model,
            'permissions' => $auth->getPermissions(),
        ]);
    }

    public function actionUpdateRole($name)
    {
        $auth = $this->auth();
        $role = $this->findRole($name);

        $model = new RoleForm(['scenario' => RoleForm::SCENARIO_UPDATE]);
        $model->originalName = $role->name;
        $model->role_name = $role->name;
        $model->description = $role->description;
        $model->permissions = $this->directPermissionNames($role->name);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = $auth->db->beginTransaction();
            try {
                // update() renames in place; MySQL cascades the new name to children and assignments.
                $role->name = $model->role_name;
                $role->description = $model->description;
                $auth->update($model->originalName, $role);

                // Only direct permissions change here; child roles (admin → researcher) are kept.
                $this->syncPermissionChildren($role, (array) $model->permissions);
                $this->ensureAdminKeepsRbac();
                $transaction->commit();

                Yii::$app->session->setFlash('success', "Role '{$role->name}' updated.");
                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Could not update role: ' . $e->getMessage());
            }
        }

        return $this->render('update-role', [
            'model' => $model,
            'permissions' => $auth->getPermissions(),
            'isUpdate' => true,
        ]);
    }

    public function actionDeleteRole($name)
    {
        $role = $this->findRole($name);

        if (in_array($role->name, Rbac::SYSTEM_ROLES, true)) {
            Yii::$app->session->setFlash('error', "'{$role->name}' is a system role and cannot be deleted.");
            return $this->redirect(['index']);
        }

        $users = count($this->auth()->getUserIdsByRole($role->name));
        // remove() also deletes its hierarchy links and user assignments.
        if ($this->auth()->remove($role)) {
            Yii::$app->session->setFlash('success', "Role '{$role->name}' deleted"
                . ($users ? " and unassigned from {$users} user(s)." : '.'));
        } else {
            Yii::$app->session->setFlash('error', "Failed to delete role '{$role->name}'.");
        }

        return $this->redirect(['index']);
    }

    /**
     * Edit the direct children of a role (roles + permissions) or a permission (permissions).
     */
    public function actionManageHierarchy($name)
    {
        $auth = $this->auth();
        $item = $auth->getRole($name) ?? $auth->getPermission($name);
        if ($item === null) {
            throw new NotFoundHttpException("Role or permission '{$name}' not found.");
        }
        $isRole = $item instanceof Role;
        $backRoute = $isRole ? ['index'] : ['permissions'];

        // Roles may contain roles and permissions; permissions may only contain permissions.
        $candidates = $isRole ? array_merge($auth->getRoles(), $auth->getPermissions()) : $auth->getPermissions();
        unset($candidates[$item->name]);

        if (Yii::$app->request->isPost) {
            $selected = array_intersect((array) Yii::$app->request->post('children', []), array_keys($candidates));
            $current = array_keys($auth->getChildren($item->name));

            $transaction = $auth->db->beginTransaction();
            try {
                foreach (array_diff($current, $selected) as $childName) {
                    $auth->removeChild($item, Rbac::item($childName));
                }
                foreach (array_diff($selected, $current) as $childName) {
                    $child = $candidates[$childName];
                    if (!$auth->canAddChild($item, $child)) {
                        throw new \RuntimeException("Adding '{$childName}' would create a loop.");
                    }
                    $auth->addChild($item, $child);
                }
                $this->ensureAdminKeepsRbac();
                $transaction->commit();

                Yii::$app->session->setFlash('success', "Hierarchy for '{$item->name}' updated.");
                return $this->redirect($backRoute);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Could not update hierarchy: ' . $e->getMessage());
            }
        }

        return $this->render('manage-hierarchy', [
            'item' => $item,
            'isRole' => $isRole,
            'allItems' => $candidates,
            'currentChildren' => $auth->getChildren($item->name),
            'backRoute' => $backRoute,
        ]);
    }

    /* ═══════════════════════════════ Assignments ═══════════════════════════════ */

    /** Users with their roles. */
    public function actionUserRoles($q = null)
    {
        $dataProvider = $this->userDataProvider($q);

        return $this->render('assignment/user-roles', [
            'dataProvider' => $dataProvider,
            'rolesByUser' => $this->rolesByUser($dataProvider->getKeys()),
            'allRoles' => $this->auth()->getRoles(),
            'q' => $q,
        ]);
    }

    public function actionAssignRole($userId)
    {
        $auth = $this->auth();
        $user = $this->findUser($userId);

        $model = new UserRoleForm();
        $model->userId = $user->id;
        $model->roles = array_keys($auth->getRolesByUser($user->id));

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = $auth->db->beginTransaction();
            try {
                $auth->revokeAll($user->id);
                foreach ((array) $model->roles as $roleName) {
                    $auth->assign($auth->getRole($roleName), $user->id);
                }
                $transaction->commit();

                Yii::$app->session->setFlash('success', "Roles for {$user->username} updated.");
                return $this->redirect(['user-roles']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Could not update roles: ' . $e->getMessage());
            }
        }

        return $this->render('assignment/assign-role', [
            'model' => $model,
            'user' => $user,
            'allRoles' => $auth->getRoles(),
        ]);
    }

    /* ═══════════════════════════════ Permissions ═══════════════════════════════ */

    public function actionPermissions()
    {
        $auth = $this->auth();
        $roles = $auth->getRoles();
        $rows = [];
        foreach ($auth->getPermissions() as $permission) {
            $parents = Rbac::parentsOf($permission->name);
            $rows[] = [
                'name' => $permission->name,
                'description' => $permission->description,
                'rule' => $permission->ruleName,
                'roles' => array_values(array_filter($parents, fn($p) => isset($roles[$p]))),
                'parentPermissions' => array_values(array_filter($parents, fn($p) => !isset($roles[$p]))),
                'system' => in_array($permission->name, Rbac::SYSTEM_PERMISSIONS, true),
            ];
        }

        return $this->render('permissions/index', [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $rows,
                'key' => 'name',
                'sort' => ['attributes' => ['name'], 'defaultOrder' => ['name' => SORT_ASC]],
                'pagination' => ['pageSize' => 20],
            ]),
        ]);
    }

    public function actionCreatePermission()
    {
        $auth = $this->auth();
        $model = new PermissionForm(['scenario' => PermissionForm::SCENARIO_CREATE]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = $auth->db->beginTransaction();
            try {
                $permission = $auth->createPermission($model->name);
                $permission->description = $model->description;
                $auth->add($permission);
                $this->syncParents($permission, (array) $model->parents);
                $transaction->commit();

                Yii::$app->session->setFlash('success', "Permission '{$permission->name}' created.");
                return $this->redirect(['permissions']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Could not create permission: ' . $e->getMessage());
            }
        }

        return $this->render('permissions/create', [
            'model' => $model,
            'parentOptions' => $this->parentOptions(),
        ]);
    }

    public function actionUpdatePermission($name)
    {
        $auth = $this->auth();
        $permission = $auth->getPermission($name);
        if ($permission === null) {
            Yii::$app->session->setFlash('error', "Permission '{$name}' not found.");
            return $this->redirect(['permissions']);
        }

        $model = new PermissionForm(['scenario' => PermissionForm::SCENARIO_UPDATE]);
        $model->originalName = $permission->name;
        $model->name = $permission->name;
        $model->description = $permission->description;
        $model->parents = Rbac::parentsOf($permission->name);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = $auth->db->beginTransaction();
            try {
                // Renames in place, keeping the rule, children, parents and role grants.
                $permission->name = $model->name;
                $permission->description = $model->description;
                $auth->update($model->originalName, $permission);

                $this->syncParents($permission, (array) $model->parents);
                $this->ensureAdminKeepsRbac();
                $transaction->commit();

                Yii::$app->session->setFlash('success', "Permission '{$permission->name}' updated.");
                return $this->redirect(['permissions']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Could not update permission: ' . $e->getMessage());
            }
        }

        return $this->render('permissions/update', [
            'model' => $model,
            'parentOptions' => $this->parentOptions($model->originalName),
        ]);
    }

    public function actionDeletePermission($name)
    {
        $permission = $this->auth()->getPermission($name);

        if ($permission === null) {
            Yii::$app->session->setFlash('error', "Permission '{$name}' not found.");
        } elseif (in_array($permission->name, Rbac::SYSTEM_PERMISSIONS, true)) {
            Yii::$app->session->setFlash('error', "'{$permission->name}' is used by the application and cannot be deleted.");
        } elseif ($this->auth()->remove($permission)) {
            Yii::$app->session->setFlash('success', "Permission '{$permission->name}' deleted.");
        } else {
            Yii::$app->session->setFlash('error', "Failed to delete permission '{$permission->name}'.");
        }

        return $this->redirect(['permissions']);
    }

    /* ═══════════════════════════════ System users ═══════════════════════════════ */

    public function actionUsers($q = null, $status = null)
    {
        $dataProvider = $this->userDataProvider($q, $status);

        return $this->render('users', [
            'dataProvider' => $dataProvider,
            'rolesByUser' => $this->rolesByUser($dataProvider->getKeys()),
            'q' => $q,
            'status' => $status,
        ]);
    }

    /**
     * Activate (also grants the default role, like email verification) or disable an account.
     */
    public function actionSetUserStatus($id, $status)
    {
        $user = $this->findUser($id);
        $status = (int) $status;

        if (!in_array($status, [User::STATUS_ACTIVE, User::STATUS_DELETED], true)) {
            Yii::$app->session->setFlash('error', 'Unsupported status.');
        } elseif ((int) $user->id === (int) Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot change the status of your own account.');
        } elseif ($status === User::STATUS_DELETED
            && in_array((int) $user->id, Rbac::adminIds(), true) && count(Rbac::adminIds()) <= 1) {
            Yii::$app->session->setFlash('error', 'This is the only admin and cannot be disabled.');
        } else {
            $user->status = $status;
            $user->save(false);
            if ($status === User::STATUS_ACTIVE) {
                $granted = Rbac::assignDefaultRole($user->id);
                Yii::$app->session->setFlash('success', "{$user->username} activated"
                    . ($granted ? ' and given the ' . Rbac::DEFAULT_ROLE . ' role.' : '.'));
            } else {
                Yii::$app->session->setFlash('success', "{$user->username} disabled; they can no longer sign in.");
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['users']);
    }

    /* ═══════════════════════════════ Helpers ═══════════════════════════════ */

    private function findRole(string $name): Role
    {
        $role = $this->auth()->getRole($name);
        if ($role === null) {
            throw new NotFoundHttpException("Role '{$name}' not found.");
        }
        return $role;
    }

    private function findUser($id): User
    {
        $user = User::findOne((int) $id);
        if ($user === null) {
            throw new NotFoundHttpException('User not found.');
        }
        return $user;
    }

    /** @return string[] permissions that are DIRECT children of the role */
    private function directPermissionNames(string $roleName): array
    {
        return array_keys(array_filter(
            $this->auth()->getChildren($roleName),
            fn($child) => $child instanceof Permission
        ));
    }

    /** Make the role's direct permission children exactly $names (child roles untouched). */
    private function syncPermissionChildren(Role $role, array $names): void
    {
        $auth = $this->auth();
        $current = $this->directPermissionNames($role->name);
        foreach (array_diff($current, $names) as $name) {
            $auth->removeChild($role, $auth->getPermission($name));
        }
        foreach (array_diff($names, $current) as $name) {
            $auth->addChild($role, $auth->getPermission($name));
        }
    }

    /** Make the items that include $permission exactly $parentNames. */
    private function syncParents(Permission $permission, array $parentNames): void
    {
        $auth = $this->auth();
        $current = Rbac::parentsOf($permission->name);
        foreach (array_diff($current, $parentNames) as $name) {
            $auth->removeChild(Rbac::item($name), $permission);
        }
        foreach (array_diff($parentNames, $current) as $name) {
            $parent = Rbac::item($name);
            if (!$auth->canAddChild($parent, $permission)) {
                throw new \RuntimeException("'{$name}' cannot include '{$permission->name}' (it would create a loop).");
            }
            $auth->addChild($parent, $permission);
        }
    }

    /** Re-links admin → manageRbac if an edit removed it, so the RBAC screens stay reachable. */
    private function ensureAdminKeepsRbac(): void
    {
        $auth = $this->auth();
        $admin = $auth->getRole(Rbac::ROLE_ADMIN);
        $perm = $auth->getPermission(Rbac::PERM_MANAGE_RBAC);
        if ($admin && $perm && !$auth->hasChild($admin, $perm)) {
            $auth->addChild($admin, $perm);
            Yii::$app->session->addFlash('warning', "The admin role must keep '" . Rbac::PERM_MANAGE_RBAC . "'; it was kept.");
        }
    }

    /** Grouped dropdown data for "Granted by": roles first, then other permissions. */
    private function parentOptions(?string $exclude = null): array
    {
        $auth = $this->auth();
        $roles = array_keys($auth->getRoles());
        $perms = array_diff(array_keys($auth->getPermissions()), [$exclude]);
        sort($roles);
        sort($perms);

        return [
            'Roles' => array_combine($roles, $roles) ?: [],
            'Permissions' => array_combine($perms, $perms) ?: [],
        ];
    }

    private function userDataProvider(?string $q, $status = null): ActiveDataProvider
    {
        $query = User::find();
        if ($q !== null && trim($q) !== '') {
            $query->andWhere(['or', ['like', 'username', trim($q)], ['like', 'email', trim($q)]]);
        }
        if ($status !== null && $status !== '') {
            $query->andWhere(['status' => (int) $status]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['username', 'email', 'status', 'created_at'],
                'defaultOrder' => ['username' => SORT_ASC],
            ],
            'pagination' => ['pageSize' => 20],
        ]);
    }

    /** @return array<int, string[]> userId => role names, in one query for the current page */
    private function rolesByUser(array $userIds): array
    {
        if ($userIds === []) {
            return [];
        }
        $auth = $this->auth();
        $roleNames = array_keys($auth->getRoles());
        $rows = (new Query())
            ->select(['user_id', 'item_name'])
            ->from($auth->assignmentTable)
            ->where(['user_id' => array_map('strval', $userIds), 'item_name' => $roleNames])
            ->orderBy('item_name')
            ->all($auth->db);

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['user_id']][] = $row['item_name'];
        }
        return $map;
    }
}
