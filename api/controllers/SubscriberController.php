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
use common\helpers\CommonConst;
use common\helpers\CommonUtils;
use common\helpers\MyCurl;
use common\helpers\VasProvisioning;
use common\helpers\VNPHelper;
use common\models\Content;
use common\models\ContentFeedback;
use common\models\ContentSiteAsm;
use common\models\ContentViewLog;
use common\models\ContentViewLogSearch;
use common\models\Device;
use common\models\Service;
use common\models\Site;
use common\models\Subscriber;
use common\models\SubscriberActivity;
use common\models\SubscriberContentAsm;
use common\models\SubscriberFavorite;
use common\models\SubscriberFavoriteSearch;
use common\models\SubscriberFeedback;
use common\models\SubscriberSearch;
use common\models\SubscriberServiceAsm;
use common\models\SubscriberServiceAsmSearch;
use common\models\SubscriberToken;
use common\models\SubscriberTransaction;
use common\models\SubscriberTransactionSearch;
use Yii;
use yii\base\Exception;
use yii\base\InvalidCallException;
use yii\base\InvalidValueException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class SubscriberController extends ApiController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'search',
            'feedbacks',
            'register',
            'login',
//            'change-password',
//            'edit-profile',
            'list-feedback',
            'get-msisdn',
            'verify-otp-password',
            'send-otp-password',
            'delete-sub',
//            'save-time-view',
//            'last-time-view',
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'register' => ['GET'],
            'login' => ['GET'],
            'change-password' => ['GET'],
            'edit-profile' => ['GET'],
//            'edit-profile' => ['POST'],
            'feedback' => ['POST'],
            'list-feedback' => ['GET'],
            'my-favorite' => ['GET'],
            'favorite' => ['GET'],
            'favorites' => ['GET'],
            'change-package' => ['POST'],
            'purchase-service-package' => ['POST'],
            'cancel-service-package' => ['POST'],
            'get-msisdn' => ['GET'],
            'download' => ['POST'],
            'verify-otp-password' => ['POST'],
            'send-otp-password' => ['GET'],
            'purchase-content' => ['GET'],
            'transaction-id' => ['POST'],
            'save-time-view' => ['GET'],
            'last-time-view' => ['GET'],

        ];
    }

//    public function actionSearch()
//    {
//        $subscriber = Yii::$app->user->identity;
//        $params = Yii::$app->request->queryParams;
//        $searchModel = new SubscriberSearch();
//
//        $searchModel->site_id = $this->site->id;
//        if (isset($subscriber)) {
//            $searchModel->id = $subscriber->id;
//        }
//        $searchModel->id = isset($params['id']) ? ($params['id']) : [];
//        $searchModel->status = Subscriber::STATUS_ACTIVE;
//        if (!$searchModel->validate()) {
//            $error = $searchModel->firstErrors;
//            $message = "";
//            foreach ($error as $key => $value) {
//                $message .= $value;
//                break;
//            }
//            throw new InvalidValueException($message);
//        }
//        $dataProvider = $searchModel->search($params);
//        return $dataProvider;
//    }

    public function actionTest(){
        /** Save SubscriberActivity */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        $description = 'cuongvm'.' login time:'.date('d-m-Y H:i:s', time());
        $s = SubscriberActivity::createSubscriberActivity($subscriber,$description,7,5);
        if($s){
            echo $s['message'];
        }else{
            echo ' dung: '.$s['message'];
        }
    }
    /**
     * @description Cho phép đăng ký account theo username, password.
     * @param $username
     * @param $password
     * @param $msisdn
     * @param $city
     * @return array
     * @throws ServerErrorHttpException
     */
    public function actionRegister($username, $password, $msisdn, $city,$channel = Subscriber::CHANNEL_TYPE_ANDROID)
    {
        $site_id = $this->site->id;
        if (empty($username)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['Tên đăng nhập']));
        }
        /** Không yêu cầu validate msisdn */
        if (empty($msisdn)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['Số điện thoại']));
        }

        if (empty($password)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['Mật khẩu']));
        }

        if (empty($city)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['Tỉnh/Thành phố']));
        }

        $u = Subscriber::findOne(['username' => $username, 'status' => [Subscriber::STATUS_ACTIVE, Subscriber::STATUS_INACTIVE] ]);
        if ($u) {
            throw new InvalidValueException(Message::MSG_USERNAME_ALREADY_EXIST);
        }

        $res = Subscriber::register($username, $password, $msisdn, $city, Subscriber::STATUS_ACTIVE, Subscriber::AUTHEN_TYPE_ACCOUNT, $site_id,$channel, null);
        if ($res['status']) {
            return ['message' => $res['message'],
                'subscriber' => $res['subscriber']
            ];
        } else {
            throw new ServerErrorHttpException($res['message']);
        }
    }

