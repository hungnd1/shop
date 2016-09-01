<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_activity".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property string $ip_address
 * @property string $user_agent
 * @property string $action
 * @property integer $target_id
 * @property integer $target_type
 * @property integer $created_at
 * @property string $description
 * @property string $status
 * @property integer $site_id
 * @property integer $dealer_id
 * @property string $request_detail
 * @property string $request_params
 *
 * @property Dealer $dealer
 * @property Site $site
 * @property User $user
 */
class UserActivity extends \yii\db\ActiveRecord
{
    const ACTION_TARGET_TYPE_USER = 1;
    const ACTION_TARGET_TYPE_CAT = 2;
    const ACTION_TARGET_TYPE_CONTENT = 3;
    const ACTION_TARGET_TYPE_SUBSCRIBER = 4;
    const ACTION_TARGET_TYPE_SERVICE_PROVIDER = 5;
    const ACTION_TARGET_TYPE_DEALER = 6;
    const ACTION_TARGET_TYPE_PRICING = 7;
    const ACTION_TARGET_TYPE_SERVICE = 8;
    const ACTION_TARGET_TYPE_CREDENTIAL = 9;
    const ACTION_TARGET_TYPE_DEVICE = 10;
    const ACTION_TARGET_TYPE_OTHER = 12;


    public static $action_targets = [
        self::ACTION_TARGET_TYPE_USER => 'User',
        self::ACTION_TARGET_TYPE_CAT => 'Category',
        self::ACTION_TARGET_TYPE_CONTENT => 'Content',
        self::ACTION_TARGET_TYPE_SUBSCRIBER => 'Subscriber',
        self::ACTION_TARGET_TYPE_SERVICE_PROVIDER => 'Site',
        self::ACTION_TARGET_TYPE_DEALER => 'Dealer',
        self::ACTION_TARGET_TYPE_PRICING => 'Pricing',
        self::ACTION_TARGET_TYPE_SERVICE => 'Service',
        self::ACTION_TARGET_TYPE_CREDENTIAL => 'API Key',
        self::ACTION_TARGET_TYPE_DEVICE => 'Device',
        self::ACTION_TARGET_TYPE_OTHER => 'Other'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'target_id', 'target_type', 'created_at', 'site_id', 'dealer_id'], 'integer'],
            [['description', 'request_params'], 'string'],
            [['username', 'user_agent', 'status'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 45],
            [['action'], 'string', 'max' => 126],
            [['request_detail'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'username' => 'Username',
            'ip_address' => 'Ip Address',
            'user_agent' => 'User Agent',
            'action' => 'Action',
            'target_id' => 'Target ID',
            'target_type' => 'Target Type',
            'created_at' => 'Created At',
            'description' => 'Description',
            'status' => 'Status',
            'site_id' => 'Site ID',
            'dealer_id' => 'Dealer ID',
            'request_detail' => 'Request Detail',
            'request_params' => 'Request Params',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'created_at',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealer::className(), ['id' => 'dealer_id']);
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
