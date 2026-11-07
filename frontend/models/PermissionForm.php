<?php

namespace frontend\models;

use common\rbac\Rbac;
use Yii;
use yii\base\Model;

/**
 * Create / update an RBAC permission and the roles/permissions that include it ("parents").
 */
class PermissionForm extends Model
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';

    public $name;
    public $description;
    /** @var string[] roles or permissions that grant this permission */
    public $parents = [];
    public $originalName;

    public function scenarios()
    {
        $attrs = ['name', 'description', 'parents'];
        return [
            self::SCENARIO_CREATE => $attrs,
            self::SCENARIO_UPDATE => $attrs,
        ];
    }

    public function rules()
    {
        $auth = Yii::$app->authManager;
        $allItems = array_merge(array_keys($auth->getRoles()), array_keys($auth->getPermissions()));

        return [
            [['name'], 'trim'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 64],
            [
                ['name'], 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9_\-]*$/',
                'message' => 'Use letters, numbers, "_" or "-", starting with a letter (e.g. exportTrials).',
            ],
            [['name'], 'validateName'],
            [['description'], 'string', 'max' => 255],
            [['parents'], 'default', 'value' => []],
            [['parents'], 'each', 'rule' => ['in', 'range' => $allItems]],
            [['parents'], 'validateParents'],
        ];
    }

    public function validateName($attribute)
    {
        $name = $this->$attribute;
        $isRename = $this->scenario === self::SCENARIO_UPDATE && $name !== $this->originalName;

        if ($isRename && in_array($this->originalName, Rbac::SYSTEM_PERMISSIONS, true)) {
            $this->addError($attribute, "'{$this->originalName}' is used by the application and cannot be renamed.");
            return;
        }
        if (($this->scenario === self::SCENARIO_CREATE || $isRename)
            && Rbac::item($name) !== null) {
            $this->addError($attribute, "A role or permission named '{$name}' already exists.");
        }
    }

    public function validateParents($attribute)
    {
        $self = [$this->name, $this->originalName];
        foreach ((array) $this->$attribute as $parent) {
            if (in_array($parent, $self, true)) {
                $this->addError($attribute, 'A permission cannot be its own parent.');
                return;
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Permission name',
            'description' => 'Description',
            'parents' => 'Granted by',
        ];
    }

    public function attributeHints()
    {
        return [
            'name' => 'camelCase action name, e.g. exportTrials. Code checks it with Yii::$app->user->can().',
            'parents' => 'Roles (or broader permissions) that include this permission.',
        ];
    }
}
