<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ContentLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Content Logs';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span
                        class="caption-subject font-green-sharp bold uppercase">Quản lý Content log</span>
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
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],


                        [
                            'class' => '\kartik\grid\DataColumn',
                            'format' => 'html',
                            'attribute' => 'content_name',
                            'value' => function ($model, $key, $index) {
                                /** @var $model \common\models\ContentLog */
                                return Html::a($model->content_name, ['/content/view', 'id' => $model->content->id],['class'=>'label label-primary']);
                            }
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'status',
                            'width'=>'15%',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\ContentLog */
                                return $model->getStatusName();
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => \common\models\ContentLog::$listStatus,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => 'Tất cả'],
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'type',
                            'width'=>'15%',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\ContentLog */
                                return $model->getTypeName();
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => \common\models\ContentLog::$listType,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => 'Tất cả'],
                        ],
                        'created_at:datetime',
                        'ip_address',

                        'description:ntext',
                        // 'user_agent',
                        // 'site_id',
                        // 'content_provider_id',
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'user_id',

                            'format' => 'html',
                            'value' => function ($model, $key, $index) {
                                /** @var $model \common\models\ContentLog */
                                return Html::a($model->user->username, ['/user/view', 'id' => $model->user->id],['class'=>'label label-primary']);
                            }
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',

                            'template' => '{view}  {update}',
//                            'dropdown' => true,
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>


