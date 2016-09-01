<?php

namespace backend\controllers;

use api\helpers\Message;
use common\helpers\CUtils;
use Yii;
use common\models\ActorDirector;
use common\models\ActorDirectorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * ActorDirectorController implements the CRUD actions for ActorDirector model.
 */
class ActorDirectorController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post','get'],
                ],
            ],
        ];
    }

    /**
     * Lists all ActorDirector models.
     * @return mixed
     */
    public function actionIndex()
    {
        $content_type = Yii::$app->request->get('content_type', ActorDirector::TYPE_VIDEO);
        $searchModel = new ActorDirectorSearch();
        $searchModel->content_type = $content_type;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'content_type' => $content_type,
        ]);
    }

    /**
     * Displays a single ActorDirector model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $content_type = Yii::$app->request->get('content_type', ActorDirector::TYPE_VIDEO);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'content_type' => $content_type,
        ]);
    }

    /**
     * Creates a new ActorDirector model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $content_type = Yii::$app->request->get('content_type', ActorDirector::TYPE_VIDEO);
        $model = new ActorDirector();
        $model->setScenario('create');
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->get())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'image');
            // save thumbnail to document_thumbnails
            if ($file) {
                $file_name = uniqid().time().'.'.$file->extension;
                if($file->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@content_images') . "/" . $file_name)){
                    $model->status = ActorDirector::STATUS_ACTIVE;
                    $model->content_type = $content_type;
                    $model->image=$file_name;
                    if($model->save()){
                        Yii::$app->getSession()->setFlash('success', Yii::t('app', Message::MSG_ADD_SUCCESS));
                        return $this->redirect(['index','content_type' => $content_type,]);
                    }else{
                        Yii::$app->getSession()->setFlash('error', Yii::t('app', Message::MSG_FAIL));
                    }
                }else{
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', Message::MSG_FAIL));
                }
            }else{
                Yii::$app->getSession()->setFlash('error', Yii::t('app', CUtils::replaceParam(Message::MSG_NOT_EMPTY, ['Ảnh đại diện'])));
            }
        }

        return $this->render('create', [
            'model' => $model,
            'content_type' => $content_type,
        ]);
    }

    /**
     * Updates an existing ActorDirector model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $content_type = Yii::$app->request->get('content_type', ActorDirector::TYPE_VIDEO);
        $model = $this->findModel($id);
//        $model->setScenario('update');
        $image = $model->image;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->get())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'image');
            // save thumbnail to document_thumbnails
            if ($file) {
                $file_name = uniqid().time().'.'.$file->extension;
                if($file->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@content_images') . "/" . $file_name)){
                    $model->content_type = $content_type;
                    $model->image=$file_name;
                    if($model->save()){
                        Yii::$app->getSession()->setFlash('success', Yii::t('app', Message::MSG_UPDATE_SUCCESS));
                        return $this->redirect(['index', 'content_type' => $content_type,]);
                    }
                    else{
                        Yii::$app->getSession()->setFlash('error', Yii::t('app', Message::MSG_FAIL));
                    }
                }
                else{
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', Message::MSG_FAIL));
                }
            }else{
                $model->image = $image;
                if($model->save()){
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', Message::MSG_UPDATE_SUCCESS));
                    return $this->redirect(['index', 'content_type' => $content_type,]);
                }
                Yii::$app->getSession()->setFlash('error',  Yii::t('app', Message::MSG_FAIL));
            }

        }
        return $this->render('update', [
            'model' => $model,
            'content_type' => $content_type,
        ]);
    }

    /**
     * Deletes an existing ActorDirector model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $content_type = Yii::$app->request->get('content_type', ActorDirector::TYPE_VIDEO);
        $model = $this->findModel($id);
        $adString = $content_type== ActorDirector::TYPE_VIDEO?"Diễn viên/Đạo diễn":"Ca sĩ/Nhạc sĩ";
        $cString = $content_type== ActorDirector::TYPE_VIDEO?"Phim":"Karaoke";
        $contentActorDirectorAsms = $model->contentActorDirectorAsms;
        if(count($contentActorDirectorAsms) >0){
            Yii::$app->getSession()->setFlash('error', Yii::t('app', CUtils::replaceParam(Message::MSG_CANNOT_DELETE_ACTOR_DIRECTOR, [$adString,$cString])));
            return $this->redirect(['index','content_type' => $content_type]);
        }

        $model->status = ActorDirector::STATUS_DELETE;
        if(!$model->save()){
            Yii::$app->getSession()->setFlash('error',  Yii::t('app', Message::MSG_FAIL));
            return $this->redirect(['index','content_type' => $content_type]);
        }
        Yii::$app->getSession()->setFlash('error',  Yii::t('app', Message::MSG_DELETE_SUCCESS));
        return $this->redirect(['index','content_type' => $content_type]);
    }

    /**
     * Finds the ActorDirector model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ActorDirector the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ActorDirector::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
