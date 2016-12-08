<?php

use kartik\widgets\DateTimePicker;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
$avatarPreview = !$model->isNewRecord && !empty($model->image);
/* @var $this yii\web\View */
/* @var $model common\models\Voucher */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-body">

    <?php $form = ActiveForm::begin([
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'options' => ['enctype' => 'multipart/form-data'],
        'fullSpan' => 8,
        'formConfig' => [
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'labelSpan' => 2,
            'deviceSize' => ActiveForm::SIZE_SMALL,
        ],
//        'enableAjaxValidation' => true,
//        'enableClientValidation' => false,
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sale')->textInput() ?>

    <?= $form->field($model, 'image')->widget(\kartik\file\FileInput::classname(), [
        'pluginOptions' => [

            'showCaption' => false,
            'showRemove' => false,
            'showUpload' => false,
            'browseClass' => 'btn btn-primary btn-block',
            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
            'browseLabel' => Yii::t('app','Chọn hình ảnh đại diện'),
            'initialPreview' => $avatarPreview ? [
                Html::img(Yii::getAlias('@web').'/'.Yii::$app->params['avatar'] . "/" . $model->image, ['class' => 'file-preview-image',]),

            ] : [],
        ],
        'options' => ['accept' => 'image/*'],
    ]);
    ?>

    <?= $form->field($model, 'status')->dropDownList($model->getListStatus()) ?>

    <?= $form->field($model, 'date_start')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => Yii::t('app','Thời gian bắt đầu hiệu lực')],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd-mm-yyyy hh:ii:ss',
        ]
    ]);
    ?>

    <?= $form->field($model, 'date_end')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => Yii::t('app','Thời gian kết thúc')],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd-mm-yyyy hh:ii:ss',
        ]
    ]);
    ?>

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

</div>
