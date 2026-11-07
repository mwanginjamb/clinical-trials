<?php

namespace frontend\models;

use common\rbac\Rbac;
use Yii;
use yii\base\Model;

/**
 * Create / update an RBAC role and the permissions it grants directly.
 * Child roles (e.g. admin → researcher) are managed on the hierarchy screen and are
 * left untouched by this form.
 */
class RoleForm extends Model
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';

    public $role_name;
    public $description;
    /** @var string[] permission names granted directly by this role */
    public $permissions = [];
    /** Name before editing (update scenario). */
    public $originalName;

    public function scenarios()
    {
        $attrs = ['role_name', 'description', 'permissions'];
        return [
            self::SCENARIO_CREATE => $attrs,
            self::SCENARIO_UPDATE => $attrs,
        ];
    }

    public function rules()
    {
        return [
            [['role_name'], 'trim'],
            [['role_name'], 'required'],
            [['role_name'], 'string', 'max' => 64],
            [
                ['role_name'], 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9_\-]*$/',
                'message' => 'Use letters, numbers, "_" or "-", starting with a letter.',
            ],
            [['role_name'], 'validateName'],
            [['description'], 'string', 'max' => 255],
            [['permissions'], 'default', 'value' => []],
            [['permissions'], 'each', 'rule' => ['in', 'range' => array_keys(Yii::$app->authManager->getPermissions())]],
        ];
    }

    public function validateName($attribute)
    {
        $name = $this->$attribute;
        $isRename = $this->scenario === self::SCENARIO_UPDATE && $name !== $this->originalName;

        if ($isRename && Rbac::isSystemItem($this->originalName)) {
            $this->addError($attribute, "'{$this->originalName}' is a system role and cannot be renamed.");
            return;
        }
        if (($this->scenario === self::SCENARIO_CREATE || $isRename)
            && Rbac::item($name) !== null) {
            // roles and permissions share one namespace in auth_item
            $this->addError($attribute, "A role or permission named '{$name}' already exists.");
        }
    }

    public function attributeLabels()
    {
        return [
            'role_name' => 'Role name',
            'description' => 'Description',
            'permissions' => 'Permissions granted',
        ];
    }

    public function attributeHints()
    {
        return [
            'role_name' => 'Short machine name, e.g. data_manager. Used internally; cannot contain spaces.',
            'permissions' => 'Permissions this role grants directly. Roles it inherits are managed under "Hierarchy".',
        ];
    }
}
