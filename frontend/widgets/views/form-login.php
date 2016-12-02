<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 12/2/2016
 * Time: 9:10 AM
 */
use common\models\User;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="row">
    <div class="col-sm-6">
        <div class="box-authentication">
            <div id="form_login">
                <h3 class="text-center">Đăng nhập</h3>
                <?php
                $form = ActiveForm::begin([
                    'action'=>\yii\helpers\Url::to(['site/login']),
                    'id' => 'login-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' =>false,
                ]);
                ?>

                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <button name = 'login-button' class="button"><i class="fa fa-lock"></i> Đăng nhập</button>
                <?php ActiveForm::end(); ?>
            </div>
            <div id="tp_hidden_form_register">
                <h3 class="text-center">Đăng kí</h3>
                <?php $form = ActiveForm::begin([
                    'action'=>\yii\helpers\Url::to(['site/signup']),
                    'id' => 'form-signup',
//                    'enableAjaxValidation' => false,
//                    'enableClientValidation' =>true,
                ]); ?>

                <?= $form->field($model_register, 'username')->textInput(['autofocus' => true,'placeholder'=>'Tên đăng nhập'])->label('Tên đăng nhập (*)') ?>

                <?= $form->field($model_register, 'email')->textInput(['autofocus' => true,'placeholder'=>'Email'])->label('Email (*)') ?>

                <?= $form->field($model_register, 'password')->passwordInput(['placeholder'=>'Mật khẩu'])->label('Mật khẩu (*)') ?>

                <?= $form->field($model_register, 'confirm_password')->passwordInput(['placeholder'=> 'Xác nhận mật khẩu'])->label('Nhập lại mật khẩu (*)') ?>

                <?= $form->field($model_register, 'phone_number')->textInput(['placeholder'=>'Số điện thoại']) ?>

                <?= $form->field($model_register, 'address')->textInput(['placeholder'=>'Địa chỉ']) ?>

                <?= $form->field($model_register, 'captcha')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-5">{image}</div><div class="col-lg-7">{input}</div></div>',
                ]) ?>

                <div class="re-and-log re-and-log2">
                    <?= $form->field($model_register,'accept')->checkbox()?>
                </div>

                <button name="signup-button" class="button"><i class="fa fa-user"></i> Đăng kí</button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box-authentication">
            <div class="row">
                <div class="col-md-2 col-sm-4 col-xs-4">
                    <?= yii\authclient\widgets\AuthChoice::widget([
                        'baseAuthUrl' => ['site/auth'],
                    ]) ?>
                </div>
                <div class="col-md-10 col-sm-8 col-xs-8">
                    <p class="tp_login_facebook" style="color: #176281">Đăng nhập bằng facebook</p>
                </div>
                <div class="col-xs-12">
                    <?= Html::a('Quên mật khẩu, cấp lại mật khẩu', ['site/request-password-reset']) ?>.
                </div>
            </div>
            <button id="bt_tp_show" class="button"><i class="fa fa-user"></i> Đăng kí</button>
            <button id="bt_tp_hide" class="button"><i class="fa fa-lock"></i> Đăng nhập</button>
        </div>
    </div>
</div>