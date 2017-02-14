<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 12/23/2016
 * Time: 3:38 PM
 */
use common\models\Category;

?>
<?php
    if(isset($category)){
        foreach($category as $item) {
            /** @var $item Category*/
            ?>
            <div class="category-featured">
                <nav class="navbar nav-menu nav-menu-red show-brand">
                    <div class="container">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-brand"><a href="#">
                                <img style="width: 40px;height: 40px" alt="<?=$item->display_name ?>" src="<?= Category::getImageLinkFE($item->images) ?>"/><?=$item->display_name ?></a>
                        </div>
                        <span class="toggle-menu"></span>
                        <?= \frontend\widgets\MenuContentBody::GetMenuBody($item->id) ?>
                    </div><!-- /.container-fluid -->
                    <div id="elevator-1" class="floor-elevator">
                        <a href="#" class="btn-elevator up disabled fa fa-angle-up"></a>
                        <a href="#elevator-2" class="btn-elevator down fa fa-angle-down"></a>
                    </div>
                </nav>
                <div class="category-banner">
                    <div class="col-sm-6 banner">
                        <a href="#"><img alt="ads2" class="img-responsive"
                                         src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/ads2.jpg"/></a>
                    </div>
                    <div class="col-sm-6 banner">
                        <a href="#"><img alt="ads2" class="img-responsive"
                                         src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/ads3.jpg"/></a>
                    </div>
                </div>
                <div class="product-featured clearfix">
                    <div class="banner-featured">
                        <div class="featured-text"><span>featured</span></div>
                        <div class="banner-img">
                            <a href="#"><img alt="<?=$item->display_name ?>" src="<?= Category::getImageLinkFE($item->images) ?>"/></a>
                        </div>
                    </div>
                    <div class="product-featured-content">
                        <div class="product-featured-list">
                            <div class="tab-container">
                                <?= \frontend\widgets\ContentContentBody::getContentByCategory($item->id) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
?>

