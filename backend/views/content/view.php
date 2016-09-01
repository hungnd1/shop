<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Content */
/* @var $cat_selected array */
/* @var $imageProvider \yii\data\ArrayDataProvider */
/* @var $imageModel \sp\models\Image */

$this->title = $model->display_name;
$this->params['breadcrumbs'][] = ['label' => \common\models\Category::getTypeName($model->type), 'url' => Yii::$app->urlManager->createUrl(['content/index', 'type' => $model->type])];
if($model->parent){
    $this->params['breadcrumbs'][] = ['label' => $model->parent->display_name, 'url' => ['view', 'id' => $model->parent->id]];

}
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('Cập nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

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
                        <?php if ($model->type == \common\models\Category::TYPE_LIVE) { ?>
                            <li class="<?php echo $active == 2 ? 'active' : ''; ?>">
                                <a href="#tab_live" data-toggle="tab">
                                    EPG Programs </a>
                            </li>
                        <?php } ?>
                        <li class="<?php echo $active==2? 'active':'';?>">
                            <a href="#tab_images" data-toggle="tab">
                                Ảnh </a>
                        </li>
                        <?php if ($model->type != \common\models\Category::TYPE_NEWS && !$model->is_series) { ?>
                            <li class="<?php echo $active==2? 'active':'';?>">
                                <a href="#tab_streams" data-toggle="tab">
                                    Content Profile </a>
                            </li>
                        <?php } ?>
                        <li class="<?php echo $active==4? 'active':'';?>">
                            <a href="#tab_feedback" data-toggle="tab">
                                Content Feedback </a>
                        </li>
                        <?php if ($model->is_series) { ?>
                            <li class="<?php echo $active==5? 'active':'';?>">
                                <a href="#tab_episodes" data-toggle="tab">
                                    Episodes </a>
                            </li>
                        <?php } ?>
                        <li class="<?php echo $active==4? 'active':'';?>">
                            <a href="#tab_transfer" data-toggle="tab">
                                Phân phối nội dung </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane <?php echo $active==1? 'active':'';?>" id="tab_info">
                            <?= $this->render('_detail', [
                                'model' => $model,
                            ]) ?>
                        </div>
                        <?php if ($model->type == \common\models\Category::TYPE_LIVE): ?>
                        <div class="tab-pane <?php echo $active==2? 'active':'';?>" id="tab_live">
                            <?= $this->render('_broadcast', [
                                'model' => $model,
                                'liveModel' => $liveModel,
                                'liveProvider' => $liveProvider,
                                'id' => $id,
                            ]) ?>
                        </div>
                        <?php endif ?>

                        <div class="tab-pane <?php echo $active==2? 'active':'';?>" id="tab_images">
                            <?= $this->render('_images', [
                                'model' => $model,
                                'image' => $imageModel,
                                'dataProvider' => $imageProvider

                            ]) ?>
                        </div>
                        <?php if ($model->type != \common\models\Category::TYPE_NEWS && !$model->is_series) { ?>
                            <div class="tab-pane <?php echo $active==3? 'active':'';?>" id="tab_streams">
                                <?= $this->render('_profile', [
                                    'model' => $model,
                                    'profile' => $profileModel,
                                    'profileProvider' => $profileProvider

                                ]) ?>
                            </div>
                        <?php } ?>
                        <div class="tab-pane <?php echo $active==4? 'active':'';?>" id="tab_feedback">
                            <?= $this->render('_feedback', [
                                'dataProvider' => $feedbackProvider,
                                'feedbackSearch'=>$feedbackSearch

                            ]) ?>
                        </div>

                        <?php if ($model->is_series) { ?>
                            <div class="tab-pane <?php echo $active==5? 'active':'';?>" id="tab_episodes">
                                <?= $this->render('_episode', [
                                    'model' => $model,
                                    'episode' => $episodeModel,
                                    'episodeProvider' => $episodeProvider,
                                    'episodeSearch'=>$episodeSearch

                                ]) ?>
                            </div>
                        <?php } ?>
                        <div class="tab-pane <?php echo $active==4? 'active':'';?>" id="tab_transfer">
                            <?= $this->render('_transfer', [
                                    'contentSiteProvider' => $contentSiteProvider,
                                    'id' => $id,
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
