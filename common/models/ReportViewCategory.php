<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report_view_category".
 *
 * @property integer $id
 * @property string $report_date
 * @property integer $site_id
 * @property integer $content_provider_id
 * @property integer $category_id
 * @property integer $view_count
 * @property integer $download_count
 * @property integer $type
 * @property double $buy_revenues
 */
class ReportViewCategory extends \yii\db\ActiveRecord
{
    // Do 1 view thuoc nhieu category -> cong view cua cac category != tong view cua cp
    const TYPE_CATEGORY = 1; // Đếm số lượt view của 1 category
    const TYPE_FULL = 2; // Đếm tổng số lượt view của cp

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_view_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_date'], 'safe'],
            [['site_id', 'content_provider_id', 'category_id', 'view_count', 'download_count', 'type'], 'integer'],
            [['buy_revenues'], 'number']
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
            'site_id' => 'Service Provider ID',
            'content_provider_id' => 'Content Provider ID',
            'category_id' => 'Category ID',
            'view_count' => 'View Count',
            'download_count' => 'Download Count',
            'buy_revenues' => 'Buy Revenues',
        ];
    }
}
