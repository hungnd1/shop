<?php

namespace common\models;

use api\helpers\Message;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%content_view_log}}".
 *
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $content_id
 * @property integer $category_id
 * @property string $msisdn
 * @property integer $created_at
 * @property string $ip_address
 * @property integer $status
 * @property integer $type
 * @property string $description
 * @property string $user_agent
 * @property integer $channel
 * @property integer $site_id
 * @property integer $started_at
 * @property integer $stopped_at
 * @property integer $view_date
 * @property integer $view_count
 *
 * @property Category $category
 * @property Content $content
 * @property Site $site
 * @property Subscriber $subscriber
 */
class ContentViewLog extends \yii\db\ActiveRecord
{
    const STATUS_SUCCESS = 10;
    const STATUS_FALSE = 0;

//    const TYPE_SERVICE = 1; //Xem qua gói cước ( chia sẻ doanh thu)
//    const TYPE_CONTENT = 2; //Xem qua mua lẻ ( không chia sẻ doanh thu)

    const TYPE_VIDEO        = 1;
    const TYPE_LIVE         = 2;
    const TYPE_MUSIC        = 3;
    const TYPE_NEWS         = 4;
    const TYPE_CLIP         = 5;
    const TYPE_KARAOKE      = 6;
    const TYPE_RADIO        = 7;
    const TYPE_LIVE_CONTENT = 8;


    const CHANNEL_TYPE_API = 1;
    const CHANNEL_TYPE_SYSTEM = 2;
    const CHANNEL_TYPE_CSKH = 3;
    const CHANNEL_TYPE_SMS = 4;
    const CHANNEL_TYPE_WAP = 5;
    const CHANNEL_TYPE_MOBILEWEB = 6;
    const CHANNEL_TYPE_ANDROID = 7;
    const CHANNEL_TYPE_IOS = 8;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_view_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'content_id', 'category_id', 'created_at', 'status', 'type', 'channel', 'site_id','view_count', 'started_at', 'stopped_at', 'view_date'], 'integer'],
            [['content_id', 'site_id'], 'required'],
            [['description'], 'string'],
            [['msisdn'], 'string', 'max' => 20],
            [['ip_address'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subscriber_id' => 'Subscriber ID',
            'content_id' => 'Content ID',
            'category_id' => 'Category ID',
            'msisdn' => 'Msisdn',
            'created_at' => 'Created At',
            'ip_address' => 'Ip Address',
            'status' => 'Status',
            'type' => 'Type',
            'description' => 'Description',
            'user_agent' => 'User Agent',
            'channel' => 'Channel',
            'site_id' => 'Site ID',
            'started_at' => 'Started At',
            'stopped_at' => 'Stopped At',
            'view_date' => 'View Date',
            'view_count' => 'View Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriber()
    {
        return $this->hasOne(Subscriber::className(), ['id' => 'subscriber_id']);
    }

    /**
     * @return array
     */
    public static function listStatus()
    {
        $lst = [
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_FALSE => 'False',
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getStatusName()
    {
        $lst = self::listStatus();
        if (array_key_exists($this->status, $lst)) {
            return $lst[$this->status];
        }
        return $this->status;
    }

    /**
     * @return array
     */
    public static function listType()
    {
        $lst = [
            self::TYPE_VIDEO   => 'Phim',
            self::TYPE_CLIP    => 'Clip',
            self::TYPE_LIVE    => 'Live',
            self::TYPE_MUSIC   => 'Âm nhạc',
            self::TYPE_NEWS    => 'Tin tức',
            self::TYPE_KARAOKE => 'Karaoke',
            self::TYPE_RADIO   => 'Radio',
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getTypeName()
    {
        $lst = self::listType();
        if (array_key_exists($this->type, $lst)) {
            return $lst[$this->type];
        }
        return $this->type;
    }

    /**
     * @return array
     */
    public static function listChannelType()
    {
        $lst = [
            self::CHANNEL_TYPE_API => 'Api',
            self::CHANNEL_TYPE_SYSTEM => 'System',
            self::CHANNEL_TYPE_CSKH => 'Cskh',
            self::CHANNEL_TYPE_SMS => 'Sms',
            self::CHANNEL_TYPE_WAP => 'Wap',
            self::CHANNEL_TYPE_MOBILEWEB => 'Mobile Web',
            self::CHANNEL_TYPE_ANDROID => 'Android',
            self::CHANNEL_TYPE_IOS => 'IOS',
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getChannelName()
    {
        $lst = self::listChannelType();
        if (array_key_exists($this->channel, $lst)) {
            return $lst[$this->channel];
        }
        return $this->channel;
    }

    /**
     * @param $subscriber Subscriber
     * @param $content Content
     * @param $type
     * @param $channel
     * @param $site_id
     * @param $start_time
     * @param $stop_time
     * @param $log_id
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public static function createViewLog($subscriber, $content, $category_id, $type, $channel, $site_id, $start_time, $stop_time, $log_id)
    {
        $res = [];
        if (!$log_id) {
            /** @var  $log ContentViewLog */
            $log = new ContentViewLog();
            $log->subscriber_id = $subscriber->id;
            $log->content_id = $content->id;
            $log->category_id = $category_id;
            $log->created_at = time();
            $log->ip_address = Yii::$app->request->getUserIP();
            $log->user_agent = Yii::$app->request->getUserAgent();
            $log->status = ContentViewLog::STATUS_SUCCESS;
            $log->type = $type;
            $log->channel = $channel;
            $log->site_id = $site_id;
            if ($start_time > 0) {
                $log->started_at = $start_time;
            }
            if ($stop_time) {
                $log->stopped_at = $stop_time;
            }
            $log->view_date = time();
            //Tăng số lượt xem của 1 nội dung
            $log->view_count =1;
            /** Validate và save, nếu có lỗi thì return message_error */
            if (!$log->validate() || !$log->save()) {
                $message = $log->getFirstMessageError();
                $res['status'] = false;
                $res['message'] = $message;
                return $res;
            }
            $res['status'] = true;
            $res['message'] = Message::MSG_SUCCESS;
            $res['item'] = $log;
        } else {
            /** @var  $log ContentViewLog */
            $log = ContentViewLog::findOne(['id' => $log_id, 'channel' => $channel, 'site_id' => $site_id,'type'=>$type]);
            if (!$log) {
                return false;
            }
            if ($start_time > 0) {
                $log->started_at = $start_time;
            }
            if ($stop_time) {
                $log->stopped_at = $stop_time;
            }
            $log->view_date = time();
            //Tăng số lượt xem của 1 nội dung
            $log->view_count++;
            /** Validate và save, nếu có lỗi thì return message_error */
            if (!$log->validate() || !$log->save()) {
                $message = $log->getFirstMessageError();
                $res['status'] = false;
                $res['message'] = $message;
                return $res;
            }
            $res['status'] = true;
            $res['message'] = Message::MSG_SUCCESS;
            $res['item'] = $log;

        }
        return $res;
    }

    private function getFirstMessageError()
    {
        $error = $this->firstErrors;
        $message = "";
        foreach ($error as $key => $value) {
            $message .= $value;
            break;
        }
        return $message;
    }

    /**
     * @param $subscriber
     * @param $site_id
     * @param $channel
     * @param $content_id
     * @param $view_date
     * @return ActiveDataProvider
     */
    public static function viewLogSearch($subscriber, $site_id, $channel, $content_id, $view_date)
    {
        /**
         * check view logs theo kênh - theo nội dung - theo ngày
         */
        if ($view_date) {
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $view_date)) {
                $res = [
                    'status' => false,
                    'message' => 'Sai định dạng ngày tháng',
                ];
                return $res;
            }
        }

        $params = Yii::$app->request->queryParams;
        $from = new \DateTime($view_date);
        $from->setTime(0, 0, 0);
        $to = new \DateTime($view_date);
        $to->setTime(23, 59, 59);
        $logSearch = new ContentViewLogSearch();
        $logSearch->subscriber_id = $subscriber->id;
        $logSearch->site_id = $site_id;
        if ($channel) {
            $logSearch->channel = $channel;
        }
        if ($content_id) {
            $logSearch->content_id = $content_id;
        }
        if ($view_date) {
            $logSearch->from_time = $from->getTimestamp();
            $logSearch->to_time = $to->getTimestamp();
        }
        $items = $logSearch->search($params);
        $res = [
            'status' => true,
            'items' => $items,
        ];
        return $res;
        /*

        $viewLog = ContentViewLog::find()
            ->andWhere(['content_view_log.status' => ContentViewLog::STATUS_SUCCESS]);
        if ($subscriber) {
            $viewLog->andWhere(['subscriber_id' => $subscriber->id])
                ->andWhere(['site_id' => $site_id]);
        }
        if ($channel) {
            $viewLog->andWhere(['channel' => $channel]);
        }
        if ($content_id) {
            $viewLog->andWhere(['content_id' => $content_id]);
        }
        if ($view_date) {
            $from = new \DateTime($view_date);
            $from->setTime(0, 0, 0);
            $to = new \DateTime($view_date);
            $to->setTime(23, 59, 59);

            $viewLog->andWhere(['>=', 'view_date', $from->getTimestamp()])
                ->andWhere(['<=', 'view_date', $to->getTimestamp()]);
        }
        $activeData = new ActiveDataProvider([
            'query' => $viewLog,
            'sort' => [
                'defaultOrder' => SORT_DESC,
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ]
        ]);
        return $activeData;
        */
    }


}
