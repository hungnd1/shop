<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "program_suppost".
 *
 * @property integer $id
 * @property string $image
 * @property string $name
 * @property string $short_des
 * @property string $des
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 */
class ProgramSuppost extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE   = 10; // hoạt động
    const STATUS_INACTIVE = 0;  // không hoạt động


    public static function getListStatus()
    {
        return [
            self::STATUS_ACTIVE   => Yii::t('app','Hoạt động'),
            self::STATUS_INACTIVE => Yii::t('app','Tạm khóa'),
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
        return 'program_suppost';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['des'], 'string'],
            [['created_at', 'updated_at', 'status'], 'integer'],
            [['image', 'name', 'short_des'], 'string', 'max' => 255],
            ['name','required','message'=> Yii::t('app','{attribute} không được để trống')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => Yii::t('app','Ảnh hiển thị'),
            'name' => Yii::t('app','Tên hiển thị'),
            'short_des' => Yii::t('app','Mô tả ngắn'),
            'des' => Yii::t('app','Mô tả'),
            'created_at' => Yii::t('app','Ngày tạo'),
            'updated_at' => Yii::t('app','Ngày cập nhật'),
            'status' => Yii::t('app','Trạng thái'),
        ];
    }

    public function getImageLink(){
        if(empty($this->image)){
            return Yii::t('app', 'Hình ảnh không tìm thấy');
        }
        return Yii::getAlias('@web') . '/' . Yii::getAlias('@voucher_images') . '/'.$this->image;
    }
}
