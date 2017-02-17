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
            'title_short',
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
                'value' => date('d-m-Y H:i:s', $model->created_at)
            ],
            [
                'attribute' => 'updated_at',
                'value' => date('d-m-Y H:i:s', $model->updated_at)
            ],
            [
                'attribute' => 'honor',
                'format'=>'html',
                'value' =>"<span class='label label-primary'>" . Content::$list_honorDetail[$model->honor]."</span>"
            ],
            [
                'attribute' => 'type',
                'format'=>'html',
                'value' =>"<span class='label label-primary'>" . Content::$list_type[$model->type]."</span>"
            ],
            [
                'attribute' => 'type_status',
                'format'=>'html',
                'value' =>"<span class='label label-primary'>" . Content::$listTypeStatus[$model->type_status] ."</span>"
            ],
            [
                'attribute' => 'availability',
                'format'=>'html',
                'value' =>"<span class='label label-primary'>" .Content::$listAvailability[$model->availability]."</span>"
            ],
            'order',
            [
                'label' => Yii::t('app','Ngày phê duyệt'),
                'value' => $model->approved_at?date('d-m-Y H:i:s', $model->approved_at):''
            ],
        ];


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
