<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Subcriber */

$this->title = Yii::t('app','Cập nhật khách hàng: ' ). $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý khách hàng'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Cập nhật');
?>
<div class="row">
    <div class="col-md-12">

        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i><?= Html::encode($this->title) ?>
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
