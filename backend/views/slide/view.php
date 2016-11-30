<?php

use common\models\SlideContent;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SlideContent */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Slide Contents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$types = SlideContent::getListType();
$attributes = [
    [
        'attribute' => 'name',
    ],
    [
        'attribute' => 'type',
        'value' => isset($types[$model->type])?$types[$model->type]:'Unknown'
    ],
];
if ($model->type == SlideContent::SLIDE_TYPE_BANNER) {
    $attributes[] =
        [
            'attribute' => 'banner',
            'format' => 'html',
            'value' => \yii\helpers\Html::img($model->getBannerUrl(),['style' => 'height:320px'])
        ];
    $attributes[] =
        [
            'attribute' => 'open_url',
            'format' => 'raw',
            'value' => \yii\helpers\Html::a($model->open_url,$model->open_url, ['class' => 'label label-primary', 'target' => 'blank'])
        ];
}else{
    $attributes[] =
        [
            'attribute' => 'content_id',
            'format' => 'html',
            'value' => \yii\helpers\Html::a($model->content->display_name,['content/view', 'id' => $model->content->id], ['class' => 'label label-primary'])
        ];
}
$attributes[] = 'description:ntext';
$attributes[] = 'weight';
$attributes[] = 'open_count';
$attributes[] = 'rating_count';
$attributes[] = [
    'label' => 'Show on platforms',
    'value' => $model->getPlatforms()
];
$attributes[] = [
    'attribute' => 'status',
    'value' => ($model->status) ? Yii::t('app', 'Active') : Yii::t('app', 'Disable')
];
$attributes[] = 'created_at:datetime';
$attributes[] = 'updated_at:datetime';
?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase"><?php echo $model->name;?></span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse" data-original-title="" title="">
                    </a>

                </div>
            </div>
            <div class="portlet-body">
                <p>
                    <?= Html::a(Yii::t('app', 'Cập nhật'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('app', 'Xóa'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you want to delete this content?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => $attributes,
                ]) ?>
            </div>
        </div>
    </div>
</div>
