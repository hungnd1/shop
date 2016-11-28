<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ServiceProviderApiCredential */

$this->title = 'Tạo API KEY';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách API KEY', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">

        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i>Tạo API KEY
                </div>
            </div>
            <div class="portlet-body form">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
