<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%subscriber_transaction}}".
 *
 * @property integer $id
 * @property integer $subscriber_id
 * @property string $msisdn
 * @property integer $type
 * @property integer $service_id
 * @property integer $content_id
 * @property integer $transaction_time
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property string $shortcode
 * @property string $description
 * @property double $cost
 * @property double $balance
 * @property double $currency
 * @property integer $channel
 * @property string $event_id
 * @property string $error_code
 * @property integer $subscriber_activity_id
 * @property integer $subscriber_service_asm_id
 * @property integer $site_id
 * @property string $vnp_user_ip
 * @property string $vnp_username
 * @property string $application
 * @property integer $content_provider_id
 *
 * @property SubscriberServiceAsm[] $subscriberServiceAsms
 * @property SubscriberServiceAsm[] $subscriberServiceAsms0
 * @property SubscriberServiceAsm[] $subscriberServiceAsms1
 * @property Service $service
 * @property Subscriber $subscriber
 * @property Content $content
 * @property SubscriberActivity $subscriberActivity
 * @property SubscriberServiceAsm $subscriberServiceAsm
 * @property ServiceProvider $serviceProvider
 */
class SubscriberTransaction extends \yii\db\ActiveRecord
{
    const STATUS_SUCCESS = 10;
    const STATUS_FAIL = 0;
    const STATUS_PENDING = 1;

