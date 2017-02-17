<?php

namespace frontend\controllers;

use api\models\Subscriber;
use common\models\Content;
use common\models\ContentCategoryAsm;
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
        $product_hots = Content::find()
            ->select('content.id,content.display_name,content.images')
            ->innerJoin('content_category_asm','content_category_asm.content_id = content.id')
            ->innerJoin('category','content_category_asm.category_id = category.id')
            ->andWhere('category.is_news <> :is_news',['is_news'=>1])
            ->andWhere('content.id <> :content_id1',['content_id1'=>$id])
            ->andWhere(['content.status'=>Content::STATUS_ACTIVE])
            ->andWhere(['content.type'=>Content::TYPE_SELLER])
            ->orderBy(['content.created_at'=>'DESC'])
            ->limit(3)
            ->all();
        return $this->render('detail',[
            'content'=>$content,
            'link'=>$link,
            'product_hots'=>$product_hots
        ]);
    }
}