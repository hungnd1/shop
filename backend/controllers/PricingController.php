<?php

namespace backend\controllers;

use common\models\Pricing;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * ServiceGroupController implements the CRUD actions for ServiceGroup model.
 */
class PricingController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'view' => ['GET'],
                ],
            ],
        ]);
    }


    /**
     * Displays a single ServiceGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $active = 1)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'active' => $active
        ]);
    }

    /**
     * Finds the ServiceGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pricing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pricing::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
