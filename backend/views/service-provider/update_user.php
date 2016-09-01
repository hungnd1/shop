<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Cập nhật người dùng: ' . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách nhà cung cấp dịch vụ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->site->name, 'url' => ['view', 'id' => $model->site_id]];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view-user', 'id' => $model->id]];
$this->params['breadcrumbs'][] = "Cập nhật";
?>
<div class="row">
    <div class="col-md-12">

        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i><?= $this->title ?>
                </div>
            </div>
            <div class="portlet-body form">
                <?= $this->render('_form_user', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>

        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i>Thay đổi mật khẩu
                </div>
            </div>
            <div class="portlet-body form">
                <?= $this->render('_form_change_password', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
