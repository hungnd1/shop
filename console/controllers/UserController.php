<?php

/**
 * Swiss army knife to work with user and rbac in command line
 * @author: Nguyen Chi Thuc
 * @email: gthuc.nguyen@gmail.com
 */
namespace console\controllers;

use common\auth\helpers\AuthHelper;
use common\helpers\StringUtils;
use common\models\AuthItem;
use common\models\Dealer;
use common\models\Site;
use common\models\User;
use ReflectionClass;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\rbac\DbManager;
use yii\rbac\Item;
use common\models\SubscriberServiceAsm;

/**
 * UserController create user in commandline
 */
class UserController extends Controller
{


    /**
     * Sample: ./yii user/create-subscriber-service "1" "1" "1"
     * @param $service_id
     * @param $subscriber_id
     * @param $site_id
     */
    public function actionCreateSubscriberService($service_id,$subscriber_id,$site_id){
        $sc = new SubscriberServiceAsm();
        $sc->site_id =$site_id;
        if($site_id ==1){
            $sc->service_id =$service_id;
            $sc->service_name = "Pk1";
        }elseif($site_id ==2){
            $sc->service_id =$service_id;
            $sc->service_name = "Xp2";
        }
        $sc->subscriber_id =3;
        $sc->msisdn = "0987658888";
        $sc->activated_at = time();
        $sc->expired_at = time();
        if($sc->save()){
            echo 'Done !';
        }else{
            var_dump($sc->getFirstErrors());
        }
    }

    /**
     * Sample: ./yii be-user/create-admin-user "thucnc@vivas.vn" "123456"
     * @param $email
     * @param $password
     * @throws Exception
     */
    public function actionCreateAdminUser($email, $password) {
        $this->actionCreateUser('admin', $email, $password);
    }


    /**
     * Sample: ./yii be-user/create-dealer-user "huydq" "huydq@vivas.vn" "123456" 1
     * @param $username
     * @param $email
     * @param $password
     * @param $dealer_id
     * @throws Exception
     */
    public function actionCreateDealerUser($user, $email, $password,$dealer_id) {
        $sp_user = $this->actionCreateUser($user, $email, $password);
        /**
         * @var $dealer Dealer
         */
        $dealer = Dealer::findOne($dealer_id);
        if(!$dealer){
            echo "Dealer not available\n";
        }
        $sp_user->site_id = $dealer->site_id;
        $sp_user->dealer_id = $dealer->id;
        $sp_user->type = User::USER_TYPE_DEALER;
        $sp_user->update();
    }

    /**
     * Sample: ./yii be-user/create-sp-user "huydq" "huydq@vivas.vn" "123456" 1
     * @param $username
     * @param $email
     * @param $password
     * @param $sp_id
     * @throws Exception
     */
    public function actionCreateSpUser($user, $email, $password, $sp_id) {
        $sp_user = $this->actionCreateUser($user, $email, $password);
        $sp_user->site_id = $sp_id;
        $sp_user->type = User::USER_TYPE_SP;
        $sp_user->update();
        return $sp_user;
    }

    public function actionSetPassword($user, $password) {
        $user = User::findByUsername($user);
        if ($user) {
            $user->setPassword($password);
            if ($user->save()) {
                echo 'Password changed!\n';
                return 0;
            }
            else {
                Yii::error($user->getErrors());
                VarDumper::dump($user->getErrors());
                throw new Exception("Cannot change password!");
            }
        }
        else {
            echo "User not found!\n";
            return 1;
        }
    }

    /**
     * @param $username
     * @param $email
     * @param $password
     * @param string $full_name
     * @return $user User
     * @throws Exception
     */
    public function actionCreateUser($username, $email, $password,  $full_name = "") {
        $user = new User();
        $user->username = $username;
        $user->status = User::STATUS_ACTIVE;
//        $user->full_name = $full_name;
        $user->email = $email;
//        $user->type = $type;
        $user->setPassword($password);
        $user->generateAuthKey();

        if ($user->save()) {
            echo 'User created!\n';
            return $user;
        }
        else {
            Yii::error($user->getErrors());
            VarDumper::dump($user->getErrors());
            throw new Exception("Cannot create User!");
        }
    }

    /**
     * Add permission.
     * Sample: ./yii be-user/add-permission createUser "Create backend user" "be-user/create" UserManager
     * @param $name
     * @param $description
     * @param $route
     * @param null $parent
     */
    public function actionAddPermission($name, $description, $route, $parent = null) {
        $this->addAuthItem($name, $description, $route, AuthItem::TYPE_PERMISSION, $parent);

    }

    public function actionAddRole($name, $description, $route = null, $parent = null) {
        $this->addAuthItem($name, $description, $route, AuthItem::TYPE_ROLE, $parent);
    }

    /**
     * Assign permission/role to user
     * Sample: ./yii be-user/assign admin createUser
     * @param $username
     * @param $auth_item
     */
    public function actionAssign($username, $auth_item) {
        /* @var $auth DbManager */
        $auth = Yii::$app->authManager;
        $user = User::findByUsername($username);
        if (!$user) {
            echo "User not found!\n";
            return 1;
        }

        $item = $auth->getPermission($auth_item);
        if (!empty($item)) {
            echo "Permission with name `$auth_item` found\n";
        }
        else {
            $item = $auth->getRole($auth_item);
            if (!empty($item)) {
                echo "Role with name `$auth_item` found\n";
            }
            else {
                echo "No auth_item named `$auth_item` found\n";
                return 1;
            }
        }

        if (!$auth->getAssignment($auth_item, $user->id)) {
            $auth->assign($item, $user->id);
            echo "Auth_item `$auth_item` has been assigned to `$username`\n";
        }
        else {
            echo "Assignment existed!\n";
        }
    }

    private function addAuthItem($name, $description, $route, $type, $parent)
    {
        /* @var $auth DbManager */
        $auth = Yii::$app->authManager;

        $item = $auth->getRole($name);
        $newItem = false;
        if (!empty($item)) {
            echo "Role with name `$name` existed, update it...\n";
        }
        else {
            $item = $auth->getPermission($name);
            if (!empty($item)) {
                echo "Permission with name `$name` existed, update it...\n";
            }
            else {
                $newItem = true;
                if ($type == AuthItem::TYPE_ROLE) {
                    $item = $auth->createRole($name);
                }
                else {
                    $item = $auth->createPermission($name);
                }
            }
        }

        if ($route) {
            $item->data = $route;
        }

        $item->description = $description;
        if (!empty($parent)) {
            /* @var $parentItem Item */
            $parentItem = $auth->getRole($parent);
            if (empty($parentItem)) {
                $parentItem = $auth->getPermission($parent);
            }
            if (empty($parentItem)) {
                echo "Parent item not found\n";
                return 1;
            }

            if ($auth->hasChild($parentItem, $item)) {
                echo "Parent-child asm already exited\n";
            }
            else {
                $auth->addChild($parentItem, $item);
            }
        }

        if ($newItem) {
            $auth->add($item);
        }
        return 0;
    }

    public function actionListActions($alias = '@app') {
        $actionAuth = AuthHelper::listActions(@$alias);
        VarDumper::dump($actionAuth);
    }


}
