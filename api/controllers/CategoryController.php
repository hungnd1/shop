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
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET'],
        ];
    }


    public function actionIndex($type = 0)
    {

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