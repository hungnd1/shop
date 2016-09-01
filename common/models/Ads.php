<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Url;

/**
 * This is the model class for table "ads".
 *
 * @property integer $id
 * @property integer $app_ads_id
 * @property integer $site_id
 * @property string $name
 * @property integer $type
 * @property string $image
 * @property string $target_url
 * @property string $extra
 * @property integer $status
 * @property integer $expired_date
 *
 * @property AppAds $appAds
 * @property Site $site
 */
class Ads extends \yii\db\ActiveRecord
{
    public $app_ads;
    public $app_name;

    const TYPE_BANNER = 1;
    const TYPE_ORTHER = 2;
    const TYPE_HTML   = 3;
    const TYPE_VIDEO  = 4;
    const TYPE_URL    = 5;

    const STATUS_ACTIVE   = 10;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETED = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ads';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_ads_id', 'site_id', 'name', 'expired_date'], 'required', 'message' => '{attribute} không thể để trống'],
            [['app_ads_id', 'site_id', 'type', 'status'], 'integer'],
            [['extra'], 'string'],
            [['name'], 'string', 'max' => 45],
            [['target_url'], 'string', 'max' => 255],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => 10 * 1024 * 1024],
            ['target_url', 'url'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'app_ads_id'   => 'Ứng dụng',
            'app_name'     => 'Tên ứng dụng',
            'site_id'      => 'Site ID',
            'name'         => 'Tên quảng cáo',
            'type'         => 'Loại quảng cáo',
            'image'        => 'Ảnh quảng cáo',
            'target_url'   => 'Đường dẫn quảng cáo',
            'extra'        => 'Thông tin bổ sung',
            'status'       => 'Trạng thái',
            'expired_date' => 'Ngày hết hạn',
        ];
    }

    public function upload($file_name)
    {
        if ($this->validate()) {
            $this->image->saveAs(Yii::getAlias('@webroot') . '/' . Yii::getAlias('@content_images') . '/' . $file_name);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppAds()
    {
        return $this->hasOne(AppAds::className(), ['id' => 'app_ads_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }

    /**
     * @param null $type
     * @return array
     */
    public function getType($type = null)
    {
        $list = [
            self::TYPE_BANNER => 'Banner',
            self::TYPE_ORTHER => 'Orther App',
            self::TYPE_HTML   => 'Html',
            self::TYPE_VIDEO  => 'Video',
            self::TYPE_URL    => 'Url',
        ];

        if ($type) {
            return $list[$type];
        }

        return $list;
    }

    public function getListStatus($stt = null)
    {
        $list = [
            self::STATUS_ACTIVE   => 'Hoạt động',
            self::STATUS_INACTIVE => 'Tạm dừng',
        ];

        if ($stt !== null) {
            return $list[$stt];
        }

        return $list;
    }

    public function getImageLink()
    {
        return $this->image ? Url::to('@web/' . Yii::getAlias('@content_images') . '/' . $this->image, true) : '';
    }
}
