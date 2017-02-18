<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/18/2017
 * Time: 2:19 PM
 */
use yii\helpers\Url;

?>
<div class="breadcrumb clearfix">
    <a class="home" href="<?= Url::to(['site/index']) ?>" title="Pando Shop"><?= Yii::t('app','Trang chủ') ?></a>
    <?php if(isset($cat_parent)){ /** @var \common\models\Category $cat_parent */?>
    <span class="navigation-pipe">&nbsp;</span>
    <a href="<?= Url::to(['category/index','id'=>$cat_parent->id ]) ?>" title="<?= $cat_parent->display_name ?>"><?= $cat_parent->display_name ?></a>
        <?= \frontend\widgets\FindBreadcrumb::getBreadcrumbChild($cat_parent->id,$id_content) ?>
    <?php } ?>
</div>
