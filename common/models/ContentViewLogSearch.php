<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ContentViewLog;

/**
 * ContentViewLogSearch represents the model behind the search form about `common\models\ContentViewLog`.
 */
class ContentViewLogSearch extends ContentViewLog
{
    public $from_date;
    public $to_date;
    public $from_time;
    public $to_time;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'subscriber_id', 'content_id', 'category_id', 'created_at', 'status', 'type', 'channel', 'site_id', 'started_at', 'stopped_at', 'view_date'], 'integer'],
            [['msisdn', 'ip_address', 'description', 'user_agent'], 'safe'],
            [['from_date', 'to_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        //        $query = ContentViewLog::find();  //Dùng thằng API/Model để get thêm trường khi trả về cho client
        $query = \api\models\ContentViewLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'view_date' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
//            'id' => $this->id,
            'subscriber_id' => $this->subscriber_id,
            'content_id' => $this->content_id,
//            'created_at' => $this->created_at,
            'status' => $this->status,
            'type' => $this->type,
            'channel' => $this->channel,
            'site_id' => $this->site_id,
//            'started_at' => $this->started_at,
//            'stopped_at' => $this->stopped_at,
//            'view_date' => $this->view_date,
        ]);

//        $query->andFilterWhere(['like', 'msisdn', $this->msisdn])
//            ->andFilterWhere(['like', 'ip_address', $this->ip_address])
//            ->andFilterWhere(['like', 'description', $this->description])
//            ->andFilterWhere(['like', 'user_agent', $this->user_agent]);

        if ($this->started_at) {
            $query->andFilterWhere(['>=', 'started_at', strtotime($this->started_at)]);
        }
        if ($this->view_date) {
            $query->andFilterWhere(['>=', 'view_date', strtotime($this->view_date)]);
        }
        if ($this->from_date) {
            $query->andFilterWhere(['>=', 'started_at', strtotime($this->from_date)]);
        }
        if ($this->to_date) {
            $query->andFilterWhere(['<=', 'started_at', strtotime($this->to_date)]);
        }

        if ($this->from_time && $this->to_time) {
            $query->andFilterWhere(['>=', 'view_date', $this->from_time])
                ->andFilterWhere(['<=', 'view_date', $this->to_time]);
        }

        return $dataProvider;
    }
}
