<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "content_site_asm".
 *
 * @property integer $id
 * @property integer $content_id
 * @property integer $site_id
 * @property integer $status
 * @property integer $pricing_id
 * @property string $subtitle
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Pricing $pricing
 * @property Content $content
 * @property Site $site
 */
class ContentSiteAsm extends \yii\db\ActiveRecord
{

    const STATUS_INACTIVE       = 0; // Tam dung
    const STATUS_ACTIVE         = 10; // Da san sang
    const STATUS_INVISIBLE      = 4; // Ngung cung cap
    const STATUS_NOT_TRANSFER   = 1; // Chua phan phoi
    const STATUS_TRANSFER_ERROR = 2; // Phan phoi loi
    const STATUS_TRANSFERING    = 3; // Dang phan phoi

    public static $_status = [
        self::STATUS_NOT_TRANSFER   => 'Chưa phân phối',
        self::STATUS_TRANSFERING    => 'Đang phân phối',
        self::STATUS_TRANSFER_ERROR => 'Phân phối lỗi',
        self::STATUS_ACTIVE         => 'Đã sẵn sàng',
        self::STATUS_INACTIVE       => 'Tạm dừng',
        self::STATUS_INVISIBLE      => 'Ngừng cung cấp',
    ];

    public static $_spStatus = [
        self::STATUS_ACTIVE   => 'Đã sẵn sàng',
        self::STATUS_INACTIVE => 'Tạm dừng',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content_site_asm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_id', 'site_id'], 'required'],
            [['content_id', 'site_id', 'status', 'created_at', 'updated_at', 'pricing_id'], 'integer'],
            [['subtitle'], 'file', 'extensions' => ['txt', 'smi', 'srt', 'ssa', 'sub', 'ass', 'style'], 'checkExtensionByMimeType' => false, 'maxSize' => 1024 * 1024 * 10],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'content_id' => Yii::t('app', 'Content ID'),
            'site_id'    => Yii::t('app', 'Site ID'),
            'status'     => Yii::t('app', 'Status'),
            'subtitle'   => Yii::t('app', 'Phụ đề'),
            'pricing_id' => Yii::t('app', 'Gia'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
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
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricing()
    {
        return $this->hasOne(Pricing::className(), ['id' => 'pricing_id']);
    }

    public function getSiteList($condition = [], $listFieldSelect = [])
    {
        $output;

        if (count($condition) === 0) {
            $site = ContentSiteAsm::find()->all();
        } else {
            $site = ContentSiteAsm::findAll($condition);
        }
        if (count($listFieldSelect) > 0 && count($listFieldSelect) === 2) {
            $output = [];
            foreach ($site as $v) {
                $output[$v[$listFieldSelect[0]]] = $v[$listFieldSelect[1]];
            }
        } else {
            $output = $site;
        }
        return $output;
    }
}