//    public function actionRegister($username,$password,$authen_type){
//        $site_id = $this->site->id;
//        /** validate input */
//        if (empty($username)) {
//            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['username']));
//        }
//        /** validate mobile */
//        $username = CommonUtils::validateMobile($username,1);
//        if(!$username){
//            throw new InvalidValueException(Message::WRONG_PHONE_NUMBER_REGISTER);
//        }
//
//        if (empty($authen_type)) {
//            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['authen_type']));
//        }
//        if(!is_numeric($authen_type)){
//            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['authen_type']));
//        }
//
//        $u = Subscriber::findOne(['username' => $username, 'status' => Subscriber::STATUS_ACTIVE] );
//        if($u){
//            throw new InvalidValueException(Message::MSG_USERNAME_ALREADY_EXIST);
//        }
//
//        if($authen_type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS){
//            /** convert về chữ thường */
//            $username = strtolower($username);
//            $device = Device::findOne(['device_id' => $username, 'status' => Device::STATUS_ACTIVE] );
//            if(!$device){
//                throw new InvalidValueException(Message::MSG_DEVICE_NOT_EXIST);
//            }
//        }
//
//        $res = Subscriber::register($username,$password,$authen_type,$site_id );
//        if($res['status']){
//            return [    'message'=>$res['message'] ,
//                'subscriber'=>$res['subscriber']
//            ];
//        }else{
//            throw new ServerErrorHttpException($res['message']);
//        }
//    }

    public function actionLogin($username, $password, $mac_address, $package_name, $channel = Subscriber::CHANNEL_TYPE_ANDROID,$authen_type = Subscriber::AUTHEN_TYPE_MAC_ADDRESS)
    {
        $site_id = $this->site->id;
        /** validate input */
        if (empty($username)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['Tên đăng nhập']));
        }
        if (empty($mac_address)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['Mac']));
        }
        if (empty($package_name)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['package_name']));
        }

        /** Kiểm tra xem có đúng MAC gửi lên là của VNPT Technology không */
        $device = Device::findByMac($mac_address, $site_id);
        if (!$device) {
            throw new NotFoundHttpException(Message::MSG_DEVICE_NOT_EXIST);
        }

        /**
         * Nếu kiểu authen_type = 1 thì đăng nhập bằng account, ngược lại thì là tài khoản default đăng nhập bằng MAC( không cần đăng ký)
         */
