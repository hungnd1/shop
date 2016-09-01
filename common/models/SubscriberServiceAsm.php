<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%subscriber_service_asm}}".
 *
 * @property integer $id
 * @property integer $service_id
 * @property integer $subscriber_id
 * @property integer $site_id
 * @property integer $dealer_id
 * @property string $msisdn
 * @property string $service_name
 * @property string $description
 * @property integer $activated_at
 * @property integer $renewed_at
 * @property integer $expired_at
 * @property integer $last_renew_fail_at
 * @property integer $renew_fail_count
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $pending_date
 * @property integer $view_count
 * @property integer $download_count
 * @property integer $gift_count
 * @property integer $watching_time
 * @property integer $subscriber2_id
 * @property integer $transaction_id
 * @property integer $cancel_transaction_id
 * @property integer $last_renew_transaction_id
 * @property integer $canceled_at
 * @property integer $auto_renew
 *
 * @property Dealer $dealer
 * @property Service $service
 * @property Subscriber $subscriber
 * @property Site $site
 * @property Subscriber $subscriber2
 * @property SubscriberTransaction $transaction
 * @property SubscriberTransaction $cancelTransaction
 * @property SubscriberTransaction $lastRenewTransaction
 * @property SubscriberTransaction[] $subscriberTransactions
 */
class SubscriberServiceAsm extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;
    const STATUS_PENDING = 2;
    const STATUS_RESTORE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subscriber_service_asm}}';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id', 'subscriber_id', 'site_id', 'activated_at', 'auto_renew'], 'required'],
            [['canceled_at', 'service_id', 'auto_renew', 'subscriber_id', 'site_id','dealer_id', 'activated_at', 'renewed_at', 'expired_at', 'last_renew_fail_at', 'renew_fail_count', 'status', 'created_at', 'updated_at', 'pending_date', 'view_count', 'download_count', 'gift_count', 'watching_time', 'subscriber2_id', 'transaction_id', 'cancel_transaction_id', 'last_renew_transaction_id'], 'integer'],
            [['description'], 'string'],
            [['msisdn'], 'string', 'max' => 20],
            [['service_name'], 'string', 'max' => 45]
        ];
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_id' => 'Service ID',
            'subscriber_id' => 'Subscriber ID',
            'site_id' => 'Service Provider ID',
            'dealer_id' => 'Dealer ID',
            'msisdn' => 'Msisdn',
            'service_name' => 'Service Name',
            'description' => 'Description',
            'activated_at' => 'Activated At',
            'renewed_at' => 'Renewed At',
            'expired_at' => 'Expired At',
            'last_renew_fail_at' => 'Last Renew Fail At',
            'renew_fail_count' => 'Renew Fail Count',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'pending_date' => 'Pending Date',
            'view_count' => 'View Count',
            'download_count' => 'Download Count',
            'gift_count' => 'Gift Count',
            'watching_time' => 'Watching Time',
            'subscriber2_id' => 'Subscriber2 ID',
            'transaction_id' => 'Transaction ID',
            'cancel_transaction_id' => 'Cancel Transaction ID',
            'last_renew_transaction_id' => 'Last Renew Transaction ID',
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
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriber2()
    {
        return $this->hasOne(Subscriber::className(), ['id' => 'subscriber2_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return $this->hasOne(SubscriberTransaction::className(), ['id' => 'transaction_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCancelTransaction()
    {
        return $this->hasOne(SubscriberTransaction::className(), ['id' => 'cancel_transaction_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastRenewTransaction()
    {
        return $this->hasOne(SubscriberTransaction::className(), ['id' => 'last_renew_transaction_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberTransactions()
    {
        return $this->hasMany(SubscriberTransaction::className(), ['subscriber_service_asm_id' => 'id']);
    }

    /**
     * ******************************** MY FUNCTION ***********************
     */

    /**
     * @return array
     */
    public static function listStatus()
    {
        $lst = [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_RESTORE => 'Restore',
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
     * @param $subscriber Subscriber
     * @param $service Service
     * @return SubscriberServiceAsm|null
     */
    public static function createNewMapping($subscriber, $service)
    {
        $mapping = new SubscriberServiceAsm();
        $mapping->subscriber_id = $subscriber->id;
        $mapping->service_id = $service->id;
        $mapping->activated_at = time();
        $mapping->expired_at = time();
        $mapping->created_at = time();
        $mapping->auto_renew = Service::TYPE_AUTO_RENEW;
        $mapping->download_count = ($service->free_download_count) ? $service->free_download_count : 0;
        if ($mapping->save()) {
            return $mapping;
        } else {
            Yii::error($mapping->getErrors());
            return null;
        }
    }
}
