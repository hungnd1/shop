<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%report_subscriber_activity}}".
 *
 * @property integer $id
 * @property integer $report_date
 * @property integer $site_id
 * @property integer $via_site_daily
 * @property integer $total_via_site
 *
 * @property Site $site
 */
class ReportSubscriberActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%report_subscriber_activity}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_date', 'site_id', 'via_site_daily', 'total_via_site'], 'integer'],
            [['site_id'], 'required']
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
            'site_id' => 'Site ID',
            'via_site_daily' => 'Số lượt truy cập trong ngày',
            'total_via_site' => 'Tổng lượt truy cập',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }
}
