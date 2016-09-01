<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ContentFeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Content Feedbacks';
$this->params['breadcrumbs'][] = $this->title;
\common\assets\ToastAsset::register($this);
\common\assets\ToastAsset::config($this, [
    'positionClass' => \common\assets\ToastAsset::POSITION_TOP_RIGHT
]);
?>


<?php
$approveUrl = \yii\helpers\Url::to(['content-feedback/approve']);
$rejectUrl = \yii\helpers\Url::to(['content-feedback/reject']);


$js = <<<JS
    function approveFeedback(){
    feedbacks = $("#content-feedback-grid").yiiGridView("getSelectedRows");
    if(feedbacks.length <= 0){
    alert("Chưa chọn feedback! Xin vui lòng chọn ít nhất một feedback để duyệt.");
    return;
    }

    jQuery.post(
        '{$approveUrl}',
        { ids:feedbacks }
    )
    .done(function(result) {
    if(result.success){
    toastr.success(result.message);
    jQuery.pjax.reload({container:'#content-feedback-grid'});
    }else{
    toastr.error(result.message);
    }
    })
    .fail(function() {
    toastr.error("server error");
    });
    }
JS;

$this->registerJs($js, \yii\web\View::POS_END);

$js = <<<JS
    function rejectFeedback(){
    feedbacks = $("#content-feedback-grid").yiiGridView("getSelectedRows");
    if(feedbacks.length <= 0){
    alert("Chưa chọn feedback! Xin vui lòng chọn ít nhất một feedback để duyệt.");
    return;
    }

    jQuery.post(
    '{$rejectUrl}',
    { ids:feedbacks }
    )
    .done(function(result) {
    if(result.success){
    toastr.success(result.message);
    jQuery.pjax.reload({container:'#content-feedback-grid'});
    }else{
    toastr.error(result.message);
    }
    })
    .fail(function() {
    toastr.error("server error");
    });
    }
JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Quản lý content feedback</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">


                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'id' => 'content-feedback-grid',
                    'responsive' => true,
                    'pjax' => true,
                    'hover' => true,
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => 'Danh sách Feedback'
                    ],
                    'toolbar' => [
                        [
                            'content' =>
                                Html::button('<i class="glyphicon glyphicon-ok"></i> Approve', [
                                    'type' => 'button',
                                    'title' => 'Duyệt feedback',
                                    'class' => 'btn btn-success',
                                    'onclick' => 'approveFeedback();'
                                ])

                        ],
                        [
                            'content' =>
                                Html::button('<i class="glyphicon glyphicon-minus"></i> Reject', [
                                    'type' => 'button',
                                    'title' => 'Từ chối feedback',
                                    'class' => 'btn btn-danger',
                                    'onclick' => 'rejectFeedback();'
                                ])

                        ],

                    ],
                    'columns' => [
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'subscriber_id',
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\ContentFeedback */

                                return $model->subscriber ?    Html::a($model->subscriber->msisdn, ['/subscriber/view', 'id' => $model->subscriber->id],['class'=>'label label-primary']) : '';
                            },
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'content_id',
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\ContentFeedback */

                                return $model->content0 ?    Html::a($model->content0->display_name, ['/content/view', 'id' => $model->content0->id],['class'=>'label label-primary']) : '';
                            },
                        ],
                        'content',
                        'created_at:datetime',
                        [
                            'class' => 'kartik\grid\EditableColumn',
                            'attribute' => 'status',
                            'width' => '200px',
                            'refreshGrid' => true,
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\ContentFeedback */

                                return $model->getStatusName();
                            },
                            'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'header' => 'Trạng thái',
                                    'size' => 'md',
                                    'displayValueConfig' => \common\models\ContentFeedback::$listStatus,
                                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                                    'data' => \common\models\ContentFeedback::$listStatus,
                                    'placement' => \kartik\popover\PopoverX::ALIGN_LEFT
                                ];
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => \common\models\ContentFeedback::$listStatus,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => 'Tất cả'],
                        ],

                        [
                            'class' => 'kartik\grid\EditableColumn',
                            'attribute' => 'admin_note',
                            'width' => '200px',
                            'refreshGrid' => true,
                            'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'header' => 'Admin Note',
                                    'size' => 'md',
                                    'value' => $model->admin_note,
                                    'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                                    'placement' => \kartik\popover\PopoverX::ALIGN_LEFT
                                ];
                            },

                        ],
                        [
                            'class' => 'kartik\grid\CheckboxColumn',
                            'headerOptions' => ['class' => 'kartik-sheet-style'],
                        ],

                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>