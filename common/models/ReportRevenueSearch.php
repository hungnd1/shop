<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ReportRevenue;

/**
 * ReportRevenueSearch represents the model behind the search form about `common\models\ReportRevenue`.
 */
class ReportRevenueSearch extends ReportRevenue
{
    public $from_date;
    public $to_date;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'report_date', 'site_id', 'service_id', 'total_revenues', 'renew_revenues', 'register_revenues', 'content_buy_revenues'], 'integer'],
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
        $query = ReportRevenue::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'report_date' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

//        $query->andFilterWhere([
//            'id' => $this->id,
//            'report_date' => $this->report_date,
//            'site_id' => $this->site_id,
//            'service_id' => $this->service_id,
//            'total_revenues' => $this->total_revenues,
//            'renew_revenues' => $this->renew_revenues,
//            'register_revenues' => $this->register_revenues,
//            'content_buy_revenues' => $this->content_buy_revenues,
//        ]);
        $query->select('report_date,
                            sum(total_revenues) as total_revenues,
                            sum(renew_revenues) as renew_revenues,
                            sum(register_revenues) as register_revenues,
                            sum(content_buy_revenues) as content_buy_revenues'
        );

        if($this->site_id){
            $query->where(['site_id'=>$this->site_id]);
        }

        if($this->service_id){
            $query->andFilterWhere(['service_id'=>$this->service_id]);
        }

        if ($this->from_date) {
            $query->andFilterWhere(['>=', 'report_date', $this->from_date]);
        }
        if ($this->to_date) {
            $query->andFilterWhere(['<=', 'report_date', $this->to_date]);
        }
        $query->groupBy('report_date');

        return $dataProvider;
    }
}
