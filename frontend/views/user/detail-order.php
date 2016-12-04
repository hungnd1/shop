<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 12/2/2016
 * Time: 11:20 PM
 */
use common\models\User;
use frontend\widgets\UserWidget;
use yii\helpers\Url;

/* @var $model common\models\User */
?>
<!-- page wapper-->
<div class="columns-container">
    <div class="container" id="columns">
        <!-- breadcrumb -->
        <div class="breadcrumb clearfix">
            <a class="home" href="<?= Url::to(['site/index']) ?>" title="Trang chủ">Home</a>
            <span class="navigation-pipe">&nbsp;</span>
            <span class="navigation_page">
                <a class="home" href="<?= Url::to(['user/info']) ?>" title="Trang cá nhân">
                    <?= $model->fullname?$model->fullname:$model->username ?>
                </a>
            </span>
            <span class="navigation-pipe">&nbsp;</span>
            <span class="navigation_page">Xem chi tiết đơn hàng</span>
        </div>
        <!-- ./breadcrumb -->
        <!-- page heading-->
        <h2 class="page-heading">
            <span class="page-heading-title2">Thông tin cá nhân</span>
        </h2>
        <!-- ../page heading-->
        <div class="page-content">
            <div class="row">
                <div class="col-sm-3">
                    <?= UserWidget::widget() ?>
                </div>
                <div class="col-sm-9">
                    <div class="page-content page-order">
                        <h3>Chi tiết đơn hàng</h3>
                        <div class="heading-counter warning">Tổng số sản phẩm:
                            <span>1 Sản phẩm</span>
                        </div>
                        <div class="order-detail-content">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ./page wapper-->
