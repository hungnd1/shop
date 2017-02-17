<?php

namespace common\models;

use api\helpers\UserHelpers;
//use api\models\Content;
use common\helpers\CUtils;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property integer $is_news
 * @property integer $hide
 * @property integer $show_on_portal
 * @property string $display_name
 * @property string $ascii_name
 * @property string $description
 * @property integer $status
 * @property integer $location_image
 * @property integer $order_number
 * @property integer $parent_id
 * @property string $path
 * @property integer $level
 * @property integer $child_count
 * @property string $images
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $type
 *
 * @property Category $parent
 * @property Category[] $categories
 * @property ContentCategoryAsm[] $contentCategoryAsms
 *
 */
class Category extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE   = 10;
    const STATUS_INACTIVE = 0;
    const CHILD_NODE_PREFIX  = '|--';
    public $path_name;
    private static $catTree  = array();

    const TYPE_MENU_RIGHT = 1;
    const TYPE_MENU_ABOVE = 2;
    const TYPE_NONE = 0;

    // type tin tức không hiển thị trong body index
    const TYPE_NEWS_BODY = 3;

    // vị trí hiển thị ảnh
    const NO_IMAGE = 0;
    const LOCATION_TOP = 1;
    const LOCATION_LEFT = 2;
    const LOCATION_BOTTOM = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['display_name'], 'required', 'message' => '{attribute} không được để trống', 'on' => 'admin_create_update'],
            [
                [
                    'status',
                    'order_number',
                    'parent_id',
                    'level',
                    'child_count',
                    'created_at',
                    'updated_at',
                    'type',
                    'location_image',
                    'is_news',
                    'hide',
                ],
                'integer',
            ],
            [['description'], 'string'],
            [['display_name', 'ascii_name'], 'string', 'max' => 200],
            [['images'], 'string', 'max' => 500],
            [['images'], 'safe'],
            [['images'],
                'file',
                'tooBig'         => ' File ảnh chưa đúng quy cách. Vui lòng thử lại',
                'wrongExtension' => ' File ảnh chưa đúng quy cách. Vui lòng thử lại',
                'skipOnEmpty'    => true,
                'extensions'     => 'png, jpg, jpeg', 'maxSize' => 10 * 1024 * 1024]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => Yii::t('app', 'ID'),
            'display_name'     => Yii::t('app', 'Tên danh mục'),
            'ascii_name'       => Yii::t('app', 'Ascii Name'),
            'description'      => Yii::t('app', 'Mô tả'),
            'status'           => Yii::t('app', 'Trạng thái'),
            'order_number'     => Yii::t('app', 'Sắp xếp'),
            'parent_id'        => Yii::t('app', 'Danh mục cha'),
            'path'             => Yii::t('app', 'Path'),
            'level'            => Yii::t('app', 'Level'),
            'child_count'      => Yii::t('app', 'Child Count'),
            'images'           => Yii::t('app', 'Ảnh đại diện'),
            'created_at'       => Yii::t('app', 'Ngày tạo'),
            'updated_at'       => Yii::t('app', 'Ngày thay đổi thông tin'),
            'type'             => Yii::t('app', 'Kiểu menu'),
            'location_image'   => Yii::t('app', 'Vị trí hiển thị ảnh'),
            'hide'   => Yii::t('app', 'Không hiện trên trang chủ chỉ hiện trên menu'),
            'is_news'   => Yii::t('app', 'Danh mục dành riêng cho loại tin tức'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
            return $this->hasMany(Category::className(), ['parent_id' => 'id'])
                ->andWhere(['category.status' => Category::STATUS_ACTIVE]) // Ai commen phải báo loop vì comment là không đúng
                ->orderBy(['order_number' => SORT_DESC])->all();

    }

    public function getBECategories()
    {
            return $this->hasMany(Category::className(), ['parent_id' => 'id'])
                ->orderBy(['order_number' => SORT_DESC])->all();

    }

    public static  function getListType(){
        return $getListType = [
            self::TYPE_NONE => Yii::t('app','Menu bình thường'),
            self::TYPE_MENU_ABOVE => Yii::t('app','Menu trên'),
            self::TYPE_MENU_RIGHT => Yii::t('app','Menu trái'),
        ];
    }

    public static  function getLocationImage(){
        return $getLocationImage = [
            self::NO_IMAGE => Yii::t('app','Không có ảnh'),
            self::LOCATION_TOP => Yii::t('app','Hiển thị bên trên'),
            self::LOCATION_LEFT => Yii::t('app','Hiển thị bên trái'),
            self::LOCATION_BOTTOM => Yii::t('app','Hiển thị bên dưới'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentCategoryAsms()
    {
        return $this->hasMany(ContentCategoryAsm::className(), ['category_id' => 'id']);
    }

    public static function getTreeCategories($sp_id = null)
    {
        return ArrayHelper::map(Category::getAllCategories(null, true, $sp_id), 'id', 'path_name');
    }

    public static function getAllCategories($cat_id = null, $recursive = true)
    {
        $res = [];
        if ($cat_id != null) {
            $model = Category::findOne(['id' => $cat_id]);

            if ($model === null) {
                throw new NotFoundHttpException(404, "The requested Vod Category (#$cat_id) does not exist.");
            }

            //Thuc: them 'order'=>'order_number ASC' trong ham relations
            $children = $model->getBECategories();

            if ($children) {
                foreach ($children as $child) {
                    $path = "";
                    for ($i = 0; $i < $child->level; $i++) {
                        $path .= Category::CHILD_NODE_PREFIX;
                    }
//                    $child->name = $path . $child->name;
                    $child->path_name = $path . $child->display_name;
                    $res[]            = $child;
                    if ($recursive) {
                        $res = ArrayHelper::merge($res,
                            Category::getAllCategories($child->id, $recursive));
                    }
                }
            }
        } else {
                $root_cats = Category::find()->andWhere(['level' => 0])
                    ->orderBy(['order_number' => SORT_DESC])->all();
            if ($root_cats) {
                foreach ($root_cats as $cat) {
                    /* @var $cat Category */
                    $cat->path_name = $cat->display_name;
                    $res[]          = $cat;
                    if ($recursive) {
                        $res = ArrayHelper::merge($res,
                            Category::getAllCategories($cat->id, $recursive));
                    }
                }
            }
        }

        return $res;
    }

    public static function getListStatus()
    {
        return [
            self::STATUS_ACTIVE   => 'Đang hoạt động',
            self::STATUS_INACTIVE => 'Tạm khóa',
        ];
    }

    public function getStatusName()
    {
        $listStatus = self::getListStatus();
        if (isset($listStatus[$this->status])) {
            return $listStatus[$this->status];
        }
        return '';
    }

    public function getIconUrl()
    {
        return Yii::getAlias($this->images);
    }

    public function getImageLink()
    {
        $cat_image=  Yii::getAlias('@cat_image');
        return $this->images ? Url::to('@web/'.$cat_image.'/'.$this->images, true) : '';
    }
    public static function getImageLinkFE($images)
    {
        return $images?Url::to('@web/staticdata/category_image/'. $images, true) : '';
    }


    /**
     * return : 1: max, 2: min, 3: middle
     */
    public function checkPositionOnTree()
    {
        if ($this->parent_id == null) {
            $minMaxOrder = Category::find()->select(['max(order_number) as max', 'min(order_number) as min'])
                ->where('parent_id is null')->asArray()->one();
        } else {
            $minMaxOrder = Category::find()->select(['max(order_number) as max', 'min(order_number) as min'])
                ->where('parent_id =:p_parent_id', [':p_parent_id' => $this->parent_id])->asArray()->one();
        }
        if ($minMaxOrder) {
            if ($minMaxOrder['max'] == $minMaxOrder['min']) {
                return 3;
            }
            if ($minMaxOrder['max'] <= $this->order_number) {
                return 1;
            } else if ($minMaxOrder['min'] >= $this->order_number) {
                return 2;
            }

            return 4;
        }
    }

    public static function getMenuTree($type)
    {
        if (empty(self::$catTree[$type])) {
            $query = Category::find();
            $query->andWhere(['category.level' => 0]);
            $query->andWhere(['category.status' => self::STATUS_ACTIVE]);
            $query->orderBy(['category.order_number' => SORT_ASC]);
            $rows = $query->all();
            // var_dump($cp_id);die;
            if (count($rows) > 0) {
                foreach ($rows as $item) {
                    /** @var $item Category */
                    self::$catTree[$type][] = self::getMenuItems($item);
                }
            } else {
                self::$catTree[$type] = [];
            }
            Yii::info(self::$catTree[$type]);
        }
        return self::$catTree[$type];

    }

    /**
     * @param $modelRow Category
     * @return array|void
     */
    private static function getMenuItems($modelRow)
    {

        if (!$modelRow) {
            return;
        }

        if (isset($modelRow->categories)) {
            /** @var  $modelRow Category */
            $childCategories = $modelRow->getCategories();

            $chump = self::getMenuItems($childCategories);
            if ($chump != null) {
                $res = array('id' => $modelRow->id, 'label' => $modelRow->display_name, 'items' => $chump);
            } else {
                $res = array('id' => $modelRow->id, 'label' => $modelRow->display_name, 'items' => array());
            }
            return $res;
        } else {
            if (is_array($modelRow)) {
                $arr = array();
                foreach ($modelRow as $leaves) {
                    $arr[] = self::getMenuItems($leaves);
                }
                return $arr;
            } else {
                return array('id' => $modelRow->id, 'label' => ($modelRow->display_name));
            }
        }
    }

    /**
     * @param null $cat_id
     * @param bool $recursive
     * @param int $type
     * @param null $sp_id
     * @param int $is_content_service
     * @return array
     * @throws NotFoundHttpException
     */
    public static function getApiAllCategories($parren_id = null, $recursive = true)
    {
        $res = [];
        if ($parren_id != null) {
//            $model = Category::findOne(['id' => $parren_id,'status'=>Category::STATUS_ACTIVE]);
            /** @var  $model Category*/
            $model = Category::find()
                ->andWhere(['category.id' => $parren_id, 'status' => Category::STATUS_ACTIVE])
                ->one();

            if ($model === null) {
                throw new NotFoundHttpException(404, "The requested Vod Category (#$parren_id) does not exist.");
            }

            //Thuc: them 'order'=>'order_number ASC' trong ham relations
            $children = $model->getCategories();
            if ($children) {
                /**
                 * @var $child Category
                 */
                foreach ($children as $child) {
                    $path = "";
                    for ($i = 0; $i < $child->level; $i++) {
                        $path .= Category::CHILD_NODE_PREFIX;
                    }
//                    $child->name = $path . $child->name;
                    $child->path_name         = $path . $child->display_name;
                    $child_array              = $child->getAttributes(null, ['tvod1_id', 'is_content_service', 'show_on_portal', 'show_on_client', 'order_number', 'admin_note', 'path', 'updated_at', 'created_at']);
                    $child_array['images']    = $child->getImageLink();
                    $child_array['shortname'] = CUtils::parseTitleToKeyword($child->display_name);
                    if ($recursive) {
                        $child_array['children'] = Category::getApiAllCategories($child->id, $recursive);
                    }
                    $res[] = $child_array;
                }
            }
        } else {
        }

        return $res;
    }

    /*
     * API của anh Cường tạm thời comment lại để viết lại và bổ sung
     */



    public static function countContent($cat_id)
    {
        //validate
        /** @var $count */
        $count = ContentCategoryAsm::findAll(['category_id' => $cat_id]);
        if (count($count)) {
            return count($count);
        }
        return false;
    }

    public static function getAllChildCats( $parent = null)
    {
        if ($parent === null) {
            return [];
        }

        static $listCat = [];

        $cats = self::findAll([ 'parent_id' => $parent]);

        if (!empty($cats)) {
            foreach ($cats as $cat) {

                $listCat[$cat->id] = ['disabled' => true];
                self::getAllChildCats( $cat->id);
            }
        }

        return $listCat;
    }


}
