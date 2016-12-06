<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SlideContent */

$this->title = Yii::t('app', 'Create Slide Content');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Slide Contents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
                <?= $this->render('_form', [
                    'model' => $model,
                    'type'=>$type
                ]) ?>
            </div>
        </div>
    </div>
</div>
