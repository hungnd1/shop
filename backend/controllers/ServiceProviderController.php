<?php

namespace backend\controllers;

use backend\models\ServiceProviderForm;
use common\components\ActionLogTracking;
use common\models\Content;
use common\models\Service;
use common\models\ServiceProvider;
use common\models\ServiceSearch;
use common\models\Site;
use common\models\SiteSearch;
use common\models\SiteStreamingServerAsm;
use common\models\User;
use common\models\UserActivity;
use kartik\widgets\ActiveForm;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UrlManager;

/**
 * ServiceProviderController implements the CRUD actions for ServiceProvider model.
 */
class ServiceProviderController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class'              => ActionLogTracking::className(),
                'user'               => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_SERVICE_PROVIDER,
                'model_types'        => [
                    'update-user'     => UserActivity::ACTION_TARGET_TYPE_USER,
                    'change-password' => UserActivity::ACTION_TARGET_TYPE_USER,
                ],
                'post_action'        => [
                    ['action' => 'create', 'accept_ajax' => false],
                    ['action' => 'update', 'accept_ajax' => false],
                    ['action' => 'delete', 'accept_ajax' => false],
                    ['action' => 'mo-update-status', 'accept_ajax' => true],
                    ['action' => 'update-user', 'accept_ajax' => false],
                    ['action' => 'change-password', 'accept_ajax' => false],
                ],
                'only'               => ['create', 'update', 'delete', 'update-user', 'change-password'],
            ],
        ]);
    }

    /**
     * Lists all ServiceProvider models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model       = new Site();
        $searchModel = new SiteSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'model'        => $model,
        ]);
    }

    /**
     * Displays a single ServiceProvider model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $active = 1)
    {
        $model = $this->findModel($id);

        $serviceSearchModel                 = new ServiceSearch();
        $params                             = Yii::$app->request->queryParams;
        $params['ServiceSearch']['site_id'] = $model->id;
        $serviceDataProvider                = $serviceSearchModel->search($params);
        $serviceDataProvider->query->andFilterWhere(['in', 'status', [Service::STATUS_ACTIVE, Service::STATUS_PENDING, Service::STATUS_PAUSE]]);
        $serviceDataProvider->query->andWhere(['is', 'root_service_id', null]);

        $sp_admin = $model->userAdmin;

        return $this->render('view', [
            'model'               => $model,
            'active'              => $active,
            'user_admin'          => $sp_admin,
            'serviceSearchModel'  => $serviceSearchModel,
            'serviceDataProvider' => $serviceDataProvider,
        ]);
    }

    /**
     * Creates a new ServiceProvider model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ServiceProviderForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sp = $model->saveRecord();
            if($sp) {
                Yii::$app->session->setFlash('success', 'Tạo nhà cung cấp dịch vụ thành công!');
                return $this->redirect(['view', 'id' => $sp->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ServiceProvider model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Cập nhật nhà cung cấp dịch vụ thành công!');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        if (isset($_POST['hasEditable'])) {
            // read your posted model attributes
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                // read or convert your posted information
                $service = Service::findOne($post['editableKey']);
                $index   = $post['editableIndex'];
                if ($service || $model->id != $service->site_id) {
                    $service->load($post['Service'][$index], '');
                    if ($service->update()) {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Dữ liệu không hợp lệ']);
                    }
                } else {
                    echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Dữ liêuj không tồn tại']);
                }

            } // else if nothing to do always return an empty JSON encoded output
            else {
                echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
            }
            return;
        }
    }

    /**
     * Deletes an existing ServiceProvider model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model         = $this->findModel($id);
        $model->status = Site::STATUS_REMOVE;
        if (!$model->update()) {
            Yii::error($model->getErrors());
            Yii::$app->session->setFlash('error', 'Xóa thất bại');
            return $this->redirect(['index']);
        }
        $user = $this->findUserModel($model->user_admin_id);
        if ($user) {
            $user->status = User::STATUS_DELETED;
            if (!$user->save()) {
                Yii::error($user->getErrors());
                Yii::$app->session->setFlash('error', 'Xóa thất bại.');
                return $this->redirect(['index']);
            }
        }
        Yii::$app->session->setFlash('success', 'Xóa thành công.');
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateUser($id, $active = 1)
    {
        $model = $this->findUserModel($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Cập nhật User thành công!');
            return $this->redirect(['view', 'id' => $model->site_id, 'active' => $active]);
        } else {
            return $this->render('update_user', [
                'model' => $model,
            ]);
        }
    }

    public function actionChangePassword($id, $active = 1)
    {
        $model = $this->findUserModel($id);
        $model->setScenario('reset-password');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->new_password);
            $model->generateAuthKey();
            $model->old_password = $model->new_password;
            if ($model->update()) {
                Yii::$app->session->setFlash('success', 'Thay đổi mật khẩu "' . $model->username . '" thành công!');
                return $this->redirect(['view', 'id' => $model->site_id, 'active' => $active]);
            } else {
                Yii::error($model->getErrors());
            }
        } else {
            Yii::info($model->getErrors());
            return $this->goHome();
        }
    }

    public function actionViewUser($id, $active = 1)
    {
        return $this->render('view_user', [
            'model' => $this->findUserModel($id),
        ]);
    }

    public function actionLoginAsSp($id)
    {
        $sp = $this->findModel($id);
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        if (!$user->haveAccessSP()) {
            throw new ForbiddenHttpException('Tài khoản này không có quyền login sang bên SP');
        }

        $sp_domain = (Yii::$app->params['sp_domain']) ? Yii::$app->params['sp_domain'] : '';
        if (empty($sp_domain)) {
            throw new NotFoundHttpException('SP Domain does not exist.');
        }
        /**
         * Create authen_key to login as
         */
        $user->generateAccessLoginToken();
        $user->site_id = $sp->id;
        $user->update();
        Yii::$app->urlManager->setScriptUrl($sp_domain);
        $url_redirect = Url::toRoute(['site/login-as', 'key' => $user->access_login_token], true);
        return $this->redirect($url_redirect);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteUser($id, $active = 1)
    {
//        $this->findModel($id)->delete();
        //
        //        return $this->redirect(['index']);

        $model = $this->findUserModel($id);
        if ($model->id == Yii::$app->user->getId() || $model->type == User::USER_TYPE_ADMIN) {
            Yii::$app->session->setFlash('error', 'Bạn không thể thực hiện chức năng này!');
            return $this->redirect(['view', 'id' => $model->site_id, 'active' => $active]);
        }
        $model->status = User::STATUS_DELETED;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Xóa User thành công!');
            return $this->redirect(['view', 'id' => $model->site_id, 'active' => 1]);
        }

    }

    /**
     * Finds the ServiceProvider model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Site the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Site::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findUserModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTransfer($id)
    {
        $streaming_servers = SiteStreamingServerAsm::findAll(['site_id' => $id]);
        foreach ($streaming_servers as $streaming_server) {
            Content::syncDataToSite($id, $streaming_server);
        }

        return $this->redirect(['index']);
    }
}
