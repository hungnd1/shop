<?php

use common\models\Service;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách gói cước';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span
                        class="caption-subject font-green-sharp bold uppercase">Quản lý gói cước</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'responsive' => true,
                    'pjax' => true,
                    'hover' => true,
                    'columns' => [
                        'display_name',
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'name',
                            'format' => 'html',
                            'value'=>function ($model, $key, $index, $widget) {
                                return '<a href = "'.\yii\helpers\Url::to(['view', 'id' => $model->id]).'">'.$model->name.'</a>';
                            },
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'price',
                            'value'=>function ($model, $key, $index, $widget) {
                                /** @var $model Service */
                                return intval($model->pricing->price_coin);
                            },
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'period',
                            'value'=>function ($model, $key, $index, $widget) {
                                /** @var $model Service */
                                return intval($model->period).' ngày';
                            },
                        ],
                        [
                            'attribute' => 'auto_renew',
                            'class' => '\kartik\grid\DataColumn',
                            'width'=>'300px',
                            'value' => function ($model, $key, $index, $widget) {
                                /**
                                 * @var $model \common\models\Service
                                 */
                                return $model->auto_renew?Service::$service_autorenew[$model->auto_renew]:'';
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => Service::$service_autorenew,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => "Tất cả"],
                        ],
                        [
                            'attribute' => 'created_at',
                            'value' => function($model){
                                return date('d-m-Y H:i:s', $model->created_at);
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'class' => '\kartik\grid\DataColumn',
                            'width'=>'200px',
                            'value' => function ($model, $key, $index, $widget) {
                                /**
                                 * @var $model \common\models\ServiceProvider
                                 */
                                return Service::$service_status[$model->status];
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => Service::$service_status,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => "Tất cả"],
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'template' => '{view}',
//                            'dropdown' => true,
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
