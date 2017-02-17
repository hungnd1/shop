<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/16/2017
 * Time: 11:03 AM
 */
?>
<div class="row">
    <?php if(isset($content)){ ?>
    <?php foreach($content as  $item){ ?>
    <?php /** @var  \common\models\Content $item*/ ?>
    <div class="col-xs-12 col-sm-6 product-item">
        <div class="image-product hover-zoom">
            <a href="#"><img class="img-repon" src="<?= $item->getFirstImageLinkFE() ?>" alt="<?= $item->display_name ?>"></a>
        </div>
        <div class="info-product">
            <a href="#">
                <h5><?= $item->display_name ?></h5>
            </a>
            <span class="product-price"><?= \common\models\Content::formatNumber($item->price_promotion) ?> VND</span>
            <div class="product-star">
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
            </div>
            <a class="btn-view-more" title="View More" href="#"><?= Yii::t('app','Xem chi tiết') ?></a>
        </div>
    </div>
    <?php $i++; } ?>
    <?php } ?>
</div>
