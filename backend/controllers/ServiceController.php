<?php

namespace backend\controllers;

use backend\models\AdminNoteForm;
use common\auth\filters\Yii2Auth;
use common\components\ActionLogTracking;
use common\components\ServiceCycleFilter;
use common\models\Service;
use common\models\UserActivity;
use common\models\ServiceSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ServiceController implements the CRUD actions for Service model.
 */
class ServiceController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            [
                'class' => ServiceCycleFilter::className(),
                'scope' => Service::SCOPE_ADMIN,
                'model_service' => function ($action, $params) {
                    return Service::findOne($params['id']);
                },
                'only' => ['update', 'delete', 'update-service-category-asm']
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
                'only' => ['approve', 'suspend', 'delete']
            ],
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new ServiceSearch();
        $params = Yii::$app->request->queryParams;
        // $params['ServiceSearch']['site_id'] = $this->sp_user->site_id;
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['is', 'root_service_id', null]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Service model.a
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $active = 1)
    {
        $viewModel = $this->findModel($id);
        $model = ($viewModel->rootService == null) ? $viewModel : $viewModel->rootService;
        $title = $model->display_name;
        $tempService = $model->tempService;
        $sp = $model->site;

        $pro_model = null;
        $video_cat_selected = $live_cat_selected = $music_cat_selected = $new_cat_selected = $clip_cat_selected = $karaoke_cat_selected = $radio_cat_selected ='';
        $temp_model = null;
        $temp_video_cat_selected = $temp_live_cat_selected = $temp_music_cat_selected = $temp_new_cat_selected = $temp_clip_cat_selected = $temp_karaoke_cat_selected = $temp_radio_cat_selected = '';

        if ($model->type == Service::SERVICE_TYPE_PRODUCTION && $model->status >= Service::STATUS_PAUSE) {
            $pro_model = $model;
            $video_cat_selected = $model->getVodCategories();
            $live_cat_selected = $model->getLiveCategories();
            $music_cat_selected = $model->getMusicCategories();
            $new_cat_selected = $model->getNewCategories();
            $clip_cat_selected = $model->getClipCategories();
            $karaoke_cat_selected = $model->getKaraokeCategories();
            $radio_cat_selected = $model->getRadioCategories();
            if($tempService){
                $temp_model = $tempService;
                $temp_video_cat_selected = $tempService->getVodCategories();
                $temp_live_cat_selected = $tempService->getLiveCategories();
                $temp_music_cat_selected = $tempService->getMusicCategories();
                $temp_new_cat_selected = $tempService->getNewCategories();
                $temp_clip_cat_selected = $tempService->getClipCategories();
                $temp_karaoke_cat_selected = $tempService->getKaraokeCategories();
                $temp_radio_cat_selected = $tempService->getRadioCategories();
            }
        }else{
            $temp_model = $model;
            $temp_video_cat_selected = $model->getVodCategories();
            $temp_live_cat_selected = $model->getLiveCategories();
            $temp_music_cat_selected = $model->getMusicCategories();
            $temp_new_cat_selected = $model->getNewCategories();
            $temp_clip_cat_selected = $model->getClipCategories();
            $temp_karaoke_cat_selected = $model->getKaraokeCategories();
            $temp_radio_cat_selected = $model->getRadioCategories();
        }

        return $this->render('view', [
            'title' => $title,
            'active' => $active,
            'sp' => $sp,
            'model' => $pro_model,
            'video_cat_selected' => explode(',', $video_cat_selected),
            'live_cat_selected' => explode(',', $live_cat_selected),
            'music_cat_selected' => explode(',', $music_cat_selected),
            'new_cat_selected' => explode(',', $new_cat_selected),
            'clip_cat_selected' => explode(',', $clip_cat_selected),
            'karaoke_cat_selected' => explode(',', $karaoke_cat_selected),
            'radio_cat_selected' => explode(',', $radio_cat_selected),
            'temp_model' => $temp_model,
            'temp_video_cat_selected' => explode(',', $temp_video_cat_selected),
            'temp_live_cat_selected' => explode(',', $temp_live_cat_selected),
            'temp_music_cat_selected' => explode(',', $temp_music_cat_selected),
            'temp_new_cat_selected' => explode(',', $temp_new_cat_selected),
            'temp_clip_cat_selected' => explode(',', $temp_clip_cat_selected),
            'temp_karaoke_cat_selected' => explode(',', $temp_karaoke_cat_selected),
            'temp_radio_cat_selected' => explode(',', $temp_radio_cat_selected)
        ]);
    }


    /**
     * Updates an existing Service model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        $rootService = ($model->rootService == null) ? $model : $model->rootService;
        $status = Service::STATUS_ACTIVE;
        if ($model->validateServiceCycle($status, Service::SCOPE_ADMIN)) {
            if ($model->rootService) {
                if ($model->mergeRoot()) {
                    Yii::$app->session->setFlash('success',
                        "Cập nhật trang thái gói cước " . $model->display_name . " thành công ! Xin vui lòng đợi Admin kiểm duyệt gói cước này");
                } else {
                    Yii::$app->session->setFlash('error', "Cập nhật thông tin gói cước chạy production bị lỗi!");
                }
            } else {
                $model->status = $status;
                $model->type = Service::SERVICE_TYPE_PRODUCTION;
                $model->root_service_id = null;
                $model->admin_note = "Admin đã duyệt vào ngày " . date('d-m-Y H:i:s');
                $model->update();
                Yii::$app->session->setFlash('success',
                    "Cập nhật trang thái gói cước " . $model->display_name . " thành công ! Xin vui lòng đợi Admin kiểm duyệt gói cước này");
            }
        } else {
            Yii::$app->session->setFlash('error',
                "Hiện tại bạn không được quyền chuyển trạng thái service sang " . Service::$service_status[$model->status]);
        }
        return $this->redirect(['view', 'id' => $rootService->id]);
    }

    public function actionSuspend($id)
    {
        $model = $this->findModel($id);
        $rootService = ($model->rootService == null) ? $model : $model->rootService;
        $status = Service::STATUS_TEMP;
        $note = new AdminNoteForm();
        if ($note->load(Yii::$app->request->post()) && $model->validateServiceCycle($status, Service::SCOPE_ADMIN)) {
            $model->admin_note = "Admin đã loại bỏ vào ngày " . date('d-m-Y H:i:s') . ": " . $note->admin_note;
            if ($model->rootService || $model->tempService == null) {
                /**
                 * Service la ban nhap
                 */
                $model->status = $status;
                $model->type = Service::SERVICE_TYPE_TEMP;
                $model->update();
                Yii::$app->session->setFlash('success',
                    "Gói cước " . $model->display_name . " đã bị dừng và chuyển sang trạng thái Nháp của SP! Thông báo cho SP để cập nhật gói cước này");
            } else {
                /**
                 * Service la root
                 */
                if ($model->mergeTemp()) {
                    Yii::$app->session->setFlash('success',
                        "Gói cước " . $model->display_name . " đã bị dừng và chuyển sang trạng thái Nháp! Thông báo cho SP để cập nhật gói cước này");
                } else {
                    Yii::$app->session->setFlash('error', "Cập nhật thông tin gói cước chạy production bị lỗi!");
                }
            }
        } else {
            Yii::$app->session->setFlash('error',
                "Hiện tại bạn không được quyền chuyển trạng thái service sang " . Service::$service_status[$model->status]);
        }
        return $this->redirect(['view', 'id' => $rootService->id]);
    }

    /**
     * Deletes an existing Service model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Service::STATUS_REMOVE;
        $model->update();
        return $this->redirect(['index']);
    }

    public function actionUpdateNote($id)
    {
        $model = $this->findModel($id);
        $note = new AdminNoteForm();
        if ($note->load(Yii::$app->request->post()) && $model->validate()) {
            $model->admin_note = "Admin cập nhật vào ngày " . date('d-m-Y H:i:s') . ": " .$note->admin_note;
            if ($model->update()) {
                Yii::$app->session->setFlash('success',
                    "Cập nhật Ghi Chú cho gói cước " . $model->display_name . " thành công !");
            } else {
                Yii::$app->session->setFlash('error', "Lỗi hệ thống, cập nhật Ghi Chú thất bại!");
            }
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the Service model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Service the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Service::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
