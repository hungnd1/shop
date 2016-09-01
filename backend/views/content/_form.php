<?php

use common\models\ActorDirector;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use common\models\Content;
use common\models\Category;
use common\models\Site;
use common\models\ContentSiteAsm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use common\helpers\CVietnameseTools;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Content */
/* @var $form yii\widgets\ActiveForm */
?>
<?php


$price_id = Html::getInputId($model, 'price');
$price_download = Html::getInputId($model, 'price_download');
$price_gift = Html::getInputId($model, 'price_gift');
$upload_update_url = \yii\helpers\Url::to(['/content/upload-file', 'id' => $model->id]);
$upload_create_url = \yii\helpers\Url::to(['/content/upload-file']);

$upload_url = $model->isNewRecord ? $upload_create_url : $upload_update_url;

$js = <<<JS
$(document).ready(function() {
    var the_terms = $("#free_id");

    if (the_terms.is(":checked")) {
        $("#pricing_id").attr("disabled", "disabled");
    } else {
        $("#pricing_id").removeAttr("disabled");
    }

    the_terms.click(function() {
        if ($(this).is(":checked")) {
            $("#pricing_id").attr("disabled", "disabled");
        } else {
            $("#pricing_id").removeAttr("disabled");
        }
    });
    // the_terms.click();

    $('button.kv-file-remove').click(function(e){
        console.log(e);
    });

});
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>


<div class="form-body">

<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'id' => 'form-create-content',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,

]); ?>

<h3 class="form-section">Thông tin nội dung</h3>
<div class="row">
    <div class="col-md-12">
        <?= $form->field($model, 'display_name')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle']) ?>
    </div>
</div>
<?php
echo $parent ? $form->field($model, 'parent_id')->hiddenInput(['value' => $parent])->label(false) : '';
?>
<?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'created_user_id')->hiddenInput()->label(false) ?>

