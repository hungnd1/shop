<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ServiceProvider */

$this->title = 'Tạo Nhà cung cấp dịch vụ';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách nhà cung cấp dịch vụ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">

        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i>Tạo Nhà cung cấp dịch vụ
                </div>
            </div>
            <div class="portlet-body form">
                <?= $this->render('_form_create', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
