<?php

namespace frontend\models\query;

/**
 * This is the ActiveQuery class for [[\frontend\models\Funding]].
 *
 * @see \frontend\models\Funding
 */
class FundingQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \frontend\models\Funding[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\Funding|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
