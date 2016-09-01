<?php

namespace backend\controllers;

use Yii;
use common\models\SumServiceAmount;
use common\models\SumServiceAmountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\auth\filters\Yii2Auth;
use yii\data\ArrayDataProvider;
use common\helpers\CUtils;

/**
 * SumServiceAmountController implements the CRUD actions for SumServiceAmount model.
 */
class SumServiceAmountController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all SumServiceAmount models.
     * @return mixed
     */
    public function actionIndex()
    {
        $param = Yii::$app->request->post();

        $currentTime = time();
        $currentDate = date('Y-m-d',$currentTime);

        $query = SumServiceAmount::find();
        $typeTime = isset($param['typeTime'])?$param['typeTime']:3;

        //1: Năm    , 2: Tháng - Năm     , 3: Khoảng thời gian
        if($typeTime == 1){
            $year = $param['divYears'] + 2014;//theo index thi index = 1 -> 2014
            $from_date = $year.'-01-01';
            $to_date = $year.'-12-31';
        }else if($typeTime ==2 ){
            $year = $param['divYears'] + 2014;//theo index thi index = 1 -> 2014
            $month =  $param['divMonths'];
            $from_date = $year.'-'.$month.'-01';
            $to_date = CUtils::lastday('Y-m-d',$month,$year);
        }else if($typeTime ==3 ){
            //Nếu không chọn khoảng thời gian thì sẽ lấy ra mặc định là 1 tháng trước đó
            if(isset($param['from_date']) && !empty($param['from_date']) ){
                $from_date = date_format(date_create($param['from_date']), 'Y-m-d');
            }else{
                $from_date = date('Y-m-d',strtotime('-30 day', $currentTime) );
            }
            $query = $query->andWhere(':from_date <= report_date',[':from_date' =>$from_date] );

            if(isset($param['to_date']) && !empty($param['from_date']) ){
                $to_date = date_format(date_create($param['to_date']), 'Y-m-d');
            }else{
                $to_date = $currentDate;
            }
            $query = $query->andWhere(':to_date >= report_date',[':to_date' =>$to_date] );
        }

        // Add query phần ngày tháng
        $query = $query->andWhere(':from_date <= report_date',[':from_date' =>$from_date] );
        $query = $query->andWhere(':to_date >= report_date',[':to_date' =>$to_date] );

        //Nếu không chọn site_id thì search all
        if(isset($param['site_id']) && !empty($param['site_id']) ){
            $site_id = $param['site_id'];
            $query = $query->andWhere('site_id =:site_id',[':site_id' =>(int)$site_id] );
        }


        $lst = array();
        //Nếu là kiểu Year thì sẽ groupBy theo tháng
        if($typeTime ==1){
            $query = $query->addSelect('sum(amount) as amount')
                ->addSelect('report_date')
                ->groupBy('month(report_date)');
            //Thực thi SQL và push dữ liệu cho View
            $lstItems = $query->asArray()->all();
            foreach($lstItems as $item){
                $obj = new SumServiceAmount();
                $obj->site_id =isset($site_id)?$site_id:'';
                $obj->amount = $item['amount'];
                $obj->report_date = date_format(date_create($item['report_date']), 'm-Y');

                array_push($lst,$obj);
            }
        }else{
            $query = $query->addSelect('sum(amount) as amount')
                ->addSelect('report_date')
                ->groupBy('report_date');
            //Thực thi SQL và push dữ liệu cho View
            $lstItems = $query->asArray()->all();
            foreach($lstItems as $item){
                $obj = new SumServiceAmount();
                $obj->site_id =isset($site_id)?$site_id:'';
                $obj->amount = $item['amount'];
                $obj->report_date = date_format(date_create($item['report_date']), 'd-m-Y');

                array_push($lst,$obj);
            }
        }

        $dataProvider = self::getDataProvider($lst);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'site_id' => isset($site_id)?$site_id:'',
            'typeTime' => $typeTime,
            'monthIndex' => isset($month)?$month:(int)date('m'),
            'yearIndex' => isset($year)?$year:1,
        ]);
    }

    /**
     * @param $models
     * @return ArrayDataProvider
     */
    private function getDataProvider($models){
        return new ArrayDataProvider([
            'allModels' => $models,
            'sort' => [
                'attributes' => ['report_date'],
            ],
//            'pagination' => [
//                'pageSize' => Yii::$app->params['pageSize'],
//            ],
        ]);
    }

    /**
     * Displays a single SumServiceAmount model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SumServiceAmount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SumServiceAmount();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SumServiceAmount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SumServiceAmount model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SumServiceAmount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SumServiceAmount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SumServiceAmount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
