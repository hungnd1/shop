<?php

namespace backend\controllers;

use common\components\ActionLogTracking;
use common\components\ActionSPCPFilter;
use common\components\SPOwnerFilter;
use common\models\UserActivity;
use Yii;
use common\models\ContentFeedback;
use common\models\ContentFeedbackSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ContentFeedbackController implements the CRUD actions for ContentFeedback model.
 */
class ContentFeedbackController extends BaseBEController
{
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
     * Lists all ContentFeedback models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['hasEditable'])) {
            // read your posted model attributes
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                // read or convert your posted information
                $feedback = ContentFeedback::findOne($post['editableKey']);
                $index = $post['editableIndex'];
                if ($feedback) {
                    $feedback->load($post['ContentFeedback'][$index], '');
                    if ($feedback->update()) {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Dữ liệu không hợp lệ']);
                    }
                } else {
                    echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Feedback không tồn tại']);
                }

            } // else if nothing to do always return an empty JSON encoded output
            else {
                echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
            }
            return;
        }
        $searchModel = new ContentFeedbackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ContentFeedback model.
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
     * Creates a new ContentFeedback model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ContentFeedback();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ContentFeedback model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ContentFeedback model.
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
     * Finds the ContentFeedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContentFeedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContentFeedback::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionApprove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $cp = Yii::$app->user->id;
        if (isset($post['ids'])) {
            $ids = $post['ids'];
            $feedbacks = ContentFeedback::findAll($ids);
            $feedbacksApprove = 0;
            foreach ($feedbacks as $feedback) {
                if ($feedback->approve($cp)) {
                    $feedbacksApprove++;
                }
            }
            return [
                'success' => true,
                'message' => "Duyệt " . $feedbacksApprove . " feedback thành công!"
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy feedback trên hệ thống'
            ];
        }
    }

    public function actionReject()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        if (isset($post['ids'])) {
            $ids = $post['ids'];
            $feedbacks = ContentFeedback::findAll($ids);
            $feedbacksReject = 0;
            foreach ($feedbacks as $feedback) {
                if ($feedback->reject()) {
                    $feedbacksReject++;
                }
            }
            return [
                'success' => true,
                'message' => "Từ chối " . $feedbacksReject . " feedback thành công!"
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy feedback trên hệ thống'
            ];
        }
    }

}
