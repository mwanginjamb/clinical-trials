<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\StudyIntervention;

/**
 * StudyInterventionSearch represents the model behind the search form of `frontend\models\StudyIntervention`.
 */
class StudyInterventionSearch extends StudyIntervention
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type_of_outcome', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['intervention_name', 'intervention_description', 'control_comparator'], 'safe'],
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
        $query = StudyIntervention::find();

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
            'type_of_outcome' => $this->type_of_outcome,
            'trial_id' => $this->trial_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'intervention_name', $this->intervention_name])
            ->andFilterWhere(['like', 'intervention_description', $this->intervention_description])
            ->andFilterWhere(['like', 'control_comparator', $this->control_comparator]);

        return $dataProvider;
    }
}
