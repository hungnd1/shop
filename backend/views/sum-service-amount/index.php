<?php

use kartik\helpers\Html;
use kartik\grid\GridView;
use common\models\ServiceProvider;
use kartik\form\ActiveForm;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\SubscriberTransaction;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SumServiceAmountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Thống kê doanh thu nhà cung cấp dịch vụ';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$js = <<<JS
    function onchangeTypeTime(){
    var current_value =$('#typeTime input:checked').val();
         if(current_value ==1){
            $("#divYears").show();
            $("#divMonths").hide();
            $("#divDates").hide();
        }else if(current_value ==2){
            $("#divMonths").show();
            $("#divYears").show();
            $("#divDates").hide();
        }else if(current_value ==3){
            $("#divDates").show();
            $("#divMonths").hide();
            $("#divYears").hide();
        }
    }
JS;
$this->registerJs($js, \yii\web\View::POS_END);
$this->registerJs('onchangeTypeTime()');
?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Thống kê doanh thu nhà cung cấp dịch vụ</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <?php
                    $form = ActiveForm::begin([
                        'method' => 'post',
                        'type' => ActiveForm::TYPE_VERTICAL,
                        'action' => ['sum-service-amount/index'],
                    ]);
                    $formId = $form->id;
                ?>
                <?php
                    $items = array( 1 => 'Báo cáo theo năm',2 => 'Báo cáo theo tháng', 3 => 'Báo cáo theo khoảng thời gian');
                ?>
                <?=
                    Html::radioList('typeTime',$typeTime,$items,['id'=>"typeTime",'onclick' => 'onchangeTypeTime()','separator' => '<br/>','class' =>'radio-inline', 'labelOptions' => array('style' => 'display:inline;width:250px;')])
                ?>

                <div><br/></div>
                <div class="col-md">
                    <div class="portlet light">
                        <div class="portlet-body">

                            <div id = "divMonths">
                                <label class="control-label col-md-1">Tháng </label>
                                <div>
                                    <?php
                                    $arrMonths = ['1' => 'Tháng 1', '2' => 'Tháng 2', '3' => 'Tháng 3', '4' => 'Tháng 4', '5' => 'Tháng 5', '6' => 'Tháng 6', '7' => 'Tháng 7', '8' => 'Tháng 8', '9' => 'Tháng 9', '10' => 'Tháng 10', '11' => 'Tháng 11', '12' => 'Tháng 12'];
                                    echo Html::dropdownList('divMonths', $monthIndex, $arrMonths);
                                    ?>
                                </div>
                                <div><br /></div>
                            </div>
                            <div id = "divYears">
                                <label class="control-label col-md-1">Năm </label>
                                <div >
                                    <?php
                                    $arrYears = ['1' => '2015', '2' => '2016', '3' => '2017', '4' => '2018', '5' => '2019', '6' => '2020', '7' => '2021', '8' => '2022', '9' => '2023', '10' => '2024', '11' => '2025', '12' => '2026'];
                                    echo Html::dropdownList('divYears', $yearIndex, $arrYears);
                                    ?>
                                </div>
                            </div>

                            <div id="divDates" name ="divDates">
                                <?php
                                echo DatePicker::widget([
                                    'name' => 'from_date',
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'options' => ['placeholder' => 'Từ ngày...'],
                                    //                                        'value' => '23-Feb-1982 12:35 AM',
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'todayHighlight' => true,
                                        //                            'todayBtn' => true,
                                        'autoclose'=>true,
                                        'format' => 'dd-M-yyyy'
                                        //                            'format' => 'yyyy-M-dd'
                                    ]
                                ]);

                                echo DatePicker::widget([
                                    'name' => 'to_date',
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'options' => ['placeholder' => 'Đến ngày...'],
                                    //                                        'value' => '23-Feb-1982 12:35 AM',
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'todayHighlight' => true,
                                        //                            'todayBtn' => true,
                                        'autoclose'=>true,
                                        'format' => 'dd-M-yyyy'
                                        //                            'format' => 'yyyy-M-dd'
                                    ]
                                ]);
                                ?>
                            </div>

                        </div>
                    </div>
                </div>

                <div id = "divTypeTransaction">
                    <div class="col-md">
                        <!-- BEGIN SAMPLE TABLE PORTLET-->
                        <div class="portlet light">
                            <div class="portlet-body">
                                <?= Html::label("Nhà cung cấp: ") ;?>
                                <?= Html::dropDownList('site_id',$site_id, ArrayHelper::merge(['' => 'Tất cả'], ArrayHelper::map(ServiceProvider::find()->andWhere(['status'=>ServiceProvider::STATUS_ACTIVE])->asArray()->all(), 'id', 'name') )); ?>
                            </div>
                        </div>
                        <!-- END SAMPLE TABLE PORTLET-->
                    </div>
                </div>


                <div class="portlet-body">
                    <?php echo Html::submitButton("Tìm kiếm", ['class' => 'btn btn-success',]) ?>
                </div>

                <?php $form->end();?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'showPageSummary' => true,
                    'columns' => [
                        ['class' => 'kartik\grid\SerialColumn'],
                        [
                            'attribute' => 'report_date',
                            'pageSummary'=>'Tổng',
                            'label' => 'Ngày',
                            'filterType' => GridView::FILTER_DATETIME,
                            'width'=>'100px',
                            'filterWidgetOptions' => [
                                'pluginOptions' => [
                                    'showSeconds' => true,
                                    'showMeridian' => false,
                                    'minuteStep' => 1,
                                    'secondStep' => 1,
                                    'disableMousewheel' => false
                                ]
                            ],
                            'value' => function ($model, $key, $index, $widget) {
                                /**
                                 * @var $model \common\models\SumServiceAmount
                                 */
                                return $model->report_date;
                            },
                        ],
                        [
                            'attribute'=>'amount',
                            'label'=>'Doanh thu',
                            'vAlign'=>'middle',
                            'hAlign'=>'right',
//                            'width'=>'200px',
                            'format'=>['decimal', 2],
                            'pageSummary'=>true,
                        ],


                    ],
                ]); ?>

            </div>


        </div>
    </div>
</div>
