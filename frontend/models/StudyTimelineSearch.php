<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\StudyTimeline;

/**
 * StudyTimelineSearch represents the model behind the search form of `frontend\models\StudyTimeline`.
 */
class StudyTimelineSearch extends StudyTimeline
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'study_duration', 'recruitment_status', 'recruiting_country', 'centre_region', 'trial_id', 'created_at', 'updated_at', 'updated_by', 'created_by'], 'integer'],
            [['study_site_location', 'centre_postal_address', 'anticipated_start_date', 'anticipated_end_date', 'centre_pysical_address'], 'safe'],
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
        $query = StudyTimeline::find();

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
            'study_duration' => $this->study_duration,
            'anticipated_start_date' => $this->anticipated_start_date,
            'anticipated_end_date' => $this->anticipated_end_date,
            'recruitment_status' => $this->recruitment_status,
            'recruiting_country' => $this->recruiting_country,
            'centre_region' => $this->centre_region,
            'trial_id' => $this->trial_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'study_site_location', $this->study_site_location])
            ->andFilterWhere(['like', 'centre_postal_address', $this->centre_postal_address])
            ->andFilterWhere(['like', 'centre_pysical_address', $this->centre_pysical_address]);

        return $dataProvider;
    }
}
