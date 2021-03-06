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
use DateTime;
use yii\base\Widget;
use Yii;

class BestSaleNew extends Widget{

    public $message;

    public  function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public  function run()
    {
        // lấy 6 sản phẩm được tạo trong vòng 1 tháng gần thời điểm hiện tại nhất
        $product_news = Content::find()
            ->select('content.id,content.display_name,content.type,content.short_description,content.price,content.images,content.price_promotion')
            ->innerJoin('content_category_asm','content_category_asm.content_id = content.id')
            ->innerJoin('category','content_category_asm.category_id = category.id')
            ->andWhere('category.is_news <> :is_news',['is_news'=>1])
            ->andWhere(['content.status'=>Content::STATUS_ACTIVE])
            ->andWhere(['content.type'=>Content::TYPE_NEWEST])
            ->orderBy(['content.created_at'=>'DESC'])
            ->limit(6)
            ->all();
        // sản phẩm sale
        $product_sales = Content::find()
            ->select('content.id,content.display_name,content.type,content.short_description,content.price,content.images,content.price_promotion')
            ->innerJoin('content_category_asm','content_category_asm.content_id = content.id')
            ->innerJoin('category','content_category_asm.category_id = category.id')
            ->andWhere('category.is_news <> :is_news',['is_news'=>1])
            ->andWhere(['content.status'=>Content::STATUS_ACTIVE])
            ->andWhere(['content.type'=>Content::TYPE_PRICEPROMO])
            ->orderBy(['content.created_at'=>'DESC'])
            ->limit(6)
            ->all();
        // sản phẩm hot
        $product_hots = Content::find()
            ->select('content.id,content.display_name,content.type,content.short_description,content.price,content.images,content.price_promotion')
            ->innerJoin('content_category_asm','content_category_asm.content_id = content.id')
            ->innerJoin('category','content_category_asm.category_id = category.id')
            ->andWhere('category.is_news <> :is_news',['is_news'=>1])
            ->andWhere(['content.status'=>Content::STATUS_ACTIVE])
            ->andWhere(['content.type'=>Content::TYPE_SELLER])
            ->orderBy(['content.created_at'=>'DESC'])
            ->limit(6)
            ->all();
        return $this->render('best-sale-new',[
            'product_news'=>$product_news,
            'product_sales'=>$product_sales,
            'product_hots'=>$product_hots,
        ]);
    }
}
