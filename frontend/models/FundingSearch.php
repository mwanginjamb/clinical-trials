<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Funding;

/**
 * FundingSearch represents the model behind the search form of `frontend\models\Funding`.
 */
class FundingSearch extends Funding
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sponsor_name', 'country', 'funding_Sector', 'trial_id', 'created_at', 'updated_at', 'created_by', 'update_by'], 'integer'],
            [['Amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Funding::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'sponsor_name' => $this->sponsor_name,
            'Amount' => $this->Amount,
            'country' => $this->country,
            'funding_Sector' => $this->funding_Sector,
            'trial_id' => $this->trial_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'update_by' => $this->update_by,
        ]);

        return $dataProvider;
    }
}
