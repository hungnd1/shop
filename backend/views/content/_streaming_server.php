<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;


ActiveForm::begin([
            'id' => 'update-content-profile-popup',
            'enableClientValidation' => true,
            'action' => $action,
        ]);
if($countSS > 0){

echo GridView::widget([
    'dataProvider' => $streamingServers,
    'responsive'   => true,
    'id'           => 'list-streaming-server',
    'pjax'         => true,
    'hover'        => true,
    'columns'      => [
        [
            'class'  => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'label'  => 'Địa chỉ phân phối nội dung',
            'value'  => function ($model, $key, $index, $widget) {
                return $model->name;
            },
        ],
        [
            'class'  => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'label'  => 'IP',
            'value'  => function ($model, $key, $index, $widget) {
                return $model->ip;
            },
        ],
        [
            'class' => 'kartik\grid\CheckboxColumn',
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'checkboxOptions' => function($model) {
                return ['value' => $model->id];
            }
        ]
    ],
]);
?>
<div class="form-group">
    <?= Html::submitButton('Phân phối', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Quay lại', ['index'], ['class' => 'btn btn-default', 'data-dismiss'=> 'modal']) ?>
</div>
<?php 

}else{
    echo '<p>Nhà cung cấp này chưa có địa chỉ phân phối nào</p>';
    echo Html::a('Thêm mới địa chỉ phân phối nội dung', ['streaming-server/create'], ['class' => 'btn btn-primary']);
}

ActiveForm::end();
?>
