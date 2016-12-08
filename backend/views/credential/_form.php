<?php

use common\models\ServiceProviderApiCredential;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\SiteApiCredential */
/* @var $form yii\widgets\ActiveForm */
$credential_type = Html::getInputId($model,'type');
$js = <<<JS
function updateForm(){
        scenario = jQuery('#$credential_type').val();
        console.log('scenario:' + scenario);
        jQuery('.form-group').show();
        switch (scenario){
            case "0":
                //Giao dien web application
                jQuery('#credential-form .field-siteapicredential-package_name').hide();
                jQuery('#credential-form .field-siteapicredential-certificate_fingerprint').hide();
                jQuery('#credential-form .field-siteapicredential-bundle_id').hide();
                jQuery('#credential-form .field-siteapicredential-appstore_id').hide();
                break;
            case "1":
                //Giao dien Android application
                jQuery('#credential-form .field-siteapicredential-bundle_id').hide();
                jQuery('#credential-form .field-siteapicredential-appstore_id').hide();
                jQuery('#credential-form .field-siteapicredential-client_secret').hide();
                break;
            case "2":
                //Giao dien iOS application
                jQuery('#credential-form .field-siteapicredential-package_name').hide();
                jQuery('#credential-form .field-siteapicredential-certificate_fingerprint').hide();
                break;
        }
    }
JS;

$this->registerJs($js, View::POS_END);
$this->registerJs('updateForm();',View::POS_READY);

?>

<?php $form = ActiveForm::begin([
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'fullSpan' => 8,
    'id' => 'credential-form' ,
    'formConfig' => [
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'labelSpan' => 3,
        'deviceSize' => ActiveForm::SIZE_SMALL,
    ],
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
]); ?>
<div class="form-body">
    <?= $form->field($model, 'client_name')->textInput(['maxlength' => 200, 'class' => 'input-circle']) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6, 'class' => 'input-circle']) ?>
    <?= $form->field($model, 'type')->dropDownList(\common\models\SiteApiCredential::$api_key_types,['onchange' => 'updateForm()']); ?>
    <?= $form->field($model, 'client_api_key')->textInput(['maxlength' => 200, 'class' => 'input-circle', 'readonly' => true]) ?>
    <?= $form->field($model, 'client_secret')->textInput(['maxlength' => 200, 'class' => 'input-circle', 'readonly' => true]) ?>
    <?= $form->field($model, 'status')->dropDownList(\common\models\SiteApiCredential::$credential_status) ?>

</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Tạo API Key') : Yii::t('app','Cập nhật'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app','Quay lại'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
