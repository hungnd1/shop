<?php
/**
 * Created by PhpStorm.
 * User: VS9 X64Bit
 * Date: 23/05/2015
 * Time: 4:37 PM
 */

namespace api\controllers;


use common\helpers\MTParam;
use common\helpers\SMSGW;
use common\models\Content;
use common\models\Service;
use common\models\ServiceProvider;
use common\models\SmsMtTemplate;
use common\models\Subscriber;

class TestController extends ApiController {
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'list-content',
            'detail',
            'test-register',
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'list-content' => ['GET'],
            'detail' => ['GET'],
            'related' => ['GET'],
        ];
    }
    public function actionGetTime(){
//        $start_of_day = time() - 86400 + (time() % 86400);
//        $end_of_day = $start_of_day + 86400;
//        echo date('d-m-Y H:i:s', time());
        //echo 'start_of_day: '.date('d-m-Y H:i:s', $start_of_day).' - end_of_day'.date('d-m-Y H:i:s', $end_of_day);


//        echo date('d-m-Y H:i:s', strtotime('-1 day', time()) );
//        $stamp = mktime(0, 0, 0);
        $stamp = mktime(23, 59, 59);
        echo date('m-d-Y H:i:s',$stamp);
    }

}