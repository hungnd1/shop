<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Content */

$this->title = 'Tạo nội dung';
$this->params['breadcrumbs'][] = ['label' => 'Nội dung', 'url' => Yii::$app->urlManager->createUrl(['content/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">

        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i>Tạo nội dung
                </div>
            </div>
            <div class="portlet-body form">
                <?= $this->render('_form', [
                    'model' => $model,
                    'logoInit' => $logoInit,
                    'logoPreview' => $logoPreview,
                    'thumbnail_epgPreview'=>$thumbnail_epgPreview,
                    'thumbnail_epgInit'=>$thumbnail_epgInit,
                    'thumbnailInit' => $thumbnailInit,
                    'slideInit' => $slideInit,
                    'slidePreview' => $slidePreview,
                    'thumbnailPreview' => $thumbnailPreview,
                    'screenshootInit' => $screenshootInit,
                    'screenshootPreview' => $screenshootPreview,
                    'selectedCats' => $selectedCats,
                ]) ?>
            </div>
        </div>
    </div>
</div>
