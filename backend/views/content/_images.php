<?php
use common\assets\ToastAsset;
use common\widgets\MultiFileUpload;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $model \common\models\Content
 * @var $image \backend\models\Image
 * @var $dataProvider
 */
ToastAsset::register($this);
ToastAsset::config($this, [
    'positionClass' => ToastAsset::POSITION_BOTTOM_RIGHT
]);
$m1 = Yii::t('app','Bạn có chắc chắn muốn xóa ảnh này không');
$js = <<<JS
    function deleteImage(data){
        var allow = confirm('{$m1}');
        if(allow){
            var url = jQuery(data).attr('href');
            jQuery.get(url)
            .done(function(result) {
                if(result.success){
                    toastr.success(result.message);
                    jQuery.pjax.reload({container:'#image-grid-pjax'});
                }else{
                    toastr.error(result.message);
                }
            })
            .fail(function() {
                toastr.error("server error");
            });
        }
        return false;
    }
JS;
$this->registerJs($js,  View::POS_END);
$contentId=$model->id;
?>


<?= GridView::widget([
    'id' => 'image-grid',
    'dataProvider' => $dataProvider,
    'responsive' => true,
    'pjax' => true,
    'hover' => true,
    'columns' => [
        [
            'format' => 'html',
            'header' => 'Ảnh',
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'name',
            'value' => function($model, $key, $index){
                return Html::img($model->getImageUrl(), ['height' => '100']);
            }
        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'type',
            'label' => 'Loại ảnh',
            'value' => function($model, $key, $index){
                return $model->getImageType();
            },

        ],

    ],
]); ?>