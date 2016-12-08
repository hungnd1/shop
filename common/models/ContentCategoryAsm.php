<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%content_category_asm}}".
 *
 * @property integer $id
 * @property integer $content_id
 * @property integer $category_id
 * @property string $description
 * @property integer $created_at
 *
 * @property Content $content
 * @property Category $category
 */
class ContentCategoryAsm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_category_asm}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_id', 'category_id'], 'required'],
            [['content_id', 'category_id', 'created_at'], 'integer'],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'content_id' => Yii::t('app', 'ID Sản phẩm'),
            'category_id' => Yii::t('app', 'ID Danh mục'),
            'description' => Yii::t('app', 'Mô tả'),
            'created_at' => Yii::t('app', 'Ngày tạo'),
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
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
