<?php

namespace backend\controllers;

use common\components\ActionLogTracking;
use common\models\ItemKodiSearch;
use common\models\KodiCategoryItemAsm;
use common\models\UserActivity;
use Yii;
use common\models\ItemKodi;
use yii\data\ActiveDataProvider;
use kartik\form\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ItemKodiController implements the CRUD actions for ItemKodi model.
 */
class ItemKodiController extends BaseBEController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class' => ActionLogTracking::className(),
                'user' => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_PRICING,
            ],
        ]);
    }

    /**
     * Lists all ItemKodi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ItemKodi::find()->orderBy(['created_at'=>SORT_DESC]),
        ]);
        $searchModel = new ItemKodiSearch();
        $params = Yii::$app->request->queryParams;
        $selectedCats = isset($params['ContentSearch']['categoryIds']) ? explode(',', $params['ContentSearch']['categoryIds']) : [];
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single ItemKodi model.
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
     * Creates a new ItemKodi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ItemKodi();

        $model->setScenario('admin_create_update');
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $image = UploadedFile::getInstance($model, 'image');
            if ($image) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                $tmp = Yii::getAlias('@backend') . '/web/' . Yii::getAlias('@cat_image') . '/';
                if (!file_exists($tmp)) {
                    mkdir($tmp, 0777, true);
                }
                if ($image->saveAs($tmp . $file_name)) {
                    $model->image = $file_name;
                }
            }

            $file_download  = UploadedFile::getInstance($model, 'file_download');
            if ($file_download) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $file_download->extension;
                $tmp = Yii::getAlias('@backend') . '/web/' . Yii::getAlias('@file_downloads') . '/';
                if (!file_exists($tmp)) {
                    mkdir($tmp, 0777, true);
                }
                if ($file_download->saveAs($tmp . $file_name)) {
                    $model->file_download = $file_name;
                }
            }
            if ($model->save()) {
                $model->created_at = time();
                $model->updated_at = time();
                $model->createCategoryAsm();
                $model->save();

                Yii::info($model->getErrors());

                \Yii::$app->getSession()->setFlash('success', 'Thêm mới thành công');

                return $this->redirect(['index']);
            } else {
                // Yii::info($model->getErrors());
                // Yii::$app->getSession()->setFlash('error', 'Lỗi lưu danh mục');
            }
        }



//            $model->list_cat_id = $model->getAllCategoryId();
//            $selectedCats = explode(',', $model->list_cat_id);
            return $this->render('create', [
                'model' => $model,
//                'selectedCats' => $selectedCats,
                'site_id' => Yii::$app->user->id,
            ]);
        }

    /**
     * Updates an existing ItemKodi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $file_name_image = $model->image;
        $file_name_download = $model->file_download;
        $model->setScenario('admin_create_update');
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $image = UploadedFile::getInstance($model, 'image');
            if ($image) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                $tmp = Yii::getAlias('@backend') . '/web/' . Yii::getAlias('@cat_image') . '/';
                if (!file_exists($tmp)) {
                    mkdir($tmp, 0777, true);
                }
                if ($image->saveAs($tmp . $file_name)) {
                    $model->image = $file_name;
                }
            }else{
                $model->image = $file_name_image;
            }

            $file_download  = UploadedFile::getInstance($model, 'file_download');
            if ($file_download) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $file_download->extension;
                $tmp = Yii::getAlias('@backend') . '/web/' . Yii::getAlias('@file_downloads') . '/';
                if (!file_exists($tmp)) {
                    mkdir($tmp, 0777, true);
                }
                if ($file_download->saveAs($tmp . $file_name)) {
                    $model->file_download = $file_name;
                }
            }else{
                $model->file_download = $file_name_download;
            }
            if ($model->save()) {
                $model->createCategoryAsm();

                Yii::info($model->getErrors());

                $model->updated_at = time();
                $model->save();
                \Yii::$app->getSession()->setFlash('success', 'Cập nhật thành công');

                return $this->redirect(['index']);
            } else {
                // Yii::info($model->getErrors());
                // Yii::$app->getSession()->setFlash('error', 'Lỗi lưu danh mục');
            }
        }



        $model->list_cat_id = $model->getAllCategoryId($model->id);
            $selectedCats = explode(',', $model->list_cat_id);
        return $this->render('update', [
            'model' => $model,
            'selectedCats' => $selectedCats,
            'site_id' => Yii::$app->user->id,
        ]);
    }

    /**
     * Deletes an existing ItemKodi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(KodiCategoryItemAsm::DeleteAsm($id,null)){
            $model->delete();
            \Yii::$app->getSession()->setFlash('success', 'Xóa thành công');
            return $this->redirect(['index']);
        }
        \Yii::$app->getSession()->setFlash('success', 'Xóa thất bại');
        return $this->redirect(['index']);
    }

    /**
     * Finds the ItemKodi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ItemKodi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemKodi::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