    const TYPE_REGISTER = 1; // mua goi
    const TYPE_RENEW = 2;
    const TYPE_DOWNLOAD = 3;
    const TYPE_USER_CANCEL = 4;
    const TYPE_CANCEL = 5;
    const TYPE_RETRY = 6;
    const TYPE_CANCEL_SERVICE_BY_SYSTEM = 7;
    const TYPE_CONTENT_PURCHASE = 8;  // Mua phim le de xem
    const TYPE_CANCEL_BY_API_VNPT = 9;// huy boi api vnpt
    const TYPE_CANCEL_SERVICE_BY_CHANGE_PACKAGE = 10; // Huy do doi goi khac trong cung nhom
    const TYPE_REGISTER_BY_CHANGE_PACKAGE = 11; // Mua goi do doi goi khac trong cung nhom
//    const TYPE_CONTENT_PURCHASE_DOWNLOAD=9; // MUA content de download
    const TYPE_CSKH_SUBSCRIBER_INFO = 12; // Lay thong tin subscriber tu CSKH
    const TYPE_CSKH_GET_ALL_SUBSCRIBER_INFO = 13; // Lay thong tin tat ca cac goi cuoc cua subscriber tren he thong
    const TYPE_CSKH_GET_TRANSACTION_INFO = 14; // Lay thong tin giao dich cua nguoi dung
    const TYPE_CSKH_CHANGE_USER = 15; // Nguoi dung doi so mobile
    const TYPE_CHARGE_COIN = 16;

//    const CHANNEL_TYPE_WAP = 1;
//    const CHANNEL_TYPE_SMS = 2;
//    const CHANNEL_TYPE_SYSTEM = 3;
//    const CHANNEL_TYPE_ADMIN = 4;
//    const CHANNEL_TYPE_API_VNPT = 5;
//    const CHANNEL_TYPE_ANDROID=6;

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
        return '{{%subscriber_transaction}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'status', 'site_id'], 'required'],
            [['subscriber_id', 'type', 'service_id', 'content_id', 'transaction_time', 'created_at', 'updated_at', 'status', 'channel', 'subscriber_activity_id', 'subscriber_service_asm_id', 'site_id', 'dealer_id'], 'integer'],
            [['cost', 'balance'], 'number'],
            [['msisdn'], 'string', 'max' => 20],
            [['shortcode'], 'string', 'max' => 45],
            [['description', 'application'], 'string', 'max' => 200],
            [['event_id', 'currency'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subscriber_id' => Yii::t('app', 'Subscriber ID'),
            'msisdn' => Yii::t('app', 'Msisdn'),
            'type' => Yii::t('app', 'Loại'),
            'service_id' => Yii::t('app', 'Service ID'),
            'content_id' => Yii::t('app', 'Content ID'),
            'transaction_time' => Yii::t('app', 'Thời gian giao dịch'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Trạng thái'),
            'shortcode' => Yii::t('app', 'Shortcode'),
            'description' => Yii::t('app', 'Description'),
            'cost' => Yii::t('app', 'Cost'),
            'channel' => Yii::t('app', 'Kênh giao dịch'),
            'event_id' => Yii::t('app', 'Event ID'),
            'error_code' => Yii::t('app', 'Error Code'),
            'subscriber_activity_id' => Yii::t('app', 'Subscriber Activity ID'),
            'subscriber_service_asm_id' => Yii::t('app', 'Subscriber Service Asm ID'),
            'site_id' => Yii::t('app', 'Service Provider ID'),
            'application' => 'Ứng dụng',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberServiceAsms()
    {
        return $this->hasMany(SubscriberServiceAsm::className(), ['transaction_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberServiceAsms0()
    {
        return $this->hasMany(SubscriberServiceAsm::className(), ['cancel_transaction_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberServiceAsms1()
    {
        return $this->hasMany(SubscriberServiceAsm::className(), ['last_renew_transaction_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriber()
    {
        return $this->hasOne(Subscriber::className(), ['id' => 'subscriber_id']);
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
    public function getSubscriberActivity()
    {
        return $this->hasOne(SubscriberActivity::className(), ['id' => 'subscriber_activity_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberServiceAsm()
    {
        return $this->hasOne(SubscriberServiceAsm::className(), ['id' => 'subscriber_service_asm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceProvider()
    {
        return $this->hasOne(ServiceProvider::className(), ['id' => 'site_id']);
    }


    /**
     * @return array
     */
    public static function listStatus()
    {
        $lst = [
            self::STATUS_SUCCESS => 'Thành công',
            self::STATUS_FAIL => 'Thất bại',
            self::STATUS_PENDING => 'Đang xử lý',
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
            self::TYPE_REGISTER => 'Đăng ký gói',
            self::TYPE_RENEW => 'Gia hạn',
//            self::TYPE_RETRY => 'Retry',
            self::TYPE_DOWNLOAD => 'Tải',
            self::TYPE_CANCEL => 'Hủy gói',
            self::TYPE_CONTENT_PURCHASE => 'Mua lẻ',
//            self::TYPE_CONTENT_PURCHASE_DOWNLOAD => 'Download',
            self::TYPE_CHARGE_COIN => 'Nạp coin',
        ];
        return $lst;
    }

    /**
     * @return array
     */
    public static function listTypeReport()
    {
        $lst = [
            self::TYPE_REGISTER => 'Đăng ký',
            self::TYPE_RENEW => 'Gia hạn',
            self::TYPE_DOWNLOAD => 'Tải',
            self::TYPE_CONTENT_PURCHASE => 'Mua lẻ',
//            self::TYPE_CONTENT_PURCHASE_DOWNLOAD => 'Download',
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
            self::CHANNEL_TYPE_SMS => 'Sms',
//            self::CHANNEL_TYPE_WAP => 'Wap',
            self::CHANNEL_TYPE_MOBILEWEB => 'Mobile Web',
            self::CHANNEL_TYPE_CSKH => 'CSKH',
            self::CHANNEL_TYPE_ANDROID => 'Android',
            self::CHANNEL_TYPE_IOS => 'Ios',
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

    public function get_event()
    {
        switch ($this->type) {
            case self::TYPE_USER_CANCEL:
            case self::TYPE_CANCEL_BY_API_VNPT:
            case self::TYPE_CANCEL_SERVICE_BY_CHANGE_PACKAGE:
            case self::TYPE_CANCEL_SERVICE_BY_SYSTEM:
            case self::TYPE_CANCEL:
                return "UNSUBSCRIBE";
            case self::TYPE_CONTENT_PURCHASE:
            case self::TYPE_REGISTER:
                return "SUBSCRIBE";
            case self::TYPE_RENEW:
                return "RENEW";
        }
        return '';
    }

}
