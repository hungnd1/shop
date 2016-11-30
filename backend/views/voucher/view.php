<?php

use common\models\Voucher;
use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Voucher */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Vouchers', 'url' => ['index']];
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
                    <?= Html::a(Yii::t('app', 'Cập nhật'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('app', 'Xóa'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Bạn chắc chắn muốn xóa voucher "'.$model->name.'"?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        [
                            'label' => 'Ảnh đại diện',
                            'format' => 'html',
                            'value' => $model->image?Html::img(Yii::getAlias('@web') . "/" . Yii::getAlias('@voucher_image') . "/" .$model->image, ['width' => '200px']):'',
                        ],
                        'name',
                        [                      // the owner name of the model
                            'attribute' => 'sale',
                            'value' => $model->sale.' %',
                        ],
                        [
                            'attribute' => 'status',
                            'label' => 'Trạng thái',
                            'format' => 'raw',
                            'value' => ($model->status == Voucher::STATUS_ACTIVE) ?
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
                            'attribute' => 'start_date',
                            'value' => date('d/m/Y H:i:s', $model->start_date),
                        ],
                        [                      // the owner name of the model
                            'attribute' => 'end_date',
                            'value' => date('d/m/Y H:i:s', $model->end_date),
                        ],
                        [                      // the owner name of the model
                            'attribute' => 'created_at',
                            'label' => 'Ngày tham gia',
                            'value' => date('d/m/Y H:i:s', $model->created_at),
                        ],
                        [                      // the owner name of the model
                            'attribute' => 'updated_at',
                            'label' => 'Ngày thay đổi thông tin',
                            'value' => date('d/m/Y H:i:s', $model->updated_at),
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
