<?php

use common\models\SlideContent;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Slide */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Quản lý Slide'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$attributes = [
    [
        'attribute' => 'id',
    ],
];
    $attributes[] =
        [
            'attribute' => 'content_id',
            'format' => 'html',
            'value' => \yii\helpers\Html::a($model->content->display_name,['content/view', 'id' => $model->content->id], ['class' => 'label label-primary'])
        ];
$attributes[] = 'des:ntext';
$attributes[] = [
    'attribute' => 'status',
    'value' => ($model->status) ? Yii::t('app', 'Hoạt động') : Yii::t('app', 'Tạm khóa')
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
                    <span class="caption-subject font-green-sharp bold uppercase"><?php echo $model->id;?></span>
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
                            'confirm' => Yii::t('app', 'Bạn chắc chắn muốn xóa?'),
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
