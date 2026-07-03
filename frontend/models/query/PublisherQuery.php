<?php

namespace frontend\models\query;

/**
 * This is the ActiveQuery class for [[\frontend\models\Publisher]].
 *
 * @see \frontend\models\Publisher
 */
class PublisherQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \frontend\models\Publisher[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \frontend\models\Publisher|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
