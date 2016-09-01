<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report_revenues_dealer".
 *
 * @property integer $id
 * @property string $report_date
 * @property integer $site_id
 * @property integer $dealer_id
 * @property double $total_revenues
 */
class ReportRevenuesDealer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_revenues_dealer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_date'], 'safe'],
            [['site_id', 'dealer_id'], 'integer'],
            [['total_revenues'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_date' => 'Report Date',
            'site_id' => 'Site ID',
            'dealer_id' => 'Dealer ID',
            'total_revenues' => 'Total Revenues',
        ];
    }
}
