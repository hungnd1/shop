<?php
use frontend\widgets\BestSaleNew;
use frontend\widgets\Fashion;
use frontend\widgets\HomeSlide;
use frontend\widgets\ProgramerSuppostFe;

?>
<!-- Home slideder-->
<?= HomeSlide::widget() ?>
<!-- END Home slideder-->
<!-- servives -->
<?= ProgramerSuppostFe::widget()?>
<!-- end services -->
<?= BestSaleNew::widget() ?>
<!---->
<div class="content-page">
    <div class="container">
        <!-- featured category fashion -->
            <?= \frontend\widgets\ContentBody::widget()?>
        <!-- end featured category fashion -->

        <!-- Baner bottom -->
        <div class="row banner-bottom">
            <div class="col-sm-6">
                <div class="banner-boder-zoom">
                    <a href="#"><img alt="ads" class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/ads17.jpg" /></a>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="banner-boder-zoom">
                    <a href="#"><img alt="ads" class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/ads18.jpg" /></a>
                </div>
            </div>
        </div>
        <!-- end banner bottom -->
    </div>
</div>

<?= \frontend\widgets\ForYou::widget() ?>

<?= \frontend\widgets\ListNews::widget()?>