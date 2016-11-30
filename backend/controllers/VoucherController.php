<?php

namespace backend\controllers;

use DateTime;
use Yii;
use common\models\Voucher;
use common\models\VoucherSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * VoucherController implements the CRUD actions for Voucher model.
 */
class VoucherController extends Controller
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
     * Lists all Voucher models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VoucherSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Voucher model.
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
     * Creates a new Voucher model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Voucher();

        if ($model->load(Yii::$app->request->post())) {
            $started_at = 0;
            $finished_at = 0;
            if ($model->date_start) {
                $started_at = strtotime(DateTime::createFromFormat("d-m-Y H:i:s", $model->date_start)->format('Y-m-d H:i:s'));;
            }

            if ($model->date_end) {
                $finished_at = strtotime(DateTime::createFromFormat("d-m-Y H:i:s", $model->date_end)->format('Y-m-d H:i:s'));;
            }

            if ($finished_at < $started_at) {
                Yii::$app->getSession()->setFlash('error', 'Ngày kết thúc không được nhỏ hơn ngày bắt đầu');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $thumbnail = UploadedFile::getInstance($model, 'image');
            if ($thumbnail) {
                $file_name = uniqid() . time() . '.' . $thumbnail->extension;
                if ($thumbnail->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@voucher_image') . "/" . $file_name)) {
                    $model->image = $file_name;
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Không thành công, vui lòng thử lại');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }
            $model->start_date = $started_at;
            $model->end_date = $finished_at;
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', ' Thêm mới voucher thành công');
                $this->redirect(['view', 'id' => $model->id]);

            } else {
                Yii::error($model->getErrors());
                Yii::$app->getSession()->setFlash('error', 'Không thành công, vui lòng thử lại');
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Voucher model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $started_at = 0;
            $finished_at = 0;
            if ($model->date_start) {
                $started_at = strtotime(DateTime::createFromFormat("d-m-Y H:i:s", $model->date_start)->format('Y-m-d H:i:s'));;
            }

            if ($model->date_end) {
                $finished_at = strtotime(DateTime::createFromFormat("d-m-Y H:i:s", $model->date_end)->format('Y-m-d H:i:s'));;
            }

            if ($finished_at < $started_at) {
                Yii::$app->getSession()->setFlash('error', 'Ngày kết thúc không được nhỏ hơn ngày bắt đầu');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $thumbnail = UploadedFile::getInstance($model, 'image');
            if ($thumbnail) {
                $file_name = uniqid() . time() . '.' . $thumbnail->extension;
                if ($thumbnail->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@voucher_image') . "/" . $file_name)) {
                    $model->image = $file_name;
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Không thành công, vui lòng thử lại');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }
            $model->start_date = $started_at;
            $model->end_date = $finished_at;
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', ' Cập nhật mới voucher thành công');
                $this->redirect(['view', 'id' => $model->id]);

            } else {
                Yii::error($model->getErrors());
                Yii::$app->getSession()->setFlash('error', 'Không thành công, vui lòng thử lại');
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Voucher model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Voucher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Voucher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Voucher::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
