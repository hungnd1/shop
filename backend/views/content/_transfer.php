<?php
use common\models\Content;
use common\models\ContentSiteAsm;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

?>
<?php

$changeStatusURL = Yii::$app->urlManager->createUrl(['content/change-content-site-status']);
$js              = <<<JS
function loadModalDataStream(url){
    $("#streaming-server-view").find(".modal-body").html('');
    $.ajax({
        type     :'GET',
        cache    : false,
        url  :url,
        success  : function(response) {
            $("#streaming-server-view").find(".modal-body").html(response);
        }
    });
}

$('select[name=change-status]').change(function(){
    var site_id = $(this).attr('site_id'),
        content_id = $(this).attr('content_id'),
        status = $(this).val(),
        url = ['{$changeStatusURL}', site_id, content_id].join('&');

    $.get(url, {site_id: site_id, content_id:content_id, status: status}, function(res){
        if(res.success)
            jQuery.pjax.reload({container:'#list-content-site-container'});
    })
})

JS;
$this->registerJs($js, View::POS_END);
echo GridView::widget([
    'dataProvider' => $contentSiteProvider,
    'responsive'   => true,
    'id'           => 'list-content-site',
    'pjax'         => true,
    'hover'        => true,
    'columns'      => [
        [
            'class'  => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'label'  => 'Nhà cung cấp dịch vụ',
            'value'  => function ($model, $key, $index, $widget) {
                return $model->site_name;
            },
        ],
        [
            'class'  => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'label'  => 'Trạng thái phân phối',
            'value'  => function ($model, $key, $index, $widget) {
                /** @var $model \common\models\Content */
                return ContentSiteAsm::$_status[$model->content_site_asm_status];
            },
        ],
        [
            'class'  => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'label'  => 'Tác động',
            'value'  => function ($model, $key, $index, $widget) use ($id) {

                $modalStreamingServer = Yii::$app->urlManager->createUrl(['/content/modal-streaming-server', 'site_id' => $model->site_id, 'content_id' => $id]);

                $display = [
                    ContentSiteAsm::STATUS_NOT_TRANSFER   => Html::a('Phân phối', '#streaming-server-view', ['class' => 'btn btn-primary', 'data-toggle' => 'modal', 'data-backdrop' => "static", 'data-keyboard' => "false", 'onclick' => "js:loadModalDataStream('$modalStreamingServer');"]),
                    ContentSiteAsm::STATUS_TRANSFERING    => Html::label(ContentSiteAsm::$_status[$model->content_site_asm_status]),
                    ContentSiteAsm::STATUS_TRANSFER_ERROR => Html::a('Phân phối lại', '#streaming-server-view', ['class' => 'btn btn-primary', 'data-toggle' => 'modal', 'data-backdrop' => "static", 'data-keyboard' => "false", 'onclick' => "js:loadModalDataStream('$modalStreamingServer');"]),
                    ContentSiteAsm::STATUS_ACTIVE         => Html::dropDownList('change-status', null, [
                        ContentSiteAsm::STATUS_ACTIVE    => ContentSiteAsm::$_status[ContentSiteAsm::STATUS_ACTIVE],
                        ContentSiteAsm::STATUS_INACTIVE  => ContentSiteAsm::$_status[ContentSiteAsm::STATUS_INACTIVE],
                        ContentSiteAsm::STATUS_INVISIBLE => ContentSiteAsm::$_status[ContentSiteAsm::STATUS_INVISIBLE],
                    ], ['class' => 'btn btn-default', 'site_id' => $model->site_id, 'content_id' => $id]),
                    ContentSiteAsm::STATUS_INACTIVE       => Html::dropDownList('change-status', null, [
                        ContentSiteAsm::STATUS_INACTIVE  => ContentSiteAsm::$_status[ContentSiteAsm::STATUS_INACTIVE],
                        ContentSiteAsm::STATUS_ACTIVE    => ContentSiteAsm::$_status[ContentSiteAsm::STATUS_ACTIVE],
                        ContentSiteAsm::STATUS_INVISIBLE => ContentSiteAsm::$_status[ContentSiteAsm::STATUS_INVISIBLE],
                    ], ['class' => 'btn btn-default', 'site_id' => $model->site_id, 'content_id' => $id]),
                    ContentSiteAsm::STATUS_INVISIBLE      => Html::dropDownList('change-status', null, [
                        ContentSiteAsm::STATUS_INVISIBLE => ContentSiteAsm::$_status[ContentSiteAsm::STATUS_INVISIBLE],
                        ContentSiteAsm::STATUS_ACTIVE    => ContentSiteAsm::$_status[ContentSiteAsm::STATUS_ACTIVE],
                        ContentSiteAsm::STATUS_INACTIVE  => ContentSiteAsm::$_status[ContentSiteAsm::STATUS_INACTIVE],
                    ], ['class' => 'btn btn-default', 'site_id' => $model->site_id, 'content_id' => $id]),
                ];

                return $display[$model->content_site_asm_status];

            },
        ],
    ],
]);

\yii\bootstrap\Modal::begin([
    'header'      => 'Chọn máy chủ',
    'closeButton' => ['label' => 'Cancel'],
    'options'     => ['id' => 'streaming-server-view'],
    'size'        => \yii\bootstrap\Modal::SIZE_DEFAULT,
]);
?>

<?php \yii\bootstrap\Modal::end();?>
