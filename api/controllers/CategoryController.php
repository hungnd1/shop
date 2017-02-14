<?php
/**
 * Created by PhpStorm.
 * User: VS9 X64Bit
 * Date: 22/05/2015
 * Time: 2:28 PM
 */

namespace api\controllers;


use api\helpers\Message;
use common\models\AccessSystem;
use common\models\Category;

use common\models\Subscriber;
use Yii;

use common\models\CategorySearch;

use yii\base\InvalidValueException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CategoryController extends ApiController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
//            'index',
            'view',
            'root-category',
            'test',
            'get-level-1',
            'get-level-info',
            'get-menu-top',
            'get-menu-top-all',
            'get-menu-right',
            'get-menu-right-all',
            'check-image-level-1',
            'get-image',
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET'],
            'get-menu-top' => ['GET'],
            'get-menu-top-all' => ['GET'],
            'get-level-1' => ['POST'],
            'get-menu-right' => ['GET'],
            'get-menu-right-all' => ['GET'],
            'get-level-info' => ['POST'],
            'get-image' => ['POST'],
            'check-image-level' => ['POST'],
        ];
    }


    public function actionIndex($type = 0)
    {

    }

    // menu top
    public function actionGetMenuTop(){
        $query = Category::find()
            ->select(['id','parent_id','display_name','child_count'])
            ->andWhere(['status' => Category::STATUS_ACTIVE])
            ->andWhere(['type'=> Category::TYPE_MENU_ABOVE]);
        $menu = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $menu;
    }

    public function actionGetMenuTopAll(){
        $query = Category::find()
            ->select(['id','parent_id','display_name','child_count'])
            ->andWhere(['status' => Category::STATUS_ACTIVE])
            ->andWhere(['type'=> Category::TYPE_MENU_ABOVE])
            ->andWhere("parent_id != :parent_id")->addParams([':parent_id'=>null]);
        $menu = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $menu;
    }

    public function actionGetLevel1(){
        $id = Yii::$app->request->post('id');
        $query   = Category::find()
            ->select(['id','parent_id','display_name','child_count'])
            ->andWhere(['status'=>Category::STATUS_ACTIVE])
            ->andWhere(['parent_id' => $id]);
        $cat = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $cat;
    }

    public function actionGetLevelInfo(){
        $id = Yii::$app->request->post('id');
        $query   = Category::find()
            ->select(['id','display_name', 'parent_id','child_count'])
            ->andWhere(['status'=>Category::STATUS_ACTIVE])
            ->andWhere(['id'=>$id]);
        $cat = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $cat;
    }

    public function actionGetImage(){
        $id = Yii::$app->request->post('id');
        $model = Category::findOne($id);
        $link = Category::getImageLinkFE($model->images);
        return $link;
    }

    public function actionCheckImageLevel1(){
        $id = Yii::$app->request->post('id');
        $model = Category::findOne($id);
        if($model->location_image == Category::LOCATION_LEFT){
            return 1;
        }
        if($model->location_image == Category::LOCATION_TOP){
            return  2;
        }
        if($model->location_image == Category::NO_IMAGE){
            return 3;
        }
        if($model->location_image == Category::LOCATION_BOTTOM){
            return 4;
        }
    }

    // get menu right

    public function actionGetMenuRight(){
        $query = Category::find()
            ->select(['id','parent_id','display_name','child_count'])
            ->andWhere(['status' => Category::STATUS_ACTIVE])
            ->andWhere(['parent_id'=> null])
            ->andWhere(['type'=> Category::TYPE_MENU_RIGHT]);
        $menu = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $menu;
    }
    public function actionGetMenuRightAll(){
        $query = Category::find()
            ->select(['id','parent_id','display_name','child_count'])
            ->andWhere(['status' => Category::STATUS_ACTIVE])
            ->andWhere("parent_id != :parent_id")->addParams([':parent_id'=>null])
            ->andWhere(['type'=> Category::TYPE_MENU_RIGHT]);
        $menu = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $menu;
    }


    /**
     * Build lai mang
     *
     * @param $array
     * @param $item
     * @return array
     */
    public function removeItemArray(&$array, $item)
    {
        $data = array();
        if (count($array) > 0) {
            foreach ($array as $it) {
                if ($item['id'] != $it['id']) {//khong lay phan tu da duoc dua vao trong children
                    array_push($data, $it);
                }
            }
        }
        return $data;

    }

    public function actionTest()
    {
        $res = [];
        $res['film'] = "a";
        $res['music'] = 'b';
        return $res;
    }
}