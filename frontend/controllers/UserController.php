<?php

namespace frontend\controllers;

use DateTime;
use Yii;
use common\models\User;
use common\models\UserSearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function actionInfo()
    {
        $id = Yii::$app->user->id;
        $model = User::findOne($id);
        return $this->render('info',[
            'model'=>$model
        ]);
    }


    public function actionUpdate()
    {
        $id = Yii::$app->user->id;

        $model = User::find()->andWhere(['id'=>$id])->andWhere(['status' => User::STATUS_ACTIVE])->one();
        /* @var $model User */
        if(!$model){
            throw  new BadRequestHttpException('Người dùng không tồn tại');
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $avatar_old = $model->image;

        $model->birthday = $model->birthday?date('d/m/Y',strtotime($model->birthday)):'';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $avatar  = UploadedFile::getInstance($model, 'image');
            if ($avatar) {
                $avatar_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $avatar->extension;
                if ($avatar->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@avatar') . "/" . $avatar_name)) {
                    $model->image = $avatar_name;
                    $model->birthday = $model->birthday?date('Y-m-d H:i:s',strtotime(DateTime::createFromFormat("d/m/Y", $model->birthday)->setTime(0,0)->format('Y-m-d H:i:s'))):'';
                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', 'Cập nhật thành công thông tin người dùng!');
                        return $this->redirect(['info']);
                    } else {
                        Yii::$app->getSession()->setFlash('error', 'Lỗi hệ thống vui lòng thử lại');
                        Yii::error($model->getErrors());
                        return $this->render('update',[
                            'model' => $model,
                        ]);
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Lỗi hệ thống 1, vui lòng thử lại');
                    return $this->render('update',[
                        'model' => $model,
                    ]);
                }
            }else {
                $model->image = $avatar_old;
                $model->birthday = $model->birthday?date('Y-m-d H:i:s',strtotime(DateTime::createFromFormat("d/m/Y", $model->birthday)->setTime(0,0)->format('Y-m-d H:i:s'))):'';
                $model->save(false);
                Yii::$app->getSession()->setFlash('success', 'Cập nhật thành công thông tin người dùng');
                return $this->redirect(['info']);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
}