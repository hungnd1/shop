<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 11/19/2016
 * Time: 9:09 PM
 */
namespace frontend\widgets;

use common\models\Category;
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
//        $pro = Category::find()
//            ->andWhere(['status' => Category::STATUS_ACTIVE])
//            ->andWhere(['type'=> Category::TYPE_MENU_ABOVE])
//            ->all();
        return $this->render('best-sale-new',[
//            'menu'=>$menu
        ]);
    }
}