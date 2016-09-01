<?php

use common\models\Content;
use common\models\Languages;
use common\models\Category;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ContentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Content';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('UITree.init();');

\common\assets\ToastAsset::register($this);
\common\assets\ToastAsset::config($this, [
    'positionClass' => \common\assets\ToastAsset::POSITION_TOP_RIGHT
]);
$publishStatus=\common\models\Content::STATUS_PENDING;
// $unPublishStatus=\common\models\Content::STATUS_DRAFT;
$unPublishStatus=\common\models\Content::STATUS_INVISIBLE;
$hideStatus= \common\models\Content::STATUS_INVISIBLE;
$showStatus= \common\models\Content::STATUS_ACTIVE;
$deleteStatus= \common\models\Content::STATUS_DELETE;
?>

<?php
$approveUrl = \yii\helpers\Url::to(['content/approve']);
$rejectUrl = \yii\helpers\Url::to(['content/reject']);


$js = <<<JS
    function approveContent(){
    feedbacks = $("#content-index-grid").yiiGridView("getSelectedRows");
    if(feedbacks.length <= 0){
    alert("Chưa chọn content! Xin vui lòng chọn ít nhất một content để duyệt.");
    return;
    }

    jQuery.post(
        '{$approveUrl}',
        { ids:feedbacks }
    )
    .done(function(result) {
    if(result.success){
    toastr.success(result.message);
    jQuery.pjax.reload({container:'#content-index-grid'});
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
    function rejectContent(){
    feedbacks = $("#content-index-grid").yiiGridView("getSelectedRows");
    if(feedbacks.length <= 0){
    alert("Chưa chọn content! Xin vui lòng chọn ít nhất một content để duyệt.");
    return;
    }

    jQuery.post(
    '{$rejectUrl}',
    { ids:feedbacks }
    )
    .done(function(result) {
    if(result.success){
    toastr.success(result.message);
    jQuery.pjax.reload({container:'#content-index-grid'});
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

<?php
$updateLink = \yii\helpers\Url::to(['content/update-status-content']);

$js = <<<JS
    function updateStatusContent(newStatus){
        feedbacks = $("#content-index-grid").yiiGridView("getSelectedRows");

        if(feedbacks.length <= 0){
            alert("Chưa chọn content! Xin vui lòng chọn ít nhất một content để cập nhật.");
            return;
        }
        var delConfirm = true;

        if(newStatus == 2){
            delConfirm = confirm('Bạn có muốn xóa không?');
        }

        if(delConfirm){
            jQuery.post(
                '{$updateLink}',
                { ids:feedbacks ,newStatus:newStatus}
            )
            .done(function(result) {
                if(result.success){
                    toastr.success(result.message);
                    jQuery.pjax.reload({container:'#content-index-grid'});
                }else{
                    toastr.error(result.message);
                }
            })
            .fail(function() {
                toastr.error("server error");
            });
        }

        return;
    }
JS;

$this->registerJs($js, \yii\web\View::POS_HEAD);
?>
<div class="row">
    <div class="col-md-3 col-sm-12">
        <?php
        $form = ActiveForm::begin([
            'method' => 'get',
            'id' => 'Form_Grid_Content',
            'type' => ActiveForm::TYPE_VERTICAL,
            'fullSpan' => 12,
            'formConfig' => [
                'showLabels' => false,
                'labelSpan' => 2,
                'deviceSize' => ActiveForm::SIZE_SMALL,
            ],
        ]);
        $formId = $form->id;
        echo $form->field($searchModel, 'categoryIds')->hiddenInput(['id' => 'categoryIds'])->label(false);
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i><?= "Tìm kiếm" ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"> </a>
                </div>
            </div>
            <div class="portlet-body clearfix">
                <?= $form->field($searchModel, 'keyword')->textInput(['placeholder' => 'Tìm kiếm theo keyword', 'class' => 'input-circle']); ?>
            </div>
        </div>
        <?php
        // truong hop SP view
        // if ($site_id && !$dealer_id):
            if(false):?>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs font-green-sharp"></i><?= "Tìm kiếm theo CP" ?>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                    </div>
                </div>
                <div class="portlet-body clearfix">
                    <?= $form->field($searchModel, 'cp_id')->dropDownList(ArrayHelper::merge(['' => 'Tất cả'], ArrayHelper::map(\common\models\ContentProvider::find()->andWhere(['site_id' => $site_id])->asArray()->all(), 'id', 'name')), ['placeholder' => 'Tìm kiếm theo CP', 'class' => 'input-circle', 'onChange' => 'submitForm(this)']); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Danh sách danh mục</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>


            <div class="portlet-body">
                <?= \common\widgets\Jstree::widget([
                    'clientOptions' => [
                        "checkbox" => ["keep_selected_style" => false],
                        "plugins" => ["checkbox"]
                    ],
                    'data' => $selectedCats,
                    'eventHandles' => [
                        'changed.jstree' => "function(e,data) {
                            jQuery(\"[name^='VideoSearch[categoryIds][]']\").attr('checked',null);
                            var i, j, r = [];
                            var catIds='';
                            for(i = 0, j = data.selected.length; i < j; i++) {
                                var item = $(\"#\" + data.selected[i]);
                                var value = item.attr(\"id\");
                                if(i==j-1){
                                    catIds += value;
                                } else{
                                    catIds += value +',';

                                }
                            }
                            jQuery(\"#categoryIds\").val(catIds);
                            jQuery(\"#" . $formId . "\").submit();
                         }"
                    ]
                ]) ?>
            </div>
        </div>
        <?php
        $form->end();
        ?>
    </div>
    <div class="col-md-9 col-sm-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span
                        class="caption-subject font-green-sharp bold uppercase"> Danh sách content </span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <p>
                    <?php  echo Html::a('Tạo ' , Yii::$app->urlManager->createUrl(['content/create']), ['class' => 'btn btn-success']) ?>
                </p>
                <?php
                $gridColumn = [
                    [
                        'class' => '\kartik\grid\DataColumn',
                        'format' => 'raw',
                        'label' => 'Ảnh',
                        'value' => function ($model, $key, $index, $widget) {
                            /** @var $model \common\models\Content */

                            $link = $model->getFirstImageLink();
                            return $link ? Html::img($link, ['alt' => 'Thumbnail', 'width' => '50', 'height' => '50']) : '';

                        },
                    ],
                    [
                        'format' => 'raw',
                        'class' => '\kartik\grid\DataColumn',
                        'attribute' => 'display_name',
                        'value' => function ($model, $key, $index) {
                            return Html::a($model->display_name, ['view', 'id' => $model->id],['class'=>'label label-primary']);
                        },
                    ],
                    [
                        'format' => 'raw',
                        'class' => '\kartik\grid\DataColumn',
                        'width'=>'15%',
                        'label' => 'Ngày tạo',
                        'filterType' => GridView::FILTER_DATE,
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return date('d-m-Y H:i:s', $model->created_at);
                        }
                    ],
                    // [
                    //     'class' => '\kartik\grid\DataColumn',
                    //     'attribute' => 'language',
                    //     'width'=>'15%',
                    //     'filterType' => GridView::FILTER_SELECT2,
                    //     'filter' => Languages::$language,
                    //     'filterWidgetOptions' => [
                    //         'pluginOptions' => ['allowClear' => true],
                    //     ],
                    //     'filterInputOptions' => ['placeholder' => 'Tất cả'],
                    //     'value' => function($model, $key, $index){
                    //         /**
                    //          * @var $model Content
                    //          */
                    //         return isset(Languages::$language[$model->language])?Languages::$language[$model->language]:'N/A';
                    //     },
                    // ],
                    [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'status',
                        'width' => '200px',
                        'refreshGrid' => true,
                        'editableOptions' => function ($model, $key, $index) {
                            return [
                                'header' => 'Trạng thái',
                                'size' => 'md',
                                'displayValueConfig' =>\common\models\Content::getListStatus('filter'),
                                'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                                'data' => \common\models\Content::getListStatus('filter'),
                                'placement' => \kartik\popover\PopoverX::ALIGN_LEFT,
                                'formOptions' => [
                                    'action' => ['content/update-status', 'id' => $model->id]
                                ],
                            ];
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => \common\models\Content::getListStatus('filter'),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],

                        'filterInputOptions' => ['placeholder' => 'Tất cả'],
                    ],

                ];

//                    $gridColumn[] = [
//                        'class' => '\kartik\grid\DataColumn',
//                        'attribute' => 'is_series',
//                        'width' => '200px',
//                        'filterType' => GridView::FILTER_SELECT2,
//                        'filter' => \common\models\Content::$filmType,
//                        'filterWidgetOptions' => [
//                            'pluginOptions' => ['allowClear' => true],
//                        ],
//                        'filterInputOptions' => ['placeholder' => 'Tất cả'],
//                        'value' => function ($model, $key, $index) {
//                            /** @var $model \common\models\Content */
//                            return \common\models\Content::$filmType[$model->is_series];
//                        }
//                    ];



                $gridColumn[] = [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{view}&nbsp;{update}',
                ];
                $gridColumn[] = [
                    'class' => 'kartik\grid\CheckboxColumn',
                    'headerOptions' => ['class' => 'kartik-sheet-style'],
                ];
                ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'id'=>'content-index-grid',
                    'filterModel' => $searchModel,
                    'responsive' => true,
                    'pjax' => true,
                    'hover' => true,
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => 'Danh sách Nội dung'
                    ],
                    'toolbar' => [
                        // [
                        //     'content' =>
                        //         Html::button('<i class="glyphicon glyphicon-ok"></i> Approve', [
                        //             'type' => 'button',
                        //             'title' => 'Duyệt content',
                        //             'class' => 'btn btn-success',
                        //             'onclick' => 'approveContent();'
                        //         ])
                        //
                        // ],
                        // [
                        //     'content' =>
                        //         Html::button('<i class="glyphicon glyphicon-minus"></i> Reject', [
                        //             'type' => 'button',
                        //             'title' => 'Từ chối content',
                        //             'class' => 'btn btn-danger',
                        //             'onclick' => 'rejectContent();'
                        //         ])
                        //
                        // ],
                        [
                            'content' =>
                                Html::button('<i class="glyphicon glyphicon-ok"></i> Publish', [
                                    'type' => 'button',
                                    'title' => 'Publish',
                                    'class' => 'btn btn-success',
                                    'onclick' => 'updateStatusContent("'.$showStatus.'");'
                                ])

                        ],
                        [
                            'content' =>
                                Html::button('<i class="glyphicon glyphicon-minus"></i> Unpublish', [
                                    'type' => 'button',
                                    'title' => 'Unpublish',
                                    'class' => 'btn btn-danger',
                                    'onclick' => 'updateStatusContent("'.$unPublishStatus.'");'
                                ])

                        ],
                        // [
                        //     'content' =>
                        //         Html::button('<i class="glyphicon glyphicon-eye-close"></i> Hide', [
                        //             'type' => 'button',
                        //             'title' => 'Hide',
                        //             'class' => 'btn bg-grey-gallery',
                        //             'onclick' => 'updateStatusContent("'.$hideStatus.'");'
                        //         ])

                        // ],
                        // [
                        //     'content' =>
                        //         Html::button('<i class="glyphicon glyphicon-eye-open"></i> Show', [
                        //             'type' => 'button',
                        //             'title' => 'Unpublish',
                        //             'class' => 'btn bg-green-jungle',
                        //             'onclick' => 'updateStatusContent("'.$showStatus.'");'
                        //         ])

                        // ],
                        [
                            'content' =>
                                Html::button('<i class="glyphicon glyphicon-trash"></i> Delete', [
                                    'type' => 'button',
                                    'title' => 'Delete',
                                    'class' => 'btn btn-danger',
                                    'onclick' => 'updateStatusContent("'.$deleteStatus.'");'
                                ])

                        ],

                    ],
                    'columns' => $gridColumn
                ]); ?>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<<JS
function submitForm(){
jQuery("#Form_Grid_Content").submit();
}
JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);
?>
