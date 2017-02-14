<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "subcriber".
 *
 * @property integer $id
 * @property integer $id_facebook
 * @property string $user_name
 * @property string $full_name
 * @property integer $gender
 * @property integer $status
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $address
 * @property integer $phone
 * @property string $birthday
 * @property string $about
 * @property integer $created_at
 * @property integer $updated_at
 */
class Subcriber extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 1;
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;

    public static function listStatus()
    {
        $lst = [
            self::STATUS_ACTIVE => Yii::t('app','Kích hoạt'),
            self::STATUS_INACTIVE => Yii::t('app','Tạm dừng'),
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

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    public static function listGender()
    {
        $gender = [
            self::GENDER_MALE => Yii::t('app','Nam'),
            self::GENDER_FEMALE => Yii::t('app','Nữ'),
        ];
        return $gender;
    }

    public function getGenderName()
    {
        $lst = self::listGender();
        if (array_key_exists($this->gender, $lst)) {
            return $lst[$this->gender];
        }
        return $this->gender;
    }

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
    public static function tableName()
    {
        return 'subcriber';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gender', 'status', 'phone', 'created_at', 'updated_at','id_facebook'], 'integer'],
//            [['auth_key', 'password_hash', 'email'], 'required'],
            [['birthday'], 'safe'],
            [['user_name', 'full_name', 'password_hash', 'password_reset_token', 'email', 'address'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['about'], 'string', 'max' => 600],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','Mã'),
            'user_name' => Yii::t('app','Tên đăng nhập'),
            'full_name' => Yii::t('app','Tên đầy đủ '),
            'gender' => Yii::t('app','Giới tính'),
            'status' => Yii::t('app','Trạng thái'),
            'auth_key' => Yii::t('app','Auth Key'),
            'password_hash' => Yii::t('app','Password Hash'),
            'password_reset_token' => Yii::t('app','Password Reset Token'),
            'email' => Yii::t('app','Địa chỉ Email'),
            'address' => Yii::t('app','Địa chỉ'),
            'phone' => Yii::t('app','Số điện thoại'),
            'birthday' => Yii::t('app','Ngày sinh'),
            'about' => Yii::t('app','Giới thiệu'),
            'created_at' => Yii::t('app','Ngày tạo'),
            'updated_at' => Yii::t('app','Ngày thay đổi thông tin'),
            'id_facebook' => Yii::t('app','Tài khoản facebook'),
        ];
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getImageLink()
    {
        $pathLink = Yii::getAlias('@web') . '/' . Yii::getAlias('@image_avatar') . '/';
        $filename = null;
        if ($this->image) {
            $filename = $this->image;
        }
        if (!$filename) {
            $pathLink = Yii::getAlias("@web/images/");
            $filename = 'avt_df.png';
        }

        return Url::to($pathLink . $filename, true);

    }
}
