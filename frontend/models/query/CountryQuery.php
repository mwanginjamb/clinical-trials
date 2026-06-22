<?php

namespace frontend\models\query;

/**
 * This is the ActiveQuery class for [[\frontend\models\Country]].
 *
 * @see \frontend\models\Country
 */
class CountryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \frontend\models\Country[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\Country|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
