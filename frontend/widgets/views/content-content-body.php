<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 1/5/2017
 * Time: 8:27 AM
 */
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
                /** @var $value \common\models\Content */
                ?>
                    <li>
                        <div class="left-block">
                            <a href="#">
                                <img class="img-responsive" alt="product"
                                     src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/01_blue-dress.jpg"/></a>
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
                            <h5 class="product-name"><a href="#"><?= $item->display_name ?></a></h5>
                            <div class="content_price">
                                <span class="price product-price"><?= $item->price ?> VND</span>
                                <span class="price old-price"><?= $item->price_promotion ?> VND</span>
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
