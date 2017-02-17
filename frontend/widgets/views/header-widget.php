<?php
use frontend\widgets\FormLogin;
use frontend\widgets\MenuRight;
use frontend\widgets\MenuTop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div id="header" class="header">
    <div class="top-header">
        <div class="container">
            <div class="nav-top-links">
                <a class="first-item" href="#"><img alt="phone" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/phone.png" />00-62-658-658</a>
                <a href="<?= Url::to(['site/index']) ?>"><img alt="email" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/email.png" />Contact us today!</a>
            </div>
            <div class="currency ">
                <div class="dropdown">
                    <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">USD</a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">Dollar</a></li>
                        <li><a href="#">Euro</a></li>
                    </ul>
                </div>
            </div>
            <div class="language ">
                <div class="dropdown">
                    <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                        <img alt="email" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/fr.jpg" />French

                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#"><img alt="email" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/en.jpg" />English</a></li>
                        <li><a href="#"><img alt="email" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/fr.jpg" />French</a></li>
                    </ul>
                </div>
            </div>

            <div class="support-link">
                <a href="#">Services</a>
                <a href="#">Support</a>
            </div>

            <div id="user-info-top" class="user-info pull-right">
                <div class="dropdown">
                    <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><span>Tài khoản</span></a>
                    <ul class="dropdown-menu mega_dropdown" role="menu">
                        <?php
                        if(Yii::$app->user->isGuest){
                            ?>
                            <li><a  data-toggle="modal" data-target="#myModal" href="javascript:void(0)">Đăng nhập</a></li>
                            <?php
                        }else{
                            ?>
                            <li><a class="uppercase" href="<?= Url::to(['subcriber/info']) ?>"><?= Yii::$app->user->identity->fullname?Yii::$app->user->identity->fullname:Yii::$app->user->identity->username ?></a></li>
                            <li><a href="<?= Url::to(['#']) ?>">Đơn hàng</a></li>
                            <?php
                        }
                        ?>
                        <li><a href="#">So sánh</a></li>
                        <li><a href="#">Yêu thích</a></li>
                        <?php
                        if(!Yii::$app->user->isGuest){
                            ?>
                            <li><a href="<?= \yii\helpers\Url::to(['site/logout']) ?>" data-method="post">Đăng xuất</a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--/.top-header -->
    <!-- MAIN HEADER -->
    <div class="container main-header">
        <div class="row">
            <div class="col-xs-12 col-sm-3 logo">
                <a href="<?= Url::to(['site/index']) ?>"><img alt="Kute shop - GFXFree.Net" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/images/logo.png" /></a>
            </div>
            <?= \frontend\widgets\SearchCategory::widget() ?>
            <div id="cart-block" class="col-xs-5 col-sm-2 shopping-cart-box">
                <a class="cart-link" href="order.html">
                    <span class="title">Shopping cart</span>
                    <span class="total">2 items - 122.38 €</span>
                    <span class="notify notify-left">2</span>
                </a>
                <div class="cart-block">
                    <div class="cart-block-content">
                        <h5 class="cart-title">2 Items in my cart</h5>
                        <div class="cart-block-list">
                            <ul>
                                <li class="product-info">
                                    <div class="p-left">
                                        <a href="#" class="remove_link"></a>
                                        <a href="#">
                                            <img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/product-100x122.jpg" alt="p10">
                                        </a>
                                    </div>
                                    <div class="p-right">
                                        <p class="p-name">Donec Ac Tempus</p>
                                        <p class="p-rice">61,19 €</p>
                                        <p>Qty: 1</p>
                                    </div>
                                </li>
                                <li class="product-info">
                                    <div class="p-left">
                                        <a href="#" class="remove_link"></a>
                                        <a href="#">
                                            <img class="img-responsive" src="<?= Yii::$app->getUrlManager()->getBaseUrl() ?>/data/product-s5-100x122.jpg" alt="p10">
                                        </a>
                                    </div>
                                    <div class="p-right">
                                        <p class="p-name">Donec Ac Tempus</p>
                                        <p class="p-rice">61,19 €</p>
                                        <p>Qty: 1</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="toal-cart">
                            <span>Total</span>
                            <span class="toal-price pull-right">122.38 €</span>
                        </div>
                        <div class="cart-buttons">
                            <a href="order.html" class="btn-check-out">Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- END MANIN HEADER -->
    <div id="nav-top-menu" class="nav-top-menu">
        <div class="container">
            <div class="row">
                <?= MenuRight::widget()?>
                <?= MenuTop::widget() ?>
            </div>
            <!-- userinfo on top-->
            <div id="form-search-opntop">
            </div>
            <!-- userinfo on top-->
            <div id="user-info-opntop">
            </div>
            <!-- CART ICON ON MMENU -->
            <div id="shopping-cart-box-ontop">
                <i class="fa fa-shopping-cart"></i>
                <div class="shopping-cart-box-ontop-content"></div>
            </div>
        </div>
    </div>
</div>
<!-- end header -->


<!-- Start Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="box-authentication tp_001">
                <?= FormLogin::widget()?>
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->