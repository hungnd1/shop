<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%report_monthly_cp_revenue_detail}}".
 *
 * @property integer $id
 * @property integer $subscriber_id
 * @property integer $site_id
 * @property integer $content_provider_id
 * @property integer $view_count
 * @property double $view_percent
 * @property double $revenue
 * @property double $revenue_percent
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $report_date
 *
 * @property Subscriber $subscriber
 * @property ServiceProvider $serviceProvider
 * @property ContentProvider $contentProvider
 */
class ReportMonthlyCpRevenueDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%report_monthly_cp_revenue_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'site_id', 'content_provider_id', 'report_date'], 'required'],
            [['subscriber_id', 'site_id', 'content_provider_id', 'view_count', 'created_at', 'updated_at'], 'integer'],
            [['view_percent', 'revenue', 'revenue_percent'], 'number'],
            [['report_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subscriber_id' => Yii::t('app', 'Subscriber ID'),
            'site_id' => Yii::t('app', 'Service Provider ID'),
            'content_provider_id' => Yii::t('app', 'Content Provider ID'),
            'view_count' => Yii::t('app', 'View Count'),
            'view_percent' => Yii::t('app', 'View Percent'),
            'revenue' => Yii::t('app', 'Revenue'),
            'revenue_percent' => Yii::t('app', 'Revenue Percent'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'report_date' => Yii::t('app', 'Report Date'),
        ];
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
    public function getServiceProvider()
    {
        return $this->hasOne(ServiceProvider::className(), ['id' => 'site_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentProvider()
    {
        return $this->hasOne(ContentProvider::className(), ['id' => 'content_provider_id']);
    }
}
