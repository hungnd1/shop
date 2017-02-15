<?php

use common\models\ProgramSuppost;
use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramSuppost */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Program Supposts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase"><?php echo $model->id;?></span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse" data-original-title="" title="">
                    </a>

                </div>
            </div>
            <div class="portlet-body">
                <p>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        [
                            'attribute' => 'image',
                            'format' => 'html',
                            'value' => $model->image?Html::img(Yii::getAlias('@web') . "/" . Yii::getAlias('@voucher_images') . "/" .$model->image, ['width' => '200px']):'',
                        ],

                        'name',
                        'short_des',
                        'des:ntext',
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => ($model->status == ProgramSuppost::STATUS_ACTIVE) ?
                                '<span class="label label-success">' . $model->getStatusName() . '</span>' :
                                '<span class="label label-danger">' . $model->getStatusName() . '</span>',
                            'type' => DetailView::INPUT_SWITCH,
                            'widgetOptions' => [
                                'pluginOptions' => [
                                    'onText' => 'Active',
                                    'offText' => 'Delete',
                                ]
                            ]
                        ],
                        [                      // the owner name of the model
                            'attribute' => 'created_at',
                            'value' => date('d/m/Y H:i:s', $model->created_at),
                        ],
                        [                      // the owner name of the model
                            'attribute' => 'updated_at',
                            'value' => date('d/m/Y H:i:s', $model->updated_at),
                        ],
                    ],
                ]) ?>

</div>
