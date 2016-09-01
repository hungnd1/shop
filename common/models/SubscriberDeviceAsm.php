<?php

namespace common\models;

use api\helpers\Message;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subscriber_device_asm".
 *
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $device_id
 * @property integer $status
 * @property string $decscription
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Subscriber $subscriber
 * @property Device $device
 */
class SubscriberDeviceAsm extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_REMOVED = -1;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subscriber_device_asm';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'device_id'], 'required'],
            [['subscriber_id', 'device_id', 'created_at', 'updated_at'], 'integer'],
            [['decscription'], 'string', 'max' => 255]
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
            'device_id' => 'Device ID',
            'decscription' => 'Decscription',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
    public function getDevice()
    {
        return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }

    public function getSubscribers($subscriberId, $siteId, $dealerId) {
        if ($subscriberId) {
            return Subscriber::find()->where(['id'=>$subscriberId, 'site_id'=>$siteId, 'dealer_id'=>$dealerId])->all();
        } else {
            return Subscriber::find()->all();
        }
    }

    public function getNotOwnedDevices($subscriberId, $site_id, $dealer_id) {
        $lst = SubscriberDeviceAsm::find()
            ->select(['device_id as id'])
            ->andWhere(["subscriber_id"=>$subscriberId])
            ->asArray()
            ->all();

        return Device::find()->andWhere(['status'=>Device::STATUS_ACTIVE, 'site_id'=>$site_id, 'dealer_id'=>$dealer_id])->andOnCondition(['not in','id',$lst])->all();
    }

    /**
     * @param $subscriber_id
     * @param $device_id
     * @return array
     */
    public static function createSubscriberDeviceAsm($subscriber_id,$device_id){
        $res = [];
        /** @var  $sda SubscriberDeviceAsm*/
        $sda = SubscriberDeviceAsm::findOne(['subscriber_id'=>$subscriber_id, 'device_id' =>$device_id ]);
        if($sda){
            $sda->updated_at = time();
            if (!$sda->save()) {
                $message = $sda->getFirstMessageError();
                $res['status'] = false;
                $res['message'] = $message;
                return $res;
            }
            $res['status'] = true;
            $res['message'] = Message::MSG_SUCCESS;
            $res['item'] = $sda;
            return $res;
        }

        $sda = new SubscriberDeviceAsm();
        $sda->subscriber_id = $subscriber_id;
        $sda->device_id = $device_id;

        /** Validate và save, nếu có lỗi thì return message_error */
        if (!$sda->validate() || !$sda->save()) {
            $message = $sda->getFirstMessageError();
            $res['status'] = false;
            $res['message'] = $message;
            return $res;
        }
        $res['status'] = true;
        $res['message'] = Message::MSG_SUCCESS;
        $res['item'] = $sda;
        return $res;
    }

    private function getFirstMessageError(){
        $error = $this->firstErrors;
        $message = "";
        foreach ($error as $key => $value) {
            $message .= $value;
            break;
        }
        return $message;
    }
}
