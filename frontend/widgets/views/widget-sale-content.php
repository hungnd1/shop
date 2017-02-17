<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/17/2017
 * Time: 4:39 PM
 */
use common\models\Content;
use yii\helpers\Url;

?>
<?php if(isset($product_sales) && !empty($product_sales)){ ?>
<div class="block left-module">
    <p class="title_block"><?= Yii::t('app','Đang giảm giá') ?></p>
    <div class="block_content product-onsale">
        <ul class="product-list owl-carousel" data-loop="true" data-nav = "false" data-margin = "0" data-autoplayTimeout="1000" data-autoplayHoverPause = "true" data-items="1" data-autoplay="true">
            <?php foreach($product_sales as $item){ /** @var  \common\models\Content $item */ ?>
            <li>
                <div class="product-container">
                    <div class="left-block">
                        <a href="<?= Url::to(['content/detail','id'=>$item->id]) ?>">
                            <img class="img-responsive" alt="product" src="<?= $item->getFirstImageLinkFE() ?>" />
                        </a>
                        <div class="price-percent-reduction2">-<?= round (((($item->price - $item->price_promotion)/($item->price))*100),1) ?> % </div>
                    </div>
                    <div class="right-block">
                        <h5 class="product-name">
                            <a href="<?= Url::to(['content/detail','id'=>$item->id]) ?>">
                                <?= $item->display_name ?>
                            </a>
                        </h5>
                        <div class="product-star">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star-half-o"></i>
                        </div>
                        <div class="content_price">
                            <span class="price"><?= Content::formatNumber($item->price_promotion) ?> VND</span>
                            <span class="old-price"><?= Content::formatNumber($item->price) ?> VND</span>
                        </div>
                    </div>
                    <div class="product-bottom">
                        <a class="btn-add-cart" title="Add to Cart" href="#add"><?= Yii::t('app','Thêm vào giỏ hàng')?></a>
                    </div>
                </div>
            </li>
            <?php } ?>
        </ul>
    </div>
</div>
<?php } ?>
