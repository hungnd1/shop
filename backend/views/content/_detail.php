<?php
use kartik\detail\DetailView;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Content;

/**
 * @var \common\models\Content $model
 */
?>
<?php
$grid = [
            [
                'attribute' => 'display_name',
            ],
            'short_description:html',
            'description:html',
            'content:html',
            [
                'attribute' => 'status',
                'format'=>'html',
                'value' =>"<span class='".$model->getCssStatus()."'>" . $model->getStatusName()."</span>"
            ],
            'tags',
            [
                'attribute' => 'created_at',
                // 'label' => 'Ngày tạo',
                'value' => date('d-m-Y H:i:s', $model->created_at)
            ],
            [
                'attribute' => 'updated_at',
                // 'label' => 'Ngày cập nhật',
                'value' => date('d-m-Y H:i:s', $model->updated_at)
            ],
            [
                'attribute' => 'honor',
                'format'=>'html',
                'value' =>"<span class='label label-primary'>" . Content::$list_honor[$model->honor]."</span>"
            ],
            'order',
            [
                'label' => 'Ngày phê duyệt',
                'value' => $model->approved_at?date('d-m-Y H:i:s', $model->approved_at):''
            ],
        ];

if($model->type == common\models\Category::TYPE_LIVE_CONTENT){
    array_splice($grid, 1, 0, [
        [
            'label' => 'Live Channel',
            'format' => 'html',
            'value' => Html::a(common\models\Content::findOne($model->livePrograms0[0]->channel_id)->display_name,
                Url::to(['content/view', 'id' => $model->livePrograms0[0]->channel_id]),
                ['class' => 'label label-primary'])
        ]
    ]);
    // var_dump($grid);die;
}
$grid = array_merge($grid, $model->viewAttr);

 ?>
<?= DetailView::widget([
    'model' => $model,
    'condensed' => true,
    'hover' => true,
    'mode' => DetailView::MODE_VIEW,
    'labelColOptions' => ['style' => 'width: 20%'],
    'attributes' => $grid
]) ?>
