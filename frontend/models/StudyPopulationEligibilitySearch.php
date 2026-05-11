<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\StudyPopulationEligibility;

/**
 * StudyPopulationEligibilitySearch represents the model behind the search form of `frontend\models\StudyPopulationEligibility`.
 */
class StudyPopulationEligibilitySearch extends StudyPopulationEligibility
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'participant_target_number', 'sample_size', 'final_number_of_participants', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['health_condition_studied', 'type_of_eligibility'], 'safe'],
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
        $query = StudyPopulationEligibility::find();

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
            'participant_target_number' => $this->participant_target_number,
            'sample_size' => $this->sample_size,
            'final_number_of_participants' => $this->final_number_of_participants,
            'trial_id' => $this->trial_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'health_condition_studied', $this->health_condition_studied])
            ->andFilterWhere(['like', 'type_of_eligibility', $this->type_of_eligibility]);

        return $dataProvider;
    }
}
