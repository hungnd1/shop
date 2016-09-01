<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SubscriberTransaction;

/**
 * SubscriberTransactionSearch represents the model behind the search form about `common\models\SubscriberTransaction`.
 */
class SubscriberTransactionSearch extends SubscriberTransaction
{
    public $from_date;
    public $to_date;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'subscriber_id', 'type', 'service_id', 'content_id', 'transaction_time', 'created_at', 'updated_at', 'status', 'channel', 'subscriber_activity_id', 'subscriber_service_asm_id', 'site_id'], 'integer'],
            [['msisdn', 'shortcode', 'description', 'event_id', 'error_code'], 'safe'],
            [['cost'], 'number'],
            [['from_date','to_date'], 'safe'],
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
//        $query = SubscriberTransaction::find();
        $query = \api\models\SubscriberTransaction::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'transaction_time' => SORT_DESC,
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
            'id' => $this->id,
            'subscriber_id' => $this->subscriber_id,
            'type' => $this->type,
            'service_id' => $this->service_id,
            'content_id' => $this->content_id,
            'transaction_time' => $this->transaction_time,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'cost' => $this->cost,
            'channel' => $this->channel,
            'subscriber_activity_id' => $this->subscriber_activity_id,
            'subscriber_service_asm_id' => $this->subscriber_service_asm_id,
            'site_id' => $this->site_id,
        ]);

//        $query->andFilterWhere(['like', 'msisdn', $this->msisdn])
//            ->andFilterWhere(['like', 'shortcode', $this->shortcode])
//            ->andFilterWhere(['like', 'description', $this->description])
//            ->andFilterWhere(['like', 'event_id', $this->event_id])
//            ->andFilterWhere(['like', 'error_code', $this->error_code]);

        if($this->created_at){
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->created_at)]);
        }
        if($this->updated_at){
            $query->andFilterWhere(['>=', 'updated_at', strtotime($this->updated_at)]);
        }

        if($this->from_date){
            $query->andFilterWhere(['>=', 'transaction_time', strtotime($this->from_date)]);
        }
        if($this->to_date){
            $query->andFilterWhere(['<=', 'transaction_time', strtotime($this->to_date)]);
        }

        return $dataProvider;
    }

    public function searchWithCondition($params,$type=0)
    {
        $query = SubscriberTransaction::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'user_id' => $this->user_id,
        ]);
        //R� so�t l?i xem ch? n�o c�n d�ng username l�m keyword th� b? ch? n�y ?i d�ng keyword l�m t? kh�a th�i
        $query->andFilterWhere(['like', 'username', $this->username]);
        if($this->keyword){
            $query->andFilterWhere(['or',
                ['=','username', $this->keyword],
                ['=','msisdn', $this->keyword],
            ]);
        }
        if($type==1){
            $query->andFilterWhere(['type'=>[SubscriberTransaction::TYPE_REGISTER,SubscriberTransaction::TYPE_USER_CANCEL]]);
            $query->andWhere('package_id IS NOT NULL');
        }elseif($type==2){
            $query->andFilterWhere(['type'=>SubscriberTransaction::TYPE_RENEW]);
            $query->andWhere('package_id IS NOT NULL');
        }elseif($type==3){
            $query->andFilterWhere(['type'=>[SubscriberTransaction::TYPE_CONTENT_PURCHASE,SubscriberTransaction::TYPE_DOWNLOAD] ]);
        }


        $query->orderBy(['created_at' => SORT_DESC, 'updated_at' => SORT_DESC,'id' => SORT_DESC]);

        return $dataProvider;
    }
}
