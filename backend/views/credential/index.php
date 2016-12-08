<?php

use common\models\SiteApiCredential;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SiteApiCredential */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dánh sách API KEY';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span
                        class="caption-subject font-green-sharp bold uppercase"><?= Yii::t('app','Danh sách API KEY') ?></span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <p>
                    <?= Html::a("Tạo client API Key" ,
                        Yii::$app->urlManager->createUrl(['/credential/create']),
                        ['class' => 'btn btn-success']) ?>
                </p>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'responsive' => true,
                    'pjax' => true,
                    'hover' => true,
                    'columns' => [
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'client_name',
                            'format' => 'html',
                            'value'=>function ($model, $key, $index, $widget) {
                                return '<a href = "'.\yii\helpers\Url::to(['view', 'id' => $model->id]).'">'.$model->client_name.'</a>';
                            },
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'type',
                            'value'=>function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\SiteApiCredential */
                                return SiteApiCredential::$api_key_types[$model->type];
                            },
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute' => 'client_api_key',
                        ],
                        'created_at:datetime',
                        [
                            'attribute' => 'status',
                            'class' => '\kartik\grid\DataColumn',
                            'width'=>'200px',
                            'value' => function ($model, $key, $index, $widget) {
                                /**
                                 * @var $model \common\models\SiteApiCredential
                                 */
                                return SiteApiCredential::$credential_status[$model->status];
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => SiteApiCredential::$credential_status,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => Yii::t('app',"Tất cả")],
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'template' => '{update} {delete} {view}',
//                            'dropdown' => true,
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
