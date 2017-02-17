<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/17/2017
 * Time: 4:19 PM
 */
use common\models\Content;
use yii\helpers\Url;

?>
<div class="block left-module">
    <p class="title_block"><?= Yii::t('app','Sản phẩm mới') ?></p>
    <div class="block_content">
        <div class="owl-carousel owl-best-sell" data-loop="true" data-nav = "false" data-margin = "0" data-autoplayTimeout="1000" data-autoplay="true" data-autoplayHoverPause = "true" data-items="1">
            <?php
                if(isset($product_news)) {
                    $i = 0;
                    $n = count($product_news);
                    foreach($product_news as $item) {
                        /** @var \common\models\Content $item */
                        if ($i < 3) {
                            ?>
                            <ul class="products-block best-sell">
                                <li>
                                    <div class="products-block-left">
                                        <a href="<?= Url::to(['content/detail','id'=>$item->id]) ?>">
                                            <img src="<?= $item->getFirstImageLinkFE() ?>"
                                                 alt="<?= $item->display_name ?>">
                                        </a>
                                    </div>
                                    <div class="products-block-right">
                                        <p class="product-name">
                                            <a href="<?= Url::to(['content/detail','id'=>$item->id]) ?>"><?= $item->display_name ?></a>
                                        </p>
                                        <p class="product-price"><?= $item->price_promotion?Content::formatNumber($item->price_promotion):Content::formatNumber($item->price) ?> VND</p>
                                        <p class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                        </p>
                                    </div>
                                </li>
                            </ul>
                            <?php
                        $i++;}
                        ?>
                        <ul class="products-block best-sell">
                            <li>
                                <div class="products-block-left">
                                    <a href="<?= Url::to(['content/detail','id'=>$item->id]) ?>">
                                        <img src="<?= $item->getFirstImageLinkFE() ?>"
                                             alt="<?= $item->display_name ?>">
                                    </a>
                                </div>
                                <div class="products-block-right">
                                    <p class="product-name">
                                        <a href="<?= Url::to(['content/detail','id'=>$item->id]) ?>"><?= $item->display_name ?></a>
                                    </p>
                                    <p class="product-price"><?= $item->price_promotion?Content::formatNumber($item->price_promotion):Content::formatNumber($item->price) ?> VND</p>
                                    <p class="product-star">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star-half-o"></i>
                                    </p>
                                </div>
                            </li>
                        </ul>
                        <?php
                        $i++;
                    }
                }
            ?>
        </div>
    </div>
</div>
