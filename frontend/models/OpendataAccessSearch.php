<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\OpendataAccess;

/**
 * OpendataAccessSearch represents the model behind the search form of `frontend\models\OpendataAccess`.
 */
class OpendataAccessSearch extends OpendataAccess
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'allow_publishing', 'effective_size_value', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['repository_name', 'study_identification_variable', 'sensitivity_analysis_result', 'adjustable_miltiple_comparison', 'handling_missing_data', 'document_path', 'quality_assessment_variable', 'risk_of_bias_assessment', 'study_limitation', 'funding_source', 'potential_conflict_of_interest', 'publication_bias_indicator', 'heterogenity_measure'], 'safe'],
            [['confidential_interval'], 'number'],
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
        $query = OpendataAccess::find();

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
            'allow_publishing' => $this->allow_publishing,
            'effective_size_value' => $this->effective_size_value,
            'confidential_interval' => $this->confidential_interval,
            'trial_id' => $this->trial_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'repository_name', $this->repository_name])
            ->andFilterWhere(['like', 'study_identification_variable', $this->study_identification_variable])
            ->andFilterWhere(['like', 'sensitivity_analysis_result', $this->sensitivity_analysis_result])
            ->andFilterWhere(['like', 'adjustable_miltiple_comparison', $this->adjustable_miltiple_comparison])
            ->andFilterWhere(['like', 'handling_missing_data', $this->handling_missing_data])
            ->andFilterWhere(['like', 'document_path', $this->document_path])
            ->andFilterWhere(['like', 'quality_assessment_variable', $this->quality_assessment_variable])
            ->andFilterWhere(['like', 'risk_of_bias_assessment', $this->risk_of_bias_assessment])
            ->andFilterWhere(['like', 'study_limitation', $this->study_limitation])
            ->andFilterWhere(['like', 'funding_source', $this->funding_source])
            ->andFilterWhere(['like', 'potential_conflict_of_interest', $this->potential_conflict_of_interest])
            ->andFilterWhere(['like', 'publication_bias_indicator', $this->publication_bias_indicator])
            ->andFilterWhere(['like', 'heterogenity_measure', $this->heterogenity_measure]);

        return $dataProvider;
    }
}
