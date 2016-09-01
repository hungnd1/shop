<?php
/**
 * Created by PhpStorm.
 * User: nhocconsanhdieu
 * Date: 26/5/2015
 * Time: 12:03 PM
 */

namespace console\controllers;

use common\models\AccessSystem;
use common\models\Category;
use common\models\ContentCategoryAsm;
use common\models\Dealer;
use common\models\ReportContent;
use common\models\ReportRevenue;
use common\models\ReportRevenuesContent;
use common\models\ReportRevenuesService;
use common\models\ReportSubscriberActivity;
use common\models\ReportSubscriberDaily;
use common\models\ReportUserDaily;
use common\models\ReportViewCategory;
use common\models\Service;
use common\models\Site;
use common\models\Subscriber;
use common\models\SumService;
use common\models\SumServiceAmount;
use common\models\SumViewPartner;
use cp\models\ReportViewCategoryForm;
use DateTime;
use Exception;
use Yii;
use yii\console\Controller;
use common\models\SumContent;
use common\models\ContentViewLog;
use common\models\SubscriberTransaction;
use common\models\SumContentDownload;
use common\models\SumContentView;
use common\models\Content;
use common\models\SumContentUpload;
use common\models\SubscriberServiceAsm;
use common\helpers\CUtils;

class ReportController extends Controller
{

//    public function actionUserDaily()
//    {
//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//            $today = strtotime("midnight", time());
//            $beginDay = $today;
//            $endDay = $today + 86400;
//
//            ReportUserDaily::deleteAll(['between','report_date', $beginDay, $endDay]);
//            echo "Deleted report game daily date:" . date("d-m-Y H:i:s", $today) . ' timestamp:' . $today;
//
//            $sites = Site::findAll(['status'=>Site::STATUS_ACTIVE]);
//            if(!$sites){
//                $transaction->rollBack();
//                echo 'n****** ERROR! Report User Daily Fail ******';
//            }
//            /** @var  $site Site*/
//            foreach($sites as $site) {
//                $active_user = Subscriber::find()->where(['status' => Subscriber::STATUS_ACTIVE,'site_id'=>$site->id])->count();
//                $active_user_package = SubscriberServiceAsm::find()->select('subscriber_id')->where(['status' => SubscriberServiceAsm::STATUS_ACTIVE,'site_id'=>$site->id])->distinct()->count();
//                //            $registed_user = Subscriber::find()->where(['status'=>Subscriber::STATUS_ACTIVE])->andWhere(['between','created_at', $beginDay, $endDay])->count();
//                /** @var  $rud ReportUserDaily */
//                $rud = new ReportUserDaily();
//                $rud->report_date = time();
//                $rud->site_id = $site->id;
//                $rud->active_user = $active_user;
//                $rud->active_user_package = $active_user_package;
//                if(!$rud->save()){
//                    echo '****** ERROR! Report User Daily Fail ******';
//                    $transaction->rollBack();
//                }
////                if ($rud->save()) {
////                    $transaction->commit();
////                    echo '****** Report User Daily Done ******';
////                } else {
////                    //                $error = $rud->firstErrors;
////                    //                $message = "";
////                    //                foreach ($error as $key => $value) {
////                    //                    $message .= $value;
////                    //                    break;
////                    //                }
////                    echo '****** ERROR! Report User Daily Fail ******';
////                }
//            }
//            $transaction->commit();
//            echo '****** Report User Daily Done ******';
//
//        } catch (Exception $e) {
//            $transaction->rollBack();
//            echo '****** ERROR! Report User Daily Fail Exception ******'.$e->getMessage();
//        }
//    }

