<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $name
 * @property int|null $code
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Town[] $towns
 */
class Country extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'country';
    }

    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
            \yii\behaviors\BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['name'], 'required'],
            ['name', 'unique'],
            ['code', 'unique'],
            [['code', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[Towns]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\query\TownQuery
     */
    public function getTowns()
    {
        return $this->hasMany(Town::class, ['country_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\query\CountryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \frontend\models\query\CountryQuery(get_called_class());
    }

    public function getTownsByCountry()
    {

        // 5 Towns in Kenya
        $kenya = [
            'Nairobi',
            'Mombasa',
            'Kisumu',
            'Eldoret',
            'Machakos'
        ];

        // 5 towns in Uganda
        $uganda = [
            'Kampala',
            'Mbarara',
            'Gulu',
            'Jinja',
            'Lira'
        ];

        // 5 towns in Tanzania
        $tanzania = [
            'Dar es Salaam',
            'Mwanza',
            'Dodoma',
            'Zanzibar',
            'Mbeya'
        ];
    }

}
