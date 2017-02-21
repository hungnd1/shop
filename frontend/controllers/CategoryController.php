<?php

namespace frontend\controllers;

use api\models\Subscriber;
use common\models\Category;
use common\models\Content;
use common\models\ContentCategoryAsm;
use common\models\Slide;
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
class CategoryController extends Controller
{
    public function actionIndex($id){
        $content = [];
        $banner = Slide::findAll(['status'=>Slide::STATUS_ACTIVE,'type'=>Slide::SLIDE_CATEGORY]);
        $cat = Category::findOne($id);
        $content1 =  self::getContent($id);
        if (isset($content1) && !empty($content1)) {
            foreach ($content1 as $item1) {
                $content[] = $item1;
            }
        }
        $cat1 = Category::find()
            ->select('id')
            ->andWhere(['status' => Category::STATUS_ACTIVE])
            ->andWhere(['parent_id' => $id])
            ->all();
        if (isset($cat1) && !empty($cat1)) {
            foreach ($cat1 as $item) {
                $content2 = self::getContent($item->id);
                if (isset($content2) && !empty($content2)) {
                    foreach ($content2 as $item2) {
                        $content[] = $item2;
                    }
                }
                $content3 = self::getContentCate($item->id);
                if (isset($content3) && !empty($content3)) {
                    foreach ($content3 as $item3) {
                        $content[] = $item3;
                    }
                }
            }
        }
        if (isset($content2) && !empty($content2)) {
            foreach ($content2 as $item2) {
                $content[] = $item2;
            }
        }
        return $this->render('index',[
            'content'=>$content,
            'banner'=>$banner,
            'cat'=>$cat
        ]);
    }

    public static function getContentCate($id)
    {
        $content = [];
        $cat = Category::find()
            ->select('id')
            ->andWhere(['status' => Category::STATUS_ACTIVE])
            ->andWhere(['parent_id' => $id])
            ->all();
        if (isset($cat) && !empty($cat)) {
            foreach ($cat as $item) {
                $content1 = self::getContent($item->id);
                if (isset($content1) && !empty($content1)) {
                    foreach ($content1 as $item2) {
                        $content[] = $item2;
                    }
                }
            }
            return $content;
        }
    }

    public static function getContent($id){
        $content1 = Content::find()
            ->select('content.id,content.display_name,content.type,content.short_description,content.price,content.images,content.price_promotion')
            ->innerJoin('content_category_asm', 'content_category_asm.content_id = content.id')
            ->innerJoin('category', 'content_category_asm.category_id = category.id')
            ->andWhere(['category.id' => $id])
            ->andWhere(['content.status' => Content::STATUS_ACTIVE])
            ->orderBy(['content.created_at' => 'DESC'])
            ->all();
        return $content1;
    }

}