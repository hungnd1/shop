<?php

namespace backend\controllers;

use common\auth\filters\Yii2Auth;
use common\components\ActionLogTracking;
use common\components\ActionSPFilter;
use common\components\SPOwnerFilter;
use common\models\Content;
use common\models\ServiceProvider;
use common\models\Slide;
use common\models\SlideSearch;
use common\models\UserActivity;
use kartik\form\ActiveForm;
use sp\controllers\BaseSPController;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * SlideController implements the CRUD actions for Slide model.
 */
class SlideController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
//            [
//                'class' => ActionSPFilter::className(),
//            ],
//            [
//                'class' => SPOwnerFilter::className(),
//                'user' => $this->sp_user,
//                'model_relation_sp' => function ($action, $params) {
//                    return $model = Slide::findOne($params['id']);
//                },
//                'only' => ['update', 'delete', 'view']
//            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class' => ActionLogTracking::className(),
                'user' => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_OTHER,
                'post_action' => [
                    ['action' => 'create', 'accept_ajax' => false],
                    ['action' => 'update', 'accept_ajax' => false],
                    ['action' => 'delete', 'accept_ajax' => false],
                ],
                'only' => ['create', 'update', 'delete']
            ],
        ]);
    }

    /**
     * Lists all Slide models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SlideSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Slide model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Slide model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Slide();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }


        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $model->saveBannerFile();
                \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Thêm mới Slide thành công!'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::error($model->getErrors());
                \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Thêm mới Slide không thành công!'));
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Slide model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->type == Slide::SLIDE_TYPE_BANNER) {
                $model->file_banner = UploadedFile::getInstance($model, 'file_banner');
            }
            if ($model->update()) {
                $model->saveBannerFile();
                \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Cập nhật Slide thành công!'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::error($model->getErrors());
                \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Cập nhật Slide không thành công!'));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    public function actionGetContent() {
        $out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $sp_id = $parents[0];
                $contents =  Content::find()->where(['service_provider_id' => $sp_id, 'status' => Content::STATUS_ACTIVE])->all();
                foreach($contents as $content){
                    $out[] = ['id' => $content->id, 'name' => $content->display_name.' - ('.Content::$list_type[$content->type].')'];
                }
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }


    /**
     * Deletes an existing Slide model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Xóa Slide thành công!'));
        } else {
            \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Xóa Slide không thành công!'));
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Slide model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Slide the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Slide::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Slide không tồn tại');
        }
    }
}
