<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ContentProfile */

$this->title = 'Create Content Profile';
$this->params['breadcrumbs'][] = ['label' => 'Content Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-profile-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
