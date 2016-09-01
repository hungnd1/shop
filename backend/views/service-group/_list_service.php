<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="row">

    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span
                        class="caption-subject font-green-sharp bold uppercase"> Danh sách gói cước</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <?= GridView::widget([
                    'dataProvider' => $serviceProvider,
                    'id' => 'grid-service-asm-id',
//                    'filterModel' => $searchModel,
                    'responsive' => true,
                    'pjax' => true,
                    'hover' => true,
                    'columns' => [
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'name',
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Service */

                                return Html::a($model->name, ['/service/view', 'id' => $model->id], ['class' => 'label label-primary']);
                            },
                        ],
                        [
                            'attribute' => 'price',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Service */

                                return $model->price_coin . ' VND';
                            },
                        ],
                        [
                            'attribute' => 'period',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Service */

                                return round($model->watching_period/24, 2) . ' ngày';
                            },
                        ],
                        [
                            'attribute' => 'auto_renew',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Service */

                                return $model->auto_renew?\common\models\Service::$service_autorenew[$model->auto_renew]:'';
                            },
                        ],
                        [
                            'attribute' => 'free_days',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Service */

                                return $model->free_days . ' ngày';
                            },
                        ],
                        [
                            'attribute' => 'max_daily_retry',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Service */

                                return $model->max_daily_retry . ' lượt/ngày';
                            },
                        ],
                        [
                            'attribute' => 'max_day_failure_before_cancel',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Service */

                                return $model->max_day_failure_before_cancel . ' ngày';
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Service */

                                return \common\models\Service::$service_status[$model->status];
                            },
                        ],


                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
