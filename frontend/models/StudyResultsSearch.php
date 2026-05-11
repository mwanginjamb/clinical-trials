<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\StudyResults;

/**
 * StudyResultsSearch represents the model behind the search form of `frontend\models\StudyResults`.
 */
class StudyResultsSearch extends StudyResults
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'permission_to_publish', 'publication_type', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['summary_results', 'authority_committe_name', 'publisher', 'url_doi', 'publication_title'], 'safe'],
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
        $query = StudyResults::find();

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
            'permission_to_publish' => $this->permission_to_publish,
            'publication_type' => $this->publication_type,
            'trial_id' => $this->trial_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'summary_results', $this->summary_results])
            ->andFilterWhere(['like', 'authority_committe_name', $this->authority_committe_name])
            ->andFilterWhere(['like', 'publisher', $this->publisher])
            ->andFilterWhere(['like', 'url_doi', $this->url_doi])
            ->andFilterWhere(['like', 'publication_title', $this->publication_title]);

        return $dataProvider;
    }
}
