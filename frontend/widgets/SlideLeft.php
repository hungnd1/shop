<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 11/19/2016
 * Time: 9:09 PM
 */
namespace frontend\widgets;

use common\models\Category;
use common\models\Content;
use yii\base\Widget;
use Yii;

class SlideLeft extends Widget{

    public $message;

    public  function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public  function run()
    {

    }

    public static function getSlideLeft($id = null){
        if($id != null){
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
        }else{
            $product_hots = Content::find()
                ->select('content.id,content.display_name,content.images')
                ->innerJoin('content_category_asm','content_category_asm.content_id = content.id')
                ->innerJoin('category','content_category_asm.category_id = category.id')
                ->andWhere('category.is_news <> :is_news',['is_news'=>1])
                ->andWhere(['content.status'=>Content::STATUS_ACTIVE])
                ->andWhere(['content.type'=>Content::TYPE_SELLER])
                ->orderBy(['content.created_at'=>'DESC'])
                ->limit(3)
                ->all();
        }
        $td = new SlideLeft();
        return $td->render('slide-left',[
            'product_hots'=>$product_hots
        ]);
    }
}
