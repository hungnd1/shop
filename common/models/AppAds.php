<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "app_ads".
 *
 * @property int $id
 * @property int $site_id
 * @property string $app_name
 * @property string $package_name
 * @property string $app_key
 * @property int $is_drama
 * @property int $created_at
 * @property int $updated_at
 * @property Ads[] $ads
 * @property Site $site
 */
class AppAds extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'app_ads';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['site_id', 'app_name', 'app_key', 'package_name'], 'required'],
            [['site_id', 'created_at', 'updated_at', 'is_drama'], 'integer'],
            [['app_name', 'app_key'], 'string', 'max' => 45],
            [['package_name'], 'string', 'max' => 128],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'site_id'      => 'Site ID',
            'app_name'     => 'App Name',
            'package_name' => 'Package Name',
            'app_key'      => 'App Key',
            'is_drama'     => 'Is Drama',
            'created_at'   => 'Created At',
            'updated_at'   => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAds()
    {
        return $this->hasMany(Ads::className(), ['app_ads_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }
}
