<?php

namespace backend\controllers;

use Yii;
use common\models\ProgramSuppost;
use common\models\ProgramSuppostSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ProgramSuppostController implements the CRUD actions for ProgramSuppost model.
 */
class ProgramSuppostController extends Controller
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
     * Lists all ProgramSuppost models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProgramSuppostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProgramSuppost model.
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
     * Creates a new ProgramSuppost model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProgramSuppost();

        if ($model->load(Yii::$app->request->post())) {
            $thumbnail = UploadedFile::getInstance($model, 'image');
            if ($thumbnail) {
                $file_name = uniqid() . time() . '.' . $thumbnail->extension;
                if ($thumbnail->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@voucher_images') . "/" . $file_name)) {
                    $model->image = $file_name;
                } else {
                    Yii::$app->getSession()->setFlash('error',Yii::t('app','Không thành công, vui lòng thử lại'));
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app',' Thêm mới ưu đãi thành công'));
                $this->redirect(['view', 'id' => $model->id]);

            } else {
                Yii::error($model->getErrors());
                Yii::$app->getSession()->setFlash('error',Yii::t('app', 'Không thành công, vui lòng thử lại'));
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProgramSuppost model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old_image = $model->image;
        if ($model->load(Yii::$app->request->post())) {
            $thumbnail = UploadedFile::getInstance($model, 'image');
            if ($thumbnail) {
                $file_name = uniqid() . time() . '.' . $thumbnail->extension;
                if ($thumbnail->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@voucher_images') . "/" . $file_name)) {
                    $model->image = $file_name;
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t('app','Không thành công, vui lòng thử lại'));
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }else{
                $model->image = $old_image ;
            }
            if ($model->update()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app',' Cập nhật mới ưu đãi thành công'));
                $this->redirect(['view', 'id' => $model->id]);

            } else {
                Yii::error($model->getErrors());
                Yii::$app->getSession()->setFlash('error',Yii::t('app', 'Không thành công, vui lòng thử lại'));
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProgramSuppost model.
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
     * Finds the ProgramSuppost model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProgramSuppost the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgramSuppost::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
