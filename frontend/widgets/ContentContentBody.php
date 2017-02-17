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

class ContentContentBody extends Widget{

    public $message;

    public  function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public  function run()
    {
    }

    public function getContentByCategory($id){
        // trong day se lay toan bo content cua danh muc cap cha, con cap 1, con cap 2
        $content=[];
        $content_c = Content::find()
            ->select('content.id,display_name,price,price_promotion,images')
            ->innerJoin('content_category_asm','content_category_asm.content_id = content.id')
            ->andWhere(['content_category_asm.category_id'=>$id])
            ->andWhere(['content.status'=>Content::STATUS_ACTIVE])
            ->orderBy(['updated_at' => SORT_ASC])
            ->limit(3)
            ->all();
        foreach($content_c as $c) {
            $content[] = $c;
        }
        $cat_level_1 = Category::findAll(['parent_id'=>$id]);

        if(isset($cat_level_1) && !empty($cat_level_1)){
            foreach ($cat_level_1 as $key => $item) {

                // content cua danh muc cap 1
                $content_p = Content::find()
                    ->select('content.id,display_name,price,price_promotion,images')
                    ->innerJoin('content_category_asm','content_category_asm.content_id = content.id')
                    ->andWhere(['content_category_asm.category_id'=>$item->id])
                    ->andWhere(['content.status'=>Content::STATUS_ACTIVE])
                    ->orderBy(['updated_at' => SORT_ASC])
                    ->limit(3)
                    ->all();
                foreach($content_p as $c1){
                    $content[] = $c1;
                }
                // content cua danh muc cap 2
                $cat_level_2 = Category::findAll(['parent_id'=>$item->id]);
                foreach($cat_level_2 as $value){
                    $content_p2 = Content::find()
                        ->select('content.id,display_name,price,price_promotion,images')
                        ->innerJoin('content_category_asm','content_category_asm.content_id = content.id')
                        ->andWhere(['content_category_asm.category_id'=>$value->id])
                        ->andWhere(['content.status'=>Content::STATUS_ACTIVE])
                        ->orderBy(['updated_at' => SORT_ASC])
                        ->limit(3)
                        ->all();
                    foreach($content_p2 as $c2){
                        $content[] = $c2;
                    }
                }
            }
        }
//        echo "<pre>"; print_r($content);die();
        return $this->render('content-content-body',[
            'content'=>$content,
        ]);

    }
}
