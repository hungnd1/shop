<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ContentProfile */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Content Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-profile-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'name',
            'url:url',
            'description:html:Mô tả',
            [
                'label' => 'Loại',
                'value' => $model->getTypeName()
            ],
            [
                'label' => 'Trạng thái',
                'value' => $model->getStatusName()
            ],
            'created_at:datetime',
            'updated_at:datetime',
            'bitrate',
            'width',
            'height',
            [
                'label' => 'Quality',
                'value' => $model->getQualityName()
            ],
            'progress',
        ],
    ]) ?>

</div>
