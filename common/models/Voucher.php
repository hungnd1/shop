<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "voucher".
 *
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property double $sale
 * @property integer $status
 * @property integer $start_date
 * @property integer $end_date
 * @property integer $created_at
 * @property integer $updated_at
 */
class Voucher extends \yii\db\ActiveRecord
{
    public $date_start;
    public $date_end;

    const STATUS_ACTIVE   = 10; // hoạt động
    const STATUS_INACTIVE = 0;  // không hoạt động


    public static function getListStatus()
    {
        return [
            self::STATUS_ACTIVE   => 'Hoạt động',
            self::STATUS_INACTIVE => 'Tạm khóa',
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
        return 'voucher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale'], 'number','message'=>'Vui lòng nhập số'],
            [['date_start','date_end'], 'safe'],
            ['sale','required','message'=>'Sale không được để trống'],
            ['name','required','message'=>'Tên không được để trống'],
            [['status', 'start_date', 'end_date', 'created_at', 'updated_at'], 'integer'],
            [['name','image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tên',
            'image' => 'Hình ảnh',
            'sale' => 'Sale',
            'status' => 'Trạng thái',
            'start_date' => 'Thời gian bắt đầu',
            'end_date' => 'Thời gian kết thúc',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày thay đổi thông tin',
        ];
    }
}
