<?php

namespace frontend\controllers;

use api\models\Subscriber;
use common\models\Content;
use common\models\Subcriber;
use DateTime;
use Yii;
use common\models\User;
use common\models\UserSearch;
use yii\behaviors\TimestampBehavior;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * UserController implements the CRUD actions for User model.
 */
class ContentController extends Controller
{
    public function actionDetail($id){
        $content = Content::findOne(['id'=>$id,'status'=>Content::STATUS_ACTIVE]);
        $link = $content->getImageLinkFE();
        return $this->render('detail',[
            'content'=>$content,
            'link'=>$link,
        ]);
    }
}