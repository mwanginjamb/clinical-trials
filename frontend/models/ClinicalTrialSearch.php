<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\ClinicalTrial;

/**
 * ClinicalTrialSearch represents the model behind the search form of `frontend\models\ClinicalTrial`.
 */
class ClinicalTrialSearch extends ClinicalTrial
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'registration_status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['scientific_title', 'public_title', 'scientific_acronym', 'protocol_version', 'protocol_number', 'registration_number'], 'safe'],
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
        $query = ClinicalTrial::find();

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
            'registration_status' => $this->registration_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'scientific_title', $this->scientific_title])
            ->andFilterWhere(['like', 'public_title', $this->public_title])
            ->andFilterWhere(['like', 'scientific_acronym', $this->scientific_acronym])
            ->andFilterWhere(['like', 'protocol_version', $this->protocol_version])
            ->andFilterWhere(['like', 'protocol_number', $this->protocol_number])
            ->andFilterWhere(['like', 'registration_number', $this->registration_number]);

        return $dataProvider;
    }
}
