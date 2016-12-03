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
            <a class="home" href="<?= Url::to(['site/index']) ?>" title="Return to Home">Home</a>
            <span class="navigation-pipe">&nbsp;</span>
            <span class="navigation_page"><?= $model->fullname?$model->fullname:$model->username ?></span>
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
                        <h3>Đơn hàng</h3>
                        <div class="heading-counter warning">Tổng số đơn hàng:
                            <span>1 Đơn hàng</span>
                        </div>
                        <div class="order-detail-content">
                            <table class="table table-bordered table-responsive cart_summary">
                                <thead>
                                <tr>
                                    <th class="cart_product">Product</th>
                                    <th>Mô tả</th>
                                    <th>Trạng thái</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                    <th>Voucher</th>
                                    <th>Số tiền thanh toán</th>
                                    <th>Xem</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="cart_product">
                                        <a href="#"><img src="../../web/data/product-100x122.jpg" alt="Product"></a>
                                    </td>
                                    <td class="cart_description">
                                        <p class="product-name"><a href="#">Frederique Constant </a></p>
                                        <small class="cart_ref">SKU : #123654999</small><br>
                                        <small><a href="#">Color : Beige</a></small><br>
                                        <small><a href="#">Size : S</a></small>
                                    </td>
                                    <td class="cart_avail"><span class="label label-success">In stock</span></td>
                                    <td class="qty">
                                        <span>1</span>
                                    </td>
                                    <td class="price">
                                        <span>61,19 €</span>
                                    </td>
                                    <td class="price">
                                        <span>0%</span>
                                    </td>
                                    <td class="price">
                                        <span>61,19 €</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= Url::to(['user/detail-order','id'=> 1]) ?>"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ./page wapper-->
