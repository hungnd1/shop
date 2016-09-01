<?php

/**
 * Swiss army knife to work with user and rbac in command line
 * @author: Nguyen Chi Thuc
 * @email: gthuc.nguyen@gmail.com
 */
namespace console\controllers;

use api\helpers\Message;
use common\helpers\CUtils;
use common\helpers\StringUtils;
use common\models\ActorDirector;
use common\models\ApiVersion;
use common\models\Content;
use common\models\ContentProfile;
use common\models\ContentSiteAsm;
use common\models\Site;
use common\models\User;
use ReflectionClass;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\StringHelper;

/**
 * UserController create user in commandline
 */
class ContentController extends Controller
{


    /**
     * @description Gen file karaoke local
     * @param $site_id
     */
    public function actionExportDataToFile($site_id){
        if (!isset($site_id)) {
            $message =  '****** SaveData2File ERROR: site_id empty ******';
            echo $message;
            Yii::error($message);
            return false;
        }
        $message = '****** SaveData2File BEGIN ******';
        echo $message;
        Yii::info($message);
        $lst = [];
        $items = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $site_id, 'content_site_asm.status' => ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.type' => Content::TYPE_KARAOKE, 'content.status' => Content::STATUS_ACTIVE])
            ->all();
        /** Nếu không có dữ liệu thì return */
        if (count($items) <= 0) {
            $message = '****** SaveData2File ERROR: '.Message::MSG_NOT_DATA.' ******';
            echo $message;
            Yii::error($message);
            return false;
        }
        /** @var  $item Content */
        foreach ($items as $item) {
            $group_tmp = $item->getAttributes(['id', 'display_name', 'ascii_name', 'short_description'], ['created_user_id']);
            $tempCat = "";
//            $categoryAsms = ContentCategoryAsm::find()->andWhere(['content_id'=>$item->id])->all();
            $categoryAsms = $item->contentCategoryAsms;
            if (count($categoryAsms) > 0) {
                foreach ($categoryAsms as $asm) {
                    /** @var $asm ContentCategoryAsm */
                    $tempCat .= $asm->category->id . ',';
                }
            }

            /** Cắt xâu */
            if (strlen($tempCat) >= 2) {
                $tempCat = substr($tempCat, 0, -1);
            }
            $group_tmp['categories'] = $tempCat;
            $tempA = "";
            $tempD = "";
            $contentActorDirectorAsms = $item->contentActorDirectorAsms;
//            $contentActorDirectorAsms = ContentActorDirectorAsm::find()->andWhere(['content_id'=>$item->id])->all();

            if ($contentActorDirectorAsms) {
                foreach ($contentActorDirectorAsms as $asm) {
                    if ($asm->actorDirector->type == ActorDirector::TYPE_ACTOR) {
                        /** @var $asm ContentCategoryAsm */
                        $tempA .= $asm->actorDirector->id . ',';
                    }
                    if ($asm->actorDirector->type == ActorDirector::TYPE_DIRECTOR) {
                        /** @var $asm ContentCategoryAsm */
                        $tempD .= $asm->actorDirector->id . ',';
                    }
                }
            }
            /** Cắt xâu */
            if (strlen($tempA) >= 2) {
                $tempA = substr($tempA, 0, -1);
            }
            /** Cắt xâu */
            if (strlen($tempD) >= 2) {
                $tempD = substr($tempD, 0, -1);
            }
            $group_tmp['actors'] = $tempA;
            $group_tmp['directors'] = $tempD;

            $strQuality = "";
//            $qualities  = $item->contentProfiles;
            $qualities = ContentProfile::find()->andWhere(['content_id' => $item->id, 'type' => ContentProfile::TYPE_CDN])->all();
            if ($qualities) {
                foreach ($qualities as $quality) {
                    $strQuality .= $quality->quality . ',';
                }
            }
            /** Cắt xâu */
            if (strlen($strQuality) >= 2) {
                $strQuality = substr($strQuality, 0, -1);
            }

            $group_tmp['qualities'] = $strQuality;
            $group_tmp['shortname'] = CUtils::parseTitleToKeyword($item->display_name);

            array_push($lst, $group_tmp);
        }

        $res = [
            'success' => true,
            'message' => Message::MSG_SUCCESS,
            'totalCount' => count($lst),
            'time_update' => time(),
            "date_expired" => "01/01/2018",
        ];
        $res['items'] = $lst;
        $resJson = json_encode($res);
        $path = 'staticdata/data' . $site_id . '.json';
        $save2File = CUtils::writeFile($resJson, $path);
        if ($save2File) {
            $r = ApiVersion::createApiVersion("karaoke", "version karaoke", $site_id, ApiVersion::TYPE_KARAOKE);
            if ($r['success']) {
//                Yii::$app->getSession()->setFlash('success', Message::MSG_SUCCESS);
                $message = '****** SaveData2File SUCCESS: '.Message::MSG_SUCCESS.' ******';
                echo $message;
                Yii::info($message);
                return true;
            } else {
                $message = '****** SaveData2File ERROR: khong save duoc apiversion '.Message::MSG_FAIL.' ******';
                echo $message;
                Yii::error($message);
//                Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
                return false;
            }

        } else {
            $message= '****** SaveData2File ERROR: không ghi được file '.Message::MSG_FAIL.' ******';
            echo $message;
            Yii::error($message);
//            Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
            return false;
        }
        $message= '****** SaveData2File END ******';
        echo $message;
        Yii::info($message);
        return true;
    }



}
