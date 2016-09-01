<?php
/**
 * Created by PhpStorm.
 * User: VS9 X64Bit
 * Date: 25/02/2015
 * Time: 9:03 AM
 */

namespace api\models;

class Subscriber extends \common\models\Subscriber {
    public function fields() {
        $fields = parent::fields();
        unset($fields['password']);
        unset($fields['last_login_at']);
        unset($fields['last_login_session']);
        unset($fields['verification_code']);
        unset($fields['user_agent']);


        $fields['full_name'] = function ($model) {
            /* @var $model Subscriber */
            return $model->getDisplayName();
        };

        $fields['service_provider_name'] = function ($model){
            /* @var $model Subscriber */
            return $model->serviceProvider->name;
        };

        return $fields;
    }

    public static function getLogin($username, $password){
        $query = Subscriber::find()->andWhere(['msisdn' => $username, 'password' => $password])->all();
        if($query){
            return true;
        }else{
            return false;
        }
    }

}