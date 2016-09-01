<?php

namespace backend\controllers;

use api\helpers\Message;
use common\components\ActionLogTracking;
use common\components\ActionProtectSuperAdmin;
use common\models\UserActivity;
use kartik\widgets\ActiveForm;
use Yii;
use common\models\User;
use common\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\AuthAssignment;
use yii\web\Response;
use common\auth\filters\Yii2Auth;
use common\models\AuthItem;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            [
                'class' => ActionProtectSuperAdmin::className(),
                'user' => Yii::$app->user,
                'update_user' => function ($action, $params) {
                    return $model = User::findOne($params['id']);
                },
                'only' => ['update', 'delete', 'view']
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post','get'],
                ],
            ],
            [
                'class' => ActionLogTracking::className(),
                'user' => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_USER,
                'post_action' => [
                    ['action' => 'create', 'accept_ajax' => false],
                    ['action' => 'delete', 'accept_ajax' => false],
                    ['action' => 'update', 'accept_ajax' => false],
                    ['action' => 'change-password', 'accept_ajax' => false],
                    ['action' => 'add-auth-item', 'accept_ajax' => true],
                    ['action' => 'revoke-auth-item', 'accept_ajax' => true]
                ],
                'only' => ['create', 'delete', 'update', 'change-password', 'add-auth-item', 'revoke-auth-item']
            ],
        ]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $params = Yii::$app->request->queryParams;
        $params['UserSearch']['type'] = User::USER_TYPE_ADMIN;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $active = 1)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'active' => $active
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $close int
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->setScenario('create');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) ) {
            $model->status = User::STATUS_ACTIVE;
            $model->setPassword($model->password);
            $model->generateAuthKey();
            if(!$model->save()){
                Yii::$app->session->setFlash('error', Message::MSG_FAIL);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            Yii::$app->session->setFlash('success', Message::MSG_ADD_SUCCESS);
            return $this->redirect(['index']);

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        /** get giá trị username */
        $username = $model->username;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) ) {
            /** Set lại username */
            $model->username = $username;
            if($model->update()){
                Yii::$app->session->setFlash('success', Message::MSG_UPDATE_PROFILE);
                return $this->redirect(['view', 'id' => $model->id]);
            }
            Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
        }

        return $this->render('update', [
                'model' => $model,
            ]);
    }

    public function actionInfo(){
        $user = Yii::$app->user->identity;
        return $this->render('info', ['model' => $user]);
    }

    public function actionUpdateOwner(){

        /**
         * @var $model User
         */
        $model = Yii::$app->user->identity;
//        $model->setScenario('change-password');
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Message::MSG_UPDATE_PROFILE);
            return $this->redirect(['info']);
        } else {
            Yii::$app->session->setFlash('error', Message::MSG_FAIL);
            return $this->redirect(['info']);
        }
    }

    /**
     * @param $id
     * @return array|Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionResetPassword($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('reset-password');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) ) {
            $model->password = $model->new_password;
            $model->setPassword($model->password);
            $model->generateAuthKey();
            if($model->update()){
                Yii::$app->session->setFlash('success', 'Khôi phục mật khẩu thành công');
                return $this->redirect(['view', 'id' => $model->id]);
            }
            Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);

//            if($model->validatePassword($model->old_password)){
//                $model->password = $model->new_password;
//                $model->setPassword($model->password);
//                $model->generateAuthKey();
//                $model->old_password = $model->new_password;
//                if($model->update()){
//                    Yii::$app->session->setFlash('success', 'Đổi mật khẩu thành công');
//                    return $this->redirect(['view', 'id' => $model->id]);
//                }
//                Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
//            }else{
//                Yii::$app->getSession()->setFlash('error', 'Mật khẩu cũ không đúng');
//            }

        }
//        Yii::info($model->getErrors());

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionChangePassword($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('change-password');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) ) {
            if($model->validatePassword($model->old_password)){
                $model->password = $model->new_password;
                $model->setPassword($model->password);
                $model->generateAuthKey();
                $model->old_password = $model->new_password;
                if($model->update()){
                    Yii::$app->session->setFlash('success', 'Đổi mật khẩu thành công');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
            }else{
                Yii::$app->getSession()->setFlash('error', 'Mật khẩu cũ không đúng');
            }

        }
        Yii::info($model->getErrors());

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionOwnerChangePassword()
    {
        /**
         * @var $model User
         */
        $model = Yii::$app->user->identity;
        $model->setScenario('change-password');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            $model->setPassword($model->new_password);
//            $model->generateAuthKey();
//            $model->old_password = $model->new_password;
//            if($model->update()){
//                Yii::$app->session->setFlash('success', 'Thay đổi mật khẩu user "'.$model->username.'" thành công!');
//                return $this->redirect(['info']);
//            }else{
//                Yii::error($model->getErrors());
//            }
//        } else {
//            Yii::$app->session->setFlash('error', 'Thay đổi mật khẩu user "'.$model->username.'" không thành công!');
//            return $this->redirect(['info']);
//        }
        if ($model->load(Yii::$app->request->post()) ) {
            if($model->validatePassword($model->old_password)){
                $model->setPassword($model->new_password);
                $model->generateAuthKey();
                $model->old_password = $model->new_password;
                if($model->update()){
                    Yii::$app->session->setFlash('success', 'Đổi mật khẩu thành công');
                    return $this->redirect(['info']);
                }
                Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
            }else{
                Yii::$app->getSession()->setFlash('error', 'Mật khẩu cũ không đúng');
            }

        }
        return $this->redirect(['info']);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);

        $model=$this->findModel($id);
        if($model->id == Yii::$app->user->getId()){
            Yii::$app->session->setFlash('error', 'Bạn không thể thực hiện chức năng này!');
            return $this->redirect(['index']);
        }
        $model->status=User::STATUS_DELETED;
        if($model->save()){
            Yii::$app->session->setFlash('success', 'Xóa thành công');
            return $this->redirect(['index']);
        }
        var_dump($model->getFirstErrors());exit;
        Yii::$app->session->setFlash('error', Message::MSG_FAIL);
        return $this->redirect(['index']);

    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actionRevokeAuthItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $success = false;
        $message = "Tham số không đúng";

        if (isset($post['user']) && isset($post['item'])) {
            $user = $post['user'];
            $item = $post['item'];

            $mapping = AuthAssignment::find()->andWhere(['user_id' => $user, 'item_name' => $item])->one();
            if ($mapping) {
                if ($mapping->delete()) {
                    $success = true;
                    $message = "Đã xóa quyền '$item' khỏi user '$user'!";
                } else {
                    $message = "Lỗi hệ thống, vui lòng thử lại sau";
                }
            } else {
                $message = "Quyền '$item' chưa được cấp cho user '$user'!";
            }

        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    /**
     * add items to user
     * @param  $id - id of user
     * @return mixed
     */
    public function actionAddAuthItem($id)
    {
        /* @var $model User */
        $model = User::findOne(['id' => $id]);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = false;
        $message = "User/nhóm quyền không tồn tại'";

        if ($model) {
            $post = Yii::$app->request->post();

            if (isset($post['addItems'])) {
                $items = $post['addItems'];

                $count = 0;

                foreach ($items as $item) {
                    $role = AuthItem::findOne(['name' => $item]);
                    $mapping = new AuthAssignment();
                    $mapping->item_name = $item;
                    $mapping->user_id = $id;
                    if ($mapping->save()) {
                        $count ++;
                    }
                }


                if ($count >0) {
                    $success = true;
                    $message = "Đã thêm $count nhóm quyền cho người dùng '$model->username'";

                }
            }
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

}
