<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report_revenues_service".
 *
 * @property integer $id
 * @property string $report_date
 * @property integer $site_id
 * @property integer $service_id
 * @property integer $renew_number
 * @property integer $register_number
 * @property double $renew_revenues
 * @property double $register_revenues
 * @property double $total_revenues
 */
class ReportRevenuesService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_revenues_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_date'], 'safe'],
            [['site_id', 'service_id','renew_number','register_number'], 'integer'],
            [['renew_revenues', 'register_revenues', 'total_revenues'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_date' => 'Ngày/Tháng',
            'site_id' => 'Service Provider ID',
            'service_id' => 'Service ID',
            'renew_revenues' => 'Gia hạn',
            'register_revenues' => 'Đăng ký',
            'renew_number' => 'Gia hạn',
            'register_number' => 'Đăng ký',
            'total_revenues' => 'Tổng số',
        ];
    }
}