    /**
     * @description Chạy cronjob lấy dữ liệu của ngày hôm trước 00:00:00 -> 23:59:59
     * @throws \yii\db\Exception
     */
    public function actionSubscriberDaily(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
//            $beginPreDay = strtotime('midnight -1 day');
//            $endPreDay = strtotime("midnight  -1 second");
            /** Lấy beginTime và endTime nowday */
            $beginPreDay = mktime(0, 0, 0);
            $endPreDay = mktime(23, 59, 59);

            ReportSubscriberDaily::deleteAll(['between','report_date', $beginPreDay, $endPreDay]);
            echo "Deleted report game daily date:" . date("d-m-Y H:i:s", $beginPreDay) . ' timestamp:' . $beginPreDay;

            $sites = Site::findAll(['status'=>Site::STATUS_ACTIVE]);

            if(!$sites){
                $transaction->rollBack();
                echo 'n****** ERROR! Report Subscriber Daily Fail: Site ******';
            }
            /** @var  $site Site*/
            foreach($sites as $site) {
                /** Quét tất cả Dealer của site */
                $dealers = Dealer::findAll(['site_id'=>$site->id, 'status'=>Dealer::STATUS_ACTIVE]);
                /** Add 1 thằng dealer empty vào để chạy vòng for để đảm bảo không bị sót trường hợp Dealer có thể k cần gán với Subscriber */
                $dealersEmpty = Dealer::createDealerEmpty($site->id);
                array_push($dealers,$dealersEmpty);

                foreach($dealers as $dealer) {
                    $packages = Service::findAll(['status'=>Service::STATUS_ACTIVE,'site_id'=>$site->id]);
                    if(!$packages){
                        break;
                    }

                    /** @var  $package Service*/
                    foreach($packages as $package) {
                        $total_subscriber = SubscriberServiceAsm::find()
                            ->select('subscriber_id')->distinct()
                            ->where(['site_id'=>$site->id])
                            ->andWhere(['service_id' => $package->id])
                            ->andWhere(['dealer_id' => $dealer->id])
                            ->count();
                        $total_active_subscriber = SubscriberServiceAsm::find()
                            ->select('subscriber_id')->distinct()
                            ->where(['site_id'=>$site->id])
                            ->andWhere(['service_id' => $package->id])
                            ->andWhere(['dealer_id' => $dealer->id])
                            ->andWhere(['status' => Subscriber::STATUS_ACTIVE])
                            ->count();
                        /** Tổng thuê bao hủy lũy kế */
                        $total_cancel_subscriber = SubscriberTransaction::find()
                            ->select('subscriber_id')->distinct()
                            ->andWhere(['site_id' => $site->id])
                            ->andWhere(['service_id' => $package->id])
                            ->andWhere(['dealer_id' => $dealer->id])
                            ->andWhere(['type' => SubscriberTransaction::TYPE_CANCEL])
                            ->count();
                        /** Thuê bao mới đăng ký gói trong ngày */
                        $subscriber_register_daily = SubscriberTransaction::find()
                            ->select('subscriber_id')->distinct()
                            ->andWhere(['site_id' => $site->id])
                            ->andWhere(['service_id' => $package->id])
                            ->andWhere(['dealer_id' => $dealer->id])
                            ->andWhere('created_at between :beginPreDay and :endPreDay')->addParams([':beginPreDay' => $beginPreDay, ':endPreDay' => $endPreDay])
                            ->andWhere(['type' => SubscriberTransaction::TYPE_REGISTER])
                            ->count();

                        /** Thuê bao hủy đăng ký gói trong ngày */
                        $subscriber_cancel_daily = SubscriberTransaction::find()
                            ->select('subscriber_id')->distinct()
                            ->andWhere(['site_id' => $site->id])
                            ->andWhere(['service_id' => $package->id])
                            ->andWhere(['dealer_id' => $dealer->id])
                            ->andWhere('created_at between :beginPreDay and :endPreDay')->addParams([':beginPreDay' => $beginPreDay, ':endPreDay' => $endPreDay])
                            ->andWhere(['type' => SubscriberTransaction::TYPE_CANCEL])
                            ->count();

                        /** @var  $rsd ReportSubscriberDaily*/
                        $rsd = new ReportSubscriberDaily();
                        $rsd->report_date = $beginPreDay;
                        $rsd->site_id = $site->id;
                        $rsd->dealer_id = $dealer->id;
                        $rsd->service_id = $package->id;
                        $rsd->total_subscriber = $total_subscriber;
                        $rsd->total_active_subscriber = $total_active_subscriber;
                        $rsd->subscriber_register_daily = $subscriber_register_daily;
                        $rsd->subscriber_cancel_daily = $subscriber_cancel_daily;
                        $rsd->total_cancel_subscriber = $total_cancel_subscriber;
                        if(!$rsd->save()){
                            echo '****** ERROR! Report Subscriber Daily Fail ******';
                            $transaction->rollBack();
                        }

                    }
                }


            }
            $transaction->commit();
            echo '****** Report Subscriber Daily Done ******';

        } catch (Exception $e) {
            $transaction->rollBack();
            echo '****** ERROR! Report Subscriber Daily Fail Exception ******'.$e->getMessage();
        }

    }

//    public function actionSubscriberDaily(){
//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//            $beginPreDay = strtotime('midnight -1 day');
//            $endPreDay = strtotime("midnight  -1 second");
//
//            ReportSubscriberDaily::deleteAll(['between','report_date', $beginPreDay, $endPreDay]);
//            echo "Deleted report game daily date:" . date("d-m-Y H:i:s", $beginPreDay) . ' timestamp:' . $beginPreDay;
//
//            $sites = Site::findAll(['status'=>Site::STATUS_ACTIVE]);
//
//            if(!$sites){
//                $transaction->rollBack();
//                echo 'n****** ERROR! Report Subscriber Daily Fail: Site ******';
//            }
//            /** @var  $site Site*/
//            foreach($sites as $site) {
//                $packages = Service::findAll(['status'=>Service::STATUS_ACTIVE,'site_id'=>$site->id]);
//                if(!$packages){
//                    break;
//                }
//
//                /** @var  $package Service*/
//                foreach($packages as $package) {
//                    $total_subscriber = SubscriberServiceAsm::find()
//                        ->select('subscriber_id')->distinct()
//                        ->where(['site_id'=>$site->id])
//                        ->andWhere(['service_id' => $package->id])
//                        ->count();
//                    $total_active_subscriber = SubscriberServiceAsm::find()
//                        ->select('subscriber_id')->distinct()
//                        ->where(['site_id'=>$site->id])
//                        ->andWhere(['service_id' => $package->id])
//                        ->andWhere(['status' => Subscriber::STATUS_ACTIVE])
//                        ->count();
//                    /** Tổng thuê bao hủy lũy kế */
//                    $total_cancel_subscriber = SubscriberTransaction::find()
//                        ->select('subscriber_id')->distinct()
//                        ->andWhere(['site_id' => $site->id])
//                        ->andWhere(['service_id' => $package->id])
//                        ->andWhere(['type' => SubscriberTransaction::TYPE_CANCEL])
//                        ->count();
//                    /** Thuê bao mới đăng ký gói trong ngày */
//                    $subscriber_register_daily = SubscriberTransaction::find()
//                        ->select('subscriber_id')->distinct()
//                        ->andWhere(['site_id' => $site->id])
//                        ->andWhere(['service_id' => $package->id])
//                        ->andWhere('created_at between :beginPreDay and :endPreDay')->addParams([':beginPreDay' => $beginPreDay, ':endPreDay' => $endPreDay])
//                        ->andWhere(['type' => SubscriberTransaction::TYPE_REGISTER])
//                        ->count();
//
//                    /** Thuê bao hủy đăng ký gói trong ngày */
//                    $subscriber_cancel_daily = SubscriberTransaction::find()
//                        ->select('subscriber_id')->distinct()
//                        ->andWhere(['site_id' => $site->id])
//                        ->andWhere(['service_id' => $package->id])
//                        ->andWhere('created_at between :beginPreDay and :endPreDay')->addParams([':beginPreDay' => $beginPreDay, ':endPreDay' => $endPreDay])
//                        ->andWhere(['type' => SubscriberTransaction::TYPE_CANCEL])
//                        ->count();
//
//                    /** @var  $rsd ReportSubscriberDaily*/
//                    $rsd = new ReportSubscriberDaily();
//                    $rsd->report_date = $beginPreDay;
//                    $rsd->site_id = $site->id;
//                    $rsd->service_id = $package->id;
//                    $rsd->total_subscriber = $total_subscriber;
//                    $rsd->total_active_subscriber = $total_active_subscriber;
//                    $rsd->subscriber_register_daily = $subscriber_register_daily;
//                    $rsd->subscriber_cancel_daily = $subscriber_cancel_daily;
//                    $rsd->total_cancel_subscriber = $total_cancel_subscriber;
//                    if(!$rsd->save()){
//                        echo '****** ERROR! Report Subscriber Daily Fail ******';
//                        $transaction->rollBack();
//                    }
//
//                }
//
//            }
//            $transaction->commit();
//            echo '****** Report Subscriber Daily Done ******';
//
//        } catch (Exception $e) {
//            $transaction->rollBack();
//            echo '****** ERROR! Report Subscriber Daily Fail Exception ******'.$e->getMessage();
//        }
//
//    }

    public function actionSubscriberActivity(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
//            $beginPreDay = strtotime('midnight -1 day');
//            $endPreDay = strtotime("midnight  -1 second");
            /** Lấy beginTime và endTime nowday */
            $beginPreDay = mktime(0, 0, 0);
            $endPreDay = mktime(23, 59, 59);
            ReportSubscriberActivity::deleteAll(['between','report_date', $beginPreDay, $endPreDay]);
            echo "Deleted report game daily date:" . date("d-m-Y H:i:s", $beginPreDay) . ' timestamp:' . $beginPreDay;

            $sites = Site::findAll(['status'=>Site::STATUS_ACTIVE]);

            if(!$sites){
                $transaction->rollBack();
                echo 'n****** ERROR! Report Subscriber Daily Fail: Site ******';
            }
            /** @var  $site Site*/
            foreach($sites as $site) {
                $via_site_daily = AccessSystem::find()
                    ->select('id')
                    ->andWhere(['site_id' => $site->id])
                    ->andWhere('created_at between :start_day and :end_day')->addParams([':start_day' => $beginPreDay, ':end_day' => $endPreDay])
                    ->count();
//                echo ' - via_site_daily: '.$via_site_daily.' - ';
                $total_via_site = AccessSystem::find()
                    ->select('id')
                    ->andWhere(['site_id' => $site->id])
                    ->count();

                $r = new ReportSubscriberActivity();
                $r->report_date = $beginPreDay;
                $r->site_id = $site->id;
                $r->via_site_daily = $via_site_daily;
                $r->total_via_site = $total_via_site;
                if(!$r->save()){
                    echo '****** ERROR! Report Subscriber Activity Fail ******';
                    $transaction->rollBack();
                }

            }
            $transaction->commit();
            echo '****** Report Subscriber Activity Done ******';

        } catch (Exception $e) {
            $transaction->rollBack();
            echo '****** ERROR! Report Subscriber Activity Fail Exception ******'.$e->getMessage();
        }

    }

    public function actionContent(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
//            $beginPreDay = strtotime('midnight -1 day');
//            $endPreDay = strtotime("midnight  -1 second");
            /** Lấy beginTime và endTime nowday */
            $beginPreDay = mktime(0, 0, 0);
            $endPreDay = mktime(23, 59, 59);
            ReportContent::deleteAll(['between','report_date', $beginPreDay, $endPreDay]);
            echo "Deleted report content date:" . date("d-m-Y H:i:s", $beginPreDay) . ' timestamp:' . $beginPreDay;

            $sites = Site::findAll(['status'=>Site::STATUS_ACTIVE]);

            if(!$sites){
                $transaction->rollBack();
                echo 'n****** ERROR! Report Content Fail: Site ******';
            }
            /** @var  $site Site*/
            foreach($sites as $site) {
                $lstType = Content::listType();
                if(!$lstType){
                    continue;
                }
                foreach($lstType as $key => $value){
                    $cats = Category::find()
                        ->andWhere(['category.status' => Category::STATUS_ACTIVE])
                        ->andWhere('category.type=:p_type', [':p_type' => $key])
                        ->innerJoin('category_site_asm', 'category.id=category_site_asm.category_id')
                        ->andFilterWhere(['category_site_asm.site_id' => $site->id])
                        ->all();
                    if(!$cats){
                        continue;
                    }

                    /** @var  $cat Category*/
                    foreach($cats as $cat){
                        $total_content = Content::find()
                            ->innerJoin('content_site_asm', 'content.id=content_site_asm.content_id')
                            ->andFilterWhere(['content_site_asm.site_id' => $site->id])
                            ->innerJoin('content_category_asm', 'content.id=content_category_asm.content_id')
                            ->andFilterWhere(['content.default_category_id' => $cat->id])
                            ->distinct()
                            ->count();
                        $count_content_upload_daily = Content::find()
                                        ->andWhere('content.created_at between :start_day and :end_day')->addParams([':start_day' => $beginPreDay, ':end_day' => $endPreDay])
                                        ->innerJoin('content_site_asm', 'content.id=content_site_asm.content_id')
                                        ->andFilterWhere(['content_site_asm.site_id' => $site->id])
                                        ->innerJoin('content_category_asm', 'content.id=content_category_asm.content_id')
                                        ->andFilterWhere(['content_category_asm.category_id' => $cat->id])
                                        ->distinct()
                                        ->count();
                        $total_content_view_dailys = ContentViewLog::find()
                                        ->select('sum(view_count) as view_count')
                                        ->andWhere(['site_id' => $site->id])
                                        ->andWhere(['category_id' => $cat->id])
                                        ->andWhere('view_date between :start_day and :end_day')->addParams([':start_day' => $beginPreDay, ':end_day' => $endPreDay])
                                        ->one();
                        $total_content_view_daily = $total_content_view_dailys->view_count?$total_content_view_dailys->view_count:0;
//                        var_dump($total_content_view_dailys->view_count);exit;
                        $total_buy_content_daily = SubscriberTransaction::find()
                            ->innerJoin('content_category_asm', 'subscriber_transaction.content_id=content_category_asm.content_id')
                            ->andWhere(['content_category_asm.category_id' => $cat->id])
                            ->andWhere(['subscriber_transaction.status'=>SubscriberTransaction::STATUS_SUCCESS])
                            ->andWhere(['subscriber_transaction.type'=>SubscriberTransaction::TYPE_CONTENT_PURCHASE])
                            ->andWhere('subscriber_transaction.created_at between :start_day and :end_day')->addParams([':start_day' => $beginPreDay, ':end_day' => $endPreDay])
                            ->andWhere(['subscriber_transaction.site_id'=>$site->id])
                            ->count();

                        /** @var  $r ReportContent*/
                        $r = new ReportContent();
                        $r->report_date = $beginPreDay;
                        $r->site_id = $site->id;
                        $r->content_type = $key;
                        $r->category_id = $cat->id;
                        $r->total_content = $total_content;
                        $r->count_content_upload_daily = $count_content_upload_daily;
                        $r->total_content_view = $total_content_view_daily;
                        $r->total_content_buy = $total_buy_content_daily;
                        if(!$r->save()){
//                            var_dump($r->getFirstErrors());exit;
                            echo '****** ERROR! Report Content Fail ******';
                            $transaction->rollBack();
                        }

                    }
                }

            }
            $transaction->commit();
            echo '****** Report Content Done ******';

        } catch (Exception $e) {
            $transaction->rollBack();
            echo '****** ERROR! Report Content Fail Exception: '.$e->getMessage().'******';
        }
    }

    public function actionRevenues(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
//            $beginPreDay = strtotime('midnight -1 day');
//            $endPreDay = strtotime("midnight  -1 second");
            /** Lấy beginTime và endTime nowday */
            $beginPreDay = mktime(0, 0, 0);
            $endPreDay = mktime(23, 59, 59);
            ReportRevenue::deleteAll(['between','report_date', $beginPreDay, $endPreDay]);
            echo "Deleted report revenues date:" . date("d-m-Y H:i:s", $beginPreDay) . ' timestamp:' . $beginPreDay;

            $sites = Site::findAll(['status'=>Site::STATUS_ACTIVE]);

            if(!$sites){
                $transaction->rollBack();
                echo 'n****** ERROR! Report Revenues Fail: Site ******';
            }
            /** @var  $site Site*/
            foreach($sites as $site) {
                $packages = Service::findAll(['status'=>Service::STATUS_ACTIVE,'site_id'=>$site->id]);
                if(!$packages){
                    break;
                }
                /** Add 1 thằng Service empty vào để chạy vòng for để đảm bảo không bị sót trường hợp mua phim lẻ, lúc này serive_id=null, content_id  */
                $packageEmpty = Service::createServiceEmpty($site->id);
                array_push($packages,$packageEmpty);
                /** @var  $package Service*/
                foreach($packages as $package) {
                    $total_revenues = SubscriberTransaction::find()
                        ->andWhere(['site_id' => $site->id])
                        ->andWhere('transaction_time between :beginPreDay and :endPreDay')->addParams([':beginPreDay' => $beginPreDay, ':endPreDay' => $endPreDay])
                        ->andWhere(['type' => [SubscriberTransaction::TYPE_REGISTER, SubscriberTransaction::TYPE_RENEW,SubscriberTransaction::TYPE_CONTENT_PURCHASE]])
                        ->andWhere(['status' => SubscriberTransaction::STATUS_SUCCESS])
                        ->andWhere(['service_id' => $package->id])
                        ->sum('balance');
                    $renew_revenues = SubscriberTransaction::find()
                        ->where(['site_id' => $site->id])
                        ->andWhere('transaction_time between :beginPreDay and :endPreDay')->addParams([':beginPreDay' => $beginPreDay, ':endPreDay' => $endPreDay])
                        ->andWhere(['type' => SubscriberTransaction::TYPE_RENEW])
                        ->andWhere(['status' => SubscriberTransaction::STATUS_SUCCESS])
                        ->andWhere(['service_id' => $package->id])
                        ->sum('balance');
                    $register_revenues = SubscriberTransaction::find()
                        ->where(['site_id' => $site->id])
                        ->andWhere('transaction_time between :beginPreDay and :endPreDay')->addParams([':beginPreDay' => $beginPreDay, ':endPreDay' => $endPreDay])
                        ->andWhere(['type' => SubscriberTransaction::TYPE_REGISTER])
                        ->andWhere(['status' => SubscriberTransaction::STATUS_SUCCESS])
                        ->andWhere(['service_id' => $package->id])
                        ->sum('balance');
                    $content_buy_revenues = SubscriberTransaction::find()
                        ->where(['site_id' => $site->id])
                        ->andWhere('transaction_time between :beginPreDay and :endPreDay')->addParams([':beginPreDay' => $beginPreDay, ':endPreDay' => $endPreDay])
                        ->andWhere(['type' => SubscriberTransaction::TYPE_CONTENT_PURCHASE])
                        ->andWhere(['status' => SubscriberTransaction::STATUS_SUCCESS])
                        ->andWhere(['service_id' => $package->id])
                        ->sum('balance');

                    /** @var  $rp ReportRevenue*/
                    $rp = new ReportRevenue();
                    $rp->report_date = $beginPreDay;
                    $rp->site_id = $site->id;
                    $rp->service_id = $package->id;
                    $rp->total_revenues = $total_revenues?abs($total_revenues):0;
                    $rp->renew_revenues = $renew_revenues?abs($renew_revenues):0;
                    $rp->register_revenues = $register_revenues?abs($register_revenues):0;
                    $rp->content_buy_revenues = $content_buy_revenues?abs($content_buy_revenues):0;
                    if(!$rp->save()){
//                        var_dump($rp->getFirstErrors());
                        echo '****** ERROR! Report Revenues Daily Fail ******';
                        $transaction->rollBack();
                    }
                }
            }
            $transaction->commit();
            echo '****** Report Revenues Done ******';

        } catch (Exception $e) {
            $transaction->rollBack();
            echo '****** ERROR! Report Revenues Fail Exception: '.$e->getMessage().'******';
        }
    }

    /**
     * Thong ke doanh thu theo goi dich vu
     * @param string $start_day
     * @throws \yii\db\Exception
     */
//    public function actionReportRevenuesService($start_day = '')
//    {
//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//            print('Processing.......');
//
//            if ($start_day != '') {
//                $to_day = strtotime(DateTime::createFromFormat("dmY", $start_day)->setTime(0, 0)->format('Y-m-d H:i:s'));
//                $end_day = strtotime(DateTime::createFromFormat("dmY", $start_day)->setTime(23, 59, 59)->format('Y-m-d H:i:s'));
//                $to_day_date = DateTime::createFromFormat("dmY", $start_day)->setTime(0, 0)->format('Y-m-d H:i:s');
//            } else {
//                $to_day = strtotime("midnight", time());
//                $end_day = strtotime("tomorrow", $to_day) - 1;
//                $to_day_date = (new DateTime('now'))->setTime(0, 0)->format('Y-m-d H-i-s');
//            }
//
//            print("Thoi gian bat dau: $to_day : Thoi gian ket thuc: $end_day ");
//            print("Convert sang ngay: $to_day_date");
//
//            Yii::$app->db->createCommand()->delete('report_revenues_service', ['report_date' => $to_day_date])->execute();
//
//            /** @var ServiceProvider[] $service_providers */
//            $service_providers = ServiceProvider::find()->all();
//            foreach ($service_providers as $service_provider) {
//                /** @var Service $services */
//                $services = Service::find()->andWhere(['site_id' => $service_provider->id])->all();
//                foreach ($services as $service) {
//                    $renew_revenues = SubscriberTransaction::find()
//                        ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                        ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                        ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_RENEW])
//                        ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                        ->andWhere(['site_id' => $service_provider->id])
//                        ->sum('subscriber_transaction.cost');
//
//                    $renew_number = SubscriberTransaction::find()
//                        ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                        ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                        ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_RENEW])
//                        ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                        ->andWhere(['site_id' => $service_provider->id])
//                        ->count();
//
//                    $register_revenues = SubscriberTransaction::find()
//                        ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                        ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                        ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_REGISTER])
//                        ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                        ->andWhere(['site_id' => $service_provider->id])
//                        ->sum('subscriber_transaction.cost');
//
//                    $register_number = SubscriberTransaction::find()
//                        ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                        ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                        ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_REGISTER])
//                        ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                        ->andWhere(['site_id' => $service_provider->id])
//                        ->count();
//
//                    $total_revenues = SubscriberTransaction::find()
//                        ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                        ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                        ->andWhere(['subscriber_transaction.type' => [SubscriberTransaction::TYPE_REGISTER, SubscriberTransaction::TYPE_RENEW]])
//                        ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                        ->andWhere(['site_id' => $service_provider->id])
//                        ->sum('subscriber_transaction.cost');
//
//
//                    $report = new ReportRevenuesService();
//                    $report->site_id = $service_provider->id;
//                    $report->service_id = $service->id;
//                    $report->report_date = $to_day_date;
//                    $report->renew_revenues = $renew_revenues ? $renew_revenues : 0;
//                    $report->register_revenues = $register_revenues ? $register_revenues : 0;
//                    $report->total_revenues = $total_revenues ? $total_revenues : 0;
//                    $report->renew_number = $renew_number;
//                    $report->register_number = $register_number;
//                    $report->save();
//                }
//            }
//            $transaction->commit();
//            print "Done";
//
//        } catch (Exception $e) {
//            $transaction->rollBack();
//            print "Error";
//            print $e;
//        }
//    }

//    /**
//     * Thong ke doanh thu theo goi dich vu
//     * @param string $start_day
//     * @throws \yii\db\Exception
//     */
//    public function actionReportRevenuesContent($start_day = '')
//    {
//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//            print("Processing....... \n");
//
//            if ($start_day != '') {
//                $to_day = strtotime(DateTime::createFromFormat("dmY", $start_day)->setTime(0, 0)->format('Y-m-d H:i:s'));
//                $end_day = strtotime(DateTime::createFromFormat("dmY", $start_day)->setTime(23, 59, 59)->format('Y-m-d H:i:s'));
//                $to_day_date = DateTime::createFromFormat("dmY", $start_day)->setTime(0, 0)->format('Y-m-d H:i:s');
//            } else {
//                $to_day = strtotime("midnight", time());
//                $end_day = strtotime("tomorrow", $to_day) - 1;
//                $to_day_date = (new DateTime('now'))->setTime(0, 0)->format('Y-m-d H-i-s');
//            }
//
//            print("Thoi gian bat dau: $to_day : Thoi gian ket thuc: $end_day \n");
//            print("Convert sang ngay: $to_day_date \n");
//
//            Yii::$app->db->createCommand()->delete('report_revenues_content', ['report_date' => $to_day_date])->execute();
//
//            /** @var ServiceProvider[] $service_providers */
//            $service_providers = ServiceProvider::find()->all();
//            foreach ($service_providers as $service_provider) {
//
//                $content_revenues = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_CONTENT_PURCHASE])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['site_id' => $service_provider->id])
//                    ->sum('subscriber_transaction.cost');
//
//                $buy_content_number = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_CONTENT_PURCHASE])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['site_id' => $service_provider->id])
//                    ->count();
//
//                $renew_revenues = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_RENEW])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['site_id' => $service_provider->id])
//                    ->sum('subscriber_transaction.cost');
//
//                $register_revenues = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_REGISTER])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['site_id' => $service_provider->id])
//                    ->sum('subscriber_transaction.cost');
//
//
//                $total_revenues = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => [SubscriberTransaction::TYPE_REGISTER,
//                        SubscriberTransaction::TYPE_RENEW, SubscriberTransaction::TYPE_CONTENT_PURCHASE]])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['site_id' => $service_provider->id])
//                    ->sum('subscriber_transaction.cost');
//
//                $report = new ReportRevenuesContent();
//                $report->site_id = $service_provider->id;
//                $report->report_date = $to_day_date;
//                $report->content_revenues = $content_revenues ? $content_revenues : 0;
//                $report->renew_revenues = $renew_revenues ? $renew_revenues : 0;
//                $report->register_revenues = $register_revenues ? $register_revenues : 0;
//                $report->total_revenues = $total_revenues ? $total_revenues : 0;
//                $report->buy_content_number = $buy_content_number;
//                $report->save();
//            }
//            $transaction->commit();
//            print "Done \n";
//
//        } catch (Exception $e) {
//            $transaction->rollBack();
//            print "Error \n";
//            print $e;
//        }
//    }
//
//    /**
//     * Thong ke theo goi cp
//     * @param string $start_day
//     * @throws \yii\db\Exception
//     */
//    public function actionReportRevenuesCp($start_day = '')
//    {
//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//            print("Processing....... \n");
//
//            if ($start_day != '') {
//                $to_day = strtotime(DateTime::createFromFormat("dmY", $start_day)->setTime(0, 0)->format('Y-m-d H:i:s'));
//                $end_day = strtotime(DateTime::createFromFormat("dmY", $start_day)->setTime(23, 59, 59)->format('Y-m-d H:i:s'));
//                $to_day_date = DateTime::createFromFormat("dmY", $start_day)->setTime(0, 0)->format('Y-m-d H:i:s');
//            } else {
//                $to_day = strtotime("midnight", time());
//                $end_day = strtotime("tomorrow", $to_day) - 1;
//                $to_day_date = (new DateTime('now'))->setTime(0, 0)->format('Y-m-d H-i-s');
//            }
//
//            print("Thoi gian bat dau: $to_day : Thoi gian ket thuc: $end_day \n");
//            print("Convert sang ngay: $to_day_date \n");
//
//            Yii::$app->db->createCommand()->delete('report_revenues_cp', ['report_date' => $to_day_date])->execute();
//
//            /** @var ServiceProvider[] $service_providers */
//            $service_providers = ServiceProvider::find()->all();
//            foreach ($service_providers as $service_provider) {
//
//                $content_revenues = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_CONTENT_PURCHASE])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['site_id' => $service_provider->id])
//                    ->sum('subscriber_transaction.cost');
//
//                $renew_revenues = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_RENEW])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['site_id' => $service_provider->id])
//                    ->sum('subscriber_transaction.cost');
//
//                $register_revenues = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_REGISTER])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['site_id' => $service_provider->id])
//                    ->sum('subscriber_transaction.cost');
//
//
//                $total_revenues = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => [SubscriberTransaction::TYPE_REGISTER,
//                        SubscriberTransaction::TYPE_RENEW, SubscriberTransaction::TYPE_CONTENT_PURCHASE]])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['site_id' => $service_provider->id])
//                    ->sum('subscriber_transaction.cost');
//
//                $report = new ReportRevenuesContent();
//                $report->site_id = $service_provider->id;
//                $report->report_date = $to_day_date;
//                $report->content_revenues = $content_revenues ? $content_revenues : 0;
//                $report->renew_revenues = $renew_revenues ? $renew_revenues : 0;
//                $report->register_revenues = $register_revenues ? $register_revenues : 0;
//                $report->total_revenues = $total_revenues ? $total_revenues : 0;
//                $report->save();
//            }
//            $transaction->commit();
//            print "Done \n";
//
//        } catch (Exception $e) {
//            $transaction->rollBack();
//            print "Error \n";
//            print $e;
//        }
//    }
//
//
//    public function actionReportCategoryCp($start_day = '')
//    {
//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//            print("Processing....... \n");
//
//            if ($start_day != '') {
//                $to_day = strtotime(DateTime::createFromFormat("dmY", $start_day)->setTime(0, 0)->format('Y-m-d H:i:s'));
//                $end_day = strtotime(DateTime::createFromFormat("dmY", $start_day)->setTime(23, 59, 59)->format('Y-m-d H:i:s'));
//                $to_day_date = DateTime::createFromFormat("dmY", $start_day)->setTime(0, 0)->format('Y-m-d H:i:s');
//            } else {
//                $to_day = strtotime("midnight", time());
//                $end_day = strtotime("tomorrow", $to_day) - 1;
//                $to_day_date = (new DateTime('now'))->setTime(0, 0)->format('Y-m-d H-i-s');
//            }
//
//            print("Thoi gian bat dau: $to_day : Thoi gian ket thuc: $end_day \n");
//            print("Convert sang ngay: $to_day_date \n");
//
//            Yii::$app->db->createCommand()->delete('report_view_category', ['report_date' => $to_day_date])->execute();
//
//            /** @var ContentProvider[] $content_providers */
//            $content_providers = ContentProvider::find()->all();
//            foreach ($content_providers as $content_provider) {
//                /** @var Category $categories */
//                $categories = Category::find()
//                    ->joinWith('contentProviderAsms1')
//                    ->andWhere(['content_provider_id' => $content_provider->id])->all();
//                foreach ($categories as $category) {
//                    $arr_content = [];
//                    /** @var ContentCategoryAsm[] $mappings */
//                    $mappings = ContentCategoryAsm::find()->andWhere(['category_id' => $category->id])->all();
//                    foreach ($mappings as $asm) {
//                        array_push($arr_content, $asm->content_id);
//                    }
//
//                    $view_count = ContentViewLog::find()
//                        ->andWhere('started_at >= :start')->addParams([':start' => $to_day])
//                        ->andWhere('started_at <= :end')->addParams([':end' => $end_day])
//                        ->andWhere(['content_provider_id' => $content_provider->id])
//                        ->andWhere(['content_id' => $arr_content])
//                        ->count();
//
//                    $content_revenues = SubscriberTransaction::find()
//                        ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                        ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                        ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_CONTENT_PURCHASE])
//                        ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                        ->andWhere(['content_provider_id' => $content_provider->id])
//                        ->andWhere(['content_id' => $arr_content])
//                        ->sum('subscriber_transaction.cost');
//
//                    $content_buy = SubscriberTransaction::find()
//                        ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                        ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                        ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_CONTENT_PURCHASE])
//                        ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                        ->andWhere(['content_provider_id' => $content_provider->id])
//                        ->andWhere(['content_id' => $arr_content])
//                        ->count();
//
//                    $report = new ReportViewCategory();
//                    $report->type = ReportViewCategory::TYPE_CATEGORY;
//
//                    $report->site_id = $content_provider->site_id;
//                    $report->content_provider_id = $content_provider->id;
//                    $report->report_date = $to_day_date;
//                    $report->category_id = $category->id;
//
//                    $report->view_count = $view_count;
//                    $report->download_count = $content_buy;
//                    $report->buy_revenues = $content_revenues ? $content_revenues : 0;
//
//                    $report->save();
//                }
//
//                $view_count = ContentViewLog::find()
//                    ->andWhere('started_at >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('started_at <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['content_provider_id' => $content_provider->id])
//                    ->count();
//
//                $content_revenues = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_CONTENT_PURCHASE])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['content_provider_id' => $content_provider->id])
//                    ->sum('subscriber_transaction.cost');
//
//                $content_buy = SubscriberTransaction::find()
//                    ->andWhere('subscriber_transaction.transaction_time >= :start')->addParams([':start' => $to_day])
//                    ->andWhere('subscriber_transaction.transaction_time <= :end')->addParams([':end' => $end_day])
//                    ->andWhere(['subscriber_transaction.type' => SubscriberTransaction::TYPE_CONTENT_PURCHASE])
//                    ->andWhere(['subscriber_transaction.status' => SubscriberTransaction::STATUS_SUCCESS])
//                    ->andWhere(['content_provider_id' => $content_provider->id])
//                    ->count();
//
//                $report = new ReportViewCategory();
//                $report->type = ReportViewCategory::TYPE_FULL;
//                $report->site_id = $content_provider->site_id;
//                $report->content_provider_id = $content_provider->id;
//                $report->report_date = $to_day_date;
//                $report->view_count = $view_count;
//                $report->download_count = $content_buy;
//                $report->buy_revenues = $content_revenues ? $content_revenues : 0;
//
//                $report->save();
//            }
//            $transaction->commit();
//            print "Done \n";
//
//        } catch (Exception $e) {
//            $transaction->rollBack();
//            print "Error \n";
//            print $e;
//        }
//    }
} 