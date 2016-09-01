<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ContentFeedback */

$this->title = 'Create Content Feedback';
$this->params['breadcrumbs'][] = ['label' => 'Content Feedbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-feedback-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
