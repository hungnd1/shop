<?php

use common\models\Site;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\Url;

/* @var $report \backend\models\ReportUserDailyForm */
/* @var $this yii\web\View */

$this->title = 'Thống kê người dùng';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
    function onchangeTypeTime(){
        var value =$('#typeTime').val();
         if(value ==1){
            $("#date").show();
            $("#month").hide();
        }else if(value ==2){
            $("#date").hide();
            $("#month").show();
        }
    }
    $(document).ready(function () {
        onchangeTypeTime();
    });
JS;
$this->registerJs($js, \yii\web\View::POS_END);
$this->registerJs('onchangeTypeTime()');
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
                                    'action' => Url::to(['report/user-daily']),]
                            ); ?>

                            <div class="row">

                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <?= $form->field($report, 'type')->dropDownList($report->list_type,
                                            ['id' => "typeTime", 'onchange' => 'onchangeTypeTime()']
                                        )->label("Loại báo cáo"); ?>
                                    </div>

                                    <div class="col-md-3">
                                        <?= $form->field($report, 'site_id')->dropDownList(Site::listSite()); ?>
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
                                            ])->label('Từ ngày'); ?>

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
                                            ])->label('Đến ngày'); ?>
                                        </div>
                                    </div>
                                    <div id="month">
                                        <div class="col-md-3">
                                            <?= $form->field($report, 'from_month')->widget(\kartik\widgets\DatePicker::classname(), [
                                                'options' => ['placeholder' => 'Ngày bắt đầu'],
                                                'type' => \kartik\widgets\DatePicker::TYPE_INPUT,
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'mm/yyyy',
                                                    'todayHighlight' => true,
                                                ]
                                            ])->label('Từ tháng'); ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?= $form->field($report, 'to_month')->widget(\kartik\widgets\DatePicker::classname(), [
                                                'options' => ['placeholder' => 'Ngày kết thúc'],
                                                'type' => \kartik\widgets\DatePicker::TYPE_INPUT,
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'todayHighlight' => true,
                                                    'format' => 'mm/yyyy'
                                                ]
                                            ])->label('Đến tháng'); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div style="margin-top: 25px"></div>
                                        <?= \yii\helpers\Html::submitButton('Xem báo cáo', ['class' => 'btn btn-primary']) ?>
                                    </div>

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
                                    'label' => 'Ngày báo cáo',
                                    'width' => '150px',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportUserDaily */
//                                        return DateTime::createFromFormat("Y-m-d H:i:s", $model->report_date)->format('d-m-Y');
                                        return !empty($model->report_date) ? date('d-m-Y', $model->report_date) : '';
                                    },
//                                    'pageSummary' => "Tổng số"
                                ],

                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'active_user',
                                    'label' => 'Số lượng người dùng tích lũy',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportUserDaily */
                                        return $model->active_user;
                                    },
//                                    'pageSummary' => $report->content->sum('active_user') ? $report->content->sum('active_user') : 0
                                ],

                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'active_user_package',
                                    'label' => 'Số lượng thuê bao',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportUserDaily */
                                        return $model->active_user_package;
                                    },
//                                    'pageSummary' => $report->content->sum('active_user_package') ? $report->content->sum('active_user_package') : 0
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
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>