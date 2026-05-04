<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\StudyPurpose;

/**
 * StudyPurposeSearch represents the model behind the search form of `frontend\models\StudyPurpose`.
 */
class StudyPurposeSearch extends StudyPurpose
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type_of_study', 'control_group_name', 'design_control_group_presence', 'phase_of_study', 'masking_status', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['study_purpose', 'study_objective', 'study_hypothesis', 'intervention', 'randomization_method_name', 'masking_description'], 'safe'],
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
        $query = StudyPurpose::find();

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
            'type_of_study' => $this->type_of_study,
            'control_group_name' => $this->control_group_name,
            'design_control_group_presence' => $this->design_control_group_presence,
            'phase_of_study' => $this->phase_of_study,
            'masking_status' => $this->masking_status,
            'trial_id' => $this->trial_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'study_purpose', $this->study_purpose])
            ->andFilterWhere(['like', 'study_objective', $this->study_objective])
            ->andFilterWhere(['like', 'study_hypothesis', $this->study_hypothesis])
            ->andFilterWhere(['like', 'intervention', $this->intervention])
            ->andFilterWhere(['like', 'randomization_method_name', $this->randomization_method_name])
            ->andFilterWhere(['like', 'masking_description', $this->masking_description]);

        return $dataProvider;
    }
}
