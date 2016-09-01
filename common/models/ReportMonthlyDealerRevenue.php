<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report_monthly_dealer_revenue".
 *
 * @property integer $id
 * @property integer $site_id
 * @property integer $dealer_id
 * @property double $revenue
 * @property double $revenue_percent
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $report_date
 *
 * @property Dealer $dealer
 * @property Site $site
 */
class ReportMonthlyDealerRevenue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_monthly_dealer_revenue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'dealer_id', 'report_date'], 'required'],
            [['site_id', 'dealer_id', 'created_at', 'updated_at'], 'integer'],
            [['revenue', 'revenue_percent'], 'number'],
            [['report_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => 'Site ID',
            'dealer_id' => 'Dealer ID',
            'revenue' => 'Revenue',
            'revenue_percent' => 'Revenue Percent',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'report_date' => 'Report Date',
        ];
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
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }
}
