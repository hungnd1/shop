<?php
/**
 * Created by PhpStorm.
 * User: VS9 X64Bit
 * Date: 21/05/2015
 * Time: 9:43 AM
 */

namespace api\controllers;


use api\helpers\Message;
use api\helpers\UserHelpers;
use common\helpers\CommonUtils;
use common\helpers\VNPHelper;
use common\models\Device;
use common\models\Subscriber;
use common\models\SubscriberToken;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidValueException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class UserController extends ApiController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'edit-profile',
            'change-password',
            'register',
            'verify',
            'login',
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'verify' => ['GET'],
            'register' => ['POST'],
            'login' => ['POST'],
            'logout' => ['GET'],
            'user-info' => ['GET'],
            'change-password' => ['POST'],
            'edit-profile' => ['POST'],
        ];
    }

    /**
     * HungNV edition: 16/03/16
     *
     * @return array
     * @throws ServerErrorHttpException
     */
    public function actionRegister($type = Subscriber::AUTHEN_TYPE_MSISDN)
    {
        /*
         * DROPDOWN SELECT AUTHEN_TYPE
         *      OR
         * INPUT AND IDENTIFY AUTHEN_TYPE
         */

        if ($type == Subscriber::AUTHEN_TYPE_MSISDN) {
            $username = $this->getParameterPost('phone', '');
            $username = CommonUtils::validateMobile($username, 0);
        } else {
            if ($type == Subscriber::AUTHEN_TYPE_ACCOUNT) {
                $username = $this->getParameterPost('account');
            } else {
                if ($type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS) {
                    $username = $this->getParameter('mac');
                } else {
                    throw new InvalidValueException(Message::MSG_KHONG_NHAN_DIEN_THUE_BAO);
                }
            }
        }
        if ($username == '') {
            throw new InvalidValueException(Message::WRONG_PHONE_NUMBER_REGISTER);
        }

        $password = $this->getParameterPost('password');

        $res = Subscriber::register($username, $password, $type, $this->site->id);
        if ($res['status']) {
            return ['message' => $res['message']];
        } else {
            throw new ServerErrorHttpException($res['message']);
        }
    }

    public function actionVerify()
    {
//        return '';
//        $verify_code = $this->getParameter('verify_code','');
//        $username = $this->getParameter('username','');
//
//        /* @var Subscriber $subscriber */
//        $subscriber = Subscriber::findByVerifyToken($verify_code,$username);
//        if($subscriber){
//            $subscriber->status = Subscriber::STATUS_ACTIVE;
//            $subscriber->verification_code = '';
//            if($subscriber->save()){
//                return ['message'=>Message::MSG_VERIFY_TRUE];
//            }else{
//                throw new NotFoundHttpException(Message::MSG_FAIL);
//            }
//        }
//        throw new NotFoundHttpException(Message::MSG_VERIFY_TOKEN_WRONG);
    }

    /**
     * HungNV edition: 15/03/16 - Login base on site_id, by username, password
     * @return array
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionLogin()
    {
        $username = $this->getParameterPost('username', '');
        $password = $this->getParameterPost('password', '');
        //$site_id = 1;

        /** Valid data*/
        if (empty($username)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['tên đăng nhập']));
        }

        if (empty($password)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['mật khẩu']));
        }
        // convert đầu số
        $username = CommonUtils::validateMobile($username, 0);

        if ($username == '') {
            throw new InvalidValueException(Message::WRONG_PHONE_NUMBER_LOGIN);
        }
        /* @var Subscriber $subscriber */
        $subscriber = Subscriber::findSubscriberBySP($username, $this->site->id, true);

        if (isset($subscriber)) {
            if ($subscriber->validatePassword($password)) {

                $token = SubscriberToken::generateToken(1, $subscriber->id, $subscriber->msisdn);
                if ($token != null) {
                    return ['message' => 'Đăng nhập thành công',
                        'id' => $subscriber->id,
                        'username' => $subscriber->username,
                        'access_token' => $token,
                        'full_name' => $subscriber->full_name,
                        'email' => $subscriber->email,
                        'sex' => $subscriber->sex,
                        'birthday' => $subscriber->birthday,
                    ];
                }
                throw new ServerErrorHttpException(Message::MSG_FAIL);
            } else {
                throw new InvalidValueException("Sai mật khẩu");
            }
        }
        throw new NotFoundHttpException(Message::MSG_NOT_FOUND_USER);
    }

    public function actionLogout()
    {
        /* @var $subscriber Subscriber */
        /* @var $subscriber_token SubscriberToken */

        $subscriber = Yii::$app->user->identity;

        $subscriber_token = SubscriberToken::findByAccessToken($subscriber->access_token);
        if ($subscriber_token) {
            $subscriber_token->status = Subscriber::STATUS_INACTIVE;
            if ($subscriber_token->save()) {
                return ["message" => "Logout successful"];
            } else {
                throw new ServerErrorHttpException("Cannot logout");
            }
        }
    }

    public function actionUserInfo()
    {
        /* @var $subscriber Subscriber */
        $msisdn = VNPHelper::getMsisdn(false, true);
//        var_dump($msisdn); die;
        $subscriber = null;

        if ($msisdn) {
            $subscriber = \api\models\Subscriber::findByMsisdn($msisdn, $this->site->id);
        }
//        var_dump($subscriber); die;
        if ($subscriber) {
            return $subscriber;
        }
        throw new NotFoundHttpException("Không tồn tại người dùng!");
    }

// change password subscriber
    public function actionChangePassword()
    {
        UserHelpers::manualLogin();
        $username = $this->getParameterPost('username', '');
        $new_password = $this->getParameterPost('new_password', '');
        $old_password = $this->getParameterPost('old_password', '');

        if ($old_password == '') {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['mật khẩu cũ']));
        }
        if ($new_password == '') {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['mật khẩu mới']));
        }
        $subscriber = Subscriber::find()
            ->andWhere(['msisdn' => $username])
            ->andWhere(['status' => Subscriber::STATUS_ACTIVE])
            ->One();
        /* @var $subscriber Subscriber */
        if ($subscriber->validatePassword($old_password)) {
            $subscriber->setPassword($new_password);
            if ($subscriber->save()) {
                return ['message' => Message::MSG_CHANGE_PASSWORD_SUCCESS];
            }
        } else {
            throw new InvalidValueException(Message::MSG_WRONG_PASSWORD);
        }
    }

    /**
     * HungNV edition: 17/03/16
     *
     * @return array
     * @throws ErrorException
     * @throws NotFoundHttpException
     */
    public function actionEditProfile()
    {
        $phone = $this->getParameterPost('phone_number', '');
        $full_name = $this->getParameterPost('full_name', '');
        $email = $this->getParameterPost('email', '');
        $birthday = $this->getParameterPost('birthday', '');
        $sex = $this->getParameterPost('sex', '');

        /* @var $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        $msisdn = VNPHelper::getMsisdn(false, true);
        $phone = CommonUtils::validateMobile($phone, 0);
        $subscriber = null;
        if (!$msisdn) {
            if ($phone) {
                $msisdn = $phone;
            } else {
                throw new NotFoundHttpException(Message::MSG_NOT_FOUND_USER);
            }
        }
        var_dump($this->site->id);
        die;
        $subscriber = \api\models\Subscriber::findByMsisdn($msisdn, $this->site->id);
        $subscriber->full_name = $full_name;
        $subscriber->email = $email;
        $subscriber->birthday = $birthday;
        $subscriber->sex = $sex;
        $subscriber->updated_at = time();

        if ($subscriber->update()) {
            return ['message' => Message::MSG_UPDATE_PROFILE];
        }
        throw new ErrorException(Message::MSG_FAIL);
    }

}