<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProgramSuppost */
/* @var $form yii\widgets\ActiveForm */
$avatarPreview = !$model->isNewRecord && !empty($model->image);
?>

<div class="form-body">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data'],
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'fullSpan' => 12,
        'formConfig' => [
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'showLabels' => true,
            'labelSpan' => 2,
            'deviceSize' => ActiveForm::SIZE_SMALL,
        ],
//        'enableAjaxValidation' => true,
//        'enableClientValidation' => false,
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->widget(\kartik\file\FileInput::classname(), [
        'pluginOptions' => [

            'showCaption' => false,
            'showRemove' => false,
            'showUpload' => false,
            'browseClass' => 'btn btn-primary btn-block',
            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
            'browseLabel' => Yii::t('app','Chọn hình ảnh đại diện'),
            'initialPreview' => $avatarPreview ? [
                Html::img(Yii::getAlias('@web').'/'.Yii::getAlias('@voucher_images'). "/" . $model->image, ['class' => 'file-preview-image',]),

            ] : [],
        ],
        'options' => ['accept' => 'image/*'],
    ]);
    ?>

    <?= $form->field($model, 'short_des')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'des')->widget(\dosamigos\ckeditor\CKEditor::className(), [
        'options' => ['rows' => 8],
        'preset' => 'basic'
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList($model->getListStatus()) ?>

    <div class="form-group text-center">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
