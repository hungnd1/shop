<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%report_revenue}}".
 *
 * @property integer $id
 * @property integer $report_date
 * @property integer $site_id
 * @property integer $service_id
 * @property integer $total_revenues
 * @property integer $renew_revenues
 * @property integer $register_revenues
 * @property integer $content_buy_revenues
 *
 * @property Site $site
 * @property Service $service
 */
class ReportRevenue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%report_revenue}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_date', 'site_id'], 'required'],
            [['report_date', 'site_id', 'service_id', 'total_revenues', 'renew_revenues', 'register_revenues', 'content_buy_revenues'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_date' => 'Ngày',
            'site_id' => 'Nhà cung cấp dịch vụ',
            'service_id' => 'Gói cước',
            'total_revenues' => 'Tổng doanh thu (coin)',
            'renew_revenues' => 'Doanh thu gia hạn (coin)',
            'register_revenues' => 'Doanh thu đăng ký (coin)',
            'content_buy_revenues' => 'Doanh thu mua nội dung lẻ (coin)',
        ];
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
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }
}
