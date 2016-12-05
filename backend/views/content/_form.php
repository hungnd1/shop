<?php

use common\models\Content;
use common\models\Site;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

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
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'title_short')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php $listCheckbox = Content::$list_honor; ?>
            <?= $form->field($model, 'honor')->dropDownList($listCheckbox,['prompt'=>'Chọn loại sản phẩm'])->label('Loại sản phẩm') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php $listTypeStatus = Content::$listTypeStatus; ?>
            <?= $form->field($model, 'type_status')->dropDownList($listTypeStatus)->label('Hiện trạng sản phẩm') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php $listCheckbox = Content::$listAvailability; ?>
            <?= $form->field($model, 'availability')->dropDownList($listCheckbox)->label('Tình trạng hàng') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'code')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle','readonly' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'price')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'price_promotion')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle']) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'status')->dropDownList(
                \common\models\Content::getListStatus('filter'), ['class' => 'input-circle']
            ) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'tags')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'rating')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle'])->label('Đánh giá') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'order')->textInput(['maxlength' => 128, 'class' => 'form-control  input-circle'])->label('Sắp xếp') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'short_description')->widget(\dosamigos\ckeditor\CKEditor::className(), [
                'options' => ['rows' => 6],
                'preset' => 'basic'
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'description')->widget(\dosamigos\ckeditor\CKEditor::className(), [
                'options' => ['rows' => 8],
                'preset' => 'full'
            ]) ?>
        </div>
    </div>



    <h3 class="form-section">Ảnh </h3>

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
            $form->field($model, 'slide[]')->widget(\kartik\widgets\FileInput::classname(), [
                'options' => [
                    'multiple' => false,
                    'id' => 'content-slide',
                    'accept' => 'image/*'
                ],
                'pluginOptions' => [
                    'uploadUrl' => $upload_url,
                    'uploadExtraData' => [
                        'type' => \common\models\Content::IMAGE_TYPE_SLIDE,
                        'thumbnail_old' => $model->slide
                    ],
                    'language' => 'vi-VN',
                    'showUpload' => false,
                    'showUploadedThumbs' => false,
                    'initialPreview' => $slidePreview,
                    'initialPreviewConfig' => $slideInit,
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
            $form->field($model, 'slide_category[]')->widget(\kartik\widgets\FileInput::classname(), [
                'options' => [
                    'multiple' => false,
                    'id' => 'content-slidecategory',
                    'accept' => 'image/*'
                ],
                'pluginOptions' => [
                    'uploadUrl' => $upload_url,
                    'uploadExtraData' => [
                        'type' => \common\models\Content::IMAGE_TYPE_SLIDECATEGORY,
                        'logo_old' => $model->slide_category
                    ],
                    'language' => 'vi-VN',
                    'showUpload' => false,
                    'showUploadedThumbs' => false,
                    'initialPreview' => $logoPreview,
                    'initialPreviewConfig' => $logoInit,
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
                    'maxFileCount' => 20,
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

        </div>
    </div>


    <div class="row">

        <div class="form-group field-content-price">
            <label class="control-label col-md-2" for="content-price">Danh mục</label>

            <div class="col-md-10">
                <?= \common\widgets\Jstree::widget([
                    'clientOptions' => [
                        "checkbox" => ["keep_selected_style" => false],
                        "plugins" => ["checkbox"]
                    ],
                    'type' => 1,
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





    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'images')->hiddenInput(['id' => 'images_tmp'])->label(false) ?>
    <?php endif; ?>
    <?= Html::submitButton($model->isNewRecord ? 'Tạo' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>


    <?php ActiveForm::end(); ?>

</div>
