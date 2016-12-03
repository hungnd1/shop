<?php

namespace frontend\controllers;

use Yii;
use common\models\User;
use common\models\UserSearch;
use yii\web\Controller;

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
}