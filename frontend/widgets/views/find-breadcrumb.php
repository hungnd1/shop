<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/18/2017
 * Time: 2:19 PM
 */
use frontend\widgets\FindBreadcrumb;
use yii\helpers\Url;

?>
<div class="breadcrumb clearfix">
    <a class="home" href="<?= Url::to(['site/index']) ?>" title="Pando Shop"><?= Yii::t('app','Trang chủ') ?></a>
    <?php if(isset($cat_parent)){ /** @var \common\models\Category $cat_parent */?>
    <span class="navigation-pipe">&nbsp;</span>
    <a href="<?= Url::to(['category/index','id'=>$cat_parent->id ]) ?>" title="<?= $cat_parent->display_name ?>"><?= $cat_parent->display_name ?></a>
        <?php
        if(isset($id_content)){ echo FindBreadcrumb::getBreadcrumbChild($cat_parent->id,$id_content);}
        if(isset($id_cate_old)){ echo FindBreadcrumb::getCateChild($cat_parent->id,$id_cate_old);} ?>
    <?php } ?>
</div>
