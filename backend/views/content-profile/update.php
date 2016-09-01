<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ContentProfile */

$this->title = 'Update Content Profile: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Content Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="content-profile-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
