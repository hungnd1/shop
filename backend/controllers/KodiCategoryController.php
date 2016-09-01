<?php

namespace backend\controllers;

use common\components\ActionLogTracking;
use common\models\KodiCategory;
use common\models\KodiCategoryItemAsm;
use common\models\UserActivity;
use kartik\form\ActiveForm;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * KodiCategoryController implements the CRUD actions for KodiCategory model.
 */
class KodiCategoryController extends BaseBEController
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
     * Lists all KodiCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $categories = KodiCategory::getAllCategories(true, Yii::$app->user->id);
        // foreach ($categories as $k => $cat) {
        //     Category::updateAll(['order_number' => $k+1], 'id = '.$cat->id);
        // }
        // die;
        $dataProvider = new ArrayDataProvider([
            'key' => 'id',
            'allModels' => $categories,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KodiCategory model.
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
     * Creates a new KodiCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KodiCategory();
        $model->setScenario('admin_create_update');
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $type = Yii::$app->request->get('type', null);

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
            if ($model->save()) {
                $model->created_at = time();
                $model->updated_at = time();
                $model->save();

                Yii::info($model->getErrors());

                \Yii::$app->getSession()->setFlash('success', 'Thêm mới thành công');

                return $this->redirect(['index', 'type' => $type]);
            } else {
                // Yii::info($model->getErrors());
                // Yii::$app->getSession()->setFlash('error', 'Lỗi lưu danh mục');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing KodiCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $file_name_image = $model->image;
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
            if ($model->save()) {

                $model->updated_at = time();
                $model->save();
                \Yii::$app->getSession()->setFlash('success', 'Cập nhật thành công');

                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing KodiCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(KodiCategoryItemAsm::DeleteAsm(null,$id)){
            $model->delete();
            \Yii::$app->getSession()->setFlash('success', 'Xóa thành công');
            return $this->redirect(['index']);
        }
        \Yii::$app->getSession()->setFlash('success', 'Xóa thất bại');
        return $this->redirect(['index']);
    }

    /**
     * Finds the KodiCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return KodiCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = KodiCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
