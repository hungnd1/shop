<?php

use common\models\Category;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Quản lý danh mục ');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase"><?= $this->title ?></span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <p>
                    <?php  echo Html::a("Tạo danh mục ", Yii::$app->urlManager->createUrl(['/category/create']), ['class' => 'btn btn-success']) ?>
                </p>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'id'=>'grid-category-id',
//                    'filterModel' => $searchModel,
                    'responsive' => true,
                    'pjax' => true,
                    'hover' => true,
                    'columns' => [
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'path_name',
                            'label' => Yii::t('app','Tên danh mục'),
                            'value'=>function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Category */
                                return $model->path_name;
                            },
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'format'=>'raw',
                            'label'=>Yii::t('app','Ảnh đại diện'),
                            'attribute' => 'images',
                            'value'=>function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Category */
                                $cat_image=  Yii::getAlias('@cat_image');
                                return $model->images ? Html::img('@web/'.$cat_image.'/'.$model->images, ['alt' => 'Thumbnail','width'=>'50','height'=>'50']) : '';
                            },
                        ],
                        [
                            'class' => 'kartik\grid\EditableColumn',
                            'attribute' => 'status',
                            'refreshGrid' => true,
                            'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'header' => Yii::t('app','Trạng thái'),
                                    'size' => 'md',
                                    'displayValueConfig' => $model->listStatus,
                                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                                    'data' => $model->listStatus,
                                    'placement' => \kartik\popover\PopoverX::ALIGN_LEFT
                                ];
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => [0 => 'InActive', 10 => 'Active'],
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => Yii::t('app','Tất cả')],
                        ],
                        [
                            'class' => 'kartik\grid\DataColumn',
                            'attribute' => 'type',
                            'value' => function ($model, $key, $index) {
                                return $model->type?Category::getListType()[$model->type]:'';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => Category::getListType(),
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => Yii::t('app','Tất cả')],
                        ],
                        [
                            'class' => 'kartik\grid\DataColumn',
                            'attribute' => 'location_image',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model Category*/
                                return Category::getLocationImage()[$model->location_image];
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => Category::getLocationImage(),
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => Yii::t('app','Tất cả')],
                        ],
                        [
                            'format'=>'raw',
                            'label'=>Yii::t('app','Sắp xếp'),
                            'attribute' => 'order_number',
                            'value'=>function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Category */
                                $up= Html::tag('i','',['class'=>' icon-arrow-up font-green','onclick'=>"js:moveCategory(1,$model->id)"]);
                                $down=Html::tag('i','',['class'=>' icon-arrow-down font-green','onclick'=>"js:moveCategory(2,$model->id)"]);
                                $result='';
                                switch($model->checkPositionOnTree()){
                                    case 1:
                                        $result=$down;
                                        break;
                                    case 2:
                                        $result=$up;
                                        break;
                                    case 3:
                                        $result='';
                                        break;
                                    default:
                                        $result=$up.'&nbsp;&nbsp;&nbsp;&nbsp;'.$down;
                                }
                                return $result;
                            },
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'buttons'=> [
                                'delete' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Yii::$app->urlManager->createUrl(['category/delete','id'=>$model->id]), [
                                        'title' => Yii::t('yii', 'Delete'),
                                        'data-confirm' => Yii::t('app', 'Bạn có chắc chắn xóa danh mục này?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                                }
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

<?php
$urlCategory=Yii::$app->urlManager->createUrl("category");
Yii::info($urlCategory);
$js=<<<JS

function moveCategory(urlType,id) {
    var url;
    switch (urlType) {
        case 1:
            url = "move-up";
            break;
        case 2:
            url = "move-down";
            break;
        case 3:
            url = "move-back";
            break;
        case 4:
            url = "move-forward";
            break;
    }
    $.ajax({

        type:'GET',
        url: '{$urlCategory}'+'/'+ url,

        data: {'id':id},
        success:function(data) {
            $.pjax.reload({container:'#grid-category-id'});

        }
    });
}
JS;
$this->registerJs($js,$this::POS_HEAD);
