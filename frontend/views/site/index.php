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

<div class="container">
    <div class="brand-showcase">
        <h2 class="brand-showcase-title">
            brand showcase
        </h2>
        <div class="brand-showcase-box">
            <ul class="brand-showcase-logo owl-carousel" data-loop="true" data-nav = "true" data-dots="false" data-margin = "1" data-autoplayTimeout="1000" data-autoplayHoverPause = "true" data-responsive='{"0":{"items":2},"600":{"items":5},"1000":{"items":8}}'>
                <li data-tab="showcase-1" class="item active"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/gucci.png" alt="logo" class="item-img" /></li>
                <li data-tab="showcase-2" class="item"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/gucci.png" alt="logo" class="item-img" /></li>
                <li data-tab="showcase-3" class="item"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/gucci.png" alt="logo" class="item-img" /></li>
                <li data-tab="showcase-4" class="item"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/gucci.png" alt="logo" class="item-img" /></li>
                <li data-tab="showcase-5" class="item"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/gucci.png" alt="logo" class="item-img" /></li>
                <li data-tab="showcase-6" class="item"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/gucci.png" alt="logo" class="item-img" /></li>
                <li data-tab="showcase-7" class="item"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/gucci.png" alt="logo" class="item-img" /></li>
                <li data-tab="showcase-8" class="item"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/gucci.png" alt="logo" class="item-img" /></li>
                <li data-tab="showcase-9" class="item"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/gucci.png" alt="logo" class="item-img" /></li>
            </ul>
            <div class="brand-showcase-content">
                <div class="brand-showcase-content-tab active" id="showcase-1">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo">
                                <a href="#"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/trademark.jpg" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                Whatever the occasion, complete your outfit with one of Hermes Fashion’s stylish women’s bags. Discover our spring collection.
                            </div>
                            <a href="#" class="trademark-link">shop this brand</a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-repon" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p24.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p25.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p26.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p27.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="brand-showcase-content-tab" id="showcase-2">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo">
                                <a href="#"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/trademark.jpg" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                Whatever the occasion, complete your outfit with one of Hermes Fashion’s stylish women’s bags. Discover our spring collection.
                            </div>
                            <a href="#" class="trademark-link">shop this brand</a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-repon" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p10.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p11.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p12.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p13.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="brand-showcase-content-tab" id="showcase-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo">
                                <a href="#"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/trademark.jpg" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                Whatever the occasion, complete your outfit with one of Hermes Fashion’s stylish women’s bags. Discover our spring collection.
                            </div>
                            <a href="#" class="trademark-link">shop this brand</a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-repon" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p14.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p15.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p16.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p17.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="brand-showcase-content-tab" id="showcase-4">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo">
                                <a href="#"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/trademark.jpg" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                Whatever the occasion, complete your outfit with one of Hermes Fashion’s stylish women’s bags. Discover our spring collection.
                            </div>
                            <a href="#" class="trademark-link">shop this brand</a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-repon" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p18.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p19.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p20.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p21.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="brand-showcase-content-tab" id="showcase-5">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo">
                                <a href="#"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/trademark.jpg" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                Whatever the occasion, complete your outfit with one of Hermes Fashion’s stylish women’s bags. Discover our spring collection.
                            </div>
                            <a href="#" class="trademark-link">shop this brand</a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-repon" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p22.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p23.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p24.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p25.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="brand-showcase-content-tab" id="showcase-6">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo">
                                <a href="#"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/trademark.jpg" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                Whatever the occasion, complete your outfit with one of Hermes Fashion’s stylish women’s bags. Discover our spring collection.
                            </div>
                            <a href="#" class="trademark-link">shop this brand</a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-repon" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p26.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p27.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p28.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p29.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="brand-showcase-content-tab" id="showcase-7">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo">
                                <a href="#"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/trademark.jpg" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                Whatever the occasion, complete your outfit with one of Hermes Fashion’s stylish women’s bags. Discover our spring collection.
                            </div>
                            <a href="#" class="trademark-link">shop this brand</a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-repon" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p30.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p31.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p32.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p15.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="brand-showcase-content-tab" id="showcase-8">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo">
                                <a href="#"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/trademark.jpg" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                Whatever the occasion, complete your outfit with one of Hermes Fashion’s stylish women’s bags. Discover our spring collection.
                            </div>
                            <a href="#" class="trademark-link">shop this brand</a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-repon" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p25.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p21.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p10.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p23.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="brand-showcase-content-tab" id="showcase-9">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 trademark-info">
                            <div class="trademark-logo">
                                <a href="#"><img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/trademark.jpg" alt="trademark"></a>
                            </div>
                            <div class="trademark-desc">
                                Whatever the occasion, complete your outfit with one of Hermes Fashion’s stylish women’s bags. Discover our spring collection.
                            </div>
                            <a href="#" class="trademark-link">shop this brand</a>
                        </div>
                        <div class="col-xs-12 col-sm-8 trademark-product">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-repon" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p24.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p14.jpg" alt=""></a>
                                    </div>
                                    <div class="image-product hover-zoom">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p30.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 product-item">
                                    <div class="image-product hover-zoom">
                                        <a href="#"><img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/p29.jpg" alt=""></a>
                                    </div>
                                    <div class="info-product">
                                        <a href="#">
                                            <h5>Maecenas consequat mauris</h5>
                                        </a>
                                        <span class="product-price">$38.87</span>
                                        <div class="product-star">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a class="btn-view-more" title="View More" href="#">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="content-wrap">
    <div class="container">
        <div id="hot-categories" class="row">
            <div class="col-sm-12 group-title-box">
                <h2 class="group-title ">
                    <span>Hot categories</span>
                </h2>
            </div>

            <div class="col-sm-6  col-lg-3 cate-box">
                <div class="cate-tit" >
                    <div class="div-1" style="width: 46%;">
                        <div class="cate-name-wrap">
                            <p class="cate-name">Electronics</p>
                        </div>
                        <a href="" class="cate-link link-active" data-ac="flipInX" ><span>shop now</span></a>
                    </div>
                    <div class="div-2" >
                        <a href="#">
                            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/cate-product1.png" alt="Electronics" class="hot-cate-img" />
                        </a>
                    </div>

                </div>
                <div class="cate-content">
                    <ul>
                        <li><a href="#">Batteries & Chargers</a></li>
                        <li><a href="#">Headphone, Headset</a></li>
                        <li><a href="#">Home Audio</a></li>
                        <li><a href="#">Mp3 Player Accessories</a></li>
                    </ul>
                </div>
            </div> <!-- /.cate-box -->

            <div class="col-sm-6  col-lg-3 cate-box">
                <div class="cate-tit" >
                    <div class="div-1" style="width: 46%;">
                        <div class="cate-name-wrap">
                            <p class="cate-name">Sport & Outdoors</p>
                        </div>
                        <a href="" class="cate-link" data-ac="flipInX" ><span>shop now</span></a>
                    </div>
                    <div class="div-2" >
                        <a href="#">
                            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/cate-product2.png" alt="Electronics" class="hot-cate-img" />
                        </a>
                    </div>
                </div>
                <div class="cate-content">
                    <ul>
                        <li><a href="#">Golf Supplies</a></li>
                        <li><a href="#">Outdoor & Traveling Supplies</a></li>
                        <li><a href="#">Camping & Hiking</a></li>
                        <li><a href="#">Fishing</a></li>
                    </ul>
                </div>
            </div> <!-- /.cate-box -->
            <div class="col-sm-6  col-lg-3 cate-box">
                <div class="cate-tit" >
                    <div class="div-1" style="width: 46%;">
                        <div class="cate-name-wrap">
                            <p class="cate-name">Fashion</p>
                        </div>
                        <a href="" class="cate-link" data-ac="flipInX" ><span>shop now</span></a>
                    </div>
                    <div class="div-2" >
                        <a href="#">
                            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/cate-product3.png" alt="Electronics" class="hot-cate-img"/>
                        </a>
                    </div>

                </div>
                <div class="cate-content">
                    <ul>
                        <li><a href="#">Batteries & Chargers</a></li>
                        <li><a href="#">Headphone, Headset</a></li>
                        <li><a href="#">Home Audio</a></li>
                        <li><a href="#">Mp3 Player Accessories</a></li>
                    </ul>
                </div>
            </div> <!-- /.cate-box -->
            <div class="col-sm-6  col-lg-3 cate-box">
                <div class="cate-tit" >
                    <div class="div-1" style="width: 46%;">
                        <div class="cate-name-wrap">
                            <p class="cate-name">Health & Beauty</p>
                        </div>
                        <a href="" class="cate-link" data-ac="flipInX" ><span>shop now</span></a>
                    </div>
                    <div class="div-2" >
                        <a href="#">
                            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/cate-product4.png" alt="Electronics" class="hot-cate-img" />
                        </a>
                    </div>

                </div>
                <div class="cate-content">
                    <ul>
                        <li><a href="#">Bath & Body</a></li>
                        <li><a href="#">Shaving & Hair Removal</a></li>
                        <li><a href="#">Fragrances</a></li>
                        <li><a href="#">Salon & Spa Equipment</a></li>
                    </ul>
                </div>
            </div> <!-- /.cate-box -->
            <div class="col-sm-6  col-lg-3 cate-box">
                <div class="cate-tit" >
                    <div class="div-1" style="width: 46%;">
                        <div class="cate-name-wrap">
                            <p class="cate-name">Jewelry & Watches</p>
                        </div>
                        <a href="" class="cate-link" data-ac="flipInX" ><span>shop now</span></a>
                    </div>
                    <div class="div-2" >
                        <a href="#">
                            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/cate-product5.png" alt="Electronics" class="hot-cate-img" />
                        </a>
                    </div>
                </div>
                <div class="cate-content">
                    <ul>
                        <li><a href="#">Men Watches</a></li>
                        <li><a href="#">Wedding Rings</a></li>
                        <li><a href="#">Earring</a></li>
                        <li><a href="#">Necklaces</a></li>
                    </ul>
                </div>
            </div> <!-- /.cate-box -->

            <div class="col-sm-6  col-lg-3 cate-box">
                <div class="cate-tit" >
                    <div class="div-1" style="width: 46%;">
                        <div class="cate-name-wrap">
                            <p class="cate-name">Digital</p>
                        </div>
                        <a href="" class="cate-link" data-ac="flipInX" ><span>shop now</span></a>
                    </div>
                    <div class="div-2" >
                        <a href="#">
                            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/cate-product6.png" alt="Electronics" class="hot-cate-img" />
                        </a>
                    </div>
                </div>
                <div class="cate-content">
                    <ul>
                        <li><a href="#">Accessories for iPhone</a></li>
                        <li><a href="#">Accessories for iPad</a></li>
                        <li><a href="#">Accessories for Tablet PC</a></li>
                        <li><a href="#">Tablet PC</a></li>
                    </ul>
                </div>
            </div><!-- /.cate-box -->
            <div class="col-sm-6  col-lg-3 cate-box">
                <div class="cate-tit" >
                    <div class="div-1" style="width: 46%;">
                        <div class="cate-name-wrap">
                            <p class="cate-name">Furniture</p>
                        </div>
                        <a href="" class="cate-link" data-ac="flipInX" ><span>shop now</span></a>
                    </div>
                    <div class="div-2" >
                        <a href="#">
                            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/cate-product7.png" alt="Electronics" class="hot-cate-img" />
                        </a>
                    </div>

                </div>
                <div class="cate-content">
                    <ul>
                        <li><a href="#">Batteries & Chargers</a></li>
                        <li><a href="#">Headphone, Headset</a></li>
                        <li><a href="#">Home Audio</a></li>
                        <li><a href="#">Mp3 Player Accessories</a></li>
                    </ul>
                </div>
            </div> <!-- /.cate-box -->
            <div class="col-sm-6  col-lg-3 cate-box">
                <div class="cate-tit" >
                    <div class="div-1" style="width: 46%;">
                        <div class="cate-name-wrap">
                            <p class="cate-name">Toys & Hobbies</p>
                        </div>
                        <a href="" class="cate-link" data-ac="flipInX" ><span>shop now</span></a>
                    </div>
                    <div class="div-2" >
                        <a href="#">
                            <img src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/cate-product8.png" alt="Electronics" class="hot-cate-img" />
                        </a>
                    </div>
                </div>
                <div class="cate-content">
                    <ul>
                        <li><a href="#">Walkera</a></li>
                        <li><a href="#">Fpv System & Parts</a></li>
                        <li><a href="#">RC Cars & Parts</a></li>
                        <li><a href="#">Helicopters & Part</a></li>
                    </ul>
                </div>
            </div><!-- /.cate-box -->
        </div> <!-- /#hot-categories -->

    </div> <!-- /.container -->
</div>