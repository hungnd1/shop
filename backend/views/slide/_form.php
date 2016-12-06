<?php

use common\models\Category;
use common\models\Content;
use common\models\ServiceProvider;
use common\models\Slide;
use common\models\SlideContent;
use kartik\form\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\Slide */
/* @var $form yii\widgets\ActiveForm */
$showPreview = !$model->isNewRecord && !empty($model->banner) && ($model->type == Slide::SLIDE_TYPE_BANNER);
?>

<?php $form = ActiveForm::begin([
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'options' => ['enctype' => 'multipart/form-data'],
    'fullSpan' => 8,
    'formConfig' => [
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'labelSpan' => 2,
        'deviceSize' => ActiveForm::SIZE_SMALL,
    ],
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
]); ?>
    <div class="form-body">
        <?= $form->field($model, 'des')->textarea(['rows' => 6, 'class' => 'input-circle']) ?>
    <?php if($type ==  Slide::SLIDE_HOME){ ?>
        <?php
        /**
         * @var $contents Content[]
         */
        $dataList = [];
        $contents =  Content::find()
            ->andWhere(['is_slide'=>1])
            ->all();
        foreach($contents as $content){
            $dataList[$content->id] = $content->display_name;
        }
        echo $form->field($model, 'content_id')->widget(DepDrop::classname(),
            [
                'data'=>$dataList,
                'type' => 2,
                'options'=>['placeholder'=>'-Nội dung-'],
                'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                'pluginOptions'=>[
                    'depends'=>['service-provider-id'],
                    'url'=>Url::to(['/slide-content/get-content']),
                ]
            ]);
        ?>
        <?php }else{ ?>
            <?php
            /**
             * @var $contents Content[]
             */
            $dataList = [];
            $contents =  Content::find()
                ->andWhere(['is_slide_category'=>1])
                ->all();
            foreach($contents as $content){
                $dataList[$content->id] = $content->display_name;
            }
            echo $form->field($model, 'content_id')->widget(DepDrop::classname(),
                [
                    'data'=>$dataList,
                    'type' => 2,
                    'options'=>['placeholder'=>'-Nội dung-'],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'depends'=>['service-provider-id'],
                        'url'=>Url::to(['/slide-content/get-content']),
                    ]
                ]);
            ?>
            <?php
            echo $form->field($model, 'category_id')->dropDownList(Slide::getListCategory());
            ?>
        <?php } ?>
        <?php
        echo $form->field($model, 'status')->dropDownList(Slide::$slide_status);
        ?>
    </div>

    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Thêm mới') : Yii::t('app', 'Cập nhật'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>