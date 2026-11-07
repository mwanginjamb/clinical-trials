<?php

use yii\db\Migration;

/**
 * Flags the roles/permissions the code depends on as system items
 * (auth_item.data = ['system' => true]). The RBAC screens refuse to rename or delete them.
 *
 * The names are listed here (not read from code) so the migration stays reproducible.
 * Future migrations that add a code-checked item should flag it with common\rbac\Rbac::markSystem().
 */
class m261108_100000_flag_system_rbac_items extends Migration
{
    private const ITEMS = [
        'admin',
        'researcher',
        'viewTrial',
        'createTrial',
        'updateTrial',
        'updateOwnTrial',
        'deleteTrial',
        'importTrials',
        'manageLookups',
        'manageRbac',
    ];

    public function safeUp()
    {
        $this->setFlag(true);
    }

    public function safeDown()
    {
        $this->setFlag(false);
    }

    private function setFlag(bool $on): void
    {
        $auth = Yii::$app->authManager;
        foreach (self::ITEMS as $name) {
            $item = $auth->getRole($name) ?? $auth->getPermission($name);
            if ($item === null) {
                echo "    > '{$name}' not found, skipped.\n";
                continue;
            }
            $data = is_array($item->data) ? $item->data : [];
            if ($on) {
                $data['system'] = true;
            } else {
                unset($data['system']);
            }
            $item->data = $data ?: null;
            $auth->update($name, $item);
        }
    }
}
