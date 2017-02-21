<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/21/2017
 * Time: 9:09 AM
 */
if(isset($cat)){
    foreach($cat as $item){
        /** @var \common\models\Category $item */
        ?>
        <li>
            <a href="<?= \yii\helpers\Url::to(['category/index','id'=>$item->id]) ?>"><?= $item->display_name ?></a>
        </li>
        <?php
    }
}
?>
