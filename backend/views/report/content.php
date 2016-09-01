<?php

use common\models\Content;
use common\models\Site;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use kartik\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $report \backend\models\ReportContentForm */
/* @var $this yii\web\View */

$this->title = 'Báo cáo nội dung';
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-body">

                <div class="report-user-daily-index">

                    <div class="row form-group">
                        <div class="col-md-12 col-md-offset-0">
                            <?php $form = ActiveForm::begin(
                                    ['method' => 'get',
                                    'id' => 'report-content-id',
                                    'action' => Url::to(['report/content']),]
                                );
                                $formId = $form->id;
                            ?>

                            <div class="row">

                                <div class="col-md-12">

                                    <div class="col-md-3">
                                        <?= $form->field($report, 'site_id')->dropDownList( ArrayHelper::merge(['' => 'Tất cả'],Site::listSite()), ['id'=>'site-id']); ?>
                                    </div>

                                    <div class="col-md-3">
                                        <?= $form->field($report, 'content_type')->dropDownList( ArrayHelper::merge(['' => 'Tất cả'],Content::listType()), ['id'=>'content-type']); ?>
                                    </div>

                                    <div id="date">
                                        <div class="col-md-3">
                                            <?= $form->field($report, 'from_date')->widget(\kartik\widgets\DatePicker::classname(), [
                                                'options' => ['placeholder' => 'Ngày bắt đầu'],
                                                'type' => \kartik\widgets\DatePicker::TYPE_INPUT,
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'todayHighlight' => true,
                                                    'format' => 'dd/mm/yyyy'
                                                ]
                                            ]); ?>

                                        </div>
                                        <div class="col-md-3">
                                            <?= $form->field($report, 'to_date')->widget(\kartik\widgets\DatePicker::classname(), [
                                                'options' => ['placeholder' => 'Ngày kết thúc'],
                                                'type' => \kartik\widgets\DatePicker::TYPE_INPUT,
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'todayHighlight' => true,
                                                    'format' => 'dd/mm/yyyy'
                                                ]
                                            ]); ?>
                                        </div>
                                    </div>



<!--                                    <div class="col-md-2">-->
<!--                                        --><?php
//                                        /**
//                                         * @var $services \common\models\Service[]
//                                         */
//                                        $dataList = [];
//                                        $services = Category::find()->andWhere(['status' => Category::STATUS_ACTIVE,'site_id'=>$site_id,'type'=>1])->all();
//                                        foreach ($services as $service) {
////                                            $dataList[$service->id] = $service->display_name;
//                                            $dataList[$service->id] = str_pad($service->order_number,3,0,STR_PAD_LEFT).'-'.$service->path_name;;
//                                        }
//                                        echo $form->field($report, 'category_id')->widget(DepDrop::classname(),
//                                            [
//                                                'data' => $dataList,
//                                                'type' => DepDrop::TYPE_SELECT2 ,
//                                                'options' => ['id'=>'service-id','placeholder' => 'Tất cả'],
//                                                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
//                                                'pluginOptions' => [
//                                                    'depends' => ['site-id','content-type'],
//                                                    'placeholder'=>'Tất cả',
//                                                    'nameParam'=>'display_name',
//                                                    'url' => Url::to(['/report/find-category-by-site-content']),
//                                                ]
//                                            ]);
//                                        ?>
<!--                                    </div>-->

                                </div>
                                </div>

                                <div class="col-md-12">
<!--                                    <div class="col-md-12">-->
                                        <?php echo $form->field($report, 'categoryIds')->hiddenInput(['id' => 'categoryIds'])->label(false);?>
<!--                                    </div>-->
                                    <div class="col-md-12">
                                        <div class="portlet light">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs font-green-sharp"></i>
                                                    <span class="caption-subject font-green-sharp bold uppercase">Danh mục</span>
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
                                                    'type' =>$content_type,
                                                    'sp_id' => $site_id,
                                                    'data' => $selectedCats,
                                                    'eventHandles' => [
                                                        'changed.jstree' => "function(e,data) {
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
                                                         }"
                                                    ]
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div style="margin-top: 25px"></div>
                                        <?= Html::submitButton('Xem báo cáo', ['class' => 'btn btn-primary']) ?>
                                    </div>
                                </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>

                    <?php if ($report->content) { ?>
                        <?= GridView::widget([
                            'dataProvider' => $report->dataProvider,
                            'responsive' => true,
                            'pjax' => true,
                            'hover' => true,
                            'showPageSummary' => false,
                            'columns' => [
                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'report_date',
                                    'width' => '150px',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportContent */
//                                        return DateTime::createFromFormat("Y-m-d H:i:s", $model->report_date)->format('d-m-Y');
                                        return !empty($model->report_date) ? date('d-m-Y', $model->report_date) : '';
                                    },
                                    'pageSummary' => "Tổng số"
                                ],
                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'total_content',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportContent */
                                        return $model->total_content;
                                    },
                                ],
                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'count_content_upload_daily',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportContent */
                                        return $model->count_content_upload_daily;
                                    },
                                ],
                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'total_content_view',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportContent */
                                        return $model->total_content_view;
                                    },
                                ],
                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'total_content_buy',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportContent */
                                        return $model->total_content_buy;
                                    },
                                ],


                            ],
                            'panel' => [
                                'type' => GridView::TYPE_DEFAULT,
                            ],
                            'toolbar' => [
                                '{export}',
                            ],
                            'export' => [
                                'fontAwesome' => true,
                                'showConfirmAlert' => false,
                                'target' => GridView::TARGET_BLANK,

                            ],

                            'exportConfig' => [
                                GridView::EXCEL => ['label' => 'Excel','filename' => "Report"],
                            ],
                        ]); ?>
                    <?php }else{ ?>
                        <div class="portlet-body">
                            <div class="well well-sm">
                                <p>Không có dữ liệu</p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<<JS
    $("#site-id").change(function () {
        $("#report-content-id").submit();
      });

      $("#content-type").change(function () {
        $("#report-content-id").submit();
      });
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>