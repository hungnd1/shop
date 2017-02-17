<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 1/5/2017
 * Time: 8:27 AM
 */
use common\models\Content;
use yii\helpers\Url;

?>
<?php
if($content){
    ?>
    <div class="tab-panel active" id="tab-0">
        <ul class="product-list owl-carousel" data-dots="false" data-loop="true"
            data-nav="true" data-margin="0" data-autoplayTimeout="1000"
            data-autoplayHoverPause="true"
            data-responsive='{"0":{"items":1},"600":{"items":3},"1000":{"items":4}}'>
            <?php
            foreach($content as $item) {
                /** @var $item \common\models\Content */
                ?>
                    <li>
                        <div class="left-block">
                            <a href="<?= Url::to(['content/detail','id'=>$item->id]) ?>">
                                <img style="height: 300px" class="img-responsive" alt="product"
                                     src="<?= $item->getFirstImageLinkFE() ?>"/></a>
                            <div class="quick-view">
                                <a title="Add to my wishlist" class="heart" href="#"></a>
                                <a title="Add to compare" class="compare" href="#"></a>
                                <a title="Quick view" class="search" href="#"></a>
                            </div>
                            <div class="add-to-cart">
                                <a title="Add to Cart" href="#">Thêm vào giỏ hàng</a>
                            </div>
                        </div>
                        <div class="right-block">
                            <h5 class="product-name">
                                <a href="<?= Url::to(['content/detail','id'=>$item->id]) ?>">
                                    <?= Content::substr($item->display_name,25) ?>
                                </a>
                            </h5>
                            <div class="content_price">
                                <span style="font-size: 15px" class="price product-price"><?= Content::formatNumber($item->price_promotion) ?> VND</span>
                                <span style="font-size: small" class="price old-price"><?= Content::formatNumber($item->price) ?> VND</span>
                            </div>
                            <div class="product-star">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-half-o"></i>
                            </div>
                        </div>
                    </li>
                <?php
            }
            ?>
        </ul>
    </div>
    <?php
}
?>
