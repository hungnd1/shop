<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "content_attribute".
 *
 * @property int $id
 * @property string $name
 * @property int $content_type
 * @property int $data_type
 * @property int $created_at
 * @property int $updated_at
 * @property ContentAttributeValue[] $contentAttributeValues
 */
class ContentAttribute extends \yii\db\ActiveRecord
{
    const TYPE_STRING = 1;
    const TYPE_INT    = 2;
    const TYPE_DOUBLE = 3;
    const TYPE_ARRAY  = 4;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'content_attribute';
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
    public function rules()
    {
        return [
            [['name'], 'required', 'message' => '{attribute} không thể để trống'],
            [['content_type', 'data_type'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'name'         => 'Tên thuộc tính',
            'content_type' => 'Loại nội dung',
            'data_type'    => 'Dạng dữ liệu',
            'created_at'   => 'Ngày tạo',
            'updated_at'   => 'Ngày cập nhật',
        ];
    }

    public function getDatatype($type = null)
    {
        $listType = [
            self::TYPE_STRING => 'String',
            self::TYPE_INT    => 'Integer',
            self::TYPE_DOUBLE => 'Double',
        ];

        if ($type) {
            return $listType[$type];
        }

        return $listType;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentAttributeValues()
    {
        return $this->hasMany(ContentAttributeValue::className(), ['content_attribute_id' => 'id']);
    }
}
