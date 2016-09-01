<?php

namespace console\controllers;


use common\helpers\Brandname;
use yii\console\Controller;

class TestMtController extends Controller
{
    /**
     * Test send MT with provider
     * @param $msisdn
     * @param $content
     * @param $sp
     */
    public function actionSendMt($msisdn)
    {
        $auth_code = '123456';
        $result = Brandname::sendRegisterSmsTo($msisdn, $auth_code);
        echo "Send MT to " . $msisdn . ':' . $result . "\n";
    }

    public function actionGetTelco($misisdn)
    {
        $mobiRegex = '/^(8490|8493|84120|84122|84126|84128|8489)/';
        if (preg_match($mobiRegex, $misisdn)) {
            return 'MOBI';
        }
        $vinaRegex = '/^(8491|8494|84123|84124|84125|84127|84129|8488)/';
        if (preg_match($vinaRegex, $misisdn)) {
            return 'VINA';
        }
        return 'VIETTEL';
    }
}
