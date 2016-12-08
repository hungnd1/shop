<?php

use common\models\ServiceProviderApiCredential;
use common\models\SiteApiCredential;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SiteApiCredential */

$this->title = $model->client_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Danh sách API KEY'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase"><?= Yii::t('app','Thông tin API KEY '). $model->client_name ?>"</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <p>
                    <?= Html::a('Cập nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Xóa', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app','Bạn chắc chắn muốn xóa?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>
                <?php switch($model->type){
                    case \common\models\SiteApiCredential::TYPE_WEB_APPLICATION:
                        echo DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'client_name',
                                'description:ntext',
                                'client_api_key',             // title attribute (in plain text)
                                'client_secret',  // description attribute in HTML
                                [
                                    'attribute' => 'type',
                                    'value' => SiteApiCredential::$api_key_types[$model->type]
                                ],
                                [
                                    'attribute' => 'status',
                                    'value' => SiteApiCredential::$credential_status[$model->status]
                                ],
                                [
                                    'attribute' => 'created_at',
                                    'value' => date('d/m/Y',$model->created_at)
                                ],
                                [
                                    'attribute' => 'updated_at',
                                    'value' => date('d/m/Y',$model->updated_at)
                                ],
                            ],
                        ]);
                        break;
                    case SiteApiCredential::TYPE_ANDROID_APPLICATION:
                        echo DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'client_name',
                                'description:ntext',
                                'client_api_key',             // title attribute (in plain text)
                                'package_name',
                                'certificate_fingerprint',
                                [
                                    'attribute' => 'type',
                                    'value' => SiteApiCredential::$api_key_types[$model->type]
                                ],
                                [
                                    'attribute' => 'status',
                                    'value' => SiteApiCredential::$credential_status[$model->status]
                                ],
                                [
                                    'attribute' => 'created_at',
                                    'value' => date('d/m/Y',$model->created_at)
                                ],
                                [
                                    'attribute' => 'updated_at',
                                    'value' => date('d/m/Y',$model->updated_at)
                                ],
                            ],
                        ]);
                        break;
                    case SiteApiCredential::TYPE_IOS_APPLICATION:
                        echo DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'client_name',             // title attribute (in plain text)
                                'description:ntext',
                                'client_api_key',             // title attribute (in plain text)
                                'client_secret',  // description attribute in HTML
                                'bundle_id',
                                'appstore_id',
                                [
                                    'attribute' => 'type',
                                    'value' => SiteApiCredential::$api_key_types[$model->type]
                                ],
                                [
                                    'attribute' => 'status',
                                    'value' => SiteApiCredential::$credential_status[$model->status]
                                ],
                                [
                                    'attribute' => 'created_at',
                                    'value' => date('d/m/Y',$model->created_at)
                                ],
                                [
                                    'attribute' => 'updated_at',
                                    'value' => date('d/m/Y',$model->updated_at)
                                ],
                            ],
                        ]);
                        break;
                } ?>
            </div>

        </div>
    </div>
</div>