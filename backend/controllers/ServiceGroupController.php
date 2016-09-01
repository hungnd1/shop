<?php

namespace backend\controllers;

use common\components\ActionLogTracking;
use common\components\ServiceCycleFilter;
use common\models\Service;
use common\models\ServiceGroup;
use common\models\ServiceGroupAsm;
use common\models\ServiceGroupSearch;
use common\models\UserActivity;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * ServiceGroupController implements the CRUD actions for ServiceGroup model.
 */
class ServiceGroupController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => ServiceCycleFilter::className(),
                'scope' => Service::SCOPE_ADMIN,
                'model_service' => function ($action, $params) {
                    return Service::findOne($params['id']);
                },
                'only' => ['update', 'delete', 'update-service-category-asm'],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class' => ActionLogTracking::className(),
                'user' => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_SERVICE,
                'post_action' => [
                    ['action' => 'delete', 'accept_ajax' => true],
                    ['action' => 'suspend', 'accept_ajax' => true],
                ],
                'only' => ['approve', 'suspend', 'delete'],
            ],
        ]);
    }

    /**
     * Lists all ServiceGroup models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServiceGroupSearch();

        $postData = Yii::$app->request->get();

        if (!empty($postData)) {
            $searchModel->search($postData);
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 20;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ServiceGroup model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id, $active = 1)
    {
        $model = $this->findModel($id);
        $serviceProvider = $model->getServiceProvider();
        // var_dump($serviceProvider);die;
        return $this->render('view', [
            'model' => $model,
            'serviceProvider' => $serviceProvider,
            'active' => $active,
        ]);
    }

    /**
     * Creates a new ServiceGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ServiceGroup();
        $model->site_id = $this->sp_user->site_id;
        if ($model->load(Yii::$app->request->post())) {
            $image = UploadedFile::getInstance($model, 'icon');
            Yii::info($image);
            if ($image) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                if ($image->saveAs(Yii::getAlias('@webroot') . '/' . Yii::getAlias('@service_group_icon') . '/' . $file_name)) {
                    $model->icon = $file_name;
                }
            }
            if ($model->save()) {
                $model->createServiceGroupAsm();

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ServiceGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old_img = $model->icon;
        if ($model->load(Yii::$app->request->post())) {
            $image = UploadedFile::getInstance($model, 'icon');

            if ($image && isset($image->extension)) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                if ($image->saveAs(Yii::getAlias('@webroot') . '/' . Yii::getAlias('@service_group_icon') . '/' . $file_name)) {
                    $model->icon = $file_name;
                }
            } else {
                $model->icon = $old_img;
            }
            if ($model->save()) {
                $model->createServiceGroupAsm();

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        $model->list_service_id = ArrayHelper::getColumn(ServiceGroupAsm::findAll(['service_group_id' => $model->id]), 'service_id');

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ServiceGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ServiceGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return ServiceGroup the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ServiceGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
