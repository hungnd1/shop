<?php

/* @var $this yii\web\View */
use kartik\detail\DetailView;

/* @var $model common\models\Service */
?>

<?= DetailView::widget([
    'model' => $model,
    'mode' => 'view',
    'bordered' => true,
    'responsive' => true,
    'hover' => true ,
    'hAlign' => 'right',
    'vAlign' => 'middle',
    'attributes' => [
        'name',
        'display_name',
        'description:ntext',
        [
            'attribute' => 'price',
            'value' => $model->price . ' VND'
        ],
        [
            'attribute' => 'period',
            'value' => $model->period . ' ngày'
        ],
        [
            'attribute' => 'auto_renew',
            'value' => $model->auto_renew?\common\models\Service::$service_autorenew[$model->auto_renew]:''
        ],
        [
            'attribute' => 'free_days',
            'value' => $model->free_days . ' ngày'
        ],
        [
            'attribute' => 'max_daily_retry',
            'value' => $model->max_daily_retry . ' lượt/ngày'
        ],
        [
            'attribute' => 'max_day_failure_before_cancel',
            'value' => $model->max_day_failure_before_cancel . ' ngày'
        ],
        [
            'attribute' => 'created_at',
            'value' => date('d/m/Y', $model->created_at)
        ],
        [
            'attribute' => 'updated_at',
            'value' => date('d/m/Y', $model->updated_at)
        ],
    ],
]) ?>
