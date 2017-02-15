<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramSuppost */

$this->title = Yii::t('app','Cập nhật chương trình ưu đãi: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Ưu đãi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Cập nhật';
?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-gift"></i><?= $this->title?></div>
            </div>
            <div class="portlet-body form">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
