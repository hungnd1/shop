<?php

namespace common\models;

use api\helpers\Message;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "device".
 *
 * @property integer $id
 * @property string $device_id
 * @property integer $device_type
 * @property string $device_firmware
 * @property string $last_ip
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $site_id
 * @property integer $dealer_id
 * @property integer $expired_at
 * @property integer activated_at
 *
 * @property Site $site
 * @property SubscriberDeviceAsm[] $subscriberDeviceAsms
 */
class Device extends \yii\db\ActiveRecord
{
    const IPT_ORDER = 'order';
    const IPT_DEVICE_TYPE = 'device_type';
    const IPT_MAC = 'device_id';
    const IPT_STATUS = 'status';
    const IPT_DEALER = 'dealer_id';
    const IPT_FIRMWARE = 'firmware';

    const TYPE_SMARTBOXV1 = 1;
    const TYPE_SMARTBOXV2 = 2;

    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;
    const STATUS_NEW = 1;
    const STATUS_DELETED = -1;

    // ro.build.display.id cua VNPT-Tech Smartboxes
    const TYPE_DISPLAY_SMB_1 = 'VNT001SB'; // SMB v1
    const TYPE_DISPLAY_SMB_2_ARM = 'VNPTT_SMB_AML_VER2'; // SMB v2 Armlogic
    const TYPE_DISPLAY_SMB_2_INTEL = 'VNPTT_SMB_INTEL'; // SMB v2 PC Chip Intel

    // Cau hinh cac chat luong video khong duoc ho tro theo gia tri ro.build.display.id cua thiet bi
    private static $unsupported_qualities_by_display_id = [
        self::TYPE_DISPLAY_SMB_1 => [
            ContentProfile::QUALITY_H265,
            ContentProfile::QUALITY_FP
        ],
        self::TYPE_DISPLAY_SMB_2_ARM => [
            ContentProfile::QUALITY_FP
        ],
        self::TYPE_DISPLAY_SMB_2_INTEL => [
            ContentProfile::QUALITY_H265,
            ContentProfile::QUALITY_FP
        ],
    ];

    public $activated_date;

//    public static $device_types = [
//        self::TYPE_SMARTBOXV2 => 'SMARTBOX V2',
//        self::TYPE_SMARTBOXV1 => 'SMARTBOX V1',
//    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_id', 'site_id'], 'required'],
            [['dealer_id'], 'required', 'message' => 'Bạn chưa chọn đại lý quản lý thiết bị này!'],
            [['device_type', 'created_at', 'updated_at', 'status', 'site_id', 'activated_at', 'expired_at'], 'integer'],
            [['device_id', 'last_ip'], 'string', 'max' => 45],
            [['device_firmware'], 'string', 'max' => 100],
            [['device_id'], 'validateUnique'],
            [
                'device_id',
                'match', 'pattern' => '/^([0-9A-Fa-f]{2}){6}$/',
                'message' => 'Địa chỉ Mac không đúng định dạng. Ví dụ định dạng đúng: 1ff2acbd2a3c'
            ],
            ['activated_date', 'safe']
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
            'id' => Yii::t('app', 'ID'),
            'device_id' => Yii::t('app', 'Địa chỉ MAC'),
            'device_type' => Yii::t('app', 'Loại thiết bị'),
            'device_firmware' => Yii::t('app', 'Firmware'),
            'last_ip' => Yii::t('app', 'Ip cuối'),
            'created_at' => Yii::t('app', 'Thời gian tạo'),
            'updated_at' => Yii::t('app', 'Thời gian cập nhật'),
            'status' => Yii::t('app', 'Trạng thái'),
            'site_id' => Yii::t('app', 'Thị trường'),
            'dealer_id' => Yii::t('app', 'Đại lý'),
            'activated_at' => Yii::t('app', 'Ngày kích hoạt'),
            'expired_at' => Yii::t('app', 'Thời gian hết hạn'),
            'activated_date' => Yii::t('app', 'Ngày kích hoạt'),
        ];
    }

    public function validateUnique($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($attribute == 'device_id') {
                $obj = static::find()
                    ->where(['device_id' => strtoupper($this->device_id)])
                    ->andWhere(['not', ['status'=>self::STATUS_DELETED]])
                    ->andWhere(['not', ['id' => $this->id]])
                    ->one();
            }

            if ($obj) {
                $this->addError($attribute, $this->getAttributeLabel($attribute) . ' đã tồn tại.');
            }
        }
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
    public function getDealer()
    {
        return $this->hasOne(Dealer::className(), ['id' => 'dealer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberDeviceAsms()
    {
        return $this->hasMany(SubscriberDeviceAsm::className(), ['device_id' => 'id']);
    }

    public static function getListStatus()
    {
        return [
            self::STATUS_NEW => 'Mới giao đại lý',
            self::STATUS_ACTIVE => 'Hoạt động',
            self::STATUS_INACTIVE => 'Không hoạt động',
//            self::STATUS_DELETED => 'Xóa',

        ];
    }

    public static function getListAvailableStatusesValue()
    {
        return [
            self::STATUS_NEW, self::STATUS_ACTIVE, self::STATUS_INACTIVE
        ];
    }

    public static function getListAvailableDeviceTypesValue()
    {
        return [
            self::TYPE_SMARTBOXV1, self::TYPE_SMARTBOXV2
        ];
    }

    public function getStatusName()
    {
        $listStatus = self::getListStatus();
        if (isset($listStatus[$this->status])) {
            return $listStatus[$this->status];
        }

        return '';
    }

    /**
     * @return array
     */
    public static function listDeviceTypes()
    {
        $lst = [
            self::TYPE_SMARTBOXV2 => 'SMARTBOX V2',
            self::TYPE_SMARTBOXV1 => 'SMARTBOX V1',
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getDeviceTypeName()
    {
        $lst = self::listDeviceTypes();
        if (array_key_exists($this->device_type, $lst)) {
            return $lst[$this->device_type];
        }
        return $this->device_type;
    }

    public function getDealerName()
    {
        if ($this->dealer) {
            return $this->dealer->name;
        }
    }

    public static function getMacAddress($device_id, $type, $status)
    {
        $mac = self::findOne(['device_id' => $device_id, 'status' => $status, 'type' => $type]);
        return $mac->device_id;
    }

    /**
     * @param $mac_address
     * @param $site_id
     * @param bool|true $status
     * @return null|static
     */
    public static function findByMac($mac_address, $site_id, $status = true)
    {
        if (!$status) {
            return Device::findOne(['device_id' => $mac_address, 'site_id' => $site_id]);
        }
        return Device::findOne(['device_id' => $mac_address, 'site_id' => $site_id, 'status' => Device::STATUS_ACTIVE]);
    }

    public static function findBySubscriber($subscriberId)
    {
        $deviceIds = SubscriberDeviceAsm::find()
            ->select(['device_id as id'])
            ->andWhere(["subscriber_id" => $subscriberId, "status" => SubscriberDeviceAsm::STATUS_ACTIVE])
            ->asArray()
            ->all();

        if ($deviceIds) {
            return Device::find()->where(['in', 'id', $deviceIds])->all();
        }
    }



    /**
     * @param $display_id ro.build.display.id cua client
     * @return array|mixed Danh sach cac chat luong khong ho tro tren thiet bi
     */
    public static function getUnsupportedQualities($display_id)
    {
        if (!$display_id) {
            $res['success']  = false;
            $res['message'] = Message::MSG_FAIL;
            return $res;
        }
        $display_id = strtoupper(trim($display_id));
        if (!array_key_exists($display_id, self::$unsupported_qualities_by_display_id)) {
            $res['success']  = false;
            $res['message'] = Message::MSG_NOT_DATA;
            return $res;
        }
        $res['success']  = true;
        $res['message'] = Message::MSG_SUCCESS;
        $res['data'] = self::$unsupported_qualities_by_display_id[$display_id];
        return $res;
    }
}
