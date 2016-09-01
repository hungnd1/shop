<?php

namespace backend\controllers;

use common\auth\filters\Yii2Auth;
use common\components\ActionLogTracking;
use common\models\UserActivity;
use kartik\widgets\ActiveForm;
use Yii;
use common\models\PriceLevel;
use common\models\PriceLevelSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PriceLevelController implements the CRUD actions for PriceLevel model.
 */
class PriceLevelController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
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
                'post_action' => [
                    ['action' => 'create', 'accept_ajax' => false],
                    ['action' => 'delete', 'accept_ajax' => false],
                    ['action' => 'packages', 'accept_ajax' => true],
                ],
                'only' => ['create', 'delete', 'packages']
            ],
        ]);
    }

    /**
     * Lists all PriceLevel models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['hasEditable'])) {
            // read your posted model attributes
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                // read or convert your posted information
                $model = $this->findModel($post['editableKey']);
                $index = $post['editableIndex'];
                if($model){
                    $model->load($post['PriceLevel'][$index], '');
                    if($model->update()){
                        echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'']);
                    }else{
                        echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'Dữ liệu không hợp lệ']);
                    }
                }else{
                    echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'Dữ liêuj không tồn tại']);
                }

            }
            // else if nothing to do always return an empty JSON encoded output
            else {
                echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'']);
            }
            return;
        }

        $searchModel = new PriceLevelSearch();
        $params = Yii::$app->request->queryParams;
        $params['PriceLevelSearch']['type'] = PriceLevel::TYPE_CONTENT;
        $dataProvider = $searchModel->search($params);

        $newPriceLevel = new PriceLevel();
        $newPriceLevel->type = PriceLevel::TYPE_CONTENT;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'newPriceLevel' => $newPriceLevel
        ]);
    }

    /**
     * Lists all PriceLevel models.
     * @return mixed
     */
    public function actionPackages()
    {
        if (isset($_POST['hasEditable'])) {
            // read your posted model attributes
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                // read or convert your posted information
                $model = $this->findModel($post['editableKey']);
                $index = $post['editableIndex'];
                if($model){
                    $model->load($post['PriceLevel'][$index], '');
                    if($model->update()){
                        echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'']);
                    }else{
                        echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'Dữ liệu không hợp lệ']);
                    }
                }else{
                    echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'Dữ liêuj không tồn tại']);
                }

            }
            // else if nothing to do always return an empty JSON encoded output
            else {
                echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'']);
            }
            return;
        }

        $searchModel = new PriceLevelSearch();
        $params = Yii::$app->request->queryParams;
        $params['PriceLevelSearch']['type'] = PriceLevel::TYPE_SERVICE;
        $dataProvider = $searchModel->search($params);

        $newPriceLevel = new PriceLevel();
        $newPriceLevel->type = PriceLevel::TYPE_SERVICE;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'newPriceLevel' => $newPriceLevel
        ]);
    }

    /**
     * Creates a new PriceLevel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PriceLevel();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Tạo thêm mức giá mới thành công');
            if($model->type == PriceLevel::TYPE_CONTENT){
                return $this->redirect(['index']);
            }
            return $this->redirect(['packages']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PriceLevel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('success', 'Xóa mức giá '.$model->price.' thành công!!');
        if($model->type == PriceLevel::TYPE_CONTENT){
            return $this->redirect(['index']);
        }
        return $this->redirect(['packages']);
    }

    /**
     * Finds the PriceLevel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PriceLevel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PriceLevel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
