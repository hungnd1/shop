<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ContentViewLog */

$this->title = 'Create Content View Log';
$this->params['breadcrumbs'][] = ['label' => 'Content View Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-view-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
