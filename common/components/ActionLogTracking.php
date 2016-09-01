<?php
/**
 * Created by PhpStorm.
 * User: qhuy
 * Date: 6/17/15
 * Time: 3:00 PM
 */

namespace common\components;


use common\helpers\StringUtils;
use common\models\UserActivity;
use Faker\Provider\bn_BD\Utils;
use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\base\Module;
use yii\di\Instance;
use yii\helpers\VarDumper;
use yii\web\Request;
use yii\web\Response;
use yii\web\User;

class ActionLogTracking extends ActionFilter
{

    /**
     * @var User $user
     */
    public $user = 'user';

    public $post_action = [];

    public $model_type_default = UserActivity::ACTION_TARGET_TYPE_OTHER;

    public $model_types = [];

    /**
     * @var $request Request
     */
    public $request = 'request';

    /**
     * Initializes the [[rules]] array by instantiating rule objects from configurations.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    /**
     * @param Action $action
     * @param mixed | Response $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        return $result;
    }

}