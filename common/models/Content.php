<?php

namespace common\models;

use api\models\ListContent;
use common\helpers\CVietnameseTools;
use Yii;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

//use yii\swiftmailer\Message;

/**
 * This is the model class for table "content".
 *
 * @property int $id
 * @property string $display_name
 * @property string $code
 * @property string $ascii_name
 * @property string $title_short
 * @property int $type
 * @property string $tags
 * @property string $short_description
 * @property string $description
 * @property string $content
 * @property int $version_code
 * @property string $version
 * @property int $view_count
 * @property int $download_count
 * @property int $like_count
 * @property int $dislike_count
 * @property float $rating
 * @property int $rating_count
 * @property int $comment_count
 * @property int $favorite_count
 * @property string $images
 * @property int $status
 * @property int $is_slide
 * @property int $is_slide_category
 * @property int $created_at
 * @property int $expired_at
 * @property int $updated_at
 * @property int $honor
 * @property string $address
 * @property int $approved_at
 * @property Content $parent
 * @property string $condition
 * @property string $highlight
 * @property int $price_promotion
 * @property int $price
 * @property string $language
 * @property int $order
 * @property int $type_status
 * @property int $availability
 *
 * @property ContentCategoryAsm[] $contentCategoryAsms
 */
class Content extends \yii\db\ActiveRecord
{
    const IMAGE_TYPE_SLIDECATEGORY = 1;
    const IMAGE_TYPE_THUMBNAIL = 2;
    const IMAGE_TYPE_SCREENSHOOT = 3;
    const IMAGE_TYPE_SLIDE = 4;
    const IMAGE_TYPE_THUMBNAIL_EPG = 5;

    public $logo;
    public $thumbnail;
    public $slide_category;
    public $screenshoot;
    public $slide;
    public $image_tmp;
    public $started_at;
    public $url_thumbnail;
    public $url_slide;
    public $ended_at;
    public $viewAttr = [];

    //type: 1 là bán chạy, 2 là giảm giá, 3 là sản phẩm mới,4 là lasted deal // danh cho phần quản lý seller
    //type_status: tình trạng hàng 1 là hàng mới, 2 là hàng 99%, 3 là hàng đã qua sử dụng
    //honor: thể loại hàng: Sản phẩm hot nhất, Sản phẩm mới nhất, Giá tốt, Danh cho bạn
    //availability là trạng thái hàng: 1 là còn hàng, 0 là hết hàng
    const AVAILABILITY_OK = 1;//con hàng
    const AVAILABILITY_NOK = 0;//hết hàng

    const TYPE_STATUS_NEW = 1; //hang moi
    const TYPE_STATUS_NEWLIKE = 2; //hang 99%
    const TYPE_STATUS_OLD = 3; // hang cu

    const STATUS_ACTIVE = 10; // Đã duyệt
    const STATUS_INACTIVE = 0; // khóa
    const STATUS_DELETE = 2; // Xóa
    const STATUS_PENDING = 3; // CHỜ DUYỆT

    const ORDER_NEWEST = 1;
    const HONOT_NORMAL = 0;
    const HONOR_HOT = 1;
    const HONOR_NEWEST = 2;
    const HONOR_PRICE = 3;
    const HONOR_FORYOU = 4;

    const TYPE_SELLER = 1;
    const TYPE_PRICEPROMO = 2;
    const TYPE_NEWEST = 3;
    const TYPE_DEAL = 4;

    const MAX_SIZE_UPLOAD = 10485760; // 10 * 1024 * 1024

    public static $list_type = [
        self::TYPE_SELLER => 'Bán chạy nhất',
        self::TYPE_NEWEST => 'Mới nhất',
        self::TYPE_DEAL => 'Lastest Deal',
        self::TYPE_PRICEPROMO => 'Giảm giá'
    ];

    public static $list_honor = [
        self::HONOR_HOT => 'Sản phẩm hot nhất',
        self::HONOR_NEWEST => 'Sản phẩm mới nhất',
        self::HONOR_PRICE => 'Giá tốt nhất',
        self::HONOR_FORYOU => 'Dành cho bạn',
    ];

    public static $list_honorDetail = [
        self::HONOT_NORMAL => 'Sản phẩm bình thường',
        self::HONOR_HOT => 'Sản phẩm hot nhất',
        self::HONOR_NEWEST => 'Sản phẩm mới nhất',
        self::HONOR_PRICE => 'Giá tốt nhất',
        self::HONOR_FORYOU => 'Dành cho bạn',
    ];


    public static $listTypeStatus = [
        self::TYPE_STATUS_NEW => 'New',
        self::TYPE_STATUS_NEWLIKE => '99%',
        self::TYPE_STATUS_OLD => 'Đã qua sử dụng'
    ];

    public static $listAvailability = [
        self::AVAILABILITY_OK => 'Còn hàng',
        self::AVAILABILITY_NOK => 'Hết hàng'
    ];

    public $list_cat_id;
    public $assignment_sites;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge([
            [['display_name', 'code', 'status', 'list_cat_id', 'price','expired_at'], 'required', 'on' => 'adminModify', 'message' => '{attribute} không được để trống'],
            [['started_at', 'ended_at'], 'required', 'message' => Yii::t('app','{attribute} không được để trống'), 'on' => 'adminModifyLiveContent'],
            [['ended_at'], 'validEnded', 'on' => 'adminModifyLiveContent'],
            [['display_name', 'code'], 'required', 'message' => Yii::t('app','{attribute} không được để trống')],
            [
                [
                    'type',
                    'price',
                    'is_slide',
                    'is_slide_category',
                    'price_promotion',
                    'version_code',
                    'view_count',
                    'download_count',
                    'like_count',
                    'dislike_count',
                    'rating_count',
                    'comment_count',
                    'favorite_count',
                    'status',
                    'created_at',
                    'updated_at',
                    'honor',
                    'expired_at',
                    'approved_at',
                    'order',
                    'type_status',
                    'availability'
                ], 'integer',
            ],
            [['description','url_thumbnail','url_slide', 'content', 'condition', 'images', 'short_description', 'images', 'highlight','title_short'], 'string'],
            [['rating'], 'number'],
            [['display_name', 'ascii_name', 'country'], 'string', 'max' => 128],
            [['code'], 'string', 'max' => 20],
            [['tags', 'address'], 'string', 'max' => 500],
            [['version'], 'string', 'max' => 64],
            [['language'], 'string', 'max' => 10],
            [['code'], 'unique', 'message' => Yii::t('app','{attribute} đã tồn tại trên hệ thống. Vui lòng thử lại')],
            [['thumbnail', 'screenshoot', 'slide','slide_category'],
                'file',
                'tooBig' => Yii::t('app','{attribute} vượt quá dung lượng cho phép. Vui lòng thử lại'),
                'wrongExtension' => Yii::t('app','{attribute} không đúng định dạng'),
                'extensions' => 'png, jpg, jpeg, gif',
                'maxSize' => self::MAX_SIZE_UPLOAD],
            [['thumbnail','slide_category'], 'validateThumb', 'on' => ['adminModify', 'adminModifyLiveContent']],
            [['screenshoot'], 'validateScreen', 'on' => 'adminModify'],
            [['thumbnail','screenshoot', 'slide','slide_category'], 'image', 'extensions' => 'png,jpg,jpeg,gif',
//                'minWidth' => 1, 'maxWidth' => 512,
//                'minHeight' => 1, 'maxHeight' => 512,
                'maxSize' => 1024 * 1024 * 10, 'tooBig' => Yii::t('app','Ảnh show  vượt quá dung lượng cho phép. Vui lòng thử lại'),
            ],
            [['image_tmp', 'list_cat_id'], 'safe'],
        ], $this->getValidAttr());
    }

    public function validEnded($attribute, $params)
    {
        if (strtotime($this->ended_at) < strtotime($this->started_at)) {
            $this->addError($attribute, $this->attributeLabels()[$attribute] . ' phải lớn hơn ' . $this->attributeLabels()['started_at']);
            return false;
        }
    }

    public function validateThumb($attribute, $params)
    {
        if (empty($this->images)) {
            $this->addError($attribute, str_replace('(*)', '', $this->attributeLabels()[$attribute]) . ' không được để trống');
            return false;
        }
        $images = $this->convertJsonToArray($this->images, true);

        $thumb = array_filter($images, function ($v) {
            return $v['type'] == self::IMAGE_TYPE_THUMBNAIL;
        });

        if (count($thumb) === 0) {
            $this->addError($attribute, str_replace('(*)', '', $this->attributeLabels()[$attribute]) . ' không được để trống');
            return false;
        }
    }


    public function validateScreen($attribute, $params)
    {
        if (empty($this->images)) {
            $this->addError($attribute, str_replace('(*)', '', $this->attributeLabels()[$attribute]) . ' không được để trống');
            return false;
        }

        $images = $this->convertJsonToArray($this->images, true);

        $screenshoot = array_filter($images, function ($v) {
            return $v['type'] == self::IMAGE_TYPE_SCREENSHOOT;
        });

        if (count($screenshoot) === 0) {
            $this->addError($attribute, str_replace('(*)', '', $this->attributeLabels()[$attribute]) . ' không được để trống');
            return false;
        }
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->status == self::STATUS_ACTIVE) {
                $this->approved_at = time();
            }

            return true;
        } else {
            return false;
        }
    }

    public function getValidAttr()
    {
        return [];

        // $this->getContentAttr();
        // // var_dump($this->validAttr);die;
        // return $this->validAttr;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'display_name' => Yii::t('app', 'Tên hiển thị'),
            'code' => Yii::t('app','Mã code'),
            'ascii_name' => Yii::t('app','Ascii Name'),
            'title_short' => Yii::t('app','Tên hiển thị ngắn'),
            'type' => Yii::t('app','Type'),
            'tags' => Yii::t('app','Tags'),
            'type_status'=>Yii::t('app','Tình trạng hàng'),
            'availability'=>Yii::t('app','Trạng thái hàng'),
            'short_description' => Yii::t('app','Mô tả ngắn'),
            'highlight' => Yii::t('app','Điểm nổi bật'),
            'condition' => Yii::t('app','Điều kiện sử dụng'),
            'price_promotion' => Yii::t('app','Giá khuyến mãi'),
            'price' => Yii::t('app','Giá gốc'),
            'description' => Yii::t('app','Mô tả'),
            'content' => Yii::t('app','Nội dung'),
            'urls' =>Yii::t('app', 'Urls'),
            'version_code' => Yii::t('app','Phiên bản code'),
            'version' => Yii::t('app','Phiên bản'),
            'view_count' => Yii::t('app','Tổng lượt xem'),
            'download_count' => Yii::t('app','Download Count'),
            'like_count' => Yii::t('app','Tổng lượt thích'),
            'dislike_count' => Yii::t('app','Tổng lượt ko thích'),
            'rating' => Yii::t('app','Đánh giá'),
            'rating_count' => Yii::t('app','Tổng lượt đánh giá'),
            'expired_at' => Yii::t('app','Ngày hết hạn'),
            'comment_count' => Yii::t('app','Tổng số nhận xét'),
            'favorite_count' => Yii::t('app','Tổng lượt thích'),
            'is_catchup' => Yii::t('app', 'Truyền hình xem lại'),
            'images' => Yii::t('app','Hình ảnh'),
            'status' => Yii::t('app', 'Trạng thái'),
            'created_at' => Yii::t('app','Ngày tạo'),
            'updated_at' => Yii::t('app','Ngày cập nhật'),
            'honor' => Yii::t('app','Loại nội dung'),
            'address' => Yii::t('app','Địa chỉ'),
            'approved_at' => Yii::t('app','Ngày phê duyệt'),
            'parent_id' => Yii::t('app','Cha'),
            'country' => Yii::t('app','Nước'),
            'language' => Yii::t('app','Ngôn ngữ'),
            'thumbnail' => Yii::t('app','Ảnh đại diện (*)'),
            'screenshoot' => Yii::t('app','Ảnh screen show (*)'),
            'slide' => Yii::t('app','Ảnh slide trang chủ '),
            'list_cat_id' => Yii::t('app','Danh mục  nội dung'),
            'started_at' => Yii::t('app','Thời gian bắt đầu'),
            'ended_at' => Yii::t('app','Thời gian kết thúc'),
            'order' => Yii::t('app','Sắp xếp'),
            'slide_category' => Yii::t('app','Ảnh slide Danh mục'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Content::className(), ['id' => 'parent_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentCategoryAsms()
    {
        return $this->hasMany(ContentCategoryAsm::className(), ['content_id' => 'id']);
    }


    /**
     * @author *
     * @return $this
     */
    public function getRelatedContent()
    {
        /** return a query hasMany */
        return $this->hasMany(Content::className(), ['id' => 'content_related_id'])->viaTable('{{%content_related_asm}}', ['content_id' => 'id']);
    }


    public static function getListStatus($type = 'all')
    {
        return ['all' => [
            self::STATUS_ACTIVE => 'Hoạt động',
            self::STATUS_PENDING => 'Chờ duyệt',
            self::STATUS_INACTIVE => 'Khóa',
        ],
            'filter' => [
                self::STATUS_ACTIVE => 'Hoạt động',
                self::STATUS_PENDING => 'Chờ duyệt',
                self::STATUS_INACTIVE => 'Khóa',
            ],
        ][$type];
    }

    public function getStatusName()
    {
        $listStatus = self::getListStatus();
        if (isset($listStatus[$this->status])) {
            return $listStatus[$this->status];
        }

        return '';
    }


    public function createCategoryAsm()
    {
        ContentCategoryAsm::deleteAll(['content_id' => $this->id]);
        if ($this->list_cat_id) {
            $listCatIds = explode(',', $this->list_cat_id);
            if (is_array($listCatIds) && count($listCatIds) > 0) {
                foreach ($listCatIds as $catId) {
                    $catAsm = new ContentCategoryAsm();
                    $catAsm->content_id = $this->id;
                    $catAsm->category_id = $catId;
                    $catAsm->save();
                }
            }

            return true;
        }

        return true;
    }

    public static function convertJsonToArray($input)
    {
        $listImage = json_decode($input, true);
        // var_dump($listImage);die;
        $result = [];
        if (is_array($listImage)) {
            foreach ($listImage as $item) {
                $item = is_array($item) ? $item : json_decode($item, true);

                $row['name'] = $item['name'];
                $row['type'] = $item['type'];
                $row['size'] = $item['size'];
                $result[] = $row;
            }
        }

        return $result;
    }

    public static function getListImageType()
    {
        return [
            self::IMAGE_TYPE_SLIDECATEGORY => 'Slide Category',
            self::IMAGE_TYPE_SCREENSHOOT => 'Screenshoot',
            self::IMAGE_TYPE_THUMBNAIL => 'Thumbnail',
            self::IMAGE_TYPE_SLIDE => 'Slide',
            self::IMAGE_TYPE_THUMBNAIL_EPG => 'Thumbnail_epg',
        ];
    }

    public function getImages()
    {
        try {
            $res = [];
            $images = $this->convertJsonToArray($this->images);
            $maxThumb = 0;
            if ($images) {
                for ($i = 0; $i < count($images); ++$i) {
                    $item = $images[$i];
                    if ($item['type'] == self::IMAGE_TYPE_THUMBNAIL) {
                        $maxThumb = $i;
                    }
                    if ($item['type'] == self::IMAGE_TYPE_THUMBNAIL_EPG) {
                        $maxThumb = $i;
                    }
                    $image = new \backend\models\Image();
                    $image->type = $item['type'];
                    $image->name = $item['name'];
                    $image->size = $item['size'];
                    array_push($res, $image);
                }

                return $res;
            }
        } catch (InvalidParamException $ex) {
            $images = null;
        }

        return $images;
    }

    public function getListCatIds()
    {
        $listCat = $this->contentCategoryAsms;
        $listCatId = [];
        foreach ($listCat as $catAsm) {
            $listCatId[] = $catAsm->category_id;
        }

        return $listCatId;
    }

    public static function getListContent(
        $sp_id,
        $type,
        $category = 0,
        $filter = 0,
        $keyword = '',
        $order,
        $language = ''
    )
    {
        $query = \api\models\Content::find()->andWhere(['created_user_id' => $sp_id]);
        if ($category > 0) {
            $query->joinWith('contentCategoryAsms');
            $query->andWhere(['category_id' => $category]);
        } else {
            if ($type > 0) {
                $query->andWhere(['`content`.`type`' => $type]);
            }
        }

        if ($filter > 0) {
            $query->andWhere(['`content`.`honor`' => $filter]);
        }

        if ($type > 0) {
            $query->andWhere(['`content`.`type`' => $type]);
        }

        if ($language != '') {
            $query->andWhere(['`content`.`country`' => $language]);
        }

        if ($keyword != '') {
            $keyword = CVietnameseTools::makeSearchableStr($keyword);
            $query->andwhere('`content`.`ascii_name` LIKE :query')
                ->addParams([':query' => '%' . $keyword . '%']);
        }
        $orderDefault = [];
        if ($order == self::ORDER_NEWEST) {
            $orderDefault['created_at'] = SORT_DESC;
        } else {
            $orderDefault['view_count'] = SORT_DESC;
        }
        $query->andWhere(['status' => self::STATUS_ACTIVE]);
        $query->andWhere('parent_id is null or parent_id = 0');
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => $orderDefault,
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $provider;
    }

    public static function getListContentSearch(
        $sp_id,
        $type = 0,
        $category = 0,
        $filter = 0,
        $keyword,
        $order,
        $language = ''
    )
    {
        $query = \api\models\Content::find()->andWhere(['created_user_id' => $sp_id]);
        if ($category > 0) {
            $query->joinWith('contentCategoryAsms');
            $query->andWhere(['category_id' => $category]);
        } else {
            if ($type > 0) {
                $query->andWhere(['`content`.`type`' => $type]);
            }
        }

        if ($filter > 0) {
            $query->andWhere(['`content`.`honor`' => $filter]);
        }

        if ($type > 0) {
            $query->andWhere(['`content`.`type`' => $type]);
        }

        if ($language != '') {
            $query->andWhere(['`content`.`country`' => $language]);
        }

        if ($keyword != '') {
            $keyword = CVietnameseTools::makeSearchableStr($keyword);
            $query->andwhere('`content`.`ascii_name` LIKE :query')
                ->addParams([':query' => '%' . $keyword . '%']);
        }
        $orderDefault = [];
        if ($order == self::ORDER_NEWEST) {
            $orderDefault['created_at'] = SORT_DESC;
        } else {
            $orderDefault['view_count'] = SORT_DESC;
        }

        $query->andWhere(['status' => self::STATUS_ACTIVE]);
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => $orderDefault,
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $provider;
    }


    /**
     * @param $sp_id
     * @param $id
     *
     * @return ActiveDataProvider
     */
    public static function getDetail($sp_id, $id)
    {
        $content = ContentSearch::find()
            ->andWhere(['created_user_id' => $sp_id])
            ->andWhere(['id' => $id])
            ->andWhere(['status' => self::STATUS_ACTIVE]);
        $dataProvider = new ActiveDataProvider([
            'query' => $content,
            'sort' => [],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    public static function getRelated($sp_id, $content_id)
    {
        /** @var  $content_category_asm ContentCategoryAsm */
        $content_category_asm = ContentCategoryAsm::findOne(['content_id' => $content_id]);
        if ($content_category_asm) {
            $category_id = $content_category_asm->category_id;
        } else {
            $category_id = -1;
        }
//        $query = ListContent::find()->andWhere(['created_user_id' => $sp_id]);
        $query = ListContent::find()->andWhere(['created_user_id' => $sp_id]);

        $query->joinWith('contentCategoryAsms');
        $query->andWhere(['category_id' => $category_id]);

        $query->andWhere(['status' => self::STATUS_ACTIVE]);
        $query->andwhere('`content`.`id` <> :query')
            ->addParams([':query' => $content_id]);
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $provider;
    }

    /**
     * @return null|string
     */
    public function getFirstImageLink()
    {
        // var_dump(Url::base());die;
        $link = '';
        if (!$this->images) {
            return;
        }
        $listImages = self::convertJsonToArray($this->images);
        foreach ($listImages as $key => $row) {
            if ($row['type'] == self::IMAGE_TYPE_THUMBNAIL) {
                $link = Url::to(Url::base() . DIRECTORY_SEPARATOR . Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $row['name'], true);
            }
            if ($row['type'] == self::IMAGE_TYPE_THUMBNAIL_EPG) {
                $link = Url::to(Url::base() . DIRECTORY_SEPARATOR . Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $row['name'], true);
            }
        }

        return $link;
    }

    public function getFirstImageLinkFE()
    {
        // var_dump(Url::base());die;
        $link = '';
        if (!$this->images) {
            return;
        }
        $listImages = self::convertJsonToArray($this->images);
        foreach ($listImages as $key => $row) {
            if ($row['type'] == self::IMAGE_TYPE_THUMBNAIL) {
                $link = Url::to(Url::base() . '/' . Yii::getAlias('@content_images') . '/' . $row['name'], true);
            }
            if ($row['type'] == self::IMAGE_TYPE_THUMBNAIL_EPG) {
                $link = Url::to(Url::base() . '/' . Yii::getAlias('@content_images') . '/' . $row['name'], true);
            }
        }

        return $link;
    }


    /**
     * HungNV edition: 15/03/16.
     * HungNV creation: 15/03/16.
     *
     * @param $name
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function searchByName($name)
    {
        $res = self::find()
            ->orFilterWhere(['LIKE', 'display_name', '%' . $name . '%', false])
            ->orFilterWhere(['LIKE', 'ascii_name', '%' . $name . '%', false]);
        $provider = new ActiveDataProvider([
            'query' => $res,
            'sort' => [],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $provider;
    }


    public static function getTest()
    {
        return $test = self::find()
            ->andWhere(['id' => 307])
            ->all();
    }


    public function getRelatedContents()
    {
        $output = [];
        foreach ($this->contentRelatedAsms as $related) {
            $output[] = $related->id;
        }
        return $this->related_content = $output;
    }


    /**
     * @param $link
     * @return string
     */
    public static function getSubtitleUrl($link)
    {
        return Url::to(Yii::getAlias('@web') . DIRECTORY_SEPARATOR . Yii::getAlias('@subtitle') . DIRECTORY_SEPARATOR . $link, true);

    }

    /**
     * @param $site_id
     * @return int
     */

    public function spUpdateStatus($newStatus, $sp_id)
    {
        $oldStatus = $this->status;
        $listStatusNew = self::getListStatus('filter');
        $this->status = $newStatus;
        $this->updated_at = time();
        return $this->update(false);
//        Yii::trace('aa',$listStatusNew);
//        if (isset($listStatusNew[$newStatus]) || ($newStatus == self::STATUS_DELETE && $oldStatus != self::STATUS_ACTIVE)) {
//            $this->status = $newStatus;
//            Yii::trace('bb',$listStatusNew);
//            // tao log
//            $description = 'UPDATE STATUS CONTENT';
//             $this->status = $newStatus;
//        $this->updated_at = time();
//        return $this->update(false);
//        }
//        return false;
    }

    public function getCssStatus()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'label label-primary';
            case self::STATUS_INACTIVE:
                return 'label label-warning';
            case self::STATUS_DELETE:
                return 'label label-danger';
            case self::STATUS_PENDING:
                return 'label label-info';
            default:
                return 'label label-primary';
        }
    }

    public static function substr($str, $length, $minword = 3)
    {
        $sub = '';
        $len = 0;
        foreach (explode(' ', $str) as $word) {
            $part = (($sub != '') ? ' ' : '') . $word;
            $sub .= $part;
            $len += strlen($part);
            if (strlen($word) > $minword && strlen($sub) >= $length) {
                break;
            }
        }
        return $sub . (($len < strlen($str)) ? '...' : '');
    }


    public static function formatNumber($number)
    {
        $formatter = new \yii\i18n\Formatter();
        $formatter->thousandSeparator = '.';
        $formatter->decimalSeparator = '.';
        return $formatter->asInteger($number);
    }
}
