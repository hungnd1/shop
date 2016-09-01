<?php

use common\models\Service;
use common\models\Site;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use kartik\helpers\Html;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $report \backend\models\ReportUserDailyForm */
/* @var $this yii\web\View */

$this->title = 'Báo cáo số lượng thuê bao';
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
                                    'action' => Url::to(['report/subscriber-daily']),]
                            ); ?>

                            <div class="row">

                                <div class="col-md-12">
<!--                                    <div class="col-md-2">-->
<!--                                        --><?php //$form->field($report, 'type')->dropDownList($report->list_type,
//                                            ['id' => "typeTime", 'onchange' => 'onchangeTypeTime()']
//                                        ); ?>
<!--                                    </div>-->

                                    <div class="col-md-2">
                                        <?= $form->field($report, 'site_id')->dropDownList( ArrayHelper::merge(['' => 'Tất cả'],Site::listSite()), ['id'=>'site-id']); ?>
                                    </div>

                                    <div class="col-md-2">
<!--                                        --><?php //$form->field($report, 'service_id')->dropDownList(Service::lis()); ?>

                                        <?php
                                        /**
                                         * @var $services \common\models\Service[]
                                         */
                                        $dataList = [];
                                        $services = Service::find()->andWhere(['status' => Service::STATUS_ACTIVE,'site_id'=>$site_id])->all();
                                        foreach ($services as $service) {
                                            $dataList[$service->id] = $service->name;
                                        }
                                        echo $form->field($report, 'service_id')->widget(DepDrop::classname(),
                                            [
                                                'data' => $dataList,
                                                'type' => DepDrop::TYPE_SELECT2 ,
                                                'options' => ['id'=>'service-id','placeholder' => 'Tất cả'],
                                                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                                                'pluginOptions' => [
                                                    'depends' => ['site-id'],
                                                    'placeholder'=>'Tất cả',
                                                    'url' => Url::to(['/report/find-service-by-site']),
                                                ]
                                            ]);
                                        ?>
                                    </div>

                                    <div id="date">
                                        <div class="col-md-2">
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
                                        <div class="col-md-2">
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

                                    <div class="col-md-2">
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
                            'showPageSummary' => true,
                            'columns' => [
                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'report_date',
                                    'width' => '150px',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportUserDaily */
//                                        return DateTime::createFromFormat("Y-m-d H:i:s", $model->report_date)->format('d-m-Y');
                                        return !empty($model->report_date) ? date('d/m/Y', $model->report_date) : '';
                                    },
                                    'pageSummary' => "Tổng số"
                                ],

                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'total_subscriber',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportSubscriberDaily */
                                        return $model->total_subscriber?$model->total_subscriber:0;
                                    },
//                                    'pageSummary' => $report->content->sum('active_user') ? $report->content->sum('active_user') : 0
                                ],

                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'total_active_subscriber',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportSubscriberDaily */
                                        return $model->total_active_subscriber?$model->total_active_subscriber:0;
                                    },
//                                    'pageSummary' => $report->content->sum('active_user_package') ? $report->content->sum('active_user_package') : 0
                                ],

                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'subscriber_register_daily',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportSubscriberDaily */
                                        return $model->subscriber_register_daily?$model->subscriber_register_daily:0;
                                    },
                                    'pageSummary' => $report->dataProvider->query->sum('subscriber_register_daily')?$report->dataProvider->query->sum('subscriber_register_daily'):0
                                ],
                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'total_cancel_subscriber',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportSubscriberDaily */
                                        return $model->total_cancel_subscriber?$model->total_cancel_subscriber:0;
                                    },
//                                    'pageSummary' => $report->content->sum('active_user_package') ? $report->content->sum('active_user_package') : 0
                                ],
                                [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'subscriber_cancel_daily',
                                    'value' => function ($model) {
                                        /**  @var $model \common\models\ReportSubscriberDaily */
                                        return $model->subscriber_cancel_daily?$model->subscriber_cancel_daily:0;
                                    },
                                    'pageSummary' => $report->dataProvider->query->sum('subscriber_cancel_daily')?$report->dataProvider->query->sum('subscriber_cancel_daily'):0
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