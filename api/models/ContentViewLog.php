<?php
/**
 * Created by PhpStorm.
 * User: VS9 X64Bit
 * Date: 25/02/2015
 * Time: 9:03 AM
 */

namespace api\models;

use api\helpers\UserHelpers;
use common\models\ContentCategoryAsm;
use common\models\ContentProfile;
use common\models\site;
use common\models\SubscriberContentAsm;
use Yii;
use yii\helpers\Url;

class ContentViewLog extends \common\models\ContentViewLog
{
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['user_agent']);
        unset($fields['msisdn']);
//        unset($fields['status']);

        $fields['display_name'] = function ($model)  {
            /* @var $model ContentViewLog */
            return $model->content?$model->content->display_name:null;
        };
        $fields['ascii_name'] = function ($model)  {
            /* @var $model ContentViewLog */
            return $model->content?$model->content->ascii_name:null;
        };
        $fields['images'] = function ($model) {
            /* @var $model ContentViewLog */
            $link = [];
            if (!$model->content->images) {
                return null;
            }
            $listImages = Content::convertJsonToArray($model->content->images);
            foreach ($listImages as $key => $row) {
                $link[] =[
//                    'link'=>Url::to(\Yii::getAlias('@content_images') . '/' . $row['name'],true),
                    'link' => Url::to( Yii::getAlias('@web').DIRECTORY_SEPARATOR.Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $row['name'], true),
                    'type'=>$row['type']
                ] ;
            }
            return $link;
        };

        $fields['image'] = function($model){
            /* @var $model ContentViewLog */
            $link = '';
            if (!$model->content->images) {
                return null;
            }
            $listImages = $listImages = Content::convertJsonToArray($model->content->images);
            foreach ($listImages as $key => $row) {
//                $link = Url::to(\Yii::getAlias('@content_images') . '/' . $row['name'],true);
                $link =  Url::to( Yii::getAlias('@web').DIRECTORY_SEPARATOR.Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $row['name'], true);
            }
            return $link;
        };
        $fields['is_series'] = function ($model) {
            /* @var $model ContentViewLog */
            return $model->content->is_series;
        };
        $fields['episode_count'] = function ($model) {
            /* @var $model SubscriberFavorite */
            return $model->content->episode_count;
        };
        $fields['episode_order'] = function ($model) {
            /* @var $model SubscriberFavorite */
            return $model->content->episode_order;
        };
        $fields['parent_id'] = function ($model) {
            /* @var $model SubscriberFavorite */
            return $model->content->parent_id;
        };
        return $fields;
    }


}