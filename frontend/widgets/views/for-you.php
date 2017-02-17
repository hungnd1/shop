<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/16/2017
 * Time: 8:49 AM
 */
use common\models\Category;

?>
<div class="container">
    <div class="brand-showcase">
        <h2 class="brand-showcase-title">
            <?= Yii::t('app','Dành cho bạn') ?>
        </h2>
        <div class="brand-showcase-box">
            <ul class="brand-showcase-logo owl-carousel" data-loop="true" data-nav = "true" data-dots="false" data-margin = "1" data-autoplayTimeout="1000" data-autoplayHoverPause = "true" data-responsive='{"0":{"items":2},"600":{"items":5},"1000":{"items":8}}'>
                <?php if(isset($category)){ $i = 1; ?>
                <?php foreach($category as  $item){ ?>
                <?php /** @var  \common\models\Category $item*/ ?>
                <li data-tab="showcase-<?= $i ?>" class="item <?= $i==1?'active':'' ?> tp_fix_fonts text-center">
                    <?= $item->display_name ?>
                </li>
                <?php $i++; } ?>
                <?php } ?>
            </ul>
            <div class="brand-showcase-content">
                <?php if(isset($category)){ $i = 1; ?>
                <?php foreach($category as  $item){ ?>
                <?php /** @var  \common\models\Category $item*/ ?>
                <div class="brand-showcase-content-tab <?= $i==1?'active':'' ?>" id="showcase-<?= $i ?>">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo text-center">
                                <a href="#"><img style="width: 160px" src="<?= Category::getImageLinkFE($item->images) ?>" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                <?= $item->description ?>
                            </div>
                            <a href="#" class="trademark-link"><?= $item->display_name ?></a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <?= \frontend\widgets\ContentForYou::widget() ?>
                        </div>
                    </div>
                </div>
                <?php $i++; } ?>
                <?php } ?>
            </div>
        </div>

    </div>
</div>
