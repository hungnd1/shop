<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/18/2017
 * Time: 8:24 AM
 */
?>
<?php if(isset($menu)){?>
<ul class="tree-menu">
    <?php $i=0; foreach($menu as $item){  /** @var \common\models\Category $item */ ?>
    <li>
        <span></span><a href="<?= \yii\helpers\Url::to(['category/index','id'=>$item->id]) ?>"><?= $item->display_name ?></a>
        <?= \frontend\widgets\CategoryChildLeft::getCateChildLeft($item->id) ?>
    </li>
    <?php $i++;} ?>
</ul>
<?php } ?>
