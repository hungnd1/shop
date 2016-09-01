<?php
namespace api\controllers;

use api\helpers\authentications\IdentifyMsisdn;
use common\models\Languages;
use common\models\Site;
use common\models\SiteApiCredential;
//use common\models\UserAccessToken;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * Base controller for API app
 * @author Nguyen Chi Thuc (gthuc.nguyen@gmail.com)
 */
class ApiController extends Controller
{
    const HEADER_API_KEY = "X-Api-Key";
    const HEADER_SECRET_KEY = "X-Secret-Key";
    const HEADER_PACKAGE_NAME = "X-PackageName";
    const HEADER_FINGERPRINT = "X-Fingerprint";

    const HEADER_LANGUAGE = "X-Language";

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public $site;
    public $language;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                IdentifyMsisdn::className(),
                // them header: -H "Authorization: Bearer access_token"
                HttpBearerAuth::className(),
                // them tham so 'access-token' vao query
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];
        $behaviors['corsFilter'] = ['class' => \yii\filters\Cors::className(),];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {

        $language = Yii::$app->request->headers->get(static::HEADER_LANGUAGE);

        if(!array_key_exists($language, Languages::$language)){
            throw new UnauthorizedHttpException('Not support language: '.$language);
        }
        $this->language = $language;

        /** Sửa lại phần beforeAction vì code đang sai */
        $api_key = Yii::$app->request->headers->get(static::HEADER_API_KEY);
        if (!$api_key) {
            throw new UnauthorizedHttpException('Missing api key');
        } else {
            /* @var $credential SiteApiCredential */
            $credential = SiteApiCredential::findCredentialByApiKey($api_key);

            if (!$credential) {
                throw new UnauthorizedHttpException('Invalid api key');
            }
           $this->site = Site::findOne(['id' => $credential->site_id, 'status' => Site::STATUS_ACTIVE]);

            if (!$this->site) {
                throw new UnauthorizedHttpException('Invalid Service Provider');
            } else {
                /** Set site_id để dùng cho tiện */
                Yii::$app->params['site_id'] =   $this->site->id;
                switch ($credential->type) {
                    case SiteApiCredential::TYPE_WEB_APPLICATION:
                        break;
                    case SiteApiCredential::TYPE_IOS_APPLICATION:
                        break;
                    case SiteApiCredential::TYPE_WINDOW_PHONE_APPLICATION:
                        $secret_key = Yii::$app->request->headers->get(static::HEADER_SECRET_KEY);
                        if (!$secret_key || ($secret_key != $credential->client_secret)) {
                            throw new UnauthorizedHttpException('Invalid secret key');
                        }
                        break;
                    case SiteApiCredential::TYPE_ANDROID_APPLICATION:
//                        $packageName = Yii::$app->request->headers->get(static::HEADER_PACKAGE_NAME);
//                        $fingerprint = Yii::$app->request->headers->get(static::HEADER_FINGERPRINT);
//                        if (!$packageName || ($packageName != $credential->package_name)
//                            || !$fingerprint || ($fingerprint != $credential->certificate_fingerprint)
//                        ) {
//                            throw new UnauthorizedHttpException('Invalid package name or certificate fingerprint');
//                        }
                        break;
                    default:
                        break;
                }
//                $this->site = Site::findOne(['id' => $credential->site_id, 'status' => Site::STATUS_ACTIVE]);
//                if (!$this->site) {
//                    throw new UnauthorizedHttpException('Invalid Service Provider');
//                }
            }
        }
// goi cai nay truoc de trigger event EVENT_BEFORE_ACTION
        $res = parent::beforeAction($action);
        return $res;
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
        ];
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
    }

    /**
     * replace message
     *
     * @param $message
     * @param $params
     * @return mixed
     */
    public static function replaceParam($message, $params)
    {
        if (is_array($params)) {
            $cnt = count($params);
            for ($i = 1; $i <= $cnt; $i++) {
                $message = str_replace('{' . $i . '}', $params[$i - 1], $message);
            }
        }
        return $message;
    }

    /**
     * get value of parameter
     *
     * @param $param_name
     * @param null $default
     * @return mixed
     */
    public function getParameter($param_name, $default = null)
    {
        return \Yii::$app->request->get($param_name, $default);
    }

    /**
     * get value of parameter
     *
     * @param $param_name
     * @param null $default
     * @return mixed
     */
    public function getParameterPost($param_name, $default = null)
    {
        return \Yii::$app->request->post($param_name, $default);
    }

    /**
     * set status code response
     *
     * @param $code
     */
    public function setStatusCode($code)
    {
        Yii::$app->response->setStatusCode($code);
    }

}
