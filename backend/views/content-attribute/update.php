<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ContentAttribute */

$this->title = 'Cập nhật Thuộc tính nội dung: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Thuộc tính nội dung', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="content-attribute-update portlet light">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
