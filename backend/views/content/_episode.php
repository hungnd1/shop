<?php
use common\assets\ToastAsset;
use common\models\Category;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;
use \dosamigos\ckeditor\CKEditor;

/**
 * @var $model \common\models\Content
 * @var $profile \common\models\ContentProfile
 * @var $dataProvider
 */
ToastAsset::register($this);
ToastAsset::config($this, [
    'positionClass' => ToastAsset::POSITION_BOTTOM_RIGHT
]);
$urlEpisodeCreate = \yii\helpers\Url::to(['content/create-episode-modal', 'parent_id' => $model->id]);
$js = <<<JS

   function openEpisodeModal(){
   $("#create-content-episode-modal").find(".modal-body").html('');
      $.ajax({
        type     :'GET',
        cache    : false,
        url  : '$urlEpisodeCreate',
       success  : function(response) {
       if(response.success){
                 $("#create-content-episode-modal").find(".modal-body").html(response.data);

            }else{
         }

        }
        });

    $('#create-content-episode-modal').modal({
        backdrop: "static",
        keyboard:false

    });
   }


JS;
$this->registerJs($js, View::POS_HEAD);



?>

<?= Html::a('Tạo Episode',
            Yii::$app->urlManager->createUrl(['content/create', 'type' => Category::TYPE_FILM, 'parent' => $model->id]),
            ['class' => 'btn btn-success']) ?>
<br>
<br>
<?php

echo GridView::widget([
    'dataProvider' => $episodeProvider,
    'filterModel' => $episodeSearch,
    'responsive' => true,
    'id' => 'list-episode',
    'pjax' => true,
    'hover' => true,
    'columns' => [
        [
            'class' => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'label' => 'Ảnh',
            'value' => function ($model, $key, $index, $widget) {
                /** @var $model \common\models\Content */

                $link = $model->getFirstImageLink();
                return $link ? Html::img($link, ['alt' => 'Thumbnail', 'width' => '50', 'height' => '50']) : '';

            },
        ],
        [
            'format' => 'html',
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'display_name',
            'value' => function ($model, $key, $index) {
                return Html::a($model->display_name, ['view', 'id' => $model->id]);
            }
        ],
        'episode_order',
        'created_at:datetime',
        [
            'format' => 'html',
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'status',
            'value' => function ($model, $key, $index) {
                /** @var $model \common\models\Content */
                return $model->getStatusName();
            }
        ],
        [
            'attribute' => '',
            'format' => 'raw',
            'width' => '20%',
            'value' => function ($model, $key, $index, $widget) {

                $viewUrl = Yii::$app->urlManager->createUrl(['/content/view', 'id' => $model->id]);
                $updateUrl = Yii::$app->urlManager->createUrl(['/content/update', 'id' => $model->id]);
                /**
                 * @var $model \common\models\Content
                 */
                $res = Html::a('Detail', $viewUrl,
                    [
                        'class' => 'btn btn-primary'
                    ]);
                $res .= '&nbsp;&nbsp;&nbsp;&nbsp;';
               $res .= Html::a('Update', $updateUrl,
                   [

                       'class' => 'btn btn-info'
                   ]);

                return $res;
            },


        ],

    ]
]);
?>