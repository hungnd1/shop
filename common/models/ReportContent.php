<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%report_content}}".
 *
 * @property integer $id
 * @property integer $report_date
 * @property integer $site_id
 * @property integer $content_type
 * @property integer $category_id
 * @property integer $total_content
 * @property integer $count_content_upload_daily
 * @property integer $total_content_view
 * @property integer $total_content_buy
 *
 * @property Site $site
 * @property Category $category
 */
class ReportContent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%report_content}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_date', 'site_id', 'content_type', 'category_id', 'total_content', 'count_content_upload_daily', 'total_content_view', 'total_content_buy'], 'integer']
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
            'content_type' => 'Loại nội dung',
            'category_id' => 'Category ID',
            'total_content' => 'Tổng số nội dung',
            'count_content_upload_daily' => 'Nội dung upload trong ngày',
            'total_content_view' => 'Tổng lượt xem',
            'total_content_buy' => 'Tổng lượt mua',
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
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