//        if ($site_id == Site::SITE_VIETNAM) {
        if ($authen_type == Subscriber::AUTHEN_TYPE_ACCOUNT) {
            /** Validate mobile VNP */
//            $username = CommonUtils::validateMobile($username, 2);
//            if (!$username) {
//                throw new InvalidValueException(Message::WRONG_PHONE_NUMBER_REGISTER);
//            }
            /** Check tài khoản có tồn tại không? */
            /** @var  $subscriber Subscriber */
            $subscriber = Subscriber::findByUsername($username, $site_id,false);
            if (!$subscriber) {
                throw new NotFoundHttpException(Message::MSG_WRONG_USERNAME_OR_PASSWORD);
            }
            /** Check tài khoản có bị block không? */
            $subscriber = Subscriber::findByUsername($username, $site_id);
            if (!$subscriber) {
                throw new NotFoundHttpException(Message::MSG_LOGIN_SUBSCRIBER_INACTIVE);
            }
            if (empty($password)) {
                throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['mật khẩu']));
            }
            if (!$subscriber->validatePassword($password)) {
                throw new InvalidValueException(Message::MSG_WRONG_USERNAME_OR_PASSWORD);
            }
        } else {
            /** @var  $subscriber Subscriber */
            $subscriber = Subscriber::findByUsername($mac_address, $site_id);
            /** Nếu không tồn tại subscriber thì tạo mới */
            if (!$subscriber) {
                $rs = Subscriber::register($mac_address, $password, null,null,Subscriber::STATUS_ACTIVE, Subscriber::AUTHEN_TYPE_MAC_ADDRESS, $site_id,$channel, $mac_address);
                if (!$rs['status']) {
                    throw new ServerErrorHttpException($rs['message']);
                }
                /** @var  $subscriber Subscriber */
                $subscriber = $rs['subscriber'];
            } else {
                /** Nếu tồn tại mà trạng thái không phải Active thì throw mess, không cho vào */
                if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
                    throw new ServerErrorHttpException(Message::MSG_LOGIN_SUBSCRIBER_INACTIVE);
                }
            }
        }

        /** Save SubscriberActivity */
        $description = $subscriber->username.' login time:'.date('d-m-Y H:i:s', time());
        SubscriberActivity::createSubscriberActivity($subscriber,$description,$channel,$site_id,SubscriberActivity::ACTION_LOGIN);
        /** Gen token */
        $token = SubscriberToken::generateToken($subscriber->id, $channel,$package_name);
        if (!$token) {
            throw new ServerErrorHttpException(Message::MSG_FAIL);
        }
        return ['message' => Message::MSG_LOGIN_SUCCESS,
            'id' => $subscriber->id,
            'username' => $subscriber->username,
            'full_name' => $subscriber->full_name,
            'city' => $subscriber->city,
            'msisdn' => $subscriber->msisdn,
            'balance' => $subscriber->balance,
            'token' => $token->token,
            'expired_date' => $token->expired_at,
            'authen_type' => $subscriber->authen_type,
            'package_name' => $package_name,
            'channel' => $token->channel,
            'site_id' => $site_id,
        ];
    }

    /**
     * @return array
     * @throws ServerErrorHttpException
     */
    public function actionLogout()
    {
        $site_id = $this->site->id;
        /* @var $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        /** @var  $st SubscriberToken */
        $st = SubscriberToken::findByAccessToken($subscriber->access_token);
        $st->status = SubscriberToken::STATUS_INACTIVE;
        if (!$st->save()) {
            throw new ServerErrorHttpException(Message::MSG_FAIL);
        }
        /** Save SubscriberActivity */
        $description = $subscriber->username.' logout time:'.date('d-m-Y H:i:s', time());
        SubscriberActivity::createSubscriberActivity($subscriber,$description,$st->channel,$site_id,SubscriberActivity::ACTION_LOGOUT);


        return ["message" => Message::MSG_SUCCESS];
    }

    /**
     * @param $new_password
     * @param $old_password
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionChangePassword($new_password, $old_password)
    {
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }

        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_USER);
        }
        if (!$subscriber->validatePassword($old_password)) {
            throw new InvalidValueException(Message::MSG_OLD_PASSWORD_WRONG);
        }
        $subscriber->password = $new_password;
        $subscriber->setPassword($new_password);
        if (!$subscriber->validate() || !$subscriber->save()) {
            $message = $subscriber->getFirstMessageError();
            throw new InvalidValueException($message);
        }
        /** Xóa tokent khi đổi password */
        /** @var  $st SubscriberToken */
        $st = SubscriberToken::findByAccessToken($subscriber->access_token);
        $st->status = SubscriberToken::STATUS_INACTIVE;
        if (!$st->save()) {
            throw new ServerErrorHttpException(Message::MSG_FAIL);
        }

        $res['message'] = Message::MSG_CHANGE_PASSWORD_SUCCESS;
        return $res;
    }

    /**
     * @return array
     */
    public function actionInfo()
    {
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        $rs = $subscriber->getAttributes(['id', 'username', 'full_name','balance', 'msisdn', 'status', 'birthday', 'sex', 'email', 'site_id', 'created_at', 'updated_at'], ['password_hash', 'authen_type']);
        return $rs;
    }

    /**
     * @return mixed
     * @throws ServerErrorHttpException
     */
    public function actionEditProfile()
    {
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        $full_name = $this->getParameter('full_name', '');
        $email = $this->getParameter('email', '');
        $birthday = $this->getParameter('birthday', '');
        $sex = $this->getParameter('sex', '');
        $msisdn = $this->getParameter('msisdn', '');
        if ($full_name) {
            $subscriber->full_name = $full_name;
        }
        if ($email) {
            $subscriber->email = $email;
        }
        if ($birthday) {
            $subscriber->birthday = $birthday;
        }
        if ($sex) {
            $subscriber->sex = $sex;
        }
        if ($msisdn) {
            $subscriber->msisdn = $msisdn;
        }

        if (!$subscriber->validate() || !$subscriber->save()) {
//            $message = $subscriber->getFirstMessageError();
//            throw new InvalidValueException($message);
            throw new ServerErrorHttpException(Message::MSG_FAIL);
        }
        $res['message'] = Message::MSG_UPDATE_PROFILE;
        return $res;

    }

    public function actionSaveTimeView($content_id, $category_id, $channel, $type = ContentViewLog::TYPE_VIDEO, $start_time = 0, $stop_time = null, $duration=0, $log_id = null)
    {
        $site_id = $this->site->id;
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        if (!is_numeric($content_id)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['content_id']));
        }
        if (!is_numeric($category_id)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['category_id']));
        }
        if (!is_numeric($channel)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['channel']));
        }
        /** @var  $content Content */
