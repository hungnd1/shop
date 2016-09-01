<?php

namespace common\models;

use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "live_program".
 *
 * @property int $id
 * @property int $channel_id
 * @property int $content_id
 * @property int $status
 * @property string $name
 * @property string $description
 * @property int $started_at
 * @property int $ended_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Content $channel
 * @property Content $content
 */
class LiveProgram extends \yii\db\ActiveRecord
{
    public $display_name;
    public $images;
    public $date;
    public $liveContent;

    const NOT_RECORDED           = 0;
    const RECORDED_BUT_NOT_READY = 1;
    const READY                  = 2;

//    const STATUS_ACTIVE = 2;
//    const STATUS_INACTIVE = 0;
//    const STATUS_PENDING= 1;

    public static $listStatus = [
        // self::NOT_RECORDED => 'Chưa sẵn sàng',
        self::NOT_RECORDED => 'Chưa ghi',
        self::READY => 'Sẵn sàng'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'live_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel_id'], 'required'],
            [['channel_id', 'content_id', 'status', 'started_at', 'ended_at', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1000],
            ['started_at', 'validateDates'],
        ];
    }

    public function validateDates()
    {
        if ($this->ended_at <= $this->started_at) {
            $this->addError('started_at', 'Please give correct Start and End dates');
            $this->addError('ended_at', 'Please give correct Start and End dates');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'channel_id'  => 'Channel ID',
            'content_id'  => 'Content ID',
            'status'      => 'Trạng thái',
            'name'        => 'Name',
            'description' => 'Description',
            'started_at'  => 'Started At',
            'ended_at'    => 'Ended At',
            'created_at'  => 'Created At',
            'updated_at'  => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(Content::className(), ['id' => 'channel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }

    public function getStatus($status)
    {
        switch ($status) {
            case self::NOT_RECORDED:
                return 'Chưa sẵn sàng';
                break;
            case self::RECORDED_BUT_NOT_READY:
                return 'Chưa ghi';
                break;
            case self::READY:
                return 'Sẵn sàng';
                break;
            default:
                return 'Chưa sẵn sàng';
                break;
        }
    }

    public function initStatus($recorded, $content_status)
    {
        if (!$recorded) {
            return self::NOT_RECORDED;
        } elseif ($recorded && $content_status != Content::STATUS_ACTIVE) {
            return self::RECORDED_BUT_NOT_READY;
        } elseif ($recorded && $content_status == Content::STATUS_ACTIVE) {
            return self::READY;
        }
    }

    /**
     * @return array
     */
    public function getChannels()
    {
        $channels = LiveProgram::find()
            ->where(['status' => LiveProgram::STATUS_ACTIVE])
            ->groupBy('channel_id')
            ->all();
        if (!$channels) {
            return [
                'status'  => false,
                'message' => \api\helpers\Message::MSG_NOT_FOUND_CONTENT,
            ];
        }
        foreach ($channels as $channel) {
            $ch = Content::find()
                ->andWhere(['id' => $channel->channel_id])
                ->andWhere(['status' => Content::STATUS_ACTIVE])->one();
            $chs[] = $ch;
        }
        return [
            'status' => true,
            'items'  => $chs,
        ];
    }

    /**
     * @param $channel_id
     * @param null $type
     * @param $order
     * @param $from_time
     * @param $to_time
     * @return bool|ActiveDataProvider
     */
    public function getListCatchup($channel_id, $type = null, $order, $from_time, $to_time)
    {
//        print Date("H:m", 1364922000);

        /** find content at least one */
        $content = LiveProgram::find()
            ->where(['channel_id' => $channel_id])
            ->andWhere(['>=', 'started_at', $from_time])
            ->andWhere(['<=', 'started_at', $to_time])
            ->one();
        if (!$content) {
            return false;
        }

        $query = new Query();
        $query->select(['live_program.*', 'content_profile.quality', 'content.type', 'content.parent_id', 'c.display_name', 'content.episode_order', 'content.episode_count', 'content.images'])
            ->from('live_program')
            ->innerJoin('content_profile', 'content_profile.content_id = live_program.content_id')
//            ->leftJoin('content_profile', 'content_profile.content_id = live_program.content_id')
            ->innerJoin('content', 'content.id = live_program.content_id')
            ->innerJoin('content c', 'c.id = content.parent_id')
            ->andWhere(['live_program.channel_id' => $channel_id])
            ->andWhere(['>=', 'live_program.started_at', $from_time])
            ->andWhere(['<=', 'live_program.started_at', $to_time])
            ->groupBy('live_program.content_id')
            ->all();
        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => [],
            'pagination' => [
                'defaultPageSize' => 20,
            ],
        ]);
        return $dataProvider;
    }

    public static function getEpg($channel_id,$fromDate, $toDate,$site_id){
        $livePrograms = LiveProgram::find()
            ->andWhere(['channel_id' => $channel_id])
            ->andWhere('started_at between :fromDate and :toDate')->addParams([':fromDate' => $fromDate, ':toDate' => $toDate])
            ->all();
        $arrItems = [];
        /** Duyệt từng bản ghi EPG */
        foreach($livePrograms as $liveProgram){
            $item = $liveProgram->getAttributes(['id','channel_id','content_id','status','name','started_at','ended_at'], ['created_at','updated_at']);
            /** Với mỗi bản ghi EPG lấy ra danh sách các content_profile */
            $contentProfiles = $liveProgram->content->contentProfiles?$liveProgram->content->contentProfiles:[];
            $strQualities = "";
            /** Duyệt từng content_profile kiểm tra để lấy đúng content_profile map với site */
            foreach ($contentProfiles as $contentProfile) {
                /** Không lấy thằng file RAW */
                if($contentProfile->type == ContentProfile::TYPE_RAW){
                    continue;
                }
                $contentProfileSiteAsm = ContentProfileSiteAsm::findOne(['content_profile_id'=>$contentProfile->id, 'site_id'=>$site_id,'status'=>ContentProfileSiteAsm::STATUS_ACTIVE]);
                /** Nếu content_profile không thuộc site thì bỏ qua */
                if(!$contentProfileSiteAsm){
                    continue;
                }
                /** Get object content_priofile để xử lí*/
                $cp = $contentProfileSiteAsm->contentProfile;
                $strQualities .= $cp->quality . ',';
            }
            if(strlen($strQualities) >= 2){
                $strQualities = substr($strQualities,0,-1);
            }
            $item['qualities']      = $strQualities;
            $arrItems[]      = $item;
        }
        return $arrItems;
    }
}
