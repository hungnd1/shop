<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 12/2/2016
 * Time: 11:20 PM
 */
use common\models\User;
use frontend\widgets\UserWidget;
use kartik\date\DatePicker;
use kartik\widgets\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model common\models\User */
$avatarPreview = $model->isNewRecord;
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
            <span class="navigation_page">Cập nhật</span>
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
                        <div class="heading-counter warning">
                            <h3>Cập nhật thông tin</h3>
                        </div>
                        <div class="col-md-6 col-md-offset-3 box-authentication tp_001">
                            <?php $form = ActiveForm::begin([
                                'options' => ['enctype' => 'multipart/form-data'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' =>false,
                            ]); ?>
                            <div class="text-center">
                                <img style="width: 80px" src="<?= $model->getImageLink() ?>"><br>
                            </div>
                            <?php if($model->username == '' || $model->username ==  null){ ?>
                                <?= $form->field($model, 'username')->textInput(['placeholder' => 'Tên đăng nhập']) ?>
                            <?php }else{ ?>
                                <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>
                            <?php } ?>

                            <?= $form->field($model, 'fullname')->textInput(['placeholder' => 'Họ và Tên', 'maxlength' => 100]) ?>

                            <?= $form->field($model, 'address')->textInput(['placeholder' => 'Địa chỉ', 'maxlength' => 100]) ?>

                            <?= $form->field($model, 'email')->textInput(['placeholder' => 'Email', 'maxlength' => 100]) ?>

                            <?= $form->field($model, 'phone_number')->textInput(['placeholder' => 'Số điện thoại', 'maxlength' => 100]) ?>

                            <?=
                            $form->field($model, 'gender')->dropDownList([
                                'Chọn giới tính' => User::listGender(),
                            ])
                            ?>

                            <?=
                            $form->field($model, 'birthday')->widget(DatePicker::classname(), [
                                'options' => ['placeholder' => 'Chọn ngày sinh'],
                                'pluginOptions' => [
                                    'autoclose'=>true
                                ]
                            ])
                            ?>

                            <?=
                            $form->field($model, 'image')->widget(FileInput::classname(), [
                                'pluginOptions' => [

                                    'showCaption' => false,
                                    'showRemove' => false,
                                    'showUpload' => false,
                                    'browseClass' => 'btn btn-primary btn-block',
                                    'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                                    'browseLabel' => 'Chọn hình ảnh đại diện',
                                    'initialPreview' => $avatarPreview
                                ],
                                'options' => [
                                    'accept' => 'image/*',
                                ],
                            ])
                            ?>

                            <div class="text-center">
                                <?= Html::submitButton('Cập nhật',['class' => 'button']) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ./page wapper-->