//        $content = Content::findOne(['id' => $content_id, 'status' => Content::STATUS_ACTIVE]);

        $content = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $content_id, 'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$content) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }
        /** Lưu thời gian của phim */
        if($duration){
            $content->duration = $duration;
            $content->save();
        }
        /** Chỉ ghi 1 bản ghi đối với 1 content& 1 channel , 1 type*/
        if(!$log_id){
            $cvl = ContentViewLog::findOne(['subscriber_id'=>$subscriber->id,'content_id'=>$content_id, 'channel'=>$channel,'site_id'=>$site_id,'type' =>$type,'status'=>ContentViewLog::STATUS_SUCCESS]);
            $cvl?$log_id = $cvl->id:$log_id=null;
        }

        $rs = ContentViewLog::createViewLog($subscriber, $content, $category_id, $type, $channel, $site_id, $start_time, $stop_time, $log_id);

        if (!$rs['status']) {
            throw new ServerErrorHttpException($rs['message']);
        }
        return ['message' => $rs['message'], 'log' => $rs['item']];

    }

    /**
     * @param $content_id
     * @param $channel
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionLastTimeView($content_id, $channel)
    {
        $site_id = $this->site->id;
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }

//        $content = Content::findOne(['id' => $content_id, 'status' => Content::STATUS_ACTIVE]);
        $content = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $content_id, 'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$content) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }
        $log = ContentViewLog::find()->where(['subscriber_id' => $subscriber->id, 'content_id' => $content_id, 'channel' => $channel, 'site_id' => $site_id])
            ->orderBy('view_date DESC')->one();
        if (!$log) {
            throw new NotFoundHttpException("Not found timeview ");
        }

        return $log;
    }

    /**
     * @param $content_id
     * @param int $status = 1 add, $status = 0 remove
     * @return array
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionFavorite($content_id, $status, $type)
    {
        $site_id = $this->site->id;
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        /** @var  $content Content */
//        $content = Content::findOne(['id' => $content_id, 'status' => Content::STATUS_ACTIVE]);
        $content = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $content_id, 'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$content) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }

        if (!is_numeric($status)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['status']));
        }
        if (!is_numeric($type)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['type']));
        }
        $rs = SubscriberFavorite::createFavorite($subscriber, $content, $site_id, $status, $type);
        if ($rs) {
            return ['message' => Message::MSG_ACTION_FAVORITE_SUCCESS];
        } else {
            throw new ServerErrorHttpException(Message::MSG_ACTION_FAVORITE_FALSE);
        }
    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function actionMyFavorite($type)
    {
        $site_id = $this->site->id;
        $param = Yii::$app->request->queryParams;
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        if (!is_numeric($type)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['type']));
        }
        $searchModel = new SubscriberFavoriteSearch();
        $searchModel->subscriber_id = $subscriber->id;
        $searchModel->site_id = $site_id;
        $searchModel->type = $type;

        $dataProvider = $searchModel->search($param);
//        if(!$dataProvider->getModels()){
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }
        return $dataProvider;

    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function actionWatchedVideo($type)
    {
        UserHelpers::manualLogin();
        $site_id = $this->site->id;
        $param = Yii::$app->request->queryParams;
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        if (!is_numeric($type)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['type']));
        }
        $searchModel = new ContentViewLogSearch();
        $searchModel->subscriber_id = $subscriber->id;
        $searchModel->site_id = $site_id;
        $searchModel->type = $type;

        $dataProvider = $searchModel->search($param);
        return $dataProvider;
    }

    public function actionTransaction(){
        $site_id = $this->site->id;
        $param = Yii::$app->request->queryParams;
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        $searchModel = new SubscriberTransactionSearch();
        $searchModel->subscriber_id = $subscriber->id;
        $searchModel->site_id = $site_id;

        $dataProvider = $searchModel->search($param);
        return $dataProvider;
    }

    /**
     * HungNV 14 April
     *
     * @return array
     * @throws ServerErrorHttpException
     * @throws UnauthorizedHttpException
     */
    public function actionFeedback()
    {
        /*
         * HungNV
         *
         * feedback to site about Problems or bad Contents
         *  so this not relate to rating, like, etc...
         */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new UnauthorizedHttpException(Message::MSG_ACCESS_DENNY);
        }
        $site_id = $this->site->id;
        /* Change to POST later */
        $content_id = $this->getParameterPost('content_id', '');
        $title = $this->getParameterPost('title', '');
        $content = $this->getParameterPost('content', '');
        $res = SubscriberFeedback::createFeedback($subscriber, $site_id, $content_id, $title, $content);
        if ($res) {
            return ['message' => Message::MSG_ACTION_FEEDBACK_SUCCESS];
        } else {
            throw new ServerErrorHttpException(Message::MSG_ACTION_FAIL);
        }
        /*
         * HungNV block old code
         *
        $rating = $this->getParameterPost('rating', null);
        $like = $this->getParameterPost('like', 0);
        $comment = $this->getParameterPost('content', '');
        $title = $this->getParameterPost('title', '');
        $content_id = $this->getParameterPost('content_id', 0);
        $content = Content::findOne(['id' => $content_id]);
        if (!$content) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }
        $result = ContentFeedback::createFeedback($content, $subscriber, $title, $comment, $like, $rating);
        if ($result) {
            $this->setStatusCode(200);
            return [
                'message' => 'Tạo feedback thành công'
            ];
        } else {
            $this->setStatusCode(500);
            return [
                'message' => 'Tạo feedback lỗi'
            ];
        }
        */

    }

    /**
     * HungNV 14-April
     *
     * @params content_id, from_date, to_date (statistic report)
     * @return \yii\data\ActiveDataProvider
     */
    public function actionFeedbacks()
    {
        $content_id = $this->getParameter('id');
        $from_date = $this->getParameter('from_date');
        $to_date = $this->getParameter('to_date');
        $res = SubscriberFeedback::getFeedbacks($this->site->id, $content_id, $from_date, $to_date);
        return $res;
    }

//    public function actionFavorites()
//    {
//        $msisdn = VNPHelper::getMsisdn(false, true);
//        $subscriber = null;
//        if ($msisdn) {
//            $subscriber = \api\models\Subscriber::findByMsisdn($msisdn, $this->serviceProvider->id);
//            return $subscriber->favorites($this->serviceProvider->id);
//        }
//    }


//    public function actionFavorite()
//    {
//        $content_id = $this->getParameterPost('content_id');
//        if ($content_id == null || $content_id == '') {
//            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['Nội dung']));
//        }
//        $content = Content::findOne($content_id);
//        if (!$content) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }
//        $result = SubscriberFavorite::createFavorite(Yii::$app->user->getIdentity(), $content, $this->serviceProvider->id);
//        if ($result) {
//            $this->setStatusCode(200);
//            return [
//                'message' => 'Cập nhật favorite thành công'
//            ];
//        } else {
//            $this->setStatusCode(500);
//            return [
//                'message' => 'Cập nhật favorite lỗi'
//            ];
//        }
//    }

//    public function actionMyFavorite()
//    {
//        return SubscriberFavorite::getListFavorite(Yii::$app->user->getIdentity(), $this->serviceProvider->id);
//    }

//    public function actionPurchaseServicePackage()
//    {
//        $service_id = $this->getParameter('service_id');
//        $channel = $this->getParameter('channel', SubscriberTransaction::CHANNEL_TYPE_SMS);
//
//        /** @var  $subscriber Subscriber */
//        $subscriber = Yii::$app->user->identity;
//
//        $service = Service::find()->andWhere(['id' => $service_id])->andWhere(['status' => Service::STATUS_ACTIVE])->one();
//
//        if (!$service) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_SERVICE);
//        }
//
//        $result = $subscriber->buyPackageVas($service, $channel, '', 0, false);
//        return ['message' => $result['message']];
//    }

//    public function actionPurchaseServicePackage()
//    {
//        $service_id = $this->getParameterPost('service_id');
//        $channel = $this->getParameterPost('channel', SubscriberTransaction::CHANNEL_TYPE_SMS);
//
//        /** @var  $subscriber Subscriber */
//        $subscriber = Yii::$app->user->getIdentity();
//        $service = Service::find()->andWhere(['id' => $service_id])->andWhere(['status' => Service::STATUS_ACTIVE])->one();
//
//        if (!$service) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_SERVICE);
//        }
//
//        $result = $subscriber->purchaseServicePackage($service, $channel);
//
//        if ($result['error'] == CommonConst::API_ERROR_NO_ERROR) {
//            $this->setStatusCode(200);
//        } else {
//            $this->setStatusCode(500);
//        }
//        Yii::info($result);
//        return [
//            'code' => $result['error'],
//            'message' => $result['message']
//        ];
//    }


//    public function actionTransactionId()
//    {
//        $service_id = $this->getParameterPost('service_id');
//        $channel = $this->getParameterPost('channel', SubscriberTransaction::CHANNEL_TYPE_SMS);
//
//        /** @var  $subscriber Subscriber */
//        $subscriber = Yii::$app->user->getIdentity();
//        $service = Service::findOne($service_id);
//
//        if (!$service) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_SERVICE);
//        }
//        $servicep = ServiceProvider::findOne($service->site_id);
//        Yii::info($servicep);
//        $transaction = $subscriber->newTransaction(SubscriberTransaction::TYPE_REGISTER, SubscriberTransaction::CHANNEL_TYPE_WAP, '', $service);
//
//        if ($transaction) {
//            return ['message' => 'Tạo thành công', 'request_id' => $transaction->id, 'package_name' => $transaction->service->name,'service'=>$servicep->provisioning_app,'cp'=>$servicep->vasgate_cp_id,'securecode'=>$servicep->vasgate_securecode];
//        } else {
//            throw new InvalidValueException('Hệ thống đang bận, xin vui lòng thử lại sau!');
//        }
//
//    }
//    public function actionPurchaseContentOld()
//    {
//        $content_id = $this->getParameterPost('content_id');
//        $channel = $this->getParameterPost('channel', SubscriberTransaction::CHANNEL_TYPE_SMS);
//        /** @var  $subscriber Subscriber */
//        /** @var  $content Content */
//        $subscriber = Yii::$app->user->getIdentity();
//        $content = Content::find()->andWhere(['id' => $content_id])->andWhere(['status' => Content::STATUS_ACTIVE])->one();
//        if (!$content) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }
//        $result = $subscriber->purchaseContent($this->serviceProvider, $content, $channel);
//        if ($result['error'] == CommonConst::API_ERROR_NO_ERROR) {
//            $this->setStatusCode(200);
//        } else {
//            $this->setStatusCode(500);
//        }
//        Yii::info($result['message_web'] . 'abc');
//        return ['message' => $result['message_web']];
//    }

    public function actionMyService(){
        $site_id = $this->site->id;
        $param = Yii::$app->request->queryParams;
        /** @var  $subscriber Subscriber*/
        $subscriber = Yii::$app->user->identity;
        if(!$subscriber){
            throw new UnauthorizedHttpException(Message::MSG_ACCESS_DENNY);
        }
        $searchModel = new SubscriberServiceAsmSearch();
        $searchModel->subscriber_id = $subscriber->id;
        $searchModel->status = SubscriberServiceAsm::STATUS_ACTIVE;
        $searchModel->site_id = $site_id;

        $dataProvider = $searchModel->search($param);
        if(!$dataProvider->getModels()){
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_SERVICE);
        }
        return $dataProvider;

    }

    public function actionPurchaseContent($content_id,$channel = SubscriberTransaction::CHANNEL_TYPE_ANDROID)
    {
        /** @var  $subscriber Subscriber*/
        $subscriber = Yii::$app->user->identity;
        if(!$subscriber){
            throw new UnauthorizedHttpException(Message::MSG_ACCESS_DENNY);
        }
        if (!is_numeric($content_id)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['content_id']));
        }

//        $content = Content::findOne(['id'=>$content_id,'status'=>Content::STATUS_ACTIVE]);
        $content = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $content_id, 'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$content) {
            throw new InvalidValueException(Message::MSG_NOT_FOUND_CONTENT);
        }
        $res = $subscriber->purchaseContent($this->site, $content, $channel, SubscriberTransaction::TYPE_CONTENT_PURCHASE);

        if($res['error'] != CommonConst::API_ERROR_NO_ERROR ){
            $this->setStatusCode(500);
            return ['message' => $res['message_web'],'code'=>$res['error']];
        }

        return $res;
    }


    public function actionPurchaseService($service_id, $channel = SubscriberTransaction::CHANNEL_TYPE_ANDROID)
    {
        /** @var  $subscriber Subscriber*/
        $subscriber = Yii::$app->user->identity;
        if(!$subscriber){
            throw new UnauthorizedHttpException(Message::MSG_ACCESS_DENNY);
        }
        if (!is_numeric($service_id)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['service_id']));
        }

        $service = Service::findOne(['id'=>$service_id,'status'=>Service::STATUS_ACTIVE,'site_id'=>$this->site->id]);
        if (!$service) {
            throw new InvalidValueException(Message::MSG_NOT_FOUND_SERVICE);
        }

        /** Nếu đã mua rồi thì là gia hạn, còn chưa mua thì là đăng ký mới */
        $is_my_package = $subscriber->checkMyService($service->id);
        if(!$is_my_package){
            $type = SubscriberTransaction::TYPE_REGISTER;
        }else{
            $type = SubscriberTransaction::TYPE_RENEW;
        }
        $res = $subscriber->purchaseServicePackage($service, $channel, $type,true);

        if($res['error'] != CommonConst::API_ERROR_NO_ERROR ){
            $this->setStatusCode(500);
            return ['message' => isset($res['message_web'])?$res['message_web']:$res['message'],'code'=>$res['error']];
        }

        return $res;
    }
//    /**
//     * @param $content_id
//     * @param int $type
//     * @return array
//     * @throws NotFoundHttpException
//     */
//    public function actionPurchaseContent($content_id, $type = SubscriberContentAsm::TYPE_PURCHASE_COIN)
//    {
//        /** @var $subscriber Subscriber */
//        $subscriber = Yii::$app->user->identity;
//        if (!$subscriber) {
//            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
//        }
//        /** @var $content Content */
//        $content = Content::findOne(['content.id' => $content_id, 'content.status' => Content::STATUS_ACTIVE]);
//        if (!is_numeric($content_id)) {
//            throw new InvalidValueException(Message::MSG_NUMBER_ONLY);
//        }
//        if (!$content) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }
//        $purchase = $subscriber->buyContent($subscriber, $content, $type);
//        if ($purchase['status'] == false) {
//            throw new InvalidValueException($purchase['message']);
//        }
//        return $purchase;
//    }

//    public function actionDownload()
//    {
//        $content_id = $this->getParameterPost('content_id');
//        Yii::info($content_id);
//        $channel = $this->getParameterPost('channel', SubscriberTransaction::CHANNEL_TYPE_SMS);
//        /** @var  $subscriber Subscriber */
//        /** @var  $content Content */
//        $subscriber = Yii::$app->user->getIdentity();
//        $content = Content::find()->andWhere(['id' => $content_id])->andWhere(['status' => Content::STATUS_ACTIVE])->one();
//        if (!$content) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }
//
//        $result = $subscriber->downloadContent($this->serviceProvider, $content, $channel);
//
//        if ($result['error'] == CommonConst::API_ERROR_NO_ERROR) {
//            $this->setStatusCode(200);
//            return ['message' => $result['message_web'], "url_download" => $result["link_download"]];
//        } else {
//            $this->setStatusCode(500);
//            return ['message' => $result['message_web']];
//        }
//
//    }

//    public function actionCancelServicePackage()
//    {
//        $service_id = $this->getParameterPost('service_id');
//        $channel = $this->getParameterPost('channel', SubscriberTransaction::CHANNEL_TYPE_SMS);
//        /** @var  $subscriber Subscriber */
//        $subscriber = Yii::$app->user->getIdentity();
//        $service = Service::find()->andWhere(['id' => $service_id])->andWhere(['status' => Service::STATUS_ACTIVE])->one();
//        if (!$service) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_SERVICE);
//        }
//        $result = $subscriber->cancelServicePackage($service, $channel);
//        if ($result['error'] == CommonConst::API_ERROR_NO_ERROR) {
//            $this->setStatusCode(200);
//        } else {
//            $this->setStatusCode(500);
//        }
//        return ['message' => $result['message']];
//    }

//    public function actionChangePackage()
//    {
//        $service_id = $this->getParameterPost('service_id');
//        $channel = $this->getParameterPost('channel', SubscriberTransaction::CHANNEL_TYPE_SMS);
//        /** @var Subscriber $subscriber */
//        $subscriber = Yii::$app->user->getIdentity();
//        $service = Service::find()->andWhere(['id' => $service_id])->andWhere(['status' => Service::STATUS_ACTIVE])->one();
//        if (!$service) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_SERVICE);
//        }
//        $result = $subscriber->changeService($service, $channel);
//        if ($result['error'] == CommonConst::API_ERROR_NO_ERROR) {
//            $this->setStatusCode(200);
//        } else {
//            $this->setStatusCode(500);
//        }
//        return ['message' => $result['api_message']];
//    }

//    public function actionTransaction()
//    {
//        /** @var  $subscriber Subscriber */
//        $subscriber = Yii::$app->user->getIdentity();
//        return $subscriber->getTransactions();
//    }

    /**
     * ma loi:
     * 0|success
     * 1|unknown ip
     * 2|invalid subscriber
     * 3|otpaready sent
     * 4|other error
     * @param $msisdn
     * @return json: ["success": true, "error" : "1"]
     */
//    public function actionSendOtpPassword()
//    {
//        $msisdn = $this->getParameter('msisdn');
//        $XMLGW_SERVICE_NAME = "MSP";
//        $ch = new MyCurl();
//        $response = $ch->get('http://10.1.10.47/otp/getotp', array(
//            'msisdn' => $msisdn,
//            'servicename' => $XMLGW_SERVICE_NAME,
//        ));
//
//        Yii::info("Send otp to $msisdn");
//        Yii::info($response);
//
//        $error = -1;
//        if (!$response)
//            $error = 0;
//        else {
//            $arrResponse = explode('|', $response->body);
//            if (count($arrResponse) > 1) {
//                $error = $arrResponse[0];
//            }
//        }
//        return ['success' => $error == 0, 'error' => $error . ""];
//    }

    /**
     * 0|MSISDN
     * 1|unknown ip
     * 2|wrong otp token
     * 3|max retry
     * 4|other error
     * @param $msisdn
     * @param $password
     * @return string
     */
//    public function actionVerifyOtpPassword()
//    {
//        $msisdn = $this->getParameterPost('msisdn');
//        $password = $this->getParameterPost('password');
//        $ch = new MyCurl();
//        $response = $ch->get('http://10.1.10.47/otp/checkotp', array(
//            'msisdn' => $msisdn,
//            'otptoken' => $password,
//            'servicename' => 'MSP',
//        ));
//
//        Yii::info("Check otp of $msisdn: $password");
//        Yii::info($response);
//
//        $error = -1;
//        if (!$response)
//            $error = 0;
//        else {
//            $arrResponse = explode('|', $response->body);
//            if (count($arrResponse) > 1) {
//                $error = $arrResponse[0];
//            }
//        }
//
//        return ['success' => $error == 0, 'error' => $error . ""];
//    }

//    public function actionGetMsisdn()
//    {
//        $msisdn = Subscriber::getMsisdn();
//        $result = Subscriber::getSubscriberInfo(intval($msisdn));
//        if (empty($result) || $result == '') {
//            return ['message' => 'fail'];
//        } else {
//            return $result;
//        }
//    }

//    public function actionDeleteSub($msisdn)
//    {
//        $subscriber = Subscriber::findOne(['msisdn' => $msisdn]);
//        if ($subscriber) {
//
//            Yii::$app->db->createCommand()->delete('content_view_log', ['subscriber_id' => $subscriber->id])->execute();
//            Yii::$app->db->createCommand()->delete('subscriber_activity', ['subscriber_id' => $subscriber->id])->execute();
//            Yii::$app->db->createCommand()->delete('subscriber_content_asm', ['subscriber_id' => $subscriber->id])->execute();
//            Yii::$app->db->createCommand()->delete('subscriber_favorite', ['subscriber_id' => $subscriber->id])->execute();
//            Yii::$app->db->createCommand()->delete('subscriber_feedback', ['subscriber_id' => $subscriber->id])->execute();
//
//            Yii::$app->db->createCommand()->delete('subscriber_service_asm', ['subscriber_id' => $subscriber->id])->execute();
//            Yii::$app->db->createCommand()->delete('subscriber_transaction', ['subscriber_id' => $subscriber->id])->execute();
//
//            Yii::$app->db->createCommand()->delete('subscriber_token', ['subscriber_id' => $subscriber->id])->execute();
//            Yii::$app->db->createCommand()->delete('sms_message', ['subscriber_id' => $subscriber->id])->execute();
//            Yii::$app->db->createCommand()->delete('subscriber', ['id' => $subscriber->id])->execute();
//
//            return ['message' => 'Thanh cong'];
//        } else {
//            throw new BadRequestHttpException('Không tìm thấy người dùng');
//        }
//    }

    /**
     * @return ActiveDataProvider
     */
    public function actionContentLog()
    {
        UserHelpers::manualLogin();
        /** @var $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_NUMBER_ONLY);
        }
        $channel = $this->getParameter('channel', '');
        $content_id = $this->getParameter('content_id', '');
        $view_date = $this->getParameter('view_date', '');
        if ($channel || $content_id) {
            if (!is_numeric($channel) || !is_numeric($content_id)) {
                throw new InvalidValueException(Message::MSG_NUMBER_ONLY);
            }
        }
        $viewLog = ContentViewLog::viewLogSearch($subscriber, $this->site->id, $channel, $content_id, $view_date);
        if (!$viewLog['status']) {
            throw new InvalidCallException($viewLog['message']);
        }
        return $viewLog['items'];
    }

    /**
     * @return mixed
     * @throws ServerErrorHttpException
     */
    public function actionSetBalance()
    {
        /** @var  $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        $subscriber->balance = 1000000;
        if (!$subscriber->validate() || !$subscriber->save()) {
            throw new ServerErrorHttpException(Message::MSG_FAIL);
        }
        $res['message'] = Message::MSG_SUCCESS;
        return $res;
    }

    /**
     * @param $display_id
     * @return mixed
     * @throws ServerErrorHttpException
     */
    public function actionGetUnsupportedQualities($display_id)
    {
        if (empty($display_id)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NULL_VALUE, ['display_id']));
        }
        $res = Device::getUnsupportedQualities($display_id);
        if (!$res['success']) {
            throw new ServerErrorHttpException($res['message']);
        }
        return ['qualities' => $res['data'] ];
    }

}