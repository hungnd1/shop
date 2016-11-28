<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Content */
/* @var $cat_selected array */
/* @var $imageProvider \yii\data\ArrayDataProvider */
/* @var $imageModel \backend\models\Image */

$this->title = $model->display_name;
$this->params['breadcrumbs'][] = ['label' => 'Content', 'url' => Yii::$app->urlManager->createUrl(['content/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('Cập nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Thêm mới',  Yii::$app->urlManager->createUrl(['content/create']), ['class' => 'btn btn-primary']) ?>

</p>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Thông tin nội dung</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">

                <div class="tabbable-custom nav-justified">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="<?php echo $active==1? 'active':'';?>">
                            <a href="#tab_info" data-toggle="tab">
                                Thông tin</a>
                        </li>
                        <li class="<?php echo $active==2? 'active':'';?>">
                            <a href="#tab_images" data-toggle="tab">
                                Ảnh </a>
                        </li>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane <?php echo $active==1? 'active':'';?>" id="tab_info">
                            <?= $this->render('_detail', [
                                'model' => $model,
                            ]) ?>
                        </div>

                        <div class="tab-pane <?php echo $active==2? 'active':'';?>" id="tab_images">
                            <?= $this->render('_images', [
                                'model' => $model,
                                'image' => $imageModel,
                                'dataProvider' => $imageProvider

                            ]) ?>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
