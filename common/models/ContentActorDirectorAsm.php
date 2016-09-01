<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "content_actor_director_asm".
 *
 * @property integer $id
 * @property integer $content_id
 * @property integer $actor_director_id
 * @property string $description
 * @property integer $created_at
 *
 * @property Content $content
 * @property ActorDirector $actorDirector
 */
class ContentActorDirectorAsm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content_actor_director_asm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_id', 'actor_director_id', 'created_at'], 'integer'],
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
            'content_id' => Yii::t('app', 'Content ID'),
            'actor_director_id' => Yii::t('app', 'Actor Director ID'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
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
    public function getActorDirector()
    {
        return $this->hasOne(ActorDirector::className(), ['id' => 'actor_director_id']);
    }
}
