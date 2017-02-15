<?php

use common\models\ProgramSuppost;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProgramSuppostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Ưu đãi');
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
                    <?= Html::a(Yii::t('app', 'Tạo Ưu đãi'), ['create'], ['class' => 'btn btn-success']) ?>
                </p>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'format' => 'raw',
                            'label' => 'Ảnh',
                            'value' => function ($model, $key, $index, $widget) {
                                /** @var $model \common\models\Content */
                                return  $model->image?Html::img(Yii::getAlias('@web') . "/" . Yii::getAlias('@voucher_images') . "/" .$model->image, ['width' => '50px']):'';

                            },
                        ],
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute'=>'name',
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $widget) {
                                return Html::a($model->name, ['view', 'id' => $model->id], ['class' => 'label label-primary']);
                            }
                        ],
                        'short_des',
                        [
                            'class' => '\kartik\grid\DataColumn',
                            'attribute'=>'status',
                            'width'=>'100px',
                            'format'=>'raw',
                            'value' => function ($model, $key, $index, $widget) {
                                /**
                                 * @var $model \common\models\Voucher
                                 */
                                if($model->status == ProgramSuppost::STATUS_ACTIVE){
                                    return '<span class="label label-success">'.$model->getStatusName().'</span>';
                                }else{
                                    return '<span class="label label-danger">'.$model->getStatusName().'</span>';
                                }

                            },
                            'filter' => ProgramSuppost::getListStatus(),
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            'filterInputOptions' => ['placeholder' => "Tất cả"],
                        ],

                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
