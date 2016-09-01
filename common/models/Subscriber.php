<?php

namespace common\models;

use api\helpers\Message;
use backend\controllers\VnpController;
use common\charging\helpers\ChargingGW;
use common\charging\models\ChargingConnection;
use common\charging\models\ChargingResult;
use common\helpers\CommonConst;
use common\helpers\CUtils;
use common\helpers\ResMessage;
use common\helpers\VasProvisioning;
use DateInterval;
use DateTime;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Yii;
use yii\base\InvalidCallException;
use yii\behaviors\TimestampBehavior;
use yii\console\Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "{{%subscriber}}".
 *
 * @property integer $id
 * @property integer $site_id
 * @property integer $dealer_id
 * @property integer $authen_type
 * @property integer $channel
 * @property string $msisdn
 * @property string $username
 * @property integer $balance
 * @property integer $status
 * @property string $email
 * @property string $address
 * @property string $city
 * @property string $full_name
 * @property string $auth_key
 * @property string $password_hash
 * @property integer $last_login_at
 * @property integer $last_login_session
 * @property integer $birthday
 * @property integer $sex
 * @property string $avatar_url
 * @property string $skype_id
 * @property string $google_id
 * @property string $facebook_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer activated_at
 * @property integer $client_type
 * @property integer $using_promotion
 * @property integer $auto_renew
 * @property integer $verification_code
 * @property string $user_agent
 * @property integer $expired_at
 *
 * @property ContentFeedback[] $contentFeedbacks
 * @property ContentKeyword[] $contentKeywords
 * @property ContentViewLog[] $contentViewLogs
 * @property ReportMonthlyCpRevenueDetail[] $reportMonthlyCpRevenueDetails
 * @property SmsMessage[] $smsMessages
 * @property Site $site
 * @property Dealer $dealer
 * @property SubscriberActivity[] $subscriberActivities
 * @property SubscriberContentAsm[] $subscriberContentAsms
 * @property SubscriberContentAsm[] $subscriberContentAsms0
 * @property SubscriberFavorite[] $subscriberFavorites
 * @property SubscriberFeedback[] $subscriberFeedbacks
 * @property SubscriberServiceAsm[] $subscriberServiceAsms
 * @property SubscriberServiceAsm[] $subscriberServiceAsms0
 * @property SubscriberToken[] $subscriberTokens
 * @property SubscriberTransaction[] $subscriberTransactions
 * @property Service[] $services
 * @property Content[] $contents
 */
class Subscriber extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $access_token;

    const STATUS_ACTIVE   = 10;
    const STATUS_INACTIVE = 1;
    const STATUS_DELETED  = 0;

    const SEX_NAM = 0;
    const SEX_NU  = 1;

//    const CLIENT_TYPE_WAP = 1;
    //    const CLIENT_TYPE_ANDROID = 2;
    //    const CLIENT_TYPE_IOS = 3;
    //    const CLIENT_TYPE_WP = 4;   //WindownPhone

    const CHANNEL_TYPE_API    = 1;
    const CHANNEL_TYPE_SYSTEM = 2;
    const CHANNEL_TYPE_CSKH   = 3;
    const CHANNEL_TYPE_SMS    = 4;
//    const CHANNEL_TYPE_WAP = 5;
    const CHANNEL_TYPE_MOBILEWEB = 6;
    const CHANNEL_TYPE_ANDROID   = 7;
    const CHANNEL_TYPE_IOS       = 8;

    const RENEW_AUTO     = 1;
    const RENEW_NOT_AUTO = 0;

    const AUTHEN_TYPE_ACCOUNT     = 1;
    const AUTHEN_TYPE_MAC_ADDRESS = 2;
//    const AUTHEN_TYPE_MSISDN = 3;

    /*
     * @var string password for register scenario
     */
    public $password;
    public $confirm_password;
    public $new_password;
    public $old_password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subscriber}}';
    }

    public static function getDb()
    {
        return Yii::$app->db;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'username', 'authen_type', 'password_hash'], 'required'], // Bỏ required với msisdn
            [['username', 'msisdn'], 'validateUnique', 'on' => 'create'], //** Enable cái này nếu cần thiết => $model->setScenario('create'); */
            [
                [
                    'site_id',
                    'dealer_id',
                    'authen_type',
                    'channel',
                    'status',
                    'last_login_at',
                    'last_login_session',
                    'birthday',
                    'sex',
                    'created_at',
                    'updated_at',
                    'client_type',
                    'using_promotion',
                    'auto_renew',
                    'expired_at',
                ],
                'integer',
            ],
            [
                'username',
                'match', 'pattern' => '/^[\*a-zA-Z0-9]{1,20}$/',
                'message' => 'Thông tin không hợp lệ, tên tài khoản - Tối đa 20 ký tự (bao gồm chữ cái và số) không bao gồm ký tự đặc biệt ',
//                'on' => 'create'
            ],
            [['msisdn'], 'string', 'max' => 45],
//            [
            //                'msisdn',
            ////                'match', 'pattern' => '/^0[0-9]$/',
            //                'match', 'pattern' => '/^(0)\d{9,10}$/',
            //                'message' => 'Thông tin không hợp lệ, số điện thoại - Định dạng số điện thoại bắt đầu với số 0, ví dụ 0912345678, 012312341234',
            ////                'on' => ['create','update'],
            //            ],
            [['verification_code', 'auth_key'], 'string', 'max' => 32],
            [['username', 'email'], 'string', 'max' => 100],
            [['full_name', 'password'], 'string', 'max' => 200],
            [['password_hash', 'address', 'city'], 'string', 'max' => 255],
            [['avatar_url', 'skype_id', 'google_id', 'facebook_id'], 'string', 'max' => 255],
            [['user_agent'], 'string', 'max' => 512],
            //cuongvm
            ['password', 'string', 'min' => 8, 'tooShort' => 'Mật khẩu không hợp lệ. Mật khẩu ít nhất 8 ký tự'],
//            [
            //                'password',
            //                'match', 'pattern' => '/^[\a-zA-Z0-9]{8,16}$/',
            //                'message' => 'Thông tin không hợp lệ, mật khẩu bao gồm 8 - 16 chữ cái hoặc số, xin vui lòng nhập lại',
            //            ],
            ['confirm_password', 'string', 'min' => 8, 'tooShort' => 'Xác nhận mật khẩu không hợp lệ, ít nhất 8 ký tự'],
            ['new_password', 'string', 'min' => 8, 'tooShort' => 'Mật khẩu không hợp lệ, ít nhất 8 ký tự'],
            [['confirm_password', 'password', 'dealer_id'], 'required', 'on' => 'create'],
            [
                ['confirm_password'],
                'compare',
                'compareAttribute' => 'password',
                'message'          => 'Xác nhận mật khẩu không đúng.',
                'on'               => 'create',
            ],
            [
                ['confirm_password'],
                'compare',
                'compareAttribute' => 'new_password',
                'message'          => 'Xác nhận mật khẩu chưa đúng.',
                'on'               => 'change-password',
            ],
            [['new_password'], 'required', 'on' => 'change-password'],
            [['old_password', 'new_password', 'confirm_password'], 'required', 'on' => 'change-password'],
            [['email'], 'email', 'message' => 'Email không đúng định dạng'],
            [['balance'], 'integer', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
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
            'id'                 => Yii::t('app', 'ID'),
            'site_id'            => Yii::t('app', 'Nhà cung cấp'),
            'dealer_id'          => Yii::t('app', 'Đại lý'),
            'authen_type'        => Yii::t('app', 'Loại xác thực'),
            'channel'            => Yii::t('app', 'Kênh đăng ký'),
            'msisdn'             => Yii::t('app', 'Số điện thoại'),
            'username'           => Yii::t('app', 'Tên tài khoản'),
            'auth_key'           => 'Auth Key',
            'password_hash'      => 'Password Hash',
            'status'             => Yii::t('app', 'Trạng thái'),
            'email'              => Yii::t('app', 'Email'),
            'full_name'          => Yii::t('app', 'Họ và tên'),
            'password'           => Yii::t('app', 'Mật khẩu'),
            'confirm_password'   => Yii::t('app', 'Mật khẩu xác nhận'),
            'last_login_at'      => Yii::t('app', 'Last Login At'),
            'last_login_session' => Yii::t('app', 'Last Login Session'),
            'birthday'           => Yii::t('app', 'Ngày tháng năm sinh'),
            'sex'                => Yii::t('app', 'Giới tính'),
            'avatar_url'         => Yii::t('app', 'Avatar Url'),
            'skype_id'           => Yii::t('app', 'Skype ID'),
            'google_id'          => Yii::t('app', 'Google ID'),
            'facebook_id'        => Yii::t('app', 'Facebook ID'),
            'created_at'         => Yii::t('app', 'Created At'),
            'updated_at'         => Yii::t('app', 'Updated At'),
            'client_type'        => Yii::t('app', 'Client Type'),
            'using_promotion'    => Yii::t('app', 'Using Promotion'),
            'auto_renew'         => Yii::t('app', 'Auto Renew'),
            'verification_code'  => Yii::t('app', 'Verification Code'),
            'user_agent'         => Yii::t('app', 'User Agent'),
            'balance'            => Yii::t('app', 'Tài khoản ví'),
            'address'            => Yii::t('app', 'Địa chỉ'),
            'city'               => Yii::t('app', 'Tỉnh/ Thành phố'),
        ];
    }

    public function validateUnique($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $subscriber = Subscriber::findOne(['username' => $this->username, 'status' => [Subscriber::STATUS_ACTIVE, Subscriber::STATUS_INACTIVE]]);
            if ($subscriber) {
                $this->addError($attribute, 'Tên tài khoản đã tồn tại. Vui lòng chọn tên khác!');
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentFeedbacks()
    {
        return $this->hasMany(ContentFeedback::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentKeywords()
    {
        return $this->hasMany(ContentKeyword::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentViewLogs()
    {
        return $this->hasMany(ContentViewLog::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportMonthlyCpRevenueDetails()
    {
        return $this->hasMany(ReportMonthlyCpRevenueDetail::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSmsMessages()
    {
        return $this->hasMany(SmsMessage::className(), ['subscriber_id' => 'id']);
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
    public function getSubscriberActivities()
    {
        return $this->hasMany(SubscriberActivity::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberContentAsms()
    {
        return $this->hasMany(SubscriberContentAsm::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberContentAsms0()
    {
        return $this->hasMany(SubscriberContentAsm::className(), ['subscriber2_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberFavorites()
    {
        return $this->hasMany(SubscriberFavorite::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberFeedbacks()
    {
        return $this->hasMany(SubscriberFeedback::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberServiceAsms()
    {
        return $this->hasMany(SubscriberServiceAsm::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberServiceAsms0()
    {
        return $this->hasMany(SubscriberServiceAsm::className(), ['subscriber2_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberTokens()
    {
        return $this->hasMany(SubscriberToken::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberTransactions()
    {
        return $this->hasMany(SubscriberTransaction::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::className(), ['id' => 'service_id'])
            ->viaTable('subscriber_service_asm', ['subscriber_id' => 'id'], function ($query) {
                return $query->onCondition(['status' => SubscriberServiceAsm::STATUS_ACTIVE]);
            });
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContents()
    {
        return $this->hasMany(Service::className(), ['id' => 'content_id'])
            ->viaTable('subscriber_content_asm', ['subscriber_id' => 'id']);
    }

    /**
     * ******************************** MY FUNCTION ***********************
     */
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateVerifyCode()
    {
        $this->verification_code = Yii::$app->security->generateRandomString(6);
    }

    public static function findByVerifyToken($verify_code, $username)
    {
        return static::findOne([
            'verification_code' => $verify_code,
            'username'          => $username,
            'status'            => self::STATUS_INACTIVE,
        ]);
    }

    /**
     * @param $username
     * @param $site_id
     * @param bool|true $status
     * @return null|static
     */
    public static function findByUsername($username, $site_id, $status = true)
    {
        if (!$status) {
            return Subscriber::findOne(['username' => $username, 'site_id' => $site_id]);
        }
        return Subscriber::findOne(['username' => $username, 'site_id' => $site_id, 'status' => Subscriber::STATUS_ACTIVE]);
    }

    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $username
     * @param $password
     * @param $authen_type
     * @param $site_id
     * @param null $mac_address
     * @return array
     */
    public static function register($username, $password, $msisdn, $city = null, $status = Subscriber::STATUS_ACTIVE, $authen_type, $site_id, $channel = Subscriber::CHANNEL_TYPE_ANDROID, $mac_address = null)
    {
        $res = [];
        /** Chuyển sang chữ thường */
        $username    = strtolower($username);
        $mac_address = strtolower($mac_address);

        $subscriber              = new Subscriber();
        $subscriber->username    = $username;
        $subscriber->status      = $status;
        $subscriber->msisdn      = $msisdn;
        $subscriber->city        = $city;
        $subscriber->site_id     = $site_id;
        $subscriber->channel     = $channel;
        $subscriber->authen_type = $authen_type;
        $subscriber->password    = ($authen_type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS) ? CUtils::randomString(8) : $password;
        $subscriber->setPassword($password);
        $subscriber->generateAuthKey();
        /** Validate và save, nếu có lỗi thì return message_error */
        if (!$subscriber->validate()) {
            $message        = $subscriber->getFirstMessageError();
            $res['status']  = false;
            $res['message'] = $message;
            return $res;
        }
        if (!$subscriber->save()) {
            $res['status']  = false;
            $res['message'] = Message::MSG_FAIL;
            return $res;
        }
        /** TODO tạo bảng quan hệ Subscriber với Device mỗi khi tạo account */
        if ($mac_address) {
            /** @var  $device Device */
            $device = Device::findByMac($mac_address, $site_id);
            if ($device) {
                SubscriberDeviceAsm::createSubscriberDeviceAsm($subscriber->id, $device->id);
            }
        }

        /** TODO gán cho subscriber default gói cước mặc định của thị trường */
        if ($authen_type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS) {
            /** @var  $site Site */
            $site = Site::findOne($site_id);
            /** @var  $service Service */
            $service = $site->defaultService;
            if ($service) {
                $ssa                = new SubscriberServiceAsm();
                $ssa->subscriber_id = $subscriber->id;
                $ssa->service_id    = $service->id;
                $ssa->service_name  = $service->name;
                $ssa->site_id       = $site_id;
                $ssa->activated_at  = time();

                $expiryDate = new DateTime();
                if (isset($service->period) && $service->period > 0) {
                    $expiryDate->add(new DateInterval("P" . $service->period . 'D'));
                }

                /** Nếu charging_period <=0 thì set expired_at =null. Theo yêu cầu BA và có confirm của ViệtNV */
                $ssa->expired_at = $service->period > 0 ? $expiryDate->getTimestamp() : null;
                $ssa->status     = SubscriberServiceAsm::STATUS_ACTIVE;
                $ssa->save();
            }
        }

//        $item = $subscriber->getAttributes(['id', 'username','full_name', 'msisdn', 'status', 'site_id', 'created_at', 'updated_at'], ['password_hash', 'authen_type']);
        $res['status']     = true;
        $res['message']    = Message::MSG_REGISTER_SUCCESS;
        $res['subscriber'] = $subscriber;
        return $res;
    }

    private function getFirstMessageError()
    {
        $error   = $this->firstErrors;
        $message = "";
        foreach ($error as $key => $value) {
            $message .= $value;
            break;
        }
        return $message;
    }

    public function saveProperties($mac_address = null)
    {
        $res = [];
        /** Chuyển sang chữ thường */
        $this->username = strtolower($this->username);
        $mac_address    = strtolower($mac_address);

        if (!$this->status) {
            $this->status = Subscriber::STATUS_ACTIVE;
        }
        if ($this->authen_type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS) {
            $this->password = CUtils::randomString(8);
        }
        if ($this->password) {
            $this->setPassword($this->password);
            $this->generateAuthKey();
        }
        /** Validate và save, nếu có lỗi thì return message_error */
        if (!$this->validate()) {
            $message        = $this->getFirstMessageError();
            $res['status']  = false;
            $res['message'] = $message;
            return $res;
        }
        if (!$this->save()) {
            $res['status']  = false;
            $res['message'] = Message::MSG_FAIL;
            return $res;
        }
        /** TODO tạo bảng quan hệ Subscriber với Device mỗi khi tạo account */
        if ($mac_address) {
            /** @var  $device Device */
            $device = Device::findByMac($mac_address, $this->site_id);
            if ($device) {
                SubscriberDeviceAsm::createSubscriberDeviceAsm($this->id, $device->id);
            }
        }

        /** TODO gán cho subscriber default gói cước mặc định của thị trường */
        if ($this->authen_type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS) {
            /** @var  $site Site */
            $site = Site::findOne($this->site_id);
            /** @var  $service Service */
            $service = $site->defaultService;
            if ($service) {
                $ssa                = new SubscriberServiceAsm();
                $ssa->subscriber_id = $this->id;
                $ssa->service_id    = $service->id;
                $ssa->service_name  = $service->name;
                $ssa->site_id       = $this->site_id;
                $ssa->activated_at  = time();

                $expiryDate = new DateTime();
                if (isset($service->period) && $service->period > 0) {
                    $expiryDate->add(new DateInterval("P" . $service->period . 'D'));
                }

                /** Nếu charging_period <=0 thì set expired_at =null. Theo yêu cầu BA và có confirm của ViệtNV */
                $ssa->expired_at = $service->period > 0 ? $expiryDate->getTimestamp() : null;
                $ssa->status     = SubscriberServiceAsm::STATUS_ACTIVE;
                $ssa->save();
            }
        }

        $res['status']     = true;
        $res['message']    = Message::MSG_REGISTER_SUCCESS;
        $res['subscriber'] = $this;
        return $res;
    }

//    public static function register($username, $password, $site_id)
    //    {
    //        $res = [];
    //        if (self::findSubscriberBySP($msisdn, $site_id, false)) {
    //            $res['status'] = false;
    //            $res = ['message' => 'Đã có tài khoản này rồi'];
    //            return $res;
    //        }
    //        $defaultPassword = '123456';
    //        $subscriber = new Subscriber();
    //        $subscriber->username = $msisdn;
    //        $subscriber->msisdn = $msisdn;
    //        $subscriber->setPassword($defaultPassword);
    //        $subscriber->verification_code = $defaultPassword;
    //        $subscriber->site_id = $site_id;
    //        $subscriber->created_at = time();
    //        $subscriber->updated_at = time();
    //        if ($status) {
    //            $subscriber->status = Subscriber::STATUS_ACTIVE;
    //        } else {
    //            $subscriber->status = Subscriber::STATUS_INACTIVE;
    //        }
    //
    //        if ($subscriber->save()) {
    //            $res['status'] = true;
    //            $res['message'] = "Đăng ký thành công";
    //        } else {
    //            $res['status'] = false;
    //            $res['message'] = "Đăng ký thất bại";
    //            $res['err'] = $subscriber->getFirstErrors();
    //        }
    //
    //        return $res;
    //    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* @var SubscriberToken $subscriber_token */
        /* @var Subscriber $subscriber */
        $subscriber_token = SubscriberToken::findByAccessToken($token);

        if ($subscriber_token) {
            $subscriber = $subscriber_token->getSubscriber()->one();
            if ($subscriber) {
                $subscriber->access_token = $token;
            }

            return $subscriber;
        }

        return null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
        return $this->auth_key;
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @return array
     */
    public static function listStatus()
    {
        $lst = [
            self::STATUS_ACTIVE   => 'Hoạt động',
            self::STATUS_INACTIVE => 'Tạm khóa',
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
    public static function listClientType()
    {
        $lst = [
            SubscriberTransaction::CHANNEL_TYPE_API       => 'API',
            SubscriberTransaction::CHANNEL_TYPE_SYSTEM    => 'SYSTEM',
            SubscriberTransaction::CHANNEL_TYPE_CSKH      => 'CSKH',
            SubscriberTransaction::CHANNEL_TYPE_SMS       => 'SMS',
//            SubscriberTransaction::CHANNEL_TYPE_WAP => 'Wap',
            SubscriberTransaction::CHANNEL_TYPE_MOBILEWEB => 'Mobile Web',
            SubscriberTransaction::CHANNEL_TYPE_ANDROID   => 'Android',
            SubscriberTransaction::CHANNEL_TYPE_IOS       => 'IOS',
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getClientTypeName()
    {
        $lst = self::listClientType();
        if (array_key_exists($this->client_type, $lst)) {
            return $lst[$this->client_type];
        }
        return $this->client_type;
    }

    /**
     * @return array
     */
    public static function listSex()
    {
        $lst = [
            self::SEX_NAM => 'Nam',
            self::SEX_NU  => 'Nữ',
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getSexName()
    {
        $lst = self::listSex();
        if (array_key_exists($this->sex, $lst)) {
            return $lst[$this->sex];
        }
        return $this->sex;
    }

    public function getDisplayName()
    {
        if ($this->full_name != null && $this->full_name != '') {
            return $this->full_name;
        }

        return $this->username;
    }

    public function getDealerName()
    {
        $dealer = $this->getDealer()->one();
        if ($dealer) {
            return $dealer->name;
        }

        return '';
    }

    /**
     * @param $msisdn
     * @param $site_id
     * @param bool $create
     * @return Subscriber|null|static
     */
    public static function findByMsisdn($msisdn, $site_id, $create = false)
    {
//        $msisdn = CUtils::validateMobile($msisdn);
        $subscriber = Subscriber::findOne([
            'msisdn'  => $msisdn,
            'status'  => self::STATUS_ACTIVE,
            'site_id' => $site_id,
        ]);
        if (!$create) {
            return $subscriber;
        } else {
            if ($subscriber) {
                return $subscriber;
            } else {
                $subscriber = new Subscriber();

                $subscriber->msisdn   = $msisdn;
                $subscriber->username = $msisdn;
                $subscriber->site_id  = $site_id;
                $subscriber->status   = Subscriber::STATUS_ACTIVE;

                if ($subscriber->save()) {
                    return $subscriber;
                } else {
                    Yii::trace($subscriber->errors);
                }
            }
            return null;
        }
    }

    /**
     * @param $promotion
     * @param $trial
     * @param $bundle
     * @param $service Service
     */
    public function getVpntPurchasePrice($promotion, $trial, $bundle, $service)
    {
        if (Service::freeFirst($service) && $this->isFirstRegister($service)) {
            $result = [
                'period'     => $service->period,
                'price'      => 0,
                'auto_renew' => 1,
            ];
        } else {
            $result = [
                'period'     => $service->period,
                'price'      => $service->price,
                'auto_renew' => 1,
            ];
        }

        if ($bundle == 1) {
            $result = [
                'period'     => $service->period,
                'price'      => 0,
                'auto_renew' => 0,
            ];
            return $result;
        }
        $promotion = strtoupper($promotion);
        if ($promotion != 0) {
            switch ($promotion) {
                case CUtils::endsWith($promotion, 'C'):
                    $result = [
                        'period'     => $service->period * (int) $promotion,
                        'price'      => 0,
                        'auto_renew' => 1,
                    ];
                    break;
                case CUtils::endsWith($promotion, 'D'):
                    $result = [
                        'period'     => (int) $promotion,
                        'price'      => 0,
                        'auto_renew' => 1,
                    ];
                    break;
                case CUtils::endsWith($promotion, 'W'):
                    $result = [
                        'period'     => 7 * (int) $promotion,
                        'price'      => 0,
                        'auto_renew' => 1,
                    ];
                    break;
                case CUtils::endsWith($promotion, 'M'):
                    $result = [
                        'period'     => 30 * (int) $promotion,
                        'price'      => 0,
                        'auto_renew' => 1,
                    ];
                    break;
            }
            return $result;
        }
        $trial = strtoupper($trial);
        if ($trial != 0) {
            switch ($trial) {
                case CUtils::endsWith($trial, 'C'):
                    $result = [
                        'period'     => $service->period * (int) $trial,
                        'price'      => 0,
                        'auto_renew' => 1,
                    ];
                    break;
                case CUtils::endsWith($trial, 'D'):
                    $result = [
                        'period'     => (int) $trial,
                        'price'      => 0,
                        'auto_renew' => 1,
                    ];
                    break;
                case CUtils::endsWith($trial, 'W'):
                    $result = [
                        'period'     => 7 * (int) $trial,
                        'price'      => 0,
                        'auto_renew' => 1,
                    ];
                    break;
                case CUtils::endsWith($trial, 'M'):
                    $result = [
                        'period'     => 30 * (int) $trial,
                        'price'      => 0,
                        'auto_renew' => 1,
                    ];
                    break;
            }
            return $result;
        }
        return $result;
    }

    /**
     * Lay ra danh sach cac thue bao va cac goi cuoc den ky gia han
     * TODO: Check thue bao white list hoac thue bao ko gia han o ngoai ham nay
     * @param $sp Site
     * @param $partition_count
     * @param $partition
     * @return array
     */
    public static function getSubscribersToExtendByPartition($sp, $partition_count, $partition)
    {
        // quet nhung thang het han tu thoi diem nay, de tranh trh ngay hom truoc ko gia han het!!
        // TODO: co the gian rong khoang thoi gian nay ra de quet nhung thang ko gia han kip cua ca tuan truoc
        $minExpiredTime = time() - 20 * 24 * 60 * 60;

        $maxExpiredTime = time() + 3 * 60 * 60; // Thoi gian het han lon nhat dc xu ly trong lan nay, (lay nhung sub co goi het han tu now() den thoi diem nay)

        $lastRetryTime = time() - 21 * 3600; // chi xet cac trh gia han loi truoc thoi diem nay, de tranh viec gia han lai trong cung ngay

        //TODO: test
        //        $minExpiredTime = "2014-05-14 00:00:00";
        //        $maxExpiredTime = "2014-05-14 12:00:00";
        //        $lastRetryTime = "2014-05-14 12:00:00";

        echo "\nSelect all subscribers to extend service with expiry_date from " . date('d-m-Y H:i:s',
            $minExpiredTime) . ' to ' . date('d-m-Y H:i:s',
            $maxExpiredTime) . " or last retry before " . date('d-m-Y H:i:s', $lastRetryTime) . "\n";

        $sql = "select * from `subscriber_service_asm` ssa" .
        " where " . ($partition_count > 1 ? "(ssa.id % $partition_count = $partition) AND " : "") . // xu ly phan theo partition
        " ssa.site_id = " . $sp->id . " AND" .
        " ssa.auto_renew = 1 AND" .
        " ssa.status = 10 AND " . // dieu kien chung
        " (" . // lev 1
        " (ssa.renew_fail_count = 0 and ssa.expired_at >= $minExpiredTime and ssa.expired_at < $maxExpiredTime)" . // truong hop het han goi cuoc
        " OR (ssa.renew_fail_count > 0 and (ssa.last_renew_fail_at is null or ssa.last_renew_fail_at < $lastRetryTime) )" . // truong hop gia han loi cua cac ngay truoc
        " )" . // lev 1
        " order by ssa.renew_fail_count asc, ssa.expired_at desc";

        echo "\n$sql\n";
        $ssas = SubscriberServiceAsm::findBySql($sql)->all();

        return $ssas;
    }

    /**
     * @param $transType
     * @param $channelType
     * @param $description
     * @param null $service Service
     * @param null $content Content
     * @param $status
     * @param int $cost
     * @param string $telco_code
     * @param Site $service_provider
     * @return SubscriberTransaction
     */
    public function newTransaction(
        $transType,
        $channelType,
        $description,
        $service = null,
        $content = null,
        $status = SubscriberTransaction::STATUS_FAIL,
        $cost = 0,
        $currency = 'VND',
        $balance = 0,
        $service_provider = null
    ) {
        $tr                = new SubscriberTransaction();
        $tr->subscriber_id = $this->id;
        $tr->site_id       = $this->site_id;
        $tr->msisdn        = $this->msisdn;
        $tr->type          = $transType;
        $tr->channel       = $channelType;
        $tr->description   = $description;
        /** @var $service Service */
        if ($service) {
            $tr->service_id = $service->id;
            $tr->site_id    = $service->site_id;

        }
        /** @var $content Content */
        if ($content) {
            $tr->content_id = $content->id;
        }
        if ($service_provider) {
            $tr->site_id = $service_provider->id;
        }
        $tr->created_at       = time();
        $tr->status           = $status;
        $tr->cost             = $cost;
        $tr->currency         = $currency;
        $tr->balance          = $balance;
        $tr->transaction_time = time();
        $tr->save(false);
        return $tr;
    }

    /**
     * @param $cancelPackage Service
     * @param int $channel_type
     * @param int $transaction_type
     * @param int $moID
     * @param bool $sendSMS
     * @param null $serviceNumber
     * @return array
     * @throws \Exception
     */
    public function cancelServicePackage(
        $cancelPackage,
        $channel_type = SubscriberTransaction::CHANNEL_TYPE_SMS,
        $transaction_type = SubscriberTransaction::TYPE_CANCEL_SERVICE_BY_SYSTEM,
        $sendSMS = false,
        $serviceNumber = null
    ) {
        /* @var $cancelPackage Service */
        $service_package_id = $cancelPackage->id;

        $subscriberServicesAsm = $this->subscriberServiceAsms;
//        $subscriberServicesAsm = SubscriberServiceAsm::find()
        //            ->andWhere(['status' => SubscriberServiceAsm::STATUS_ACTIVE])
        //            ->andWhere(['subscriber_id' => $this->id])
        //            ->all();

        /* @var $found SubscriberServiceAsm */
        /* @var $serviceAsm SubscriberServiceAsm */
        $found = null;
        foreach ($subscriberServicesAsm as $serviceAsm) {
//            $package = $packageAsm->servicePackage;

            // kiem tra goi cuoc da ton tai chua
            if ($serviceAsm->service_id == $service_package_id && $serviceAsm->status == SubscriberServiceAsm::STATUS_ACTIVE) {
                // goi cuoc muon mua da duoc dang ky truoc do
                $found = $serviceAsm;
                break;
            }
        }

        if (!$found) {
            // ko tim thay goi cuoc can huy
            return array(
                "error"   => CommonConst::API_ERROR_SERVICE_PACKAGE_NOT_PURCHASED,
                "message" => ResMessage::cancelFailByNotRegister($this, $cancelPackage, $sendSMS, $serviceNumber),
            );
        } else {
            // tim thay goi cuoc can huy
            $found->status     = SubscriberServiceAsm::STATUS_INACTIVE;
            $found->updated_at = time();
            $success           = $found->save(true, ['status', 'updated']);

            if (!$success) {
                CUtils::log("ERROR: can not inactivate ssa: " . Json::encode($found));
            }

            $tranDesc = "Hủy gói gói cước '" . $cancelPackage->display_name . "'";
            $tr       = $this->newTransaction($transaction_type, $channel_type, $tranDesc, $cancelPackage);
            //TODO goi dong qua qua VMS?
            // them transaction
            $tr->status     = $success ? SubscriberTransaction::STATUS_SUCCESS : SubscriberTransaction::STATUS_FAIL; //lay trang thai cua viec cap nhat trang thai inactive o tren
            $tr->cost       = 0; // huy ko mat tien
            $tr->error_code = ChargingResult::CHARGING_RESULT_OK;

            if (!$tr->update()) {
                CUtils::log("ERROR: cannot save transaction: " . Json::encode($tr));
            }

            if ($success) {

                return array(
                    "error"   => CommonConst::API_ERROR_NO_ERROR,
                    "message" => ResMessage::cancelServiceSuccess($this, $cancelPackage, $sendSMS, $serviceNumber),
                );
            } else {
                //TODO notification service cancel fail
                return array(
                    "error"   => CommonConst::API_ERROR_SYSTEM_ERROR,
                    "message" => ResMessage::cancelFailBySystemError($this, $cancelPackage, $sendSMS, $serviceNumber),
                );
            }
        }
    }

    /**
     * @param $service Service
     * @return bool
     */
    public function isFirstRegister($service, $maxDay = 90)
    {
        //Check goi cuoc trong 1 group da dc dang ky lan nao chua
        $service_related = $service->getPackageOnGroup(false);

        $ssm = SubscriberServiceAsm::find()
            ->andWhere(['subscriber_id' => $this->id])
            ->andWhere(['service_id' => $service->id])
            ->andWhere(['site_id' => $service->site_id])
            ->count();

        $lastRegister = SubscriberServiceAsm::find()
            ->andWhere(['subscriber_id' => $this->id])
            ->andWhere(['service_id' => $service_related])
            ->andWhere(['site_id' => $service->site_id])
            ->count();
        if ($ssm > 0 || $lastRegister > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $service Service
     * @param $channel_type int
     *
     */
    public function changeService($service,
        $channel_type = SubscriberTransaction::CHANNEL_TYPE_SMS, $sendSMS = false, $smsSuccess = true) {
        //Kiem tra dieu kien huy goi
        $chargingSuccess1       = false;
        $subscriber_service_asm = SubscriberServiceAsm::find()->andWhere(["subscriber_id" => $this->id, "status" => SubscriberServiceAsm::STATUS_ACTIVE])
            ->andWhere("expired_at > :p_expired_at", [":p_expired_at" => time()])->all();
        if (!$subscriber_service_asm) {
            return array(
                "error"       => CommonConst::API_ERROR_NO_SERVICE_PACKAGE,
                "api_message" => "Đổi gói thất bại, bạn chưa đăng ký gói nào",
            );
        }
        /** @var ServiceGroupAsm $service_group_change */
        $service_group_change = ServiceGroupAsm::findOne(["service_id" => $service->id]);
        if (!$service_group_change) {
            return array(
                "error"       => CommonConst::API_ERROR_SERVICE_PACKAGE_ALREADY_PURCHASED,
                "api_message" => "Đổi gói thất bại, gói bạn định đổi không thuộc nhóm nào",
            );
        }
        /** @var Service $cancel_service */
//            $cancel_service = $service_group_change->service;
        $cancel_service = null;

        //Kiem tra xem co phan tu nao cung nhom vs goi dinh doi va goi do da dc dang ky.
        /** @var SubscriberServiceAsm $ssa */
        foreach ($subscriber_service_asm as $ssa) {
            if ($ssa->service_id == $service->id) {
                return array(
                    "error"       => CommonConst::API_ERROR_SERVICE_PACKAGE_ALREADY_PURCHASED,
                    "api_message" => "Đổi gói thất bại, bạn đã đăng ký gói này",
                );
            }
            /** @var ServiceGroupAsm $subscriber_group_asm */
            $subscriber_group_asm = ServiceGroupAsm::find(["service_group_id" => $service_group_change->service_group_id, "service_id" => $ssa->service_id])->one();
            if (!$subscriber_group_asm) {
                $cancel_service = $subscriber_group_asm->service;
            }
        }

        if ($cancel_service != null) {
            // Tien hanh huy goi
            $connection  = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                $cancel_service->status     = SubscriberServiceAsm::STATUS_INACTIVE;
                $cancel_service->updated_at = time();
                if ($cancel_service->save()) {
                    $tranDesc       = "Hủy gói dịch vụ '" . $cancel_service->name . "'";
                    $tr             = $this->newTransaction(SubscriberTransaction::TYPE_CANCEL_SERVICE_BY_CHANGE_PACKAGE, $channel_type, $tranDesc, $cancel_service);
                    $tr->status     = SubscriberTransaction::STATUS_SUCCESS;
                    $tr->cost       = 0; // huy ko mat tien
                    $tr->error_code = ChargingResult::CHARGING_RESULT_OK;
                    if ($cancel_service->save()) {
                        $charging_connection = new ChargingConnection($cancel_service->site->vivas_gw_host, $cancel_service->site->vivas_gw_port, $cancel_service->site->vivas_gw_username, $cancel_service->site->vivas_gw_password);
                        $chargingRes         = ChargingGW::getInstance($charging_connection)->cancelPackage($this->msisdn, $cancel_service, $tr->id, $channel_type);
                        if ($chargingRes->result == ChargingResult::CHARGING_RESULT_OK) {
                            $chargingSuccess1 = true;
                        }
                    } else {
                        return array(
                            "error"       => CommonConst::API_ERROR_SYSTEM_ERROR,
                            "api_message" => "Hệ thống đang lỗi. Vui lòng thử lại.",
                        );
                    }
                    if ($tr->save() && $chargingSuccess1 == true) {
                        // Tien hanh mua goi
                        $tranDesc = "Mua gói dịch vụ '" . $service->display_name . "'";
                        $tr       = $this->newTransaction(SubscriberTransaction::TYPE_REGISTER_BY_CHANGE_PACKAGE, $channel_type, $tranDesc, $service);

                        // tien hanh charge tien
                        $price           = 0;
                        $chargingSuccess = false;

                        $originPrice   = round($service->price);
                        $promotion     = false;
                        $promotionNote = "";
                        //TODO xet khuyen mai o day
                        if (Service::freeFirst($service) && $this->isFirstRegister($service)) {
                            $price         = 0;
                            $promotion     = true;
                            $promotionNote = "Dang ky goi " . $service->display_name . ' lan dau';
                        } else {
                            $price = $originPrice;
                        }

                        //TODO
                        /* @var $chargingRes ChargingResult */
                        $charging_connection = new ChargingConnection($service->site->vivas_gw_host, $service->site->vivas_gw_port, $service->site->vivas_gw_username, $service->site->vivas_gw_password);
                        $chargingRes         = ChargingGW::getInstance($charging_connection)->registerPackage($this->msisdn, $price, $service, $tr->id, $channel_type, $promotion, $promotionNote);

                        $chargingTelco  = $chargingRes->error;
                        $chargingResult = $chargingRes->result;
                        if ($chargingRes->result == ChargingResult::CHARGING_RESULT_OK) {
                            $chargingSuccess = true;
                        }
                        Yii::trace("LOG : charging_code: " . $chargingRes->error);

                        $tr->error_code = $chargingResult;
                        //TODO partner_id?
                        $tr->status  = $chargingSuccess ? SubscriberTransaction::STATUS_SUCCESS : SubscriberTransaction::STATUS_FAIL;
                        $tr->cost    = $price;
                        $tr->site_id = $service->site_id;
                        if (!$tr->update()) {
                            Yii::trace("ERROR: cannot update transaction: " . Json::encode($tr->getErrors()));
                        }

                        //TODO Fix charge luon thanh cong. Chay that thi bo di
                        // $chargingSuccess = true;
                        if ($chargingSuccess) {
                            // charging thanh cong --> tao mapping trong SubscriberServicePackageAsm va tra mt
                            $ssa                 = new SubscriberServiceAsm();
                            $ssa->subscriber_id  = $this->id;
                            $ssa->msisdn         = $this->msisdn;
                            $ssa->service_name   = $service->display_name;
                            $ssa->service_id     = $service->id;
                            $ssa->site_id        = $service->site_id;
                            $ssa->transaction_id = $tr->id;
                            $ssa->status         = SubscriberServiceAsm::STATUS_ACTIVE;
                            $activationDate      = new \DateTime();
                            $expiryDate          = new \DateTime();
                            $ssa->auto_renew     = $service->auto_renew;

                            if (isset($service->charging_period) && $service->charging_period > 0) {
                                $expiryDate->add(new DateInterval("P" . $service->charging_period . 'D'));
                            }
                            $ssa->activated_at = $activationDate->getTimestamp();
                            $ssa->expired_at   = $expiryDate->getTimestamp();
                            // them partner_id , link_ads_id

                            $ssa->renew_fail_count = 0;

                            if ($ssa->save()) {
                                $transaction->commit();
                                /**
                                 * So dang trong whitelist thi ko chay sync vasgate
                                 */
                                //TODO dong bo vasgate

//                                $msgParam = [
                                //                                    ResMtParams::PARAM_SERVICE_PRICE => $service->price,
                                //                                    ResMtParams::PARAM_SERVICE_PERIOD => $service->period,
                                //                                    ResMtParams::PARAM_SERVICE_NAME => $service->display_name,
                                //
                                //                                ];
                                $tr->subscriber_service_asm_id = $ssa->id;
                                if ($promotion) {
                                    $message = ResMessage::firstRegisterSuccess($this, $service, date('d-m-Y', $ssa->expired_at), $sendSMS);
                                } else {
                                    $message = ResMessage::registerSuccess($this, $service, date('d-m-Y', $ssa->expired_at), $sendSMS);
                                }
                                $err_code = CommonConst::API_ERROR_NO_ERROR;
                                return array(
                                    "error"       => CommonConst::API_ERROR_NO_ERROR,
                                    "api_message" => "Đổi gói thành công",
                                );
                            } else {
                                return array(
                                    "error"       => CommonConst::API_ERROR_SYSTEM_ERROR,
                                    "api_message" => "Lỗi hệ thống vui lòng thử lại",
                                );
                            }
                        } else {
                            return array(
                                "error"       => CommonConst::API_ERROR_CHARGING_FAIL,
                                "api_message" => "Lỗi hệ thống vui lòng thử lại",
                            );
                        }
                    }
                } else {
                    // Huy goi that bai
                    $tranDesc       = "Hủy gói dịch vụ '" . $cancel_service->name . "'";
                    $tr             = $this->newTransaction(SubscriberTransaction::TYPE_CANCEL_SERVICE_BY_CHANGE_PACKAGE, $channel_type, $tranDesc, $cancel_service);
                    $tr->status     = SubscriberTransaction::STATUS_FAIL;
                    $tr->cost       = 0; // huy ko mat tien
                    $tr->error_code = ChargingResult::CHARGING_RESULT_OK;
                    $tr->save();
                    $transaction->commit();
                }
            } catch (Exception $e) {
                Yii::trace($e);
                $transaction->rollback();
                return array(
                    "error"       => CommonConst::API_ERROR_SYSTEM_ERROR,
                    "api_message" => "Lỗi hệ thống, vui lòng thử lại",
                );
            }

        } else {
            return array(
                "error"       => CommonConst::API_ERROR_SERVICE_PACKAGE_ALREADY_PURCHASED,
                "api_message" => "Đổi gói thất bại, bạn cần hủy gói trước khi đổi gói",
            );
        }

    }

    /**
     * @param $purchaseService Service
     * @param int $channel_type
     * @param int $transaction_type
     * @param bool $sendSMS
     * @param int $smsPrice
     * @return array
     * @throws \Exception
     */
    public function purchaseServicePackage(
        $purchaseService,
        $channel_type = SubscriberTransaction::CHANNEL_TYPE_SMS,
        $transaction_type = SubscriberTransaction::TYPE_REGISTER,
        $sendSMS = false,
        $smsPrice = 0
    ) {

        $groupAsms = $purchaseService->serviceGroupAsms;
        $activeGroup = false;
        foreach ($groupAsms as $groupAsm) {
            if ($groupAsm->serviceGroup->status == ServiceGroup::STATUS_ACTIVE) {
                $activeGroup = true;
                break;
            }
        }
        if (!$activeGroup) {
            return array(
                "error"   => CommonConst::API_ERROR_INVALID_SERVICE_PACKAGE,
                "message" => "Nhóm gói cước đã tạm dừng"
            );
        }

        $service_package_id = $purchaseService->id;
        $currentPackageAsms = $this->subscriberServiceAsms;

        if ($transaction_type == SubscriberTransaction::TYPE_REGISTER) {
            foreach ($currentPackageAsms as $packageAsm) {
                // kiem tra trang thai cua cac goi da mua
                if ($packageAsm->status != SubscriberServiceAsm::STATUS_ACTIVE) {
                    continue;
                }
                /** @var  $service Service */
                $service = $packageAsm->service;

                /**
                 * Kiem tra co trung voi goi cuoc da mua hay ko
                 */
                if ($packageAsm->service_id == $service_package_id) {
                    // goi cuoc muon mua da duoc dang ky truoc do

                    $message = ResMessage::registerFailByDuplicate($this, $service, $sendSMS);

                    return array(
                        "error"   => CommonConst::API_ERROR_SERVICE_PACKAGE_ALREADY_PURCHASED,
                        "message" => $message,
                    );

                }

                /**
                 * Kiem tra goi cuoc mua co trung voi goi cuoc trong cung group hay ko (group: vtv -> goi ngay,goi tuan,goi thang)
                 * Trong mot group thi chi dc mua 1 goi cuoc trong group do
                 */
                $groups1 = $service->serviceGroupAsms;
                $groups2 = $purchaseService->serviceGroupAsms;

                foreach ($groups1 as $group1) {
                    /** @var $group1 ServiceGroupAsm */
                    foreach ($groups2 as $group2) {
                        /** @var $group2 ServiceGroupAsm */

                        if ($group1->service_group_id == $group2->service_group_id) {
//                            $message = ResMessage::registerFailByDuplicateGroup($this, $group1->service, $sendSMS);
                            //                            return array(
                            //                                "error" => CommonConst::API_ERROR_ANOTHER_SERVICE_PACKAGE_IN_GROUP_PURCHASED,
                            //                                "message" => $message
                            //                            );
                            /** Truong hop dang ky goi moi cung nhom voi goi cuoc dang dang ky
                             * Huy goi cuoc cu thay bang goi cuoc moi
                             */
                            $cancelServicePackages[] = $service;
//                            $this->cancelServicePackage($service, $channel_type, $transaction_type);
                        }
                    }
                }
            }
        }

        // them transaction
        $tranDesc = ($transaction_type == SubscriberTransaction::TYPE_REGISTER ? "Mua gói dịch vụ '" : "Gia hạn dịch vụ '") . $purchaseService->display_name . "'";
        $tr       = $this->newTransaction($transaction_type, $channel_type, $tranDesc, $purchaseService);

        // tien hanh charge tien
        $chargingSuccess = false;
        $price           = round($channel_type == SubscriberTransaction::CHANNEL_TYPE_SMS ? $purchaseService->pricing->price_sms : $purchaseService->pricing->price_coin);

        if ($channel_type == SubscriberTransaction::CHANNEL_TYPE_SMS && $smsPrice >= $price) {
            $chargingSuccess = true;
        } else if ($price <= $this->balance) {
            $newBalance    = $this->balance - $price;
            $this->balance = $newBalance;
            $this->update(true, ['balance']);
            $chargingSuccess = true;
        }

        //TODO partner_id?
        $tr->status   = $chargingSuccess ? SubscriberTransaction::STATUS_SUCCESS : SubscriberTransaction::STATUS_FAIL;
        $tr->cost     = 0;
        $tr->balance  = -$price;
        $tr->site_id  = $purchaseService->site_id;
        $tr->currency = $channel_type == SubscriberTransaction::CHANNEL_TYPE_SMS ? $purchaseService->site->currency : 'coin';

        $err_code = CommonConst::API_ERROR_UNKNOWN;
        $message  = '';
        if ($chargingSuccess) {
            // charging thanh cong --> tao mapping trong SubscriberServicePackageAsm va tra mt
            $expiryDate = new \DateTime();
            if ($transaction_type == SubscriberTransaction::TYPE_RENEW) {
                $ssa             = SubscriberServiceAsm::findOne(['subscriber_id' => $this->id, 'service_id' => $service_package_id, 'status' => SubscriberServiceAsm::STATUS_ACTIVE]);
                $ssa->renewed_at = (new DateTime())->getTimestamp();
                if ($ssa->status == SubscriberServiceAsm::STATUS_ACTIVE && $expiryDate->getTimestamp() < $ssa->expired_at) {
                    $expiryDate = (new DateTime())->setTimestamp($ssa->expired_at);
                }
            } else {
                $ssa                   = new SubscriberServiceAsm();
                $ssa->subscriber_id    = $this->id;
                $ssa->msisdn           = $this->msisdn;
                $ssa->service_name     = $purchaseService->display_name;
                $ssa->service_id       = $service_package_id;
                $ssa->site_id          = $purchaseService->site_id;
                $activationDate        = new \DateTime();
                $ssa->auto_renew       = $purchaseService->auto_renew;
                $ssa->renew_fail_count = 0;
                $ssa->activated_at     = $activationDate->getTimestamp();
            }
            $ssa->transaction_id = $tr->id;

            if (isset($purchaseService->period) && $purchaseService->period > 0) {
                $expiryDate->add(new DateInterval("P" . $purchaseService->period . 'D'));
                $ssa->expired_at = $expiryDate->getTimestamp();
            } else {
                // Neu goi cuoc ko co thoi han thi ngay het han de trong
                $ssa->expired_at = null;
            }

            $ssa->status = SubscriberServiceAsm::STATUS_ACTIVE;

            if (!$ssa->save()) {
                Yii::trace("ERROR: cannot save ssa: " . Json::encode($ssa));
            }

            $tr->subscriber_service_asm_id = $ssa->id;
            $message                       = ResMessage::registerSuccess($this, $purchaseService, date('d-m-Y', $ssa->expired_at), $sendSMS);
            // Huy goi cuoc cu thuoc cung nhom voi goi cuoc moi
            if (isset($cancelServicePackages)) {
                foreach ($cancelServicePackages as $service) {
                    $this->cancelServicePackage($service, SubscriberTransaction::CHANNEL_TYPE_SYSTEM, SubscriberTransaction::TYPE_CANCEL, 0, false);
                }
            }
            $err_code = CommonConst::API_ERROR_NO_ERROR;

        } else {
            $message  = ResMessage::registerFailByMoney($this, $purchaseService, $sendSMS);
            $err_code = CommonConst::API_ERROR_CHARGING_FAIL;

        }

        if (!$tr->update()) {
            Yii::trace("ERROR: cannot update transaction: " . Json::encode($tr->getErrors()));
        }
        return array(
            "error"   => $err_code,
            "message" => $message,
        );
    }

    /**
     * @param $content Content
     * @param int $channel_type
     * @param int $transaction_type
     * @param Site $sp
     * @return array
     * @throws \Exception
     */
    public function purchaseContent(
        $sp,
        $content,
        $channel_type = SubscriberTransaction::CHANNEL_TYPE_SMS,
        $transaction_type = SubscriberTransaction::TYPE_CONTENT_PURCHASE
    ) {
        $currentPackageAsms = SubscriberServiceAsm::find()->andWhere(["subscriber_id" => $this->id, "status" => SubscriberServiceAsm::STATUS_ACTIVE])
            ->andWhere("expired_at > :p_expired_at", [":p_expired_at" => time()])->all();
        $catIds         = [];
        $contentCatAsms = $content->parent ? $content->parent->contentCategoryAsms : $content->contentCategoryAsms;
        foreach ($contentCatAsms as $catAsm) {
            $catIds[] = $catAsm->category_id;
        }

        /* @var $packageAsm SubscriberServiceAsm */
        foreach ($currentPackageAsms as $packageAsm) {
            $cats = $packageAsm->service->serviceCategoryAsms;
            foreach ($cats as $catPurchased) {
                if (in_array($catPurchased->category_id, $catIds)) {
                    return array(
                        "error"       => CommonConst::API_ERROR_SERVICE_PACKAGE_ALREADY_PURCHASED,
                        "message_web" => 'Nội dung thuộc gói cước bạn đã mua',
                        "message"     => 'Nội dung thuộc gói cước bạn đã mua',
                    );
                }
            }
        }
        foreach ($this->subscriberContentAsms as $contentAsm) {
            // cap nhat trang thai neu ban ghi het han
            if ($contentAsm->expired_at < time() && $contentAsm->status == SubscriberContentAsm::STATUS_ACTIVE) {
                $contentAsm->status = SubscriberContentAsm::STATUS_INACTIVE;
                $contentAsm->save(false);
                continue;
            }
            if ($content->id == $contentAsm->content_id && $contentAsm->status == SubscriberContentAsm::STATUS_ACTIVE && $contentAsm->purchase_type == SubscriberContentAsm::TYPE_PURCHASE) {
                return array(
                    "error"       => CommonConst::API_ERROR_CONTENT_ALREADY_PURCHASED,
                    "message_web" => 'Bạn đã mua nội dung này',
                    "message"     => 'Bạn đã mua nội dung này',
                );
            }
        }

        // tim thay goi cuoc can mua them h
        if ($content->getIsFree($sp->id)) {
            // goi cuoc ko app dung gia han thoi gian
            return array(
                "error"       => CommonConst::API_ERROR_NOT_FOR_SALE,
                "message_web" => 'Nội dung miễn phí hoặc không được phép mua lẻ',
                "message"     => 'Nội dung miễn phí hoặc không được phép mua lẻ',
            );
        }

        $tranDesc = "Mua lẻ nội dung: " . $content->display_name;

        /** @var SubscriberTransaction $tr */
        $tr = $this->newTransaction($transaction_type, $channel_type, $tranDesc, null, $content);

        // tien hanh charging
        $chargingSuccess = false;

        $price = $channel_type == SubscriberTransaction::CHANNEL_TYPE_SMS ? round($content->getPriceSms($sp->id)) : round($content->getPriceCoin($sp->id));

        if ($this->expired_at && $this->expired_at < time()) {
            return array(
                "error"       => CommonConst::API_ERROR_DEVICE_EXPIRED,
                "message_web" => 'Thiết bị đã hết hạn sử dụng',
                "message"     => 'Thiet bi da het han su dung',
            );
        } else if ($channel_type != SubscriberTransaction::CHANNEL_TYPE_SMS) {
            if ($this->balance >= $price) {
                $this->balance = $this->balance - $price;
                if ($this->save(true, ['balance'])) {
                    $chargingSuccess = true;
                }
            }
        } else {
            $chargingSuccess = true;
        }

        // them transaction
        $tr->status  = $chargingSuccess ? SubscriberTransaction::STATUS_SUCCESS : SubscriberTransaction::STATUS_FAIL; //lay trang thai cua viec cap nhat trang thai inactive o tren
        $tr->cost    = 0;
        $tr->balance = -$price;

        if (!$tr->update()) {
            Yii::trace("ERROR: cannot save transaction: " . Json::encode($tr));
        }

        if ($chargingSuccess) {
            $sca                = new SubscriberContentAsm();
            $sca->site_id       = $sp->id;
            $sca->content_id    = $content->id;
            $sca->subscriber_id = $this->id;
            $activated_at       = time();
            $expired_at         = $activated_at + $content->getWatchingPriod($sp->id) * 3600;
            $sca->activated_at  = time();
            $sca->expired_at    = $expired_at;
            $sca->status        = SubscriberContentAsm::STATUS_ACTIVE;
            $sca->msisdn        = $this->msisdn;
            $sca->purchase_type = SubscriberContentAsm::TYPE_PURCHASE;
            if (!$sca->save()) {
                CUtils::log($sca->getErrors());
                return array(
                    "error"       => CommonConst::API_ERROR_SYSTEM_ERROR,
                    "message_web" => 'Lỗi hệ thống',
                    "message"     => 'Loi he thong',
                );
            }
            return array(
                "error"       => CommonConst::API_ERROR_NO_ERROR,
                "message_web" => 'Mua nội dung thành công',
                "message"     => 'Mua noi dung thanh cong',
            );

        } else {
            return array(
                "error"       => CommonConst::API_ERROR_CHARGING_FAIL,
                "message_web" => 'Tài khoản không đủ',
                "message"     => 'Tai khoan khong du',
            );
        }
    }

    /**
     * @param $content_id
     * @param $subscriber Subscriber
     * @param null $subscriber2_id
     * @param $expired_at
     */
    public function createSubscriberContentAsm($content_id, $subscriber, $subscriber2_id = null, $expired_at, $purchase_type = null, $price)
    {
        $searchModel                = new SubscriberContentAsm();
        $searchModel->content_id    = $content_id;
        $searchModel->subscriber_id = $subscriber->id;
//        $searchModel->site_id = $subscriber->site_id;
        $searchModel->msisdn = $subscriber->msisdn;
        if ($purchase_type) {
            $searchModel->purchase_type = $purchase_type;
        }
        if ($subscriber2_id) {
            $searchModel->subscriber2_id = $subscriber2_id;
        }
        $searchModel->activated_at = time();
        $searchModel->expired_at   = time() + ($expired_at * 86400);
        $searchModel->created_at   = time();
        $searchModel->status       = SubscriberContentAsm::STATUS_ACTIVE;
        $searchModel->save();
        if (!$searchModel->validate() || !$searchModel->save()) {
            $message = $searchModel->getFirstErrors();
            foreach ($message as $error) {
                $firstError = $error;
                break;
            }
            return [
                'status'  => false,
                'message' => $firstError,
            ];
        }

        $subscriber->balance = $subscriber->balance - $price;
        if (!$subscriber->update()) {
            throw new InvalidCallException("FAILED");
        }
        $res['status']  = true;
        $res['message'] = Message::MSG_SUCCESS;
        return $res;
    }

    /**
     * @param $content Content
     * @param int $channel_type
     * @param int $transaction_type
     * @param Site $sp
     * @return array
     * @throws \Exception
     */
    public function downloadContent($sp,
        $content,
        $channel_type = SubscriberTransaction::CHANNEL_TYPE_SMS,
        $transaction_type = SubscriberTransaction::TYPE_CONTENT_PURCHASE) {
        // tim thay goi cuoc can mua them h

        //TODO xet khuyen mai o day
        $originPrice = $price_download = round($content->price_download);

        $tranDesc = "Tải nội dung:  " . $content->display_name;

        $tr = $this->newTransaction($transaction_type, $channel_type, $tranDesc, null, $content);
        /** @var ContentProfile $query */
        $query = ContentProfile::findOne(['content_id' => $content->id, 'status' => 1, 'quality' => ContentProfile::QUALITY_NORMAL, 'type' => ContentProfile::TYPE_STREAM]);
        Yii::info($query);
        if (!$query) {
            return array(
                "error"       => CommonConst::API_ERROR_LINK_FAIL,
                "message_web" => '111',
            );
        } else {
            //TODO tien hanh charging
            $chargingSuccess = false;
            /* @var $chargingRes ChargingResult */
            $charging_connection = new ChargingConnection($sp->vivas_gw_host, $sp->vivas_gw_port, $sp->vivas_gw_username, $sp->vivas_gw_password);
            $chargingRes         = ChargingGW::getInstance($charging_connection)->buyContent(
                $this->msisdn, $price_download, $content, $tr->id, $channel_type, false, '');

            $chargingStatus = $chargingRes->result . " - " . $chargingRes->error;

            if ($chargingRes->result == ChargingResult::CHARGING_RESULT_OK) {
                $chargingSuccess = true;
            }
            //TODO Fix charge luon thanh cong. Chay that thi bo di
            //$chargingSuccess = true;

            $tr->status = $chargingSuccess ? SubscriberTransaction::STATUS_SUCCESS : SubscriberTransaction::STATUS_FAIL; //lay trang thai cua viec cap nhat trang thai inactive o tren
            $tr->cost   = $price_download;

            if (!$tr->update()) {
                Yii::trace("ERROR: cannot save transaction: " . Json::encode($tr));
            }

//        $transactionID = (isset($tr) ? $tr->id : null);
            if ($chargingSuccess) {
                $url = "http://vplus.vinaphone.com.vn:8080/" . (string) $query->url;
                return array(
                    "error"         => CommonConst::API_ERROR_NO_ERROR,
                    "message_web"   => 'Thành công!',
                    "link_download" => $url,
                );
            } else {
                return array(
                    "error"       => CommonConst::API_ERROR_CHARGING_FAIL,
                    "message_web" => $chargingRes->result,
                );
            }
        }
    }

//    public function getTransactions()
    //    {
    //        $provider = new ActiveDataProvider([
    //            'query' => \api\models\SubscriberTransaction::find()->andWhere(['subscriber_id' => $this->id]),
    //            'sort' => [
    //                // Set the default sort by name ASC and created_at DESC.
    //                'defaultOrder' => [
    //                    'transaction_time' => SORT_DESC,
    ////                    'created_at' => SORT_DESC,
    ////                    'name' => SORT_ASC,
    //                ]
    //            ],
    //            // get ALl
    ////            'pagination' => false,
    //            'pagination' => [
    //                'defaultPageSize' => 10,
    //            ],
    //        ]);
    //
    //        return $provider;
    //    }

//    public function favorite($content_id, $site_id){
    //        $res = [];
    //        $sf = SubscriberFavorite::findOne([
    //                                            'subscriber_id' => $this->id,
    //                                            'content_id' => $content_id,
    //                                            'site_id' => $site_id
    //                                        ]);
    //        /* @var Content $content */
    //        $content = Content::findOne(['id' => $content_id, 'site_id' => $site_id]);
    //        if($sf){
    //            $res['status'] = false;
    //            $res['message'] = Message::MSG_ACTION_FAVORITE_ALREADY;
    //            return $res;
    //        }
    //        $subscriber_favorite = new SubscriberFavorite();
    //        $subscriber_favorite->content_id = $content_id;
    //        $subscriber_favorite->subscriber_id = $this->id;
    //        $subscriber_favorite->site_id = $site_id;
    //        $subscriber_favorite->created_at = time();
    //        $subscriber_favorite->updated_at = time();
    //    }

    /**
     * @param $id
     * @param $site_id
     * @throws BadRequestHttpException
     * @throws InternalErrorException
     * @throws ServerErrorHttpException
     * @throws \yii\db\Exception
     */
    public function favorite($id, $site_id)
    {
        $subscriber_favorite = SubscriberFavorite::findOne([
            'subscriber_id' => $this->id,
            'content_id'    => $id,
            'site_id'       => $site_id,
        ]);

        if (!$subscriber_favorite) {
            /* @var Content $content */
            $content     = Content::findOne(['id' => $id, 'site_id' => $site_id]);
            $connection  = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                if ($content) {
                    $subscriber_favorite                = new SubscriberFavorite();
                    $subscriber_favorite->content_id    = $id;
                    $subscriber_favorite->subscriber_id = $this->id;
                    $subscriber_favorite->site_id       = $site_id;
                    $subscriber_favorite->created_at    = time();
                    $subscriber_favorite->updated_at    = time();
                    if ($subscriber_favorite->save()) {
                        $content->favorite_count++;
                        if ($content->save()) {
                            $transaction->commit();
                            return Message::MSG_ACTION_FAVORITE_SUCCESS;
                        }
                    }
                } else {
                    throw new InternalErrorException(Message::MSG_ACTION_FAIL);
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw new ServerErrorHttpException(Message::MSG_ACTION_FAIL);
            }
        } else {
            throw new BadRequestHttpException(Message::MSG_ACTION_FAVORITE_ALREADY);
        }
    }

    public function unfavorite($id, $site_id)
    {
        $subscriber_favorite = SubscriberFavorite::findOne([
            'subscriber_id' => $this->id,
            'content_id'    => $id,
            'site_id'       => $site_id,
        ]);

        if ($subscriber_favorite) {
            /* @var Content $content */
            $content     = Content::findOne(['id' => $id, 'site_id' => $site_id]);
            $connection  = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                if ($content) {
                    if ($subscriber_favorite->delete()) {
                        $content->favorite_count--;
                        if ($content->save()) {
                            $transaction->commit();
                            return Message::MSG_ACTION_UNFAVORITE_SUCCESS;
                        }
                    }
                } else {
                    throw new InternalErrorException(Message::MSG_ACTION_FAIL);
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw new ServerErrorHttpException(Message::MSG_ACTION_FAIL);
            }
        } else {
            throw new BadRequestHttpException(Message::MSG_ACTION_UNFAVORITE_ALREADY);
        }
    }

    public function favorites($site_id)
    {
        $query = \api\models\SubscriberFavorite::find()
            ->andWhere(['site_id' => $site_id])
            ->andWhere(['subscriber_id' => $this->id]);

        $provider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);
        return $provider;
    }

    public function comment($title, $content_comment, $content_id, $site_id)
    {
        if ($content_comment == "") {
            throw new BadRequestHttpException(Message::MSG_ACTION_COMMENT_NO_CONTENT);
        }
        /* @var Content $content */
        $content = Content::findOne(['id' => $content_id]);
        if ($content) {
            $connection  = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                $comment                = new \common\models\SubscriberFeedback();
                $comment->content       = $content_comment;
                $comment->title         = $title;
                $comment->create_date   = time();
                $comment->subscriber_id = $this->id;
                $comment->site_id       = $site_id;
                $comment->content_id    = $content_id;
                $comment->status        = SubscriberFeedback::STATUS_ACTIVE;
                if ($comment->save()) {
                    $content->comment_count++;
                    if ($content->save()) {
                        $transaction->commit();
                        return Message::MSG_ACTION_COMMENT_SUCCESS;
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw new ServerErrorHttpException(Message::MSG_ACTION_FAIL);
            }

        } else {
            throw new InternalErrorException(Message::MSG_ACTION_FAIL);
        }

    }

    public function comments($site_id, $content_id)
    {
        $query = \api\models\SubscriberFeedback::find()
            ->andWhere(['site_id' => $site_id])
            ->andWhere(['content_id' => $content_id]);

        $provider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => [
                'defaultOrder' => [
                    'create_date' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);
        return $provider;
    }

    public function checkMyApp($content_id)
    {
        /** @var  $content Content */
        $content = Content::findOne(["id" => $content_id, "status" => Content::STATUS_ACTIVE]);
        if ($content->type == 2) {
            $content->price = 10;
        }

//        $currentPackageAsms = $this->subscriberServiceAsms;
        $currentPackageAsms = SubscriberServiceAsm::find()->andWhere(["subscriber_id" => $this->id, "status" => SubscriberServiceAsm::STATUS_ACTIVE])->all();
        $catIds             = [];
//        $contentCatAsm = $content->parent ? $content->parent->contentCategoryAsms : $content->contentCategoryAsms;
        $contentCatAsm = $content->contentCategoryAsms;

        foreach ($contentCatAsm as $catAsm) {
            $catIds[] = $catAsm->category_id;
        }

        /* @var $packageAsm SubscriberServiceAsm */
        foreach ($currentPackageAsms as $packageAsm) {
            $cats = $packageAsm->service->serviceCategoryAsms;
            Yii::info($packageAsm->service->name);
            foreach ($cats as $catPurchased) {
                if (in_array($catPurchased->category_id, $catIds)) {
                    return true;
                }
            }
        }
        foreach ($this->subscriberContentAsms as $contentAsm) {
            // cap nhat trang thai neu ban ghi het han
            if ($contentAsm->expired_at < time() && $contentAsm->status == SubscriberContentAsm::STATUS_ACTIVE) {
                $contentAsm->status = SubscriberContentAsm::STATUS_INACTIVE;
                $contentAsm->save(false);
                continue;
            }
            if ($content->id == $contentAsm->content_id && $contentAsm->status == SubscriberContentAsm::STATUS_ACTIVE && $contentAsm->purchase_type == SubscriberContentAsm::TYPE_PURCHASE) {
                return true;
            }
        }

        if ($content->is_free || !$content->price) {
            // goi cuoc ko app dung gia han thoi gian
            return true;
        }

        return false;

    }

    public static function getMsisdn()
    {
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } elseif (function_exists('http_get_request_headers')) {
            $headers = http_get_request_headers();
        } else {
            foreach ($_SERVER as $name => $value) {
                if (strncmp($name, 'HTTP_', 5) === 0) {
                    $name           = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $headers[$name] = $value;
                }
            }
        }
        $lcHeaders = [];
        foreach ($headers as $name => $value) {
            $lcHeaders[strtolower($name)] = $value;
        }

        $headers       = $lcHeaders;
        $clientIp      = $_SERVER['REMOTE_ADDR'];
        $msisdn        = isset($headers['msisdn']) ? $headers['msisdn'] : "";
        $xIpAddress    = isset($headers['x-ipaddress']) ? $headers['x-ipaddress'] : "";
        $xForwardedFor = isset($headers['x-forwarded-for']) ? $headers['x-forwarded-for'] : "";
        $userIp        = isset($headers['user-ip']) ? $headers['user-ip'] : "";
        $xWapMsisdn    = isset($headers['x-wap-msisdn']) ? $headers['x-wap-msisdn'] : "";

//        $clientIp = "113.186.0.123";
        /*if ($ip_validation) {
        $valid = preg_match('/10\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $clientIp);
        $valid |= preg_match('/113\.185\.\d{1,3}\.\d{1,3}/', $clientIp);
        $valid |= preg_match('/172\.16\.30\.\d{1,3}/', $clientIp);
        if (!$valid) {
        echo "IP invalid";
        return "";
        }
        else {
        echo "IP valid";
        }
        }*/

        if ($msisdn) {
            return $msisdn;
        }

        if ($xWapMsisdn) {
            return $xWapMsisdn;
        }

        return "";
    }

    public static function getSubscriberInfo($msisdn)
    {

        $arr = array();
        $i   = 0;
        try {
            if (empty($msisdn) || $msisdn == '' || !is_integer($msisdn)) {
                return ['message' => 'Khong ton tai nguoi dung'];
            } else {
                $query = Subscriber::find()
                    ->select('*')
                    ->from('subscriber')
                    ->andWhere(['msisdn' => $msisdn])
                    ->asArray()
                    ->all();
                foreach ($query as $val) {
                    return $val;
                }
            }

        } catch (\yii\db\Exception $ex) {
            return false;
        }
    }

    public function convertSubscriberErrorCode($error_code, $service, $sms = false)
    {
        if ($error_code == VasProvisioning::ERROR_NONE
            || $error_code == VasProvisioning::FREE_REGISTER_SUCCESS
            || $error_code == VasProvisioning::ERROR_ALREADY_REGISTER
            || $error_code == VasProvisioning::PRICE_REGISTER_SUCCESS
        ) {
            /** @var SubscriberServiceAsm $user_package_asm */
            $user_package_asm = SubscriberServiceAsm::findOne(['subscriber_id' => $this->id, 'service_id' => $service->id]);
            if ($user_package_asm) {
                return array(
                    "success" => true,
                    "message" => ResMessage::registerSuccess($this, $service, date('d-m-Y', $user_package_asm->expired_at), $sms),
                );
            } else {
                return array(
                    "success" => false,
                    "message" => ResMessage::registerFailBySystemError($this, $service, $sms),
                );
            }
        }

        if ($error_code == VasProvisioning::ERROR_NOT_ENOUGH_MONEY) {
            return array(
                "success" => false,
                "message" => ResMessage::registerFailByMoney($this, $service, $sms),
            );
        }

        return array(
            "success" => false,
            "message" => ResMessage::registerFailBySystemError($this, $service, $sms),
        );
    }

    public function buyPackageVas($service, $channel, $promotion_note, $day_promotion, $sms)
    {
        // KT xem da dk goi chua
        $is_registered = $this->isRegisteredReturn($service, $sms);
        if (!$is_registered['success']) {
            $vas_provisioning = new VasProvisioning();
            $transaction      = $this->newTransaction(SubscriberTransaction::TYPE_REGISTER, $channel, $promotion_note, $service);

//            $res = $vas_provisioning->vasRegisterPackage($this->msisdn, $service, $transaction->id, $channel, $day_promotion, $promotion_note);
            $res = $vas_provisioning->vasRegisterPackage($this->msisdn, $service, $transaction->id, 'API', 0, $promotion_note);
        } else {
            return ["success" => false, "message" => $is_registered["message"]];
        }

        $result = $this->convertSubscriberErrorCode($res->error_id, $service, false);
        return $result;

    }

    /**
     * Thuc hien logic core mua goi cuoc khi vas provisioning goi sang
     * @param $package Service
     * @param $transaction SubscriberTransaction
     * @param $channel (API|SMS|WEB|WAP|CLIENT|SYSTEM|CSKH)
     * @param int $promotion
     * @param int $trial
     * @param bool $sms
     * @return array
     */
    public function coreBuyPackage($package, $transaction, $channel, $promotion = 0, $trial = 0, $sms = true)
    {
        $res            = [];
        $res['success'] = false;
        $res['message'] = 'Thất bại';
        $is_promotion   = $this->isPromotion($package);
        // Kiem tra xem goi cuoc hien tai da dang ky hay chua
        /** @var SubscriberServiceAsm $is_my_package */
        $is_registered = $this->isRegistered($package);

        $day_promotion = 0;

        //T?o transaction
        $transaction->subscriber_id = $this->id;
        $transaction->msisdn        = $this->msisdn;
        if (empty($transaction->description)) {
            $transaction->description = empty($promotion_note) ? "Mua goi cuoc $package->name" : $promotion_note;
        }
        /**
         * Xu ly khi chua dang ky goi cuoc
         */
        if (!$is_registered) {
            /**
             * Xu ly voi truong hop promotion truyen tu Vina
             */
            $promotion_note = "";
            $day_promotion  = $package->free_days;

            if ($promotion > 0 || $trial > 0) {
                $real_price     = 0;
                $day_promotion  = ($promotion > 0) ? $promotion : $trial;
                $auto_recurring = ($promotion > 0) ? $package->period : Service::TYPE_NOT_RENEW;
                $promotion_note = "Khuyen mai theo yeu cau ben vinaphone";
            } else {
                /**
                 * Xu ly truong hop promotion theo kich ban kinh doanh
                 */
                if ($is_promotion) {
                    $real_price     = 0;
                    $day_promotion  = $package->free_days;
                    $promotion_note = "Khuyen mai trong chu ky khuyen mai";
                } else {
                    $real_price = intval($package->price);
                }
                $auto_recurring = $package->auto_renew;
            }
            $transaction->cost = $real_price;
            if (empty($transaction->description)) {
                $transaction->description = empty($promotion_note) ? "Mua goi cuoc $package->name" : $promotion_note;
            }
            /**
             * TODO Support white list?
             */
//            if ($this->type == self::TYPE_WHITELIST) $real_price = 0;

            /**
             * Thuc hien goi lenh charging
             * @var $chargingRes ChargingResult
             */
            $charging_connection = new ChargingConnection($package->site->vivas_gw_host, $package->site->vivas_gw_port, $package->site->vivas_gw_username, $package->site->vivas_gw_password);
            $chargingRes         = ChargingGW::getInstance($charging_connection)->registerPackage($this->msisdn, $real_price,
                $package, $transaction->id, $channel, $promotion, $promotion_note);

//            ->chargeRegisterPackage(
            //            $this->msisdn, $price, $originPrice, $purchaseService, $tr->id, $channel_type, $promotion, $promotionNote);

            $chargingTelco   = $chargingRes->error;
            $chargingResult  = $chargingRes->result;
            $chargingSuccess = false;

            if ($chargingRes->result == ChargingResult::CHARGING_RESULT_OK) {
                $chargingSuccess = true;
            }
            Yii::trace("LOG : charging_code: " . $chargingRes->error);

            $transaction->error_code = $chargingResult;
            //TODO partner_id?
            $transaction->status  = $chargingSuccess ? SubscriberTransaction::STATUS_SUCCESS : SubscriberTransaction::STATUS_FAIL;
            $transaction->site_id = $package->site_id;

            $err_code = CommonConst::API_ERROR_UNKNOWN;
            $message  = '';

            if ($chargingSuccess) {
                /**
                 * Xu ly khi charging thanh cong
                 */
                $transaction->status = SubscriberTransaction::STATUS_SUCCESS;
                $user_package_asm    = $this->createMapping($package, $day_promotion, $auto_recurring, $transaction->id);
                if ($user_package_asm) {
                    $transaction->subscriber_service_asm_id = $user_package_asm->id;

                    if (!$transaction->update()) {
                        Yii::error($transaction->getErrors());
                    }
                    $error_code = VnpController::ERROR_SUCCESS_REGISTER;
                    if ($day_promotion > 0) {
                        $error_code = VnpController::ERROR_SUCCESS_PROMOTION;
                    }

                    if ($promotion) {
                        $message = ResMessage::firstRegisterSuccess($this, $package, date('d-m-Y', $user_package_asm->expired_at), true);
                    } else {
                        $message = ResMessage::registerSuccess($this, $package, date('d-m-Y', $user_package_asm->expired_at), true);
                    }

                    return array(
                        "success"    => true,
                        "error_code" => $error_code,
                        "message"    => $message,
                    );

                } else {

                    $transaction->status     = SubscriberTransaction::STATUS_FAIL;
                    $transaction->error_code = ChargingResult::CHARGING_RESULT_UNKNOWN;
                    $transaction->update();
                    return array(
                        "success"    => false,
                        "error_code" => VnpController::ERROR_INVALID_NOT_SYNC_CCG,
                        "message"    => ResMessage::registerFailBySystemError($this, $package, $sms),
                    );
                }
            } else {
                $transaction->status = SubscriberTransaction::STATUS_FAIL;
                $transaction->update();
                if ($chargingResult == ChargingResult::CHARGING_NOK_NOT_ENOUGH_CREDIT) {
                    return array(
                        "success"    => false,
                        "error_code" => VnpController::ERROR_NOT_ENOUGH_MONEY,
                        "message"    => ResMessage::registerFailByMoney($this, $package, $sms),
                    );
                } else {
                    return array(
                        "success"    => false,
                        "error_code" => VnpController::ERROR_INVALID_NOT_SYNC_CCG,
                        "message"    => ResMessage::registerFailBySystemError($this, $package, $sms),
                    );
                }
            }
        } else {
            $transaction->status     = SubscriberTransaction::STATUS_FAIL;
            $transaction->error_code = ChargingResult::CHARGING_NOK_REGISTERED;
            $transaction->update();
            return array(
                "success"    => false,
                "error_code" => VnpController::ERROR_REGISTERING,
                "message"    => ResMessage::registerFailByDuplicate($this, $is_registered->service, $sms),
            );
        }
    }

    /**
     * @param $package Service
     * @param $transaction SubscriberTransaction
     * @param $channel (API|SMS|WEB|WAP|CLIENT|SYSTEM|CSKH)
     * @param bool $sms
     * @return array
     * @throws \Exception
     */
    public function coreCancelPackage($package, $transaction, $channel, $sms = false)
    {
        $res            = [];
        $res['success'] = false;
        $res['message'] = 'Thất bại';

        // Tao transaction
        $transaction->subscriber_id = $this->id;
        $transaction->msisdn        = $this->msisdn;
        if (empty($transaction->description)) {
            $transaction->description = "Huy goi cuoc $package->name";
        }
        // KT xem da mua goi cuoc nay chua
        /** @var SubscriberServiceAsm $cancel_package */
        $cancel_package = SubscriberServiceAsm::findOne([
            'service_id'    => $package->id,
            'subscriber_id' => $this->id,
            'status'        => SubscriberServiceAsm::STATUS_ACTIVE]);

        if ($cancel_package) {

            $transaction->description = "Huy goi dich vu '" . $package->name . "'";
            // them transaction
            $transaction->status = SubscriberTransaction::STATUS_FAIL; //lay trang thai cua viec cap nhat trang thai inactive o tren
            $transaction->cost   = 0; // huy ko mat tien

            $cancelPackage       = $package;
            $charging_connection = new ChargingConnection($cancelPackage->site->vivas_gw_host, $cancelPackage->site->vivas_gw_port, $cancelPackage->site->vivas_gw_username, $cancelPackage->site->vivas_gw_password);
            $charging_connection = new ChargingConnection($cancelPackage->site->vivas_gw_host, $cancelPackage->site->vivas_gw_port, $cancelPackage->site->vivas_gw_username, $cancelPackage->site->vivas_gw_password);
            $charging_result     = ChargingGW::getInstance($charging_connection)->cancelPackage($this->msisdn, $cancelPackage, $transaction->id, $channel);
            Yii::error($charging_result->result);
            $transaction->error_code = $charging_result->result;
            if ($charging_result->result == ChargingResult::CHARGING_RESULT_OK) {
                /**
                 * Xu ly khi charging thanh cong
                 */
                $error_code          = VnpController::ERROR_NONE;
                $transaction->status = SubscriberTransaction::STATUS_SUCCESS;
                $transaction->update();

                $cancel_package->status = SubscriberServiceAsm::STATUS_INACTIVE;
                if ($cancel_package->update()) {
                    return array(
                        "success"    => true,
                        "error_code" => $error_code,
                        "message"    => ResMessage::cancelServiceSuccess($this, $cancelPackage, true),
                    );
                } else {
                    Yii::error($cancel_package->getErrors());
                    $transaction->status     = SubscriberTransaction::STATUS_FAIL;
                    $transaction->error_code = ChargingResult::CHARGING_RESULT_UNKNOWN;
                    $transaction->update();
                    return array(
                        "success"    => false,
                        "error_code" => VnpController::ERROR_INVALID_NOT_SYNC_CCG,
                        "message"    => ResMessage::cancelFailBySystemError($this, $cancelPackage, $sms),
                    );
                }
            } else {
                $transaction->status = SubscriberTransaction::STATUS_FAIL;
                $transaction->update();

                return array(
                    "success"    => false,
                    "error_code" => VnpController::ERROR_INVALID_NOT_SYNC_CCG,
                    "message"    => ResMessage::cancelFailBySystemError($this, $cancelPackage, $sms),
                );
            }
        } else {
            $transaction->status     = SubscriberTransaction::STATUS_FAIL;
            $transaction->error_code = ChargingResult::CHARGING_NOK_OTHER_ERROR;
            $transaction->update();
            return array(
                "success"    => false,
                'error_code' => VnpController::CANCEL_USER_ERROR_ALREADY_CANCELED,
                "message"    => ResMessage::cancelFailByNotRegister($this, $package, $sms),
            );
        }
    }

    /**
     * @param $purchaseService Service
     * @return bool
     */
    private function isPromotion($purchaseService)
    {
        /*
         * Xet khuyen mai
         */
        if (Service::freeFirst($purchaseService) && $this->isFirstRegister($purchaseService)) {
            return true;
        }
        return false;
    }

    /**
     * @param $purchaseService Service
     * @return bool|SubscriberServiceAsm
     */
    private function isRegistered($purchaseService)
    {
        $service_package_id = $purchaseService->id;
        $currentPackageAsms = $this->subscriberServiceAsms;

        foreach ($currentPackageAsms as $packageAsm) {
            // kiem tra trang thai cua cac goi da mua
            if ($packageAsm->status != SubscriberServiceAsm::STATUS_ACTIVE) {
                continue;
            }
            /** @var  $service Service */
            $service = $packageAsm->service;
//            $service = Service::find()->andWhere(["id" => $service_package_id, "status" => Service::STATUS_ACTIVE])->all();

            /**
             * Kiem tra co trung voi goi cuoc da mua hay ko
             */
            if ($packageAsm->service_id == $service_package_id) {
                // goi cuoc muon mua da duoc dang ky truoc do

                return $packageAsm;

            }

            /**
             * Kiem tra goi cuoc mua co trung voi goi cuoc trong cung group hay ko (group: vtv -> goi ngay,goi tuan,goi thang)
             * Trong mot group thi chi dc mua 1 goi cuoc trong group do
             */
            $groups1 = $service->serviceGroupAsms;
            $groups2 = $purchaseService->serviceGroupAsms;

            foreach ($groups1 as $group1) {
                /** @var $group1 ServiceGroupAsm */
                foreach ($groups2 as $group2) {
                    /** @var $group2 ServiceGroupAsm */

                    if ($group1->service_group_id == $group2->service_group_id) {
                        return $packageAsm;
                    }
                }
            }
        }
        return false;
    }

    private function isRegisteredReturn($purchaseService, $sms)
    {
        $service_package_id = $purchaseService->id;
        $currentPackageAsms = $this->subscriberServiceAsms;

        foreach ($currentPackageAsms as $packageAsm) {
            // kiem tra trang thai cua cac goi da mua
            if ($packageAsm->status != SubscriberServiceAsm::STATUS_ACTIVE) {
                continue;
            }
            /** @var  $service Service */
            $service = $packageAsm->service;
//            $service = Service::find()->andWhere(["id" => $service_package_id, "status" => Service::STATUS_ACTIVE])->all();

            /**
             * Kiem tra co trung voi goi cuoc da mua hay ko
             */
            if ($packageAsm->service_id == $service_package_id) {
                // goi cuoc muon mua da duoc dang ky truoc do

                return array(
                    "success"    => true,
                    "error_code" => VnpController::ERROR_REGISTERING,
                    "message"    => ResMessage::registerFailByDuplicate($this, $service, $sms),
                );
                //return $packageAsm;

            }

            /**
             * Kiem tra goi cuoc mua co trung voi goi cuoc trong cung group hay ko (group: vtv -> goi ngay,goi tuan,goi thang)
             * Trong mot group thi chi dc mua 1 goi cuoc trong group do
             */
            $groups1 = $service->serviceGroupAsms;
            $groups2 = $purchaseService->serviceGroupAsms;

            foreach ($groups1 as $group1) {
                /** @var $group1 ServiceGroupAsm */
                foreach ($groups2 as $group2) {
                    /** @var $group2 ServiceGroupAsm */

                    if ($group1->service_group_id == $group2->service_group_id) {
                        //return $packageAsm;
                        return array(
                            "success" => true,
                            "error"   => CommonConst::API_ERROR_ANOTHER_SERVICE_PACKAGE_IN_GROUP_PURCHASED,
                            "message" => ResMessage::registerFailByDuplicateGroup($this, $group1->service, $sms),
                        );
                    }
                }
            }
        }
        return array('success' => false);
    }

    /**
     * @param $package Service
     * @param $day_promotion
     * @param $auto_recurring
     * @param $transaction_id
     * @return SubscriberServiceAsm|null
     */
    private function createMapping($package, $day_promotion, $auto_recurring, $transaction_id)
    {
        $ssa                 = new SubscriberServiceAsm();
        $ssa->subscriber_id  = $this->id;
        $ssa->msisdn         = $this->msisdn;
        $ssa->service_name   = $package->name;
        $ssa->service_id     = $package->id;
        $ssa->site_id        = $package->site_id;
        $ssa->transaction_id = $transaction_id;
        $ssa->status         = SubscriberServiceAsm::STATUS_ACTIVE;
        $activationDate      = new \DateTime();
        $expiryDate          = new \DateTime();
        $ssa->auto_renew     = $auto_recurring;

        $extend_day = ($day_promotion > 0) ? $day_promotion : $package->period;

        if ($extend_day > 0) {
//                $expiryDate->add(DateInterval::createFromDateString($purchasePackage->period . ' days'));
            $expiryDate->add(new DateInterval("P" . $extend_day . 'D'));
        }
        $ssa->activated_at     = $activationDate->getTimestamp();
        $ssa->expired_at       = $expiryDate->getTimestamp();
        $ssa->renew_fail_count = 0;

        if (!$ssa->save()) {
            Yii::trace("ERROR: cannot save ssa: " . Json::encode($ssa));
            return null;
        }
        return $ssa;

    }

    public function getPackageActivityInfo($package, $getPlatform)
    {
        /**
         * @var $service_asms SubscriberServiceAsm[]
         */
        $service_asms = SubscriberServiceAsm::find()->andWhere([
            'subscriber_id' => $this->id,
            'service_id'    => $package->id,
        ])->orderBy(['updated_at' => SORT_DESC])->all();

        if (count($service_asms) > 0) {
            $is_register  = false;
            $last_mapping = null;
            foreach ($service_asms as $mapping) {
                if ($mapping->status == SubscriberServiceAsm::STATUS_ACTIVE) {
                    $last_mapping = $mapping;
                    $is_register  = true;
                    break;
                }
            }
            if ($is_register) {
                return [
                    'errorid'             => 0,
                    'error_desc'          => '',
                    'status'              => VnpController::PACKAGE_STATUS_REGISTERED,
                    'last_time_subscribe' => date('YmdHis', $last_mapping->created_at),
                    'expire_time'         => date('YmdHis', $last_mapping->expired_at),
                    'last_time_renew'     => ($last_mapping->renewed_at) ? date('YmdHis', $last_mapping->renewed_at) : null,
                    'last_time_retry'     => ($last_mapping->last_renew_fail_at) ? date('YmdHis', $last_mapping->last_renew_fail_at) : null,
                ];
            } else {
                $last_mapping = $service_asms[0];
                return [
                    'errorid'               => 0,
                    'error_desc'            => '',
                    'status'                => VnpController::PACKAGE_STATUS_CANCELED,
                    'last_time_subscribe'   => date('YmdHis', $last_mapping->created_at),
                    'last_time_unsubscribe' => date('YmdHis', $last_mapping->updated_at),
                ];
            }
        } else {
            return [
                'errorid'    => 0,
                'error_desc' => '',
                'status'     => VnpController::PACKAGE_STATUS_NOT_REGISTER,
            ];
        }
    }

    public function getListTransactions($from, $to, $page_size = 10, $page_index = 1)
    {
        $offset = ($page_index - 1) * $page_size;
        if ($offset < 0) {
            $offset = 0;
        }
        $total_pages = 0;
        $total       = SubscriberTransaction::find()->andWhere(['>', 'created_at', $from])
            ->andWhere(['<', 'created_at', $to])
            ->andWhere(['subscriber_id' => $this->id, 'site_id' => $this->site_id, 'status' => SubscriberTransaction::STATUS_SUCCESS])
            ->andWhere(['is not', 'service_id', null])
            ->all();
        $total_pages  = intval(count($total) / $page_size);
        $transactions = SubscriberTransaction::find()->andWhere(['>', 'created_at', $from])
            ->andWhere(['<', 'created_at', $to])
            ->andWhere(['subscriber_id' => $this->id, 'site_id' => $this->site_id, 'status' => SubscriberTransaction::STATUS_SUCCESS])
            ->andWhere(['is not', 'service_id', null])
            ->orderBy('id desc')
            ->limit($page_size)
            ->offset($offset)->all();
        return [
            'total_pages'  => $total_pages,
            'transactions' => $transactions,
        ];
    }

    public function cancel()
    {
        foreach ($this->subscriberServiceAsms as $mapping) {
            $mapping->status = SubscriberServiceAsm::STATUS_INACTIVE;
            $mapping->update();
        }
        $this->status = self::STATUS_INACTIVE;

        return $this->update();
    }

    /**
     * Get list active package
     * @param int $package_id
     * @return SubscriberServiceAsm[]|null
     */
    public function getActiveServiceAsms($package_id = 0)
    {
        /**
         * @var $service_asms SubscriberServiceAsm[]
         */
        $query = SubscriberServiceAsm::find()->andWhere([
            'subscriber_id' => $this->id,
            'status'        => SubscriberServiceAsm::STATUS_ACTIVE,
        ]);
        if ($package_id > 0) {
            $query->andWhere(['service_id' => $package_id]);
        }
        return $query->orderBy(['updated_at' => SORT_DESC])->all();
    }

    /**
     * @param $subscriber_id
     * @param $content_id
     * @return bool
     */
    public static function validatePurchasing($subscriber_id, $content_id)
    {
        /** Check xem subscriber đã mua nội dung lẻ này chưa */
        $subscriberContentAsm = SubscriberContentAsm::findOne([
            'subscriber_id' => $subscriber_id,
            'content_id'    => $content_id,
            'status'        => SubscriberContentAsm::STATUS_ACTIVE,
        ]);
        if ($subscriberContentAsm && $subscriberContentAsm->expired_at >= time()) {
            return true;
        }
        /**Check xem người dùng đã mua gói cước map với nội dung này chưa */
        /** Lấy tất cả gói cước người dùng đã mua */
        $subscriberServiceAsms = SubscriberServiceAsm::findAll(['subscriber_id' => $subscriber_id, 'status' => SubscriberServiceAsm::STATUS_ACTIVE]);
        foreach ($subscriberServiceAsms as $subscriberServiceAsm) {
            /** Nếu gói cước đã hết hạn thì bỏ qua vòng lặp hiện tại*/
            if ($subscriberServiceAsm->expired_at < time()) {
                continue;
            }
            /** Kiểm tra xem gói cước người dùng đã mua gắn với category nào */
            $serviceCategoryAsms = ServiceCategoryAsm::findAll(['service_id' => $subscriberServiceAsm->service_id]);
            /** Nếu không gắn với gói cước nào thì bỏ qua vòng lặp hiện tại */
            if (!$serviceCategoryAsms) {
                continue;
            }
            /** Kiểm tra xem category có gắn với nội dung đang xem không*/
            foreach ($serviceCategoryAsms as $serviceCategoryAsm) {
                $contentCategoryAsm = ContentCategoryAsm::findOne(['category_id' => $serviceCategoryAsm->category_id, 'content_id' => $content_id]);
                if ($contentCategoryAsm) {
                    return true;
                }
            }

        }
        return false;

    }

    /**
     * @param $token
     * @return null|static
     */
    public static function findCredentialByToken($token)
    {
        return self::findOne(['token' => $token, 'status' => static::STATUS_ACTIVE]);
    }

    /**
     * @param $action
     * @param $channelType
     * @param $description
     * @param null $service Service
     * @param null $content Content
     * @param $status
     * @param int $cost
     * @param string $telco_code
     * @param Site $service_provider
     *
     * @return SubscriberTransaction
     */
    public function newActivity(
        $action,
        $channelType,
        $description,
        $status = Sub::STATUS_FAIL,
        $service_provider = null
    ) {
        $tr                = new SubscriberActivity();
        $tr->subscriber_id = $this->id;
        $tr->site_id       = $this->site_id;
        $tr->msisdn        = $this->msisdn;
        $tr->action        = $action;
        $tr->channel       = $channelType;
        $tr->description   = $description;

        if ($service_provider) {
            $tr->site_id = $service_provider->id;
        }
        $tr->created_at = time();
        $tr->status     = $status;
        $tr->created_at = time();
        $tr->save(false);
        return $tr;
    }

    public static function chargeCoin($username, $amount, $currency = 'VND', $balance, $channel_type, $msisdn, $site_id, $send_sms = false, $mo = null, $serviceNumber = null)
    {
        if (!$site_id) {
            $subscriber = Subscriber::findOne(['username' => $username, 'status' => Subscriber::STATUS_ACTIVE]);
        } else {
            $subscriber = Subscriber::findOne(['username' => $username, 'status' => Subscriber::STATUS_ACTIVE, 'site_id' => $site_id]);
        }
        if (!$subscriber) {
            return array(
                "success" => false,
                "error"   => CommonConst::API_ERROR_INVALID_USERNAME,
                "message" => ResMessage::chargeCoinFailByInvalidUsername($msisdn, $site_id, $send_sms, $serviceNumber),
            );
        }
        if ($mo) {
            $mo->subscriber_id = $subscriber->id;
            $mo->update();
        }
        $site        = $subscriber->site;
        $description = 'Nạp coin';
        $subscriber->newTransaction(SubscriberTransaction::TYPE_CHARGE_COIN, $channel_type, $description, null, null, SubscriberTransaction::STATUS_SUCCESS, $amount, $currency, $balance, $site);
        $subscriber->balance = $subscriber->balance + $balance;
        if (!$subscriber->update(true, ['balance'])) {
            Yii::error($subscriber->errors);
            return array(
                "success" => true,
                "error"   => CommonConst::API_ERROR_SYSTEM_ERROR,
                "message" => ResMessage::chargeCoinFailBySystemError($subscriber, $send_sms, $serviceNumber),
            );
        }
        return array(
            "success" => true,
            "error"   => CommonConst::API_ERROR_NO_ERROR,
            "message" => ResMessage::chargeCoinSuccess($subscriber, $amount, $send_sms, $serviceNumber),
        );
    }

    public function checkMyService($service_id)
    {
        $list_my_service = [];
        $listService     = $this->services;
        foreach ($listService as $row) {
            $list_my_service[] = $row->id;
        }

        $is_my_package = in_array($service_id, $list_my_service);
        return $is_my_package;
    }
}
