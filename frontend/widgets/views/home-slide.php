<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 12/21/2016
 * Time: 9:57 PM
 */
use common\models\Slide;

?>
<div id="home-slider">
    <div class="container">
        <div class="row">
            <div class="col-sm-3 slider-left"></div>
            <div class="col-sm-9 header-top-right">
                <div class="homeslider">
                    <div class="content-slide">
                        <ul id="contenhomeslider">
                            <?php
                                if(isset($slide)){
                                    foreach($slide as $item){
                                        /** @var  $item \common\models\Slide*/
                                        ?>
                                        <li><img style="height: 450px" alt="<?= $item->des ?>" src="<?= Slide::getSlideHomeFe($item->content_id) ?>" title="<?= $item->des ?>" /></li>
                                        <?php
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="header-banner banner-opacity">
                    <a href="#"><img alt="Funky roots" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/ads1.jpg" /></a>
                </div>
            </div>
        </div>
    </div>
</div>
