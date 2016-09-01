<?php

namespace backend\controllers;

use common\models\CategoryAddonAsm;
use common\models\ItemKodi;
use common\models\KodiCategory;
use common\models\KodiCategoryItemAsm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\form\ActiveForm;
use yii\web\Response;

/**
 * KodiAddonController implements the CRUD actions for KodiCategory model.
 */
class KodiAddonController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all KodiCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => KodiCategory::find()->andWhere('parent is not null'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KodiAddon model.
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
        if ($model->load(Yii::$app->request->post())) {
            $model->image = 'khong co anh';
            $model->status = KodiCategory::STATUS_ACTIVE;
            $model->parent = $model->list_cat_id.',';
            $model->created_at = time();
            $model->updated_at = time();
            if ($model->save() ) {
                Yii::info($model->getErrors());

                \Yii::$app->getSession()->setFlash('success', 'Thêm mới thành công');

                return $this->redirect(['index']);
            } else {
                // Yii::info($model->getErrors());
                // Yii::$app->getSession()->setFlash('error', 'Lỗi lưu danh mục');
            }
        }
        return $this->render('create', [
            'model' => $model,
//                'selectedCats' => $selectedCats,
            'site_id' => Yii::$app->user->id,
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
        $model->setScenario('admin_create_update');
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::info($model->getErrors());
                $model->parent = $model->list_cat_id.',';
                $model->updated_at = time();
                $model->save();
                \Yii::$app->getSession()->setFlash('success', 'Cập nhật thành công');

                return $this->redirect(['index']);
            } else {
                // Yii::info($model->getErrors());
                // Yii::$app->getSession()->setFlash('error', 'Lỗi lưu danh mục');
            }
        }


        $model->list_cat_id = $model->getAllCategoryId();
        $selectedCats = explode(',', $model->list_cat_id);
        return $this->render('update', [
            'model' => $model,
            'selectedCats' => $selectedCats,
            'site_id' => Yii::$app->user->id,
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
        $model_category = $this->findModel($id);
        if(KodiCategoryItemAsm::DeleteAsm(null,$id)){
            $model_category->delete();
            \Yii::$app->getSession()->setFlash('success', 'Xóa thành công');
            return $this->redirect(['index']);
        }
        \Yii::$app->getSession()->setFlash('success', 'Xóa thất bại');
        return $this->redirect(['index']);
    }

    /**
     * Finds the KodiAddon model based on its primary key value.
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