<?php if ($model->type == Category::TYPE_LIVE_CONTENT):
    // echo $form->field($model, 'is_free')->hiddenInput(['value' => 1])->label(false);
    ?>
    <?php if ($parent) {
    $disabled = ['disabled' => 'disabled'];
    echo $form->field($model, 'live_channel')->hiddenInput(['value' => $parent])->label(false);
} else {
    $disabled = [];
} ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'live_channel')->dropDownList(\common\models\Content::listLive(), ['class' => 'input', 'options' => [$parent => ['Selected' => 'selected']]]
                + $disabled
            ) ?>
        </div>
    </div>
    <?php
    $js2 = "
            $('#content-started_at-disp').val($('#content-started_at').val());
            $('#content-ended_at-disp').val($('#content-ended_at').val());
        ";

    $this->registerJs($js2, \yii\web\View::POS_END);

    ?>
    <div class="row">
        <div class="col-md-12">
            <?php
            echo $form->field($model, 'started_at')->widget(DateControl::classname(), [
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'd-M-y H:i:s',
            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            echo $form->field($model, 'ended_at')->widget(DateControl::classname(), [
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'd-M-y H:i:s'
            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <?=
            $form->field($model, 'thumbnail_epg[]')->widget(\kartik\widgets\FileInput::classname(), [
                'options' => [
                    'multiple' => false,
                    'accept' => 'image/*',
                    'id' => 'thumbnail_epg-preview'
                ],
                'language' => 'vi-VN',
                'pluginOptions' => [
                    'uploadUrl' => $upload_url,
                    'uploadExtraData' => [
                        'type' => \common\models\Content::IMAGE_TYPE_THUMBNAIL_EPG,
                        'thumbnail_epg_old' => $model->thumbnail_epg
                    ],
                    'maxFileCount' => 1,
                    'showUpload' => false,
                    'initialPreview' => $thumbnail_epgPreview,
                    'initialPreviewConfig' => $thumbnail_epgInit,


                ],
                'pluginEvents' => [
                    "fileuploaded" => "function(event, data, previewId, index) {
                    var response=data.response;
                    console.log(response.success);
                    console.log(response);
                    if(response.success){
                        console.log(response.output);
                        var current_screenshots=response.output;
                        var old_value_text=$('#images_tmp').val();
                        console.log('xxx'+old_value_text);
                        if(old_value_text !=null && old_value_text !='' && old_value_text !=undefined)
                        {
                            var old_value=jQuery.parseJSON(old_value_text);

                            if(jQuery.isArray(old_value)){
                                console.log(old_value);
                                old_value.push(current_screenshots);

                            }
                        }
                        else{
                            var old_value= [current_screenshots];
                        }
                        $('#images_tmp').val(JSON.stringify(old_value));
                    }
                }",
                    "filedeleted" => "function(event, data) {
                    var response = data.response
                    console.log(event);
                    console.log(data);
                    // if(response.success){
                    //     console.log(response.output);

                    // }
                }",
                ],

            ]) ?>
        </div>
    </div>

<?php endif ?>

<?php
if ($type == \common\models\Category::TYPE_FILM && !$model->parent) {
    echo $form->field($model, 'is_series')->checkbox(['label' => 'Phim bộ'])->label(false);
}
?>

<?php
if ($type == \common\models\Category::TYPE_LIVE && !$model->parent) {
    echo $form->field($model, 'is_catchup')->checkbox();
}
?>

<div class="row">
    <div class="col-md-12">
        <?= $form->field($model, 'status')->dropDownList(
            \common\models\Content::getListStatus('filter'), ['class' => 'input-circle']
        ) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= $form->field($model, 'honor')->dropDownList(
            \common\models\Content::$list_honor, ['class' => 'input-circle']
        ) ?>
    </div>
</div>
<?php if($type == \common\models\Category::TYPE_LIVE): ?>
<div class="row">
    <div class="col-md-12">
        <?= $form->field($model, 'order')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle']) ?>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-md-12">
        <?= $form->field($model, 'tags')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle']) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">

        <?php
        $desc =
            Content::find()
                ->innerJoin('content_related_asm', 'content.id = content_related_asm.content_id')
                ->innerJoin('content as related', 'related.id = content_related_asm.content_related_id')
                ->select('related.display_name as related_name, related.id')
                ->where(['IN', 'content_related_asm.id', $model->related_content])
                ->all();
        $des = [];
        foreach ($desc as $value) {
            $des[] = ['id' => $value->id, 'display_name' => $value->related_name];
        }
        $des = json_encode($des);

        ?>
        <?php

        echo $form->field($model, 'content_related_asm[]')->widget(\kartik\widgets\Select2::classname(), [
            'options' => [
                'placeholder' => 'Tìm kiếm nội dung liên quan...',
                'multiple' => true,
                'id' => 'related'
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                ],
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['related-list']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(city) { return city.display_name; }'),
                'templateSelection' => new JsExpression('function (city) { return city.display_name; }'),
                'initSelection' => new JsExpression("function (element, callback) {
                            callback($des);
                            $.each($des, function (i, item) {
                                var child = $('<option>', {
                                    value: item.id,
                                    text : item.display_name
                                });
                                element.append(child);
                                child.attr('selected', 'selected');
                            });
                        }")
            ],
        ])->label('Nội dung liên quan');
        ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= $form->field($model, 'description')->widget(\dosamigos\ckeditor\CKEditor::className(), [
            'options' => ['rows' => 8],
            'preset' => 'basic'
        ]) ?>
    </div>
</div>
<?php if ($type != Category::TYPE_LIVE_CONTENT): ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'short_description')->widget(\dosamigos\ckeditor\CKEditor::className(), [
                'options' => ['rows' => 6],
                'preset' => 'basic'
            ]) ?>
        </div>
    </div>

    <h3 class="form-section">Ảnh </h3>

    <div class="row">
        <?php
        //       echo  $form->field($model, 'logo[]')->widget(\kartik\widgets\FileInput::classname(), [
        //            'options' => [
        //                'multiple' => true,
        //                // 'accept' => 'image/*'
        //            ],
        //            'pluginOptions' => [
        //                'uploadUrl' => \yii\helpers\Url::to(['/content/upload-file']),
        //                'uploadExtraData' => [
        //                    'type' => \common\models\Content::IMAGE_TYPE_LOGO,
        //                    'logo_old' => $model->logo
        //                ],
        //
        //                'maxFileCount' => 10,
        //                'overwriteInitial' => false,
        //
        //                'initialPreview' => $logoPreview,
        //                'initialPreviewConfig' => $logoInit,
        //
        //
        //            ],
        //            'pluginEvents' => [
        //                "fileuploaded" => "function(event, data, previewId, index) {
        //                var response=data.response;
        //                console.log(response.success);
        //                console.log(response);
        //                if(response.success){
        //                    console.log(response.output);
        //                    var current_screenshots=response.output;
        //                    var old_value_text=$('#images_tmp').val();
        //                    console.log('xxx'+old_value_text);
        //                    if(old_value_text !=null && old_value_text !='' && old_value_text !=undefined)
        //                    {
        //                        var old_value=jQuery.parseJSON(old_value_text);
        //
        //                        if(jQuery.isArray(old_value)){
        //                            console.log(old_value);
        //                            old_value.push(current_screenshots);
        //
        //                        }
        //                    }
        //                    else{
        //                        var old_value= [current_screenshots];
        //                    }
        //                    $('#images_tmp').val(JSON.stringify(old_value));
        //                 }
        //             }",
        //                "fileclear" => "function() {  console.log('delete'); }",
        //            ],
        //
        //        ]);
        ?>
    </div>
    <div class="row">
        <div class="col-md-12">

            <?=
            $form->field($model, 'thumbnail[]')->widget(\kartik\widgets\FileInput::classname(), [
                'options' => [
                    'multiple' => false,
                    'id' => 'content-thumbnail',
                    'accept' => 'image/*'
                ],
                'pluginOptions' => [
                    'uploadUrl' => $upload_url,
                    'uploadExtraData' => [
                        'type' => \common\models\Content::IMAGE_TYPE_THUMBNAIL,
                        'thumbnail_old' => $model->thumbnail
                    ],
                    'language' => 'vi-VN',
                    'showUpload' => false,
                    'showUploadedThumbs' => false,
                    'initialPreview' => $thumbnailPreview,
                    'initialPreviewConfig' => $thumbnailInit,
                    'maxFileSize' => 1024 * 1024 * 10,
                ],
                'pluginEvents' => [
                    "fileuploaded" => "function(event, data, previewId, index) {
                    var response=data.response;
                    if(response.success){
                        var current_screenshots=response.output;
                        var old_value_text=$('#images_tmp').val();
                        if(old_value_text !=null && old_value_text !='' && old_value_text !=undefined)
                        {
                            var old_value=jQuery.parseJSON(old_value_text);

                            if(jQuery.isArray(old_value)){
                                old_value = old_value.filter(function(v){
                                    v = jQuery.parseJSON(v)
                                    console.log(typeof v.type, v.type);
                                    return v.type !== '2';
                                })
                                console.log(old_value);
                                old_value.push(current_screenshots);
                                console.log(old_value);
                            }
                        }
                        else{
                            var old_value= [current_screenshots];
                        }
                        $('#images_tmp').val(JSON.stringify(old_value));
                    }
                }",
                    "filedeleted" => "function(event, data) {
                    var response = data.response
                    console.log(event);
                    console.log(data);
                    // if(response.success){
                    //     console.log(response.output);

                    // }
                }",
                ],

            ]) ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?=
            $form->field($model, 'screenshoot[]')->widget(\kartik\widgets\FileInput::classname(), [
                'options' => [
                    'multiple' => true,
                    'accept' => 'image/png,image/jpg,image/jpeg,image/gif',
                    'id' => 'content-screenshoot'
                ],
                'pluginOptions' => [
                    'uploadUrl' => $upload_url,
                    'uploadExtraData' => [
                        'type' => \common\models\Content::IMAGE_TYPE_SCREENSHOOT,
                        'screenshots_old' => $model->screenshoot
                    ],
                    'maxFileCount' => 10,
                    'showUpload' => false,
                    'initialPreview' => $screenshootPreview,
                    'initialPreviewConfig' => $screenshootInit,
                    'maxFileSize' => 1024 * 1024 * 10,
                ],
                'pluginEvents' => [
                    "fileuploaded" => "function(event, data, previewId, index) {
                        var response=data.response;
                        if(response.success){
                            var current_screenshots=response.output;
                            var old_value_text=$('#images_tmp').val();
                            if(old_value_text !=null && old_value_text !='' && old_value_text !=undefined)
                            {
                                var old_value=jQuery.parseJSON(old_value_text);

                                if(jQuery.isArray(old_value)){
                                    old_value.push(current_screenshots);

                                }
                            }
                            else{
                                var old_value= [current_screenshots];
                            }
                            $('#images_tmp').val(JSON.stringify(old_value));
                         }
                     }",
                    "filesuccessremove" => "function() {  console.log('delete'); }",
                ],

            ]) ?>

            <?= $form->field($model, 'assignment_sites')->checkboxList(Site::getSiteList(null, ['id', 'name'])) ?>
            <?php
            // var_dump($model->readonlyAssignment_sites);die;
            $jsAs = "";
            foreach ($model->readonlyAssignment_sites as $readonlyAssignment_site) {
                $jsAs .= "var a = $('#content-assignment_sites').find('input[value=$readonlyAssignment_site]').click(function() { return false; }); a.parent('label').css('font-weight','Bold');";
            }

            $this->registerJs($jsAs, \yii\web\View::POS_END);


            ?>
            <?= $form->field($model, 'default_site_id')->dropDownList(Site::getSiteList(null, ['id', 'name'])) ?>
        </div>
    </div>

    <!--    --><?php //if (!$model->parent): ?>

    <div class="row">

        <div class="form-group field-content-price">
            <label class="control-label col-md-2" for="content-price">Danh mục</label>

            <div class="col-md-10">
                <?= \common\widgets\Jstree::widget([
                    'clientOptions' => [
                        "checkbox" => ["keep_selected_style" => false],
                        "plugins" => ["checkbox"]
                    ],
                    'type' => $model->type,
                    'sp_id' => $site_id,
                    'cp_id' => true,
                    'data' => isset($selectedCats) ? $selectedCats : [],
                    'eventHandles' => [
                        'changed.jstree' => "function(e,data) {
                            jQuery('#list-cat-id').val('');
                            var i, j, r = [];
                            var catIds='';
                            for(i = 0, j = data.selected.length; i < j; i++) {
                                var item = $(\"#\" + data.selected[i]);
                                var value = item.attr(\"id\");
                                if(i==j-1){
                                    catIds += value;
                                } else{
                                    catIds += value +',';

                                }
                            }
                            jQuery(\"#default_category_id\").val(data.selected[0])
                            jQuery(\"#list-cat-id\").val(catIds);
                            console.log(jQuery(\"#list-cat-id\").val());
                         }"
                    ]
                ]) ?>
            </div>
            <div class="col-md-offset-2 col-md-10"></div>
            <div class="col-md-offset-2 col-md-10">
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <!--    --><?php //endif; ?>
    <?= $form->field($model, 'list_cat_id')->hiddenInput(['id' => 'list-cat-id'])->label(false) ?>
    <?= $form->field($model, 'default_category_id')->hiddenInput(['id' => 'default_category_id'])->label(false) ?>


<?php endif; ?>
<?php
$actors =  ArrayHelper::map(ActorDirector::find()->andWhere(['status'=>ActorDirector::STATUS_ACTIVE,
    'content_type'=>ActorDirector::TYPE_VIDEO,
    'type'=>ActorDirector::TYPE_ACTOR])->all(), 'id', 'name');
$directors =  ArrayHelper::map(ActorDirector::find()->andWhere(['status'=>ActorDirector::STATUS_ACTIVE,
    'content_type'=>ActorDirector::TYPE_VIDEO,
    'type'=>ActorDirector::TYPE_DIRECTOR])->all(), 'id', 'name');
?>
<?php if ($model->type == Content::TYPE_VIDEO || $model->type == Content::TYPE_KARAOKE): ?>
    <?= $form->field($model, 'content_directors')->widget(\kartik\widgets\Select2::classname(), [
        'data' => $directors,
        'options' => [
            'multiple' => true
        ],
        'maintainOrder' => true,
        'pluginOptions' => [
            'tags' => true,
            'maximumInputLength' => 10
        ],
    ]);
    ?>
    <?= $form->field($model, 'content_actors')->widget(\kartik\widgets\Select2::classname(), [
        'data' => $actors,
        'maintainOrder' => true,
        'options' => [
            'multiple' => true
        ],
        'pluginOptions' => [
            'tags' => true,
            'maximumInputLength' => 10
        ],
    ]);
    ?>
<?php endif; ?>
<?php foreach ($model->extraAttr as $extra): ?>
    <?php
    $js2 = "
            $('input[id=content-'+$('#content-contentattr-$extra->id').attr('targetid')+']').val($('#content-contentattr-$extra->id').val());
            $('#content-contentattr-$extra->id').keypress(function(){
                var target = $(this).attr('targetid');
                $('input[id=content-'+target+']').val($(this).val());
            });
        ";

    $this->registerJs($js2, \yii\web\View::POS_END);

    ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, "contentAttr[$extra->id]")->textInput(['maxlength' => 128, 'class' => 'form-control', 'targetid' => CVietnameseTools::makeSearchableStr($extra->name)])->label($extra->name) ?>
        </div>
    </div>
<?php endforeach; ?>
<?php if ($model->isNewRecord): ?>
    <?= $form->field($model, 'images')->hiddenInput(['id' => 'images_tmp'])->label(false) ?>
<?php endif; ?>
<?= Html::submitButton($model->isNewRecord ? 'Tạo' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>


<?php ActiveForm::end(); ?>

</div>
