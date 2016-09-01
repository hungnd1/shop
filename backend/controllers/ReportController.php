<?php
/**
 * Created by PhpStorm.
 * User: Hoan
 * Date: 10/26/2015
 * Time: 3:36 PM
 */

namespace backend\controllers;

use api\controllers\ApiController;
use backend\models\ReportContentForm;
use backend\models\ReportRevenuesForm;
use backend\models\ReportSubscriberActivityForm;
use backend\models\ReportSubscriberDailyForm;
use common\components\ActionLogTracking;
use common\models\Category;
use common\models\Service;
use common\models\Site;
use common\models\Subscriber;
use common\models\UserActivity;
use DateTime;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Json;

class ReportController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class' => ActionLogTracking::className(),
                'user' => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_PRICING,
                'post_action' => [
                    ['action' => 'create', 'accept_ajax' => false],
                    ['action' => 'delete', 'accept_ajax' => false],
                    ['action' => 'packages', 'accept_ajax' => true],
                ],
                'only' => ['create', 'delete', 'packages']
            ],
        ]);
    }

//    public function actionUserDaily()
//    {
//        /** @var Subscriber $user */
//        $user = Yii::$app->user->identity;
//        $param = Yii::$app->request->queryParams;
//        $site_id = isset($param['ReportUserDailyForm']['site_id']) ? $param['ReportUserDailyForm']['site_id'] : null;
//        $from_date = isset($param['ReportUserDailyForm']['from_date']) ? $param['ReportUserDailyForm']['from_date'] : null;
//        $to_date = isset($param['ReportUserDailyForm']['to_date']) ? $param['ReportUserDailyForm']['to_date'] : null;
//        $to_month = isset($param['ReportUserDailyForm']['to_month']) ? $param['ReportUserDailyForm']['to_month'] : null;
//        $from_month = isset($param['ReportUserDailyForm']['from_month']) ? $param['ReportUserDailyForm']['from_month'] : null;
//        $type = isset($param['ReportUserDailyForm']['type']) ? $param['ReportUserDailyForm']['type'] : null;
////        $type = isset($param['ReportUserDailyForm']['type']) ? $param['ReportRevenuesContentForm']['type'] : null;
////        echo $from_date;exit;
//
//
////        $a = DateTime::createFromFormat("d/m/Y", $from_date)->setTime(0, 0)->format('Y/m/d H:i:s');
////        $from_date = date("Y/m/01", strtotime(DateTime::createFromFormat("d/m/Y", '15/' . $from_date)->format('Y/m/d'))) . ' 00:00:00';
////        $from_date = date("Y/m/01", strtotime((new DateTime('now'))->format('Y-m-d H:i:s'))) . ' 00:00:00';
////        $to_date = date("Y/m/t", strtotime((new DateTime('now'))->format('Y-m-d H:i:s'))) . ' 00:00:00';
////        $to_date = date("Y/m/t", strtotime(DateTime::createFromFormat("d/m/Y", '15/' . $to_month)->format('Y/m/d'))) . ' 00:00:00';
////        strtotime(str_replace('/', '-', $this->from_date) . ' 00:00:00');
////        echo $to_date;exit;
//
//
//
//        $report = new ReportUserDailyForm();
//        $report->content = null;
//        $report->site_id = $site_id;
//        $report->from_date = $from_date;
//        $report->to_date = $to_date;
//        $report->from_month = $from_month;
//        $report->to_month = $to_month;
//        $report->type = $type;
//
//        if ($from_date && $to_date) {
//            $report->generateReport(false);
//        } else {
//            $report->generateReport(true);
//        }
////        $report->generateReport($active);
////        $report->dataProvider = new ActiveDataProvider([
////            'query' => $report->content,
////            'pagination' => [
////                'pageSize' => 30,
////            ],
////            'sort' => [
////                'defaultOrder' => [
////                    'report_date' => SORT_ASC
////                ]
////            ]
////        ]);
//        return $this->render('user-daily', [
//            'report' => $report,
////            'active' => $active,
//            'site_id' => $user->site_id
//        ]);
//    }

    /**
     * @return string
     */
    public function actionSubscriberDaily()
    {
        $param = Yii::$app->request->queryParams;
        $to_date_default = (new DateTime('now'))->setTime(23, 59, 59)->format('d/m/Y');
        $from_date_default = (new DateTime('now'))->setTime(0, 0)->modify('-7 days')->format('d/m/Y');

        $site_id = isset($param['ReportSubscriberDailyForm']['site_id']) ? $param['ReportSubscriberDailyForm']['site_id'] : null;
        $service_id = isset($param['ReportSubscriberDailyForm']['service_id']) ? $param['ReportSubscriberDailyForm']['service_id'] : null;
        $from_date = isset($param['ReportSubscriberDailyForm']['from_date']) ? $param['ReportSubscriberDailyForm']['from_date'] : $from_date_default;
        $to_date = isset($param['ReportSubscriberDailyForm']['to_date']) ? $param['ReportSubscriberDailyForm']['to_date'] : $to_date_default;

        $report = new ReportSubscriberDailyForm();
//        $report->content = null;
        $report->site_id = $site_id;
        $report->service_id = $service_id;
        $report->from_date = $from_date;
        $report->to_date = $to_date;
        $report->generateReport();

        return $this->render('subscriber-daily', [
            'report' => $report,
            'site_id' => $site_id,
            'service_id' => $service_id
        ]);
    }

    public function actionSubscriberActivity()
    {
        $param = Yii::$app->request->queryParams;
        $to_date_default = (new DateTime('now'))->setTime(23, 59, 59)->format('d/m/Y');
        $from_date_default = (new DateTime('now'))->setTime(0, 0)->modify('-7 days')->format('d/m/Y');

        $site_id = isset($param['ReportSubscriberActivityForm']['site_id']) ? $param['ReportSubscriberActivityForm']['site_id'] : null;
        $from_date = isset($param['ReportSubscriberActivityForm']['from_date']) ? $param['ReportSubscriberActivityForm']['from_date'] : $from_date_default;
        $to_date = isset($param['ReportSubscriberActivityForm']['to_date']) ? $param['ReportSubscriberActivityForm']['to_date'] : $to_date_default;

        $report = new ReportSubscriberActivityForm();
        $report->site_id = $site_id;
        $report->from_date = $from_date;
        $report->to_date = $to_date;
        $report->generateReport();

        return $this->render('subscriber-activity', [
            'report' => $report,
            'site_id' => $site_id
        ]);
    }

    public function actionContent()
    {
        $param = Yii::$app->request->queryParams;
        $to_date_default = (new DateTime('now'))->setTime(23, 59, 59)->format('d/m/Y');
        $from_date_default = (new DateTime('now'))->setTime(0, 0)->modify('-7 days')->format('d/m/Y');

        $site_id = isset($param['ReportContentForm']['site_id']) ? $param['ReportContentForm']['site_id'] : null;
        $content_type = isset($param['ReportContentForm']['content_type']) ? $param['ReportContentForm']['content_type'] : null;
        $selectedCats = isset($param['ReportContentForm']['categoryIds']) ? explode(',', $param['ReportContentForm']['categoryIds']) : [];
        $categoryIds = isset($param['ReportContentForm']['categoryIds'])?$param['ReportContentForm']['categoryIds']:null;
        $from_date = isset($param['ReportContentForm']['from_date']) ? $param['ReportContentForm']['from_date'] : $from_date_default;
        $to_date = isset($param['ReportContentForm']['to_date']) ? $param['ReportContentForm']['to_date'] : $to_date_default;

        $report = new ReportContentForm();
        $report->site_id = $site_id;
        $report->content_type = $content_type;
        $report->categoryIds = $categoryIds;
        $report->from_date = $from_date;
        $report->to_date = $to_date;
        $report->generateReport();

        return $this->render('content', [
            'report' => $report,
            'content_type' => $content_type,
            'site_id' => $site_id,
            'selectedCats' => $selectedCats,
        ]);
    }


    public function actionRevenues()
    {
        $param = Yii::$app->request->queryParams;
        $to_date_default = (new DateTime('now'))->setTime(23, 59, 59)->format('d/m/Y');
        $from_date_default = (new DateTime('now'))->setTime(0, 0)->modify('-7 days')->format('d/m/Y');

        $site_id = isset($param['ReportRevenuesForm']['site_id']) ? $param['ReportRevenuesForm']['site_id'] : null;
        $service_id = isset($param['ReportRevenuesForm']['service_id']) ? $param['ReportRevenuesForm']['service_id'] : null;
        $from_date = isset($param['ReportRevenuesForm']['from_date']) ? $param['ReportRevenuesForm']['from_date'] : $from_date_default;
        $to_date = isset($param['ReportRevenuesForm']['to_date']) ? $param['ReportRevenuesForm']['to_date'] : $to_date_default;

        $report = new ReportRevenuesForm();
//        $report->content = null;
        $report->site_id = $site_id;
        $report->service_id = $service_id;
        $report->from_date = $from_date;
        $report->to_date = $to_date;

        $report->generateReport();

        return $this->render('revenues', [
            'report' => $report,
            'site_id' => $site_id,
            'service_id' => $service_id
        ]);
    }

    // THE CONTROLLER
    public function actionFindServiceBySite() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                if(!$cat_id){
                    echo Json::encode(['output'=>'', 'selected'=>'']);
                }
                $out  = Service::findServiceBySite($cat_id);
                if(count($out)<=0){
                    echo Json::encode(['output'=>'', 'selected'=>'']);
                }
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFindCategoryBySiteContent() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $site_id = $parents[0];
                $content_type = $parents[1];
                if(!$site_id){
                    echo Json::encode(['output'=>'', 'selected'=>'']);
                }
                $items  = Category::findCategoryBySiteContent($site_id,$content_type);
                foreach($items as $item){
                    $item->display_name = str_pad($item->order_number,3,0,STR_PAD_LEFT).'-'.$item->path_name;
                }
                $out = $items;

//                $out  = Service::findServiceBySite($site_id);
                if(count($out)<=0){
                    echo Json::encode(['output'=>'', 'selected'=>'']);
                }
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }


} 