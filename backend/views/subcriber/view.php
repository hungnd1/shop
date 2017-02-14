<?php

use common\models\Subcriber;
use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Subcriber */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý khách hàng'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase"> <?= $this->title;?></span>
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
                'confirm' => Yii::t('app','Bạn chắc chắn muốn xóa?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_name',
            'full_name',
            [
                'attribute'=>'gender',
                'format'=>'raw',
                'value'=>$model->getGenderName(),
                'type'=>DetailView::INPUT_SWITCH,
            ],
            [
                'attribute'=>'status',
                'format'=>'raw',
                'value'=>($model->status ==Subcriber::STATUS_ACTIVE)  ?
                    '<span class="label label-success">'.$model->getStatusName().'</span>' :
                    '<span class="label label-danger">'.$model->getStatusName().'</span>',
                'type'=>DetailView::INPUT_SWITCH,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'onText' => 'Active',
                        'offText' => 'Delete',
                    ]
                ]
            ],
            'password_hash',
            'email:email',
            'address',
            'phone',
            'birthday',
            'about',
            [                      // the owner name of the model
                'attribute'=>'created_at',
                'value' => date('d/m/Y H:i:s',$model->created_at),
            ],
            [                      // the owner name of the model
                'attribute'=>'updated_at',
                'value' => date('d/m/Y H:i:s',$model->updated_at),
            ],
        ],
    ]) ?>
            </div>
        </div>
    </div>
</div>