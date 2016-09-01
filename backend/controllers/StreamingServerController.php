<?php

namespace backend\controllers;

use common\models\SiteStreamingServerAsm;
use Yii;
use common\models\StreamingServer;
use common\models\StreamingServerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StreamingServerController implements the CRUD actions for StreamingServer model.
 */
class StreamingServerController extends Controller
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
     * Lists all StreamingServer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StreamingServerSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StreamingServer model.
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
     * Creates a new StreamingServer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StreamingServer();

        if ($model->load(Yii::$app->request->post())) {
            $res = $model->saveRecords();
            if ($res['success']) {
                Yii::$app->getSession()->setFlash('success', $res['message']);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->getSession()->setFlash('error', $res['message']);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing StreamingServer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $siteIds = SiteStreamingServerAsm::find()
            ->select(['site_id'])
            ->where(['streaming_server_id' => $id])
            ->asArray()
            ->all();
        $model->site_ids = array_column($siteIds, 'site_id');
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->saveRecords(true);
            if ($res['success']) {
                Yii::$app->getSession()->setFlash('success', $res['message']);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->getSession()->setFlash('error', $res['message']);
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing StreamingServer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        /** @var $streamingServer StreamingServer */
        $streamingServer = $this->findModel($id);

        $res = $streamingServer->softDelete();
        if ($res['success']) {
            Yii::$app->getSession()->setFlash('success', $res['message']);
            return $this->redirect(['index']);
        } else {
            Yii::$app->getSession()->setFlash('error', $res['message']);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Finds the StreamingServer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StreamingServer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StreamingServer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
