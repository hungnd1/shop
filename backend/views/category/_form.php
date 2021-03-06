<?php

use common\models\Category;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Html;
use common\models\Site;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */
$showPreview = !$model->isNewRecord && !empty($model->images);
?>

<?php $form = ActiveForm::begin([
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'fullSpan' => 8,
    'options' => ['enctype' => 'multipart/form-data'],
    'formConfig' => [
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'labelSpan' => 3,
        'deviceSize' => ActiveForm::SIZE_SMALL,
    ],
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
]); ?>
<div class="form-body">

    <?= $form->field($model, 'display_name')->textInput(['maxlength' => 200, 'class' => 'input-circle']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php if ($showPreview) { ?>
        <div class="form-group field-category-icon">
            <div class="col-sm-offset-3 col-sm-5">
                <?php echo Html::img($model->getImageLink(), ['class' => 'file-preview-image']) ?>
            </div>
        </div>
    <?php } ?>

    <?= $form->field($model, 'images')->widget(FileInput::classname(), [
        'options' => ['multiple' => true, 'accept' => 'image/*'],
        'pluginOptions' => [
            'previewFileType' => 'image',
            'showUpload' => false,
            'showPreview' => (!$showPreview) ? true : false,
        ]
    ])->hint(Yii::t('app','Ảnh danh mục cấp 1 có ảnh tỉ lệ 1.2 chính xác 16x13, Yêu cầu up nội dung chính xác.')); ?>

    <?= $form->field($model, 'location_image')->dropDownList(
        Category::getLocationImage(), ['class' => 'input-circle']
    ) ?>

    <?= $form->field($model, 'status')->dropDownList(
        Category::getListStatus(), ['class' => 'input-circle']
    ) ?>

    <?= $form->field($model, 'is_news')->checkbox() ?>
    <?= $form->field($model, 'hide')->checkbox() ?>

    <?php $listCheckbox = \common\models\Category::getListType(); ?>
    <?= $form->field($model, 'type')->dropDownList($listCheckbox)->label(Yii::t('app','Vị trí menu')) ?>

    <?php
    $dataList = \common\models\Category::getTreeCategories();
    $disableId = false;
    if (!$model->isNewRecord) {
        $disableId = $model->id;
    }
    echo $form->field($model, 'parent_id')->dropDownList($dataList,
        [
            'prompt' => Yii::t('app','-Chọn nhóm cha-'),
            'options' => \common\models\Category::getAllChildCats($model->id) + [$model->id => ['disabled' => true]]
        ]);
    ?>
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Tạo danh mục') : Yii::t('app','Cập nhật'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app','Quay lại'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
