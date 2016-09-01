<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Thuộc tính nội dung';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-attribute-index portlet light">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if(!Yii::$app->params['tvod1Only']) echo Html::a('Thêm mới thuộc tính', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'attribute' => 'content_type',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                    return \common\models\Category::getListType($model->content_type);
                }
            ],
            [
                'attribute' => 'data_type',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                    return  $model->getDatatype($model->data_type);
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date('d-m-Y H:i:s', $model->created_at);
                }
            ],
            // 'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => ' {view}{update}{delete}',
                'buttons' => [
                    'delete' => function($url, $model){
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], [
                            'class' => '',
                            'data' => [
                                'confirm' => 'Bạn có muốn xóa không?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>

</div>
