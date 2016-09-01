<?php
/**
 * Created by PhpStorm.
 * User: VS9 X64Bit
 * Date: 25/02/2015
 * Time: 9:03 AM
 */

namespace api\models;

use api\controllers\ApiController;
use api\helpers\UserHelpers;
use common\models\ActorDirector;
use common\models\ContentActorDirectorAsm;
use common\models\ContentCategoryAsm;
use common\models\ContentProfile;
use common\models\ContentProfileSiteAsm;
use common\models\ContentSiteAsm;
use common\models\site;
use common\models\Subscriber;
use common\models\SubscriberContentAsm;
use Yii;
use yii\helpers\Url;

class Content extends \common\models\Content
{
    public function fields()
    {
        $fields = parent::fields();
//        unset($fields['tvod1_id']);
        unset($fields['version_code']);
        unset($fields['version']);
        /** Bỏ 2 trường này thừa thiết kế */
        unset($fields['actor']);
        unset($fields['director']);
        $fields['image'] = function ($model) {
            /* @var $model Content */
            $link = '';
            if (!$model->images) {
                return null;
            }
            $listImages = Content::convertJsonToArray($model->images);
            foreach ($listImages as $key => $row) {
                $link = Url::to( Yii::getAlias('@web').DIRECTORY_SEPARATOR.Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $row['name'], true);
            }
            return $link;
        };
        $fields['images'] = function ($model) {
            /* @var $model Content */
            $link = [];
            if (!$model->images) {
                return null;
            }
            $listImages = Content::convertJsonToArray($model->images);
            foreach ($listImages as $key => $row) {
                $link[] = [
                    'link' => Url::to( Yii::getAlias('@web').DIRECTORY_SEPARATOR.Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $row['name'], true),
//                    'link' => Yii::getAlias('@web').DIRECTORY_SEPARATOR.Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $row['name'],
                    'type' => $row['type']
                ];
            }
            return $link;
        };

        $fields['categories'] = function ($model) {
            /* @var $model \common\models\Content */
            $categoryAsms = $model->contentCategoryAsms;
            $temp = "";
            foreach ($categoryAsms as $asm) {
                /** @var $asm ContentCategoryAsm */
                $temp .= $asm->category->id.',';
            }
            if(strlen($temp) > 2){
                $temp = substr($temp,0,-1);
            }

//            foreach ($categoryAsms as $asm) {
//                /** @var $asm ContentCategoryAsm */
//                $row = [];
//                if ($asm->category->is_content_service == 0) {
//                    $row['name'] = $asm->category->display_name;
//                    $row['id'] = $asm->category->id;
//                    $row['type'] = $asm->category->type;
//                    $list[] = $row;
//                }
//            }

            return $temp;
        };

        $fields['category_id'] = function ($model) {
            /* @var $model \common\models\Content */
            $categoryAsms = $model->contentCategoryAsms;
            $category_id = null;
            foreach ($categoryAsms as $asm) {
                /** @var $asm ContentCategoryAsm */
                if ($asm->category->is_content_service == 0) {
                    $category_id = $asm->category->id;
                    break;
                }
            }
            return $category_id;
        };

        /* @var $model Content */
//        $subscriber = Yii::$app->user->identity;
        $fields['is_favorite'] = function ($model) {
            /** @var  $subscriber Subscriber */
            $subscriber = Yii::$app->user->identity;
            if (!$subscriber) {
                return false;
            }
            $sf = SubscriberFavorite::getFavorite($subscriber->id, $model->id);
            if (!$sf) {
                return false;
            }
            return true;
        };
        /** Check free hay không */
        $fields['is_free'] = function ($model) {
            /* @var $model Content */
            return $model->getIsFree(Yii::$app->params['site_id']);
        };


        /** Nếu là free thì không hiển thị giá */
//        if ($this->is_free == \common\models\Content::NOT_FREE) {
            $fields['price_coin'] = function ($model) {
                /* @var $model Content */
                return $model->getPriceCoin(Yii::$app->params['site_id']);
            };
            $fields['price_sms'] = function ($model) {
                /* @var $model Content */
                return $model->getPriceSms(Yii::$app->params['site_id']);
            };
            $fields['watching_period'] = function ($model) {
                /* @var $model Content */
                return $model->getWatchingPriod(Yii::$app->params['site_id']);
            };
//        }

        $fields['qualities'] = function ($model) {
            $site_id = Yii::$app->params['site_id'];
            $str = "";
            $contentProfiles = $model->contentProfiles;
            foreach ($contentProfiles as $contentProfile) {
//                $str .= $quality->quality . ',';
                $contentProfileSiteAsm = ContentProfileSiteAsm::findOne(['content_profile_id'=>$contentProfile->id, 'site_id'=>$site_id, 'status'=>ContentProfileSiteAsm::STATUS_ACTIVE]);
                /** Nếu content_profile không thuộc site thì bỏ qua */
                if(!$contentProfileSiteAsm){
                    continue;
                }
                /** Get object content_priofile để xử lí*/
                $cp = $contentProfileSiteAsm->contentProfile;
                $str .= $cp->quality . ',';
            }
            if(strlen($str) >= 2){
                $str = substr($str,0,-1);
            }
            return $str;
        };

        $fields['purchased'] = function ($model) {
            /** @var  $subscriber Subscriber*/
            $subscriber = Yii::$app->user->identity;
            if(!$subscriber){
                return false;
            }
            /** Check xem người dùng đã mua nội dung này chưa hoặc đã mua gói cước chứa nội dung này không */
            $isCheck = Subscriber::validatePurchasing($subscriber->getId(), $this->id);
            return $isCheck;
        };
        /** Lấy cả đạo diễn, diễn viên cho content */
        if($this->type == \common\models\Content::TYPE_KARAOKE){
            $fields['actors'] = function ($model) {
                /* @var $model \common\models\Content */
                $items = $model->contentActorDirectorAsms;
                $temp = "";
                if(!$items){
                    return $temp;
                }
                foreach ($items as $item) {
                    /** @var $item ContentActorDirectorAsm */
                    if(!$item->actorDirector){
                        continue;
                    }
                    if ($item->actorDirector->type == ActorDirector::TYPE_ACTOR) {
                        $temp .= $item->actorDirector->id.', ';
                    }

                }
                if(strlen($temp) >= 2){
                    $temp = substr($temp,0,-2);
                }

                return $temp;
            };

            $fields['directors'] = function ($model) {
                /* @var $model \common\models\Content */
                $items = $model->contentActorDirectorAsms;
                $temp = "";
                if(!$items){
                    return $temp;
                }
                foreach ($items as $item) {
                    /** @var $item ContentActorDirectorAsm */
                    if(!$item->actorDirector){
                        continue;
                    }
                    if ($item->actorDirector->type == ActorDirector::TYPE_DIRECTOR) {
                        $temp .= $item->actorDirector->id.', ';
                    }
                }
                if(strlen($temp) >= 2){
                    $temp = substr($temp,0,-2);
                }

                return $temp;

            };
        }else{
            $fields['actors'] = function ($model) {
                /* @var $model \common\models\Content */
                $items = $model->contentActorDirectorAsms;
                $temp = "";
                if(!$items){
                    return $temp;
                }
                foreach ($items as $item) {
                    /** @var $item ContentActorDirectorAsm */
                    if(!$item->actorDirector){
                        continue;
                    }
                    if ($item->actorDirector->type == ActorDirector::TYPE_ACTOR) {
                        $temp .= $item->actorDirector->name.', ';
                    }

                }
                if(strlen($temp) >= 2){
                    $temp = substr($temp,0,-2);
                }

                return $temp;
            };

            $fields['directors'] = function ($model) {
                /* @var $model \common\models\Content */
                $items = $model->contentActorDirectorAsms;
                $temp = "";
                if(!$items){
                    return $temp;
                }
                foreach ($items as $item) {
                    /** @var $item ContentActorDirectorAsm */
                    if(!$item->actorDirector){
                        continue;
                    }
                    if ($item->actorDirector->type == ActorDirector::TYPE_DIRECTOR) {
                        $temp .= $item->actorDirector->name.', ';
                    }
                }
                if(strlen($temp) >= 2){
                    $temp = substr($temp,0,-2);
                }

                return $temp;
            };
        }


        return $fields;
    }

//    public function extraFields()
//    {
//        return ['contentCategoryAsms','contentProfiles'];
//    }


}