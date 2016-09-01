<?php
/**
 * Created by PhpStorm.
 * User: nhocconsanhdieu
 * Date: 30/6/2015
 * Time: 3:37 PM
 */

namespace console\controllers;


use common\models\SubscriberServiceAsm;
use common\models\SubscriberTransaction;
use yii\console\Controller;
use common\helpers\CUtils;
use common\models\Subscriber;
use common\helpers\CommonConst;

class DataController extends Controller {


    /**
     * Tạo subscriber demo
     */
    public function actionCreateSubscriberDemo(){
        $password = '123456';
        for($i=1;$i<=100;$i++){
            $u = new Subscriber();
            $randomNumber = CUtils::randomString(7,"987654321");
            $msisdn = '8491'.$randomNumber;
            $u->msisdn = $msisdn;
            $u->site_id = 1;
            $u->username = $msisdn;
            $u->setPassword($password);
            $u->verification_code = $password;
            $u->email = $msisdn.'@gmail.com';
            $u->status = Subscriber::STATUS_ACTIVE;
            $u->sex = (int) CUtils::randomString(1,"01");
            $u->client_type = (int) CUtils::randomString(1,"1234");
            $u->auto_renew = (int) CUtils::randomString(1,"01");
            if ($u->save()) {
                echo "User created!: username:".$u->msisdn."\n";
            } else {
                echo "Cannot create User! \n";
            }
        }
    }

    public function actionCreateSubscriberServiceDemo(){
        $site_id = 1;
        $uid = (int)rand(8,108 );
        $type = (int)rand(1,9);
        $channel_type = (int)rand(1,5);
        $service_id = (int)rand(5,15);

        /** @var  $subscriber Subscriber */
        $subscriber = Subscriber::find()->andWhere(['id' => $uid])->andWhere(['status' => Subscriber::STATUS_ACTIVE])->one();
        $result = $subscriber->purchaseServicePackage($service_id, $channel_type);
        if ($result['error'] == CommonConst::API_ERROR_NO_ERROR) {
            echo "Success \n";
        } else {
            echo "False \n";
        }
//        $trans = new SubscriberTransaction();
//        $trans->subscriber_id = $uid;
//        $trans->msisdn = $user->msisdn;
//        $trans->type = $type;
    }


} 