<?php

use common\models\Subcriber;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SubcriberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Quản lý khách hàng');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span
                        class="caption-subject font-green-sharp bold uppercase"><?= Html::encode($this->title) ?></span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <p>
                    <?= Html::a(Yii::t('app','Thêm mới khách hàng'), ['create'], ['class' => 'btn btn-success']) ?>
                </p>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
//                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'id',
                            'width'=>'50px'
                        ],
                        [
                            'attribute' => 'user_name',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $widget) {
                                /**
                                 * @var $model \common\models\Subcriber
                                 */
                                if($model->user_name != null){
                                    $res = Html::a('<kbd>'.$model->user_name.'</kbd>', ['subcriber/view', 'id' => $model->id ]);
                                    return $res;
                                }else{
                                    $res = Html::a('<kbd>'.$model->full_name.'</kbd>', ['subcriber/view', 'id' => $model->id ]);
                                    return $res;
                                }
                            },
                        ],
                        [
                            'attribute' => 'gender',
                            'class' => '\kartik\grid\DataColumn',
                            'width'=>'200px',
                            'value' => function ($model, $key, $index, $widget) {
                                /**
                                 * @var $model \common\models\Subcriber
                                 */
                                return $model->getGenderName();
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => Subcriber::listGender(),
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => Yii::t('app', 'Tất cả')],
                        ],
                        [
                            'attribute' => 'status',
                            'class' => '\kartik\grid\DataColumn',
                            'width'=>'200px',
                            'value' => function ($model, $key, $index, $widget) {
                                /**
                                 * @var $model \common\models\Subcriber
                                 */
                                return $model->getStatusName();
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => Subcriber::listStatus(),
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => Yii::t('app', 'Tất cả')],
                        ],
                         'phone',

                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>