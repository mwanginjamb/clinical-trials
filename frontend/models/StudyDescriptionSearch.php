<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\StudyDescription;

/**
 * StudyDescriptionSearch represents the model behind the search form of `frontend\models\StudyDescription`.
 */
class StudyDescriptionSearch extends StudyDescription
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'trial_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['study_website', 'lay_summary', 'scientific_summary'], 'safe'],
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
        $query = StudyDescription::find();

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
            'trial_id' => $this->trial_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'study_website', $this->study_website])
            ->andFilterWhere(['like', 'lay_summary', $this->lay_summary])
            ->andFilterWhere(['like', 'scientific_summary', $this->scientific_summary]);

        return $dataProvider;
    }
}
