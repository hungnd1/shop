<?php

namespace common\models;

use api\helpers\Message;
use api\models\ListContent;
use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use sp\models\Image;
use Yii;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

//use yii\swiftmailer\Message;

/**
 * This is the model class for table "content".
 *
 * @property int $id
 * @property string $display_name
 * @property string $code
 * @property string $ascii_name
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
 * @property int $created_at
 * @property int $updated_at
 * @property int $honor
 * @property int $approved_at
 * @property Content $parent
 * @property string $condition
 * @property string $highlight
 * @property int $price_promotion
 * @property int $price
 * @property string $language
 * @property int $order
 *
 * @property ContentActorDirectorAsm[] $contentActorDirectorAsms
 * @property ContentAttributeValue[] $contentAttributeValues
 * @property ContentCategoryAsm[] $contentCategoryAsms
 * @property ContentFeedback[] $contentFeedbacks
 * @property ContentLog[] $contentLogs
 * @property ContentProfile[] $contentProfiles
 * @property ContentRelatedAsm[] $contentRelatedAsms
 * @property ContentRelatedAsm[] $contentRelatedAsms0
 * @property ContentSiteAsm[] $contentSiteAsms
 * @property ContentViewLog[] $contentViewLogs
 * @property LiveProgram[] $livePrograms
 * @property LiveProgram[] $livePrograms0
 * @property SubscriberContentAsm[] $subscriberContentAsms
 * @property SubscriberFavorite[] $subscriberFavorites
 * @property SubscriberTransaction[] $subscriberTransactions
 * @property SumContentDownload[] $sumContentDownloads
 * @property SumContentView[] $sumContentViews
 */
class Content extends \yii\db\ActiveRecord
{
    const IMAGE_TYPE_LOGO          = 1;
    const IMAGE_TYPE_THUMBNAIL     = 2;
    const IMAGE_TYPE_SCREENSHOOT   = 3;
    const IMAGE_TYPE_SLIDE         = 4;
    const IMAGE_TYPE_THUMBNAIL_EPG = 5;

    public $logo;
    public $thumbnail_epg;
    public $thumbnail;
    public $screenshoot;
    public $slide;
    public $image_tmp;
    public $live_channel;
    public $started_at;
    public $ended_at;
    public $content_related_asm;
    public $channel_name;
    public $channel_id;
    public $pricing_content;
    public $related_content = [];
    public $related_name;
    public $contentAttr = [];
    public $viewAttr    = [];
    public $validAttr   = [];
    public $live_status;
    public $content_actors;
    public $content_directors;
    public $site_name;
    public $content_site_asm_status;
    public $site_id;
    public $pricing_id;
    public $is_free;

    public $price_coin;
    public $price_sms;
    public $watching_period;

    const STATUS_ACTIVE         = 10; // Đã duyệt
    const STATUS_INACTIVE       = 0; // khóa
    const STATUS_REJECTED       = 1; // Từ chối
    const STATUS_DELETE         = 2; // Xóa
    const STATUS_PENDING        = 3; // CHỜ DUYỆT
    const STATUS_INVISIBLE      = 4; // ẨN
    const STATUS_DRAFT          = 5;
    const STATUS_WAIT_TRANSCODE = 6;
    const STATUS_WAIT_TRANSFER  = 7;

    const DEFAULT_SITE_ID = 5; // Viet Nam

    const HONOR_NOTHING  = 0;
    const HONOR_FEATURED = 1;
    const HONOR_HOT      = 2;
    const HONOR_ESPECIAL = 3;

    const ORDER_NEWEST   = 0;
    const ORDER_MOSTVIEW = 1;
    const ORDER_EPISODE  = 2; //phim bộ
    const ORDER_ORDER    = 3; //order
    const ORDER_ID       = 4; //id
    const ORDER_TITLE    = 5; //title

    const IS_MOVIES = 0;
    const IS_SERIES = 1;

    const NOT_FREE = 0;
    const IS_FREE  = 1;

    const NOT_CATCHUP = 0;
    const IS_CATCHUP  = 1;

    const TYPE_VIDEO        = 1;
    const TYPE_LIVE         = 2;
    const TYPE_MUSIC        = 3;
    const TYPE_NEWS         = 4;
    const TYPE_CLIP         = 5;
    const TYPE_KARAOKE      = 6;
    const TYPE_RADIO        = 7;
    const TYPE_LIVE_CONTENT = 8;

    const NEXT_VIDEO     = 1;
    const PREVIOUS_VIDEO = 2;

    const IS_SINGER = "singer";
    const IS_ACTOR  = "actor";

    const MAX_SIZE_UPLOAD = 10485760; // 10 * 1024 * 1024

    public static $list_honor = [
        self::HONOR_NOTHING  => 'All',
        self::HONOR_FEATURED => 'Đặc sắc',
        self::HONOR_HOT      => 'Hot',
        self::HONOR_ESPECIAL => 'Đặc biệt',
    ];

    public static $filmType = [
        self::IS_MOVIES => 'Phim lẻ',
        self::IS_SERIES => 'Phim bộ',
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
            [['display_name', 'code',  'status',  'list_cat_id'], 'required', 'on' => 'adminModify', 'message' => '{attribute} không được để trống'],
            [['started_at', 'ended_at'], 'required', 'message' => '{attribute} không được để trống', 'on' => 'adminModifyLiveContent'],
            [['ended_at'], 'validEnded', 'on' => 'adminModifyLiveContent'],
            [['display_name', 'code', 'created_user_id'], 'required', 'message' => '{attribute} không được để trống'],
            [
                [
                    'type',
                    'price',
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
                    'approved_at',
                    'order',
                ], 'integer',
            ],
            [['description', 'content', 'condition', 'images', 'short_description', 'images', 'highlight'], 'string'],
            [['rating'], 'number'],
            [['display_name', 'ascii_name', 'country'], 'string', 'max' => 128],
            [['code'], 'string', 'max' => 20],
            [['tags'], 'string', 'max' => 500],
            [['version'], 'string', 'max' => 64],
            [['language'], 'string', 'max' => 10],
            [['code'], 'unique', 'message' => '{attribute} đã tồn tại trên hệ thống. Vui lòng thử lại'],
            [['thumbnail', 'thumbnail_epg', 'screenshoot'],
                'file',
                'tooBig'         => '{attribute} vượt quá dung lượng cho phép. Vui lòng thử lại',
                'wrongExtension' => '{attribute} không đúng định dạng',
                'uploadRequired' => '{attribute} không được để trống',
                'extensions'     => 'png, jpg, jpeg, gif',
                'maxSize'        => self::MAX_SIZE_UPLOAD],
            [['thumbnail'], 'validateThumb', 'on' => ['adminModify', 'adminModifyLiveContent']],
            [['screenshoot'], 'validateScreen', 'on' => 'adminModify'],
            [['thumbnail'], 'image', 'extensions' => 'png,jpg,jpeg,gif',
                'minWidth'  => 1, 'maxWidth'              => 512,
                'minHeight' => 1, 'maxHeight'             => 512,
                'maxSize'   => 1024 * 1024 * 10, 'tooBig' => 'Ảnh show  vượt quá dung lượng cho phép. Vui lòng thử lại',
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
        if ($this->type == self::TYPE_LIVE_CONTENT) {
            return true;
        }

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

    /** Không dùng thằng này mà phải tự add bằng tay */
    /**
     * {@inheritdoc}
     */
//    public function behaviors()
    //    {
    //        return [
    //            [
    //                'class'              => TimestampBehavior::className(),
    //                'createdAtAttribute' => 'created_at',
    //                'updatedAtAttribute' => 'updated_at',
    //            ],
    //        ];
    //    }

    /**
     * @param bool $insert
     * @return bool
     */
//    public function beforeSave($action)
    //    {
    //        if (parent::beforeSave($action)) {
    //            // ...custom code here...
    //            if($this->type == Content::TYPE_KARAOKE && $this->status == Content::STATUS_ACTIVE){
    //                $site_id = Yii::$app->user->identity->site_id;
    //                $items = \api\models\Content::find()
    //                    ->select(['content.id','display_name','ascii_name','short_description'])
    //                    ->andWhere(['type'=>$this->type,'status'=>Content::STATUS_ACTIVE,'is_series'=>Content::IS_MOVIES])
    //                    ->joinWith('contentSiteAsms')->andWhere(['site_id' =>$site_id])
    //                    ->all();
    ////                    ->limit(1)->all();
    //                $lst = [];
    //                foreach($items as $item){
    //                    $group_tmp = $item->getAttributes(['id','display_name','ascii_name','short_description'], ['created_user_id']);
    //                    $temp = "";
    //
    //                    $categoryAsms = $item->contentCategoryAsms;
    //                    if($categoryAsms){
    //                        foreach ($categoryAsms as $asm) {
    //                            /** @var $asm ContentCategoryAsm */
    //                            $temp .= $asm->category->id.',';
    //                        }
    //                        if(strlen($temp) > 2){
    //                            $temp = substr($temp,0,-1);
    //                        }
    //                    }
    //
    //                    $group_tmp['categories'] = $temp;
    //                    $tempA = "";
    //                    $tempD = "";
    //                    $contentActorDirectorAsms = $item->contentActorDirectorAsms;
    //                    if($contentActorDirectorAsms){
    //                        foreach ($contentActorDirectorAsms as $asm) {
    //                            if ($asm->actorDirector->type == ActorDirector::TYPE_ACTOR) {
    //                                /** @var $asm ContentCategoryAsm */
    //                                $tempA .= $asm->actorDirector->id . ',';
    //                            }
    //                            if ($asm->actorDirector->type == ActorDirector::TYPE_DIRECTOR) {
    //                                /** @var $asm ContentCategoryAsm */
    //                                $tempD .= $asm->actorDirector->id . ',';
    //                            }
    //                        }
    //                        if(strlen($temp) > 2){
    //                            $tempA = substr($tempA,0,-1);
    //                        }
    //                        if(strlen($temp) > 2){
    //                            $tempD = substr($tempD,0,-1);
    //                        }
    //                    }
    //                    $group_tmp['actors'] = $tempA;
    //                    $group_tmp['directors'] = $tempD;
    //                    $group_tmp['shortname'] = CUtils::parseTitleToKeyword($item->display_name);
    //
    //                    array_push($lst,$group_tmp);
    //                }
    //
    //                $res = [
    //                    'success' => true,
    //                    'message' => Message::MSG_SUCCESS,
    //                    'totalCount' => count($lst),
    //                    'time_update' => time(),
    //                    "date_expired" =>"01/01/2018",
    //                ];
    //                $res['items'] = $lst;
    //                $resJson = json_encode($res);
    //                $path = 'staticdata/data'.$site_id.'.json';
    //                $save2File = CUtils::writeFile($resJson,$path);
    //                if($save2File){
    //                    Yii::info("########CUONGVM success");
    //                }else{
    //                    Yii::info("########CUONGVM false");
    //                }
    //
    //            }
    //            return true;
    //        } else {
    //            return false;
    //        }
    //    }

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
            'id'                  => 'ID',
            'display_name'        => Yii::t('app', 'Tên hiển thị'),
            'code'                => 'Code',
            'ascii_name'          => 'Ascii Name',
            'type'                => 'Type',
            'tags'                => 'Đánh dấu',
            'short_description'   => 'Mô tả ngắn',
            'description'         => 'Mô tả',
            'content'             => 'Nội dung',
            'urls'                => 'Urls',
            'version_code'        => 'Version Code',
            'version'             => 'Version',
            'view_count'          => 'View Count',
            'download_count'      => 'Download Count',
            'like_count'          => 'Like Count',
            'dislike_count'       => 'Dislike Count',
            'rating'              => 'Rating',
            'rating_count'        => 'Rating Count',
            'comment_count'       => 'Comment Count',
            'favorite_count'      => 'Favorite Count',
            'is_catchup'          => Yii::t('app', 'Truyền hình xem lại'),
            'images'              => 'Images',
            'status'              => Yii::t('app', 'Trạng thái'),
            'created_at'          => 'Ngày tạo',
            'updated_at'          => 'Ngày cập nhật',
            'honor'               => 'Honor',
            'approved_at'         => 'Ngày phê duyệt',
            'admin_note'          => 'Admin Note',
            'is_series'           => 'Thể loại',
            'episode_count'       => 'Episode Count',
            'episode_order'       => 'Sắp xếp',
            'parent_id'           => 'Parent ID',
            'created_user_id'     => 'Created User ID',
            'day_download'        => 'Day Download',
            'author'              => 'Author',
            'director'            => 'Director',
            'actor'               => 'Actor',
            'country'             => 'Country',
            'language'            => 'Language',
            'view_date'           => 'View Date',
            'tvod1_id'            => 'Tvod1 ID',
            'assignment_sites'    => 'Nhà cung cấp dịch vụ',
            'thumbnail_epg'       => 'Ảnh Poster dọc',
            'thumbnail'           => 'Ảnh Poster dọc (*)',
            'screenshoot'         => 'Ảnh Slide show (*)',
            'list_cat_id'         => 'Danh mục  nội dung',
            'started_at'          => 'Thời gian bắt đầu',
            'ended_at'            => 'Thời gian kết thúc',
            'live_channel'        => 'Kênh Live',
            'default_site_id'     => 'Nhà cung cấp dịch vụ gốc',
            'default_category_id' => 'Danh mục',
            'content_related_asm' => 'Nội dung liên quan',
            'order'               => 'Sắp xếp',
            'content_directors'   => $this->type == self::TYPE_VIDEO ? 'Đạo diễn' : 'Nhạc sĩ',
            'content_actors'      => $this->type == self::TYPE_VIDEO ? 'Diễn viên' : 'Ca sĩ',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Content::className(), ['id' => 'parent_id']);
    }

    public function getContentActorDirectorAsms($type = ActorDirector::TYPE_DIRECTOR)
    {
        return $this->hasMany(ContentActorDirectorAsm::className(), ['content_id' => 'id']);
    }

    public function getContentAttributeValues()
    {
        return $this->hasMany(ContentAttributeValue::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentCategoryAsms()
    {
        return $this->hasMany(ContentCategoryAsm::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentFeedbacks()
    {
        return $this->hasMany(ContentFeedback::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentLogs()
    {
        return $this->hasMany(ContentLog::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentProfiles()
    {
        return $this->hasMany(ContentProfile::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentRelatedAsms()
    {
        return $this->hasMany(ContentRelatedAsm::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentRelatedAsms0()
    {
        return $this->hasMany(ContentRelatedAsm::className(), ['content_related_id' => 'id']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getContentSiteAsms($site_id)
    //    {
    //        return $this->hasMany(ContentSiteAsm::className(), ['content_id' => 'id'])->where('site_id > :site_id', [':site_id' => $site_id]);
    //    }

    public function getContentSiteAsms()
    {
        return $this->hasMany(ContentSiteAsm::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentViewLogs()
    {
        return $this->hasMany(ContentViewLog::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLivePrograms()
    {
        return $this->hasMany(LiveProgram::className(), ['channel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLivePrograms0()
    {
        return $this->hasMany(LiveProgram::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberContentAsms()
    {
        return $this->hasMany(SubscriberContentAsm::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberFavorites()
    {
        return $this->hasMany(SubscriberFavorite::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberTransactions()
    {
        return $this->hasMany(SubscriberTransaction::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSumContentDownloads()
    {
        return $this->hasMany(SumContentDownload::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSumContentViews()
    {
        return $this->hasMany(SumContentView::className(), ['content_id' => 'id']);
    }

    public static function getListStatus($type = 'all')
    {
        return ['all' => [
            self::STATUS_ACTIVE         => 'Hoạt động',
            self::STATUS_INACTIVE       => 'Khóa',
        ],
            'filter'      => [
                self::STATUS_ACTIVE         => 'Hoạt động',
                self::STATUS_INACTIVE       => 'Khóa',
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

    public static function listType()
    {
        return [
            self::TYPE_VIDEO   => 'Phim',
            self::TYPE_CLIP    => 'Clip',
            self::TYPE_LIVE    => 'Live',
            self::TYPE_MUSIC   => 'Âm nhạc',
            self::TYPE_NEWS    => 'Tin tức',
            self::TYPE_KARAOKE => 'Karaoke',
            self::TYPE_RADIO   => 'Radio',
        ];
    }

    public function getTypeName()
    {
        $lst = self::listType();
        if (array_key_exists($this->type, $lst)) {
            return $lst[$this->type];
        }
        return $this->type;
    }

    public function createCategoryAsm()
    {
        ContentCategoryAsm::deleteAll(['content_id' => $this->id]);
        if ($this->list_cat_id) {
            $listCatIds = explode(',', $this->list_cat_id);
            if (is_array($listCatIds) && count($listCatIds) > 0) {
                foreach ($listCatIds as $catId) {
                    $catAsm              = new ContentCategoryAsm();
                    $catAsm->content_id  = $this->id;
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
                $result[]    = $row;
            }
        }

        return $result;
    }

    public static function getListImageType()
    {
        return [
            self::IMAGE_TYPE_LOGO          => 'Logo',
            self::IMAGE_TYPE_SCREENSHOOT   => 'Screenshoot',
            self::IMAGE_TYPE_THUMBNAIL     => 'Thumbnail',
            self::IMAGE_TYPE_SLIDE         => 'Slide',
            self::IMAGE_TYPE_THUMBNAIL_EPG => 'Thumbnail_epg',
        ];
    }

    public function getImages()
    {
        try {
            $res      = [];
            $images   = $this->convertJsonToArray($this->images);
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
                    $image       = new Image();
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
        $listCat   = $this->contentCategoryAsms;
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
    ) {
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
            'query'      => $query,
            'sort'       => [
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
    ) {
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
            'query'      => $query,
            'sort'       => [
                'defaultOrder' => $orderDefault,
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $provider;
    }

    public static function getListContentDetail(
        $sp_id,
        $id
    ) {
        $protocol = ContentProfile::STREAMING_HLS;
        $arr      = array();
        $i        = 0;
        $query    = self::find()->andWhere(['created_user_id' => $sp_id]);
        $query->andWhere(['`content`.`status`' => self::STATUS_ACTIVE]);
        $query->andWhere(['`content`.`parent_id`' => $id]);
        $query->orderBy(['episode_order' => SORT_ASC])->all();
        $command = $query->createCommand();
        $data    = $command->queryAll();
        if (!$query->count()) {
            return false;
        }
        foreach ($data as $val) {
            $arr[$i]     = new \stdClass();
            $arr[$i]->id = $val['id'];
            $video       = self::findOne($val['id']);
            if ($video) {
                $arr[$i]->urls = $video->getStreamUrl($protocol, true);
            }
            ++$i;
        }

        return $arr;
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
            'query'      => $content,
            'sort'       => [],
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
            'query'      => $query,
            'sort'       => [
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

    public function createContentLog(
        $type = ContentLog::TYPE_CREATE,
        $user_id = null,
        $ip_address = '',
        $status = ContentLog::STATUS_SUCCESS,
        $description = '',
        $user_agent = '',
        $content_name = ''
    ) {
        $contentLog             = new ContentLog();
        $contentLog->content_id = $this->id;
        // $contentLog->content_provider_id = $this->content_provider_id;
        // $contentLog->created_user_id = $this->created_user_id;
        $contentLog->description  = $description;
        $contentLog->type         = $type;
        $contentLog->user_id      = $user_id;
        $contentLog->user_agent   = $user_agent;
        $contentLog->ip_address   = $ip_address;
        $contentLog->status       = $status;
        $contentLog->content_name = $content_name;
        if ($contentLog->save()) {
            return $contentLog;
        }
        Yii::trace($contentLog->getErrors());

        return;
    }

    public static function getCPStatusAction($current_status)
    {
        switch ($current_status) {
            case self::STATUS_DRAFT:
                return [
                    self::STATUS_DELETE => 'Xóa',
                    self::STATUS_DRAFT  => 'Nháp',
                    self::STATUS_ACTIVE => 'Publish',
                ];
            case self::STATUS_PENDING:
                return [
                    self::STATUS_DRAFT  => 'Nháp',
                    self::STATUS_ACTIVE => 'Publish',
                ];
            case self::STATUS_REJECTED:
                return [
                    self::STATUS_DRAFT    => 'Nháp',
                    self::STATUS_DELETE   => 'Xóa',
                    self::STATUS_REJECTED => 'Từ chối',
                ];
            case self::STATUS_ACTIVE:
                return [
                    self::STATUS_DRAFT     => 'Nháp',
                    self::STATUS_ACTIVE    => 'Đã Duyệt',
                    self::STATUS_INVISIBLE => 'Ẩn',
                ];
            case self::STATUS_INVISIBLE:
                return [

                    self::STATUS_ACTIVE    => 'Đã Duyệt',
                    self::STATUS_INVISIBLE => 'Ẩn',
                ];
            default:
                return [];
        }
    }

    public static function getSPStatusAction($current_status)
    {
        switch ($current_status) {
            case self::STATUS_DRAFT:
                return [
                    self::STATUS_DRAFT => 'Nháp',
                ];
            case self::STATUS_PENDING:
                return [
                    self::STATUS_PENDING  => 'Chờ duyệt',
                    self::STATUS_REJECTED => 'Từ chối',
                    self::STATUS_ACTIVE   => 'Đã Duyệt',

                ];
            case self::STATUS_REJECTED:
                return [
                    self::STATUS_REJECTED => 'Từ chối',
                    self::STATUS_ACTIVE   => 'Đã Duyệt',
                ];
            case self::STATUS_ACTIVE:
                return [
                    self::STATUS_REJECTED => 'Từ chối',
                    self::STATUS_ACTIVE   => 'Đã Duyệt',
                ];
            case self::STATUS_INVISIBLE:
                return [
                    self::STATUS_INVISIBLE => 'Ẩn',
                ];
            default:
                return [];
        }
    }

    public function cpUpdateStatus($newStatus, $cp_id)
    {
        $oldStatus     = $this->status;
        $listStatusNew = self::getListStatus();
        if (isset($listStatusNew[$newStatus]) && ($newStatus != self::STATUS_DELETE || ($newStatus == self::STATUS_DELETE && $oldStatus == self::STATUS_DRAFT))) {
            $this->status = $newStatus;
            // tao log
            $description = 'UPDATE STATUS CONTENT';
            $ip_address  = CUtils::clientIP();
            $this->createContentLog(ContentLog::TYPE_EDIT, Yii::$app->user->id, $ip_address, ContentLog::STATUS_SUCCESS, $description, '', $this->display_name);
            return $this->update(false);
        }

        return false;
    }

    public function spUpdateStatus($newStatus, $sp_id)
    {
        $oldStatus     = $this->status;
        $listStatusNew = self::getListStatus('filter');
        // $listStatusNew = Content::getSPStatusAction($oldStatus);
        // if ($sp_id != $this->created_user_id) {
        //     return false;
        // }
        // if (isset($listStatusNew[$newStatus])) {
        // var_dump(isset($listStatusNew[$newStatus]));die;
        if (isset($listStatusNew[$newStatus]) || ($newStatus == self::STATUS_DELETE && $oldStatus != self::STATUS_ACTIVE)) {
            $this->status = $newStatus;
            // tao log
            $description = 'UPDATE STATUS CONTENT';
            $ip_address  = CUtils::clientIP();
            $this->createContentLog(ContentLog::TYPE_EDIT, Yii::$app->user->id, $ip_address, ContentLog::STATUS_SUCCESS,
                $description, '', $this->display_name);
            /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - begin */
            $this->updated_at = time();
            /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - end */
            return $this->update(false);
        }
        return false;
    }

    public function getCssStatus()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'label label-primary';
            case self::STATUS_INACTIVE:
                return 'label label-warning';
            case self::STATUS_DRAFT:
                return 'label label-default';
            case self::STATUS_DELETE:
                return 'label label-danger';
            case self::STATUS_PENDING:
                return 'label label-info';
            case self::STATUS_REJECTED:
                return 'label label-danger';
            default:
                return 'label label-primary';
        }
    }

    public static function getContentProfileRaw()
    {
        /*
         * @var $dataRaw ContentProfile
         */
        $dataRaw = ContentProfile::find()
            ->where(['type' => ContentProfile::TYPE_RAW])
            ->andWhere(['status' => ContentProfile::STATUS_RAW])
            ->one();
        if (!$dataRaw) {
            return [
                'error'   => 1,
                'message' => 'No file raw',
            ];
        }
        $dataRaw->status = ContentProfile::STATUS_TRANCODE_PENDING;
        $dataRaw->update();

        return [
            'content_profile_id' => $dataRaw->id,
            'content_id'         => $dataRaw->content_id,
            'cp_id'              => 0,
            'url'                => $dataRaw->getFilePath(ContentProfile::LOCATION_STORAGE),
//            'sub_path' => $dataRaw->getSubPath(ContentProfile::LOCATION_STORAGE),
        ];
    }

    /**
     * HungNV edition: 02/04/16.
     * @param $contentProfile
     * @param $quality
     * @return array
     */
    public static function getUrl($contentProfile, $site_id)
    {
        switch ($contentProfile->type) {
            case ContentProfile::TYPE_RAW:
                /** Không xử lí với file RAW */
                $res = [
                    'success' => false,
                    'message' => Message::MSG_NOT_FOUND_CONTENT,
                ];
                return $res;
            case ContentProfile::TYPE_STREAM:
                /** @var  $cpsa ContentProfileSiteAsm */
                $cpsa = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id, 'site_id' => $site_id, 'status' => ContentProfileSiteAsm::STATUS_ACTIVE]);
                if (!$cpsa) {
                    $res['success'] = false;
                    $res['message'] = Message::MSG_NOT_FOUND_CONTENT_PROFILE;
                    return $res;
                }
                $response = ContentProfile::getStreamUrl($cpsa->url);
                if (!$response['success']) {
                    $res = [
                        'success' => false,
                        'message' => $response['message'],
                    ];
                    return $res;
                } else {
                    /** @var  $contentSiteAsm ContentSiteAsm */
                    $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $contentProfile->content_id, 'site_id' => $site_id]);
                    $subtitle       = Content::getSubtitleUrl($contentSiteAsm->subtitle);
                    $res            = [
                        'success'  => true,
                        'url'      => $response['url'],
                        'subtitle' => $subtitle,
                    ];
                    return $res;
                }

            case ContentProfile::TYPE_CDN;
                /** @var  $cpsa ContentProfileSiteAsm */
                $cpsa = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id, 'site_id' => $site_id, 'status' => ContentProfileSiteAsm::STATUS_ACTIVE]);

                if (!$cpsa) {
                    $res['success'] = false;
                    $res['message'] = Message::MSG_NOT_FOUND_CONTENT_PROFILE;
                    return $res;
                }
                $response = ContentProfile::getCdnUrl((int) $cpsa->url);
                /** Nếu CDN trả về false thì return kèm message */
                if (!$response['success']) {
                    $res = [
                        'success' => false,
                        'message' => $response['reason'],
                        'code'    => $response['errorCode'],
                    ];
                } else {
                    /** Trường hợp CDN trả về true */
                    /** @var  $contentSiteAsm ContentSiteAsm */
                    $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $contentProfile->content_id, 'site_id' => $site_id]);
                    $subtitle       = Content::getSubtitleUrl($contentSiteAsm->subtitle);
                    $res            = [
                        'success'  => true,
                        'url'      => $response['url'],
                        'subtitle' => $subtitle,
                    ];
                }
                return $res;
        }
    }

    /**
     * HungNV creation: 15/03/16: get list of Drama film without sub drama films.
     *
     * @param $type
     * @param $is_series
     * @param null $parent_id
     *
     * @return ActiveDataProvider
     */
    public static function getLiveDrama($type, $is_series, $parent_id = null)
    {
        $params = Yii::$app->request->queryParams;
        $drama  = self::find()
            ->andWhere(['type' => $type]);
        if (isset($params['id'])) {
            $drama->andWhere(['id' => $params['id']]);
        }
        $drama->andWhere(['is_series' => $is_series])
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->andWhere(['IS', 'parent_id', $parent_id]);
        $dataProvider = new ActiveDataProvider([
            'query'      => $drama,
            'sort'       => [],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $dataProvider;
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
            'query'      => $res,
            'sort'       => [],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        return $provider;
    }

    /**
     * HungNV creation: 31/03.
     *
     * @param $type
     * @param null $parent_id
     * @param null $language
     * @param null $order
     *
     * @return ActiveDataProvider
     */
    public static function getLives($type, $parent_id = null, $language = null, $order = null)
    {
        $res = self::find()
            ->andWhere(['type' => $type]);
        if (isset($parent_id) ? $parent_id : null) {
            $res->andWhere(['parent_id' => $parent_id]);
        }
        if ($language != null) {
            $res->andWhere(['language' => $language]);
        }
        $res->andWhere(['status' => self::STATUS_ACTIVE])
            ->orderBy(['created_at' => $order]);
        if (isset($res) ? $res : null) {
            // throw new Exception here
        }
        $provider = new ActiveDataProvider([
            'query'      => $res,
            'sort'       => [
            ],
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

    public static function listLive()
    {
        $lives     = self::findAll(['type' => self::TYPE_LIVE]);
        $listLives = [];
        foreach ($lives as $live) {
            $listLives[$live->id] = $live->display_name;
        }
        arsort($listLives);

        return $listLives;
    }

    public function getReadonlyAssignment_sites()
    {
        $readOnlySite = ContentSiteAsm::find()->where(['AND', ['content_id' => $this->id], ['!=', 'status', ContentSiteAsm::STATUS_NOT_TRANSFER]])->all();
        return ArrayHelper::map($readOnlySite, 'id', 'site_id');
    }

    public function getAssignment_sites()
    {
        $this->assignment_sites = ContentSiteAsm::getSiteList(['content_id' => $this->id], ['id', 'site_id']);
    }

    public function setAssignment_sites($assignment_sites = null)
    {
        $assignment_sites = $this->assignment_sites;
        if (!empty($assignment_sites)) {
            ContentSiteAsm::deleteAll(['AND', ['content_id' => $this->id], ['NOT IN', 'site_id', $assignment_sites], ['status' => ContentSiteAsm::STATUS_NOT_TRANSFER]]);

            foreach ($assignment_sites as $site_id) {
                $checkSiteAsm = ContentSiteAsm::findOne(['content_id' => $this->id, 'site_id' => $site_id]);

                if (!$checkSiteAsm) {
                    $siteAsm             = new ContentSiteAsm();
                    $siteAsm->content_id = $this->id;
                    $siteAsm->site_id    = $site_id;
                    $siteAsm->status     = $site_id == $this->default_site_id ? ContentSiteAsm::STATUS_ACTIVE : ContentSiteAsm::STATUS_NOT_TRANSFER;
                    $siteAsm->insert();
                }
            }

            // var_dump($assignment_sites);die;
            return true;
        }

        ContentSiteAsm::deleteAll(['content_id' => $this->id]);
        return false;
    }

    public function saveRelatedContent()
    {
        ContentRelatedAsm::deleteAll(['content_id' => $this->id]);
        // var_dump($this->content_related_asm);die;
        if ($this->content_related_asm) {
            foreach ($this->content_related_asm as $content) {
                $related                     = new ContentRelatedAsm();
                $related->content_id         = $this->id;
                $related->content_related_id = $content;
                $related->insert();
            }
        }
        return true;
    }

    public function getRelatedContents()
    {
        $output = [];
        foreach ($this->contentRelatedAsms as $related) {
            $output[] = $related->id;
        }
        return $this->related_content = $output;
    }

    public function getContentAttr($mode = null)
    {
        $contentAttributeValues = $this->contentAttributeValues;
        $extraAttr              = $this->getExtraAttr('view');
        $validData              = $this->getExtraAttr('validation');
        $contentAttr            = [];
        $viewAttr               = [];
        $validAttr              = [];

        if ($contentAttributeValues) {
            foreach ($contentAttributeValues as $value) {
                $contentAttr[$value->content_attribute_id] = $value->value;
                $viewAttr[]                                = [
                    'label' => $extraAttr[$value->content_attribute_id],
                    'value' => $value->value,
                ];
                $validAttr[] = [
                    CVietnameseTools::makeSearchableStr($extraAttr[$value->content_attribute_id]),
                    strtolower(ContentAttribute::getDatatype($validData[$value->content_attribute_id])),
                    'except' => 'updateStatus',
                ];
            }
        }

        $this->validAttr          = $validAttr;
        $this->viewAttr           = $viewAttr;
        return $this->contentAttr = $contentAttr;
    }

    public function getExtraAttr($mode = null)
    {

        if ($mode === 'view') {
            $out = [];
            foreach (ContentAttribute::findAll(['content_type' => $this->type]) as $value) {
                $out[$value->id] = $value->name;
            }
            return $out;
        }
        if ($mode === 'validation') {
            $out = [];
            foreach (ContentAttribute::findAll(['content_type' => $this->type]) as $value) {
                $out[$value->id] = $value->data_type;
            }
            return $out;
        }

        return ContentAttribute::findAll(['content_type' => $this->type]);
    }

    public function saveAttrValue()
    {
        ContentAttributeValue::deleteAll(['content_id' => $this->id]);
        $contentAttr = $this->contentAttr;
        // var_dump($contentAttr);die;
        if ($contentAttr) {
            foreach ($contentAttr as $k => $value) {
                $cValue = new ContentAttributeValue;

                $cValue->content_id           = $this->id;
                $cValue->content_attribute_id = $k;
                $cValue->value                = $value;
                $cValue->insert();
                // var_dump($cValue->getErrors());die;
            }
        }
    }

    public function getPriceContent($site_id)
    {
        $price = ContentSiteAsm::find()
        // ->select('pricing.id as pricing_id')
            ->innerJoin('pricing', 'pricing.id = content_site_asm.pricing_id')
            ->andWhere(['content_site_asm.site_id' => $site_id])
            ->andwhere(['content_site_asm.content_id' => $this->id])
            ->one();

        $defaultPrice = Site::findOne($site_id)->default_price_content_id;

        $price = $price === null ? $defaultPrice : $price->pricing_id;

        $this->pricing_content = $price;

        return $price;
    }

    public function getEpisodeOrder()
    {
        return $this->parent && $this->type != self::TYPE_LIVE_CONTENT ?
        Content::find()
            ->select('episode_order')
            ->andwhere(['parent_id' => $this->parent_id])
            ->orderBy(['episode_order' => SORT_DESC])->all()[0]
            ->episode_order + 1
        : null;
    }

    /**
     * @param $target_site_id
     * @param $streaming_server_id
     * @return mixed
     */
    public static function syncDataToSite($target_site_id, $streaming_server_id)
    {
        if (!is_numeric($target_site_id)) {
            $res['status']  = false;
            $res['message'] = CUtils::replaceParam(Message::MSG_NUMBER_ONLY, ['site_id']);
            return $res;
        }
        if (!is_numeric($streaming_server_id)) {
            $res['status']  = false;
            $res['message'] = CUtils::replaceParam(Message::MSG_NUMBER_ONLY, ['streaming_server_id']);
            return $res;
        }
        /** @var  $streamAsm SiteStreamingServerAsm */
        $streamAsm = SiteStreamingServerAsm::findOne(['streaming_server_id' => $streaming_server_id]);

        if (!$streamAsm) {
            $res['status']  = false;
            $res['message'] = Message::MSG_NOT_FOUND_STREAMING;
            return $res;
        }
        /** @var  $stream StreamingServer */
        $stream         = $streamAsm->streamingServer;
        $url            = $stream->content_api;
        $content_folder = $stream->content_path;

        /** Bỏ phần check theo trạng thái contentSite mà mình sẽ quét all */
        /** Update 20160721 check chỉ lấy những thằng Content có trạng thái STATUS_ACTIVE và ContentSiteAsm có trạng thái là  STATUS_NOT_TRANSFER, STATUS_TRANSFER_ERROR theo nội dung cuộc họp */
        $items = Content::find()
            ->innerJoin('content_site_asm', 'content.id=content_site_asm.content_id')
            ->andWhere(['content_site_asm.site_id' => $target_site_id, 'content_site_asm.status' => [ContentSiteAsm::STATUS_NOT_TRANSFER, ContentSiteAsm::STATUS_TRANSFER_ERROR]])
            ->andWhere(['content.status' => Content::STATUS_ACTIVE, 'content.type' => [
                Content::TYPE_VIDEO,
                Content::TYPE_MUSIC,
                Content::TYPE_NEWS,
                Content::TYPE_CLIP,
                Content::TYPE_KARAOKE,
                Content::TYPE_RADIO,
                Content::TYPE_LIVE_CONTENT,
            ]])
            ->all();

        /** Nếu không có content nào thỏa mãn thì thông báo  */
        if (count($items) <= 0) {
            $res['status']  = false;
            $res['message'] = Message::MSG_NOT_FOUND_CONTENT;
            return $res;
        }
        $data                   = [];
        $data['request_id']     = time();
        $data['site_id']        = $target_site_id;
        $data['content_folder'] = $content_folder;
        $data['token']          = md5($data['request_id'] . $target_site_id);
        $arrItems               = [];
        /** @var  $row */
        foreach ($items as $row) {
            $item = $row->getAttributes(['id', 'type', 'default_site_id'], ['tvod1_id']);
            /** Kiểm tra xem có site_default_id hay không */
            if (!$item['default_site_id']) {
                continue;
            }
            /** Lấy danh sách content_profile của content không cần map theo site */
            $contentProfiles = ContentProfile::find()
                ->andWhere(['content_id' => $row['id'], 'type' => ContentProfile::TYPE_CDN, 'status' => Content::STATUS_ACTIVE])
                ->all();
            /** Nếu tồn tại quality thì mới xử lí */
            if (!$contentProfiles) {
                continue;
            }
            $is_check = false;
            /** @var  $contentProfile ContentProfile */
            foreach ($contentProfiles as $contentProfile) {
                /** Chỉ xử lí content_profile thuộc defaultSite */
                /** @var  $defaultContentProfileSite ContentProfileSiteAsm */
                $defaultContentProfileSite = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id, 'site_id' => $item['default_site_id'], 'status' => ContentProfileSiteAsm::STATUS_ACTIVE]);
                if (!$defaultContentProfileSite) {
                    continue;
                }
                /** Nếu content_profile này đã có trong targetSite thì thôi không xử lí */
                $targetContentProfileSite = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id, 'site_id' => $target_site_id, 'status' => ContentProfileSiteAsm::STATUS_ACTIVE]);
                if ($targetContentProfileSite) {
                    continue;
                }
                /** Get object content_priofile để xử lí*/
                $contentProfileDefault = $defaultContentProfileSite->contentProfile;
                /** Nếu là kiểu CDN thì mới xử lí */
                if ($contentProfileDefault->type != ContentProfile::TYPE_CDN) {
                    continue;
                }
                /** get lấy link CDN  */
                $res = ContentProfile::getCdnUrl($defaultContentProfileSite->url);
                if (!$res['success']) {
                    continue;
                }
                $arrCP                       = [];
                $arrCP['content_profile_id'] = $contentProfileDefault->id;
                $arrCP['content_link']       = $res['success'] ? $res['url'] : "";
                $arrCP['cdn_content_id']     = $defaultContentProfileSite->url;
                $arrCP['quality']            = $contentProfileDefault->quality;
                $item['qualities'][]         = $arrCP;
                $is_check                    = true;

            }
            /** Nếu không có content_profile thì không truyền sang Downloader */
            if ($is_check) {
                /** Nếu chuyển được trạng thái sang STATUS_TRANSFERING thì mới đưa vào mảng */
                $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $row->id, 'site_id' => $target_site_id]);
                if (!$contentSiteAsm) {
                    continue;
                }
                /** Chuyển trạng thái thành STATUS_TRANSFERING */
                $contentSiteAsm->status = ContentSiteAsm::STATUS_TRANSFERING;
                if (!$contentSiteAsm->save()) {
                    continue;
                }

                /** Thỏa mãn mọi điều kiện, đưa vào mảng để truyền sang Downloader */
                unset($item['default_site_id']);
                $arrItems[] = $item;

            }

        }
        $data['items'] = $arrItems;
        /** json_encode data trước khi truyền */
        $json_data = json_encode($data); //return $json_data;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json;charset=UTF-8"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30s timeout
        Yii::info('#### Post to Downloader: ' . $json_data);
        $response = curl_exec($ch);
        if ($response === false) {
            CUtils::log('#### Post to Downloader error: ' . curl_error($ch));
            $return['succes']  = false;
            $return['message'] = Message::MSG_FAIL;
        } else {
            CUtils::log('#### Return from Downloader: ' . $response);
            $return['succes']  = true;
            $return['message'] = Message::MSG_SYNC_DATA_TO_SITE_SUCCESS;
        }
        curl_close($ch);
        return $return;

    }

//    public static function syncDataToSite($target_site_id, $streaming_server_id)
    //    {
    //        if (!is_numeric($target_site_id)) {
    //            $res['status']  = false;
    //            $res['message'] = CUtils::replaceParam(Message::MSG_NUMBER_ONLY, ['site_id']);
    //            return $res;
    //        }
    //        if (!is_numeric($streaming_server_id)) {
    //            $res['status']  = false;
    //            $res['message'] = CUtils::replaceParam(Message::MSG_NUMBER_ONLY, ['streaming_server_id']);
    //            return $res;
    //        }
    //        /** @var  $streamAsm SiteStreamingServerAsm*/
    //        $streamAsm = SiteStreamingServerAsm::findOne(['streaming_server_id' => $streaming_server_id]);
    //
    //        if (!$streamAsm) {
    //            $res['status']  = false;
    //            $res['message'] = Message::MSG_NOT_FOUND_STREAMING;
    //            return $res;
    //        }
    //        /** @var  $stream StreamingServer*/
    //        $stream         = $streamAsm->streamingServer;
    //        $url            = $stream->content_api;
    //        $content_folder = $stream->content_path;
    //
    //        /** Bỏ phần check theo trạng thái contentSite mà mình sẽ quét all */
    //        /** Update 20160721 check chỉ lấy những thằng Content có trạng thái STATUS_ACTIVE và ContentSiteAsm có trạng thái là  STATUS_NOT_TRANSFER, STATUS_TRANSFER_ERROR theo nội dung cuộc họp */
    //        $items = Content::find()
    //            ->innerJoin('content_site_asm', 'content.id=content_site_asm.content_id')
    //            ->andWhere(['content_site_asm.site_id' => $target_site_id,'content_site_asm.status' => [ContentSiteAsm::STATUS_NOT_TRANSFER, ContentSiteAsm::STATUS_TRANSFER_ERROR] ])
    //            ->andWhere(['content.status' => Content::STATUS_ACTIVE ])
    ////            ->limit(100)->all();
    //            ->all();
    //        /** Nếu không có content nào thỏa mãn thì thông báo  */
    //        if(count($items) <=0 ){
    //            $res['status']  = false;
    //            $res['message'] = Message::MSG_NOT_FOUND_CONTENT;
    //            return $res;
    //        }
    //        $data                   = [];
    //        $data['request_id']     = time();
    //        $data['site_id']        = $target_site_id;
    //        $data['content_folder'] = $content_folder;
    //        $data['token']          = md5($data['request_id'] . $target_site_id);
    //        $arrItems               = [];
    //        /** @var  $row */
    //        foreach ($items as $row) {
    //            $item = $row->getAttributes(['id', 'type', 'default_site_id'], ['tvod1_id']);
    //            /** Kiểm tra xem có site_default_id hay không */
    //            if (!$item['default_site_id']) {
    //                continue;
    //            }
    //            $contentProfiles = $row->contentProfiles;
    //            /** Nếu tồn tại quality thì mới xử lí */
    //            if (!$contentProfiles) { echo "deo dadc";exit;
    //                continue;
    //            }
    //            $is_check = false;
    //            /** @var  $contentProfile ContentProfile*/
    //            foreach ($contentProfiles as $contentProfile) {
    //                /** Chỉ xử lí content_profile thuộc defaultSite */
    //                /** @var  $defaultContentProfileSite ContentProfileSiteAsm*/
    ////                echo $item['default_site_id'];exit;
    //                $defaultContentProfileSite = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id, 'site_id' => $item['default_site_id'], 'status' => ContentProfileSiteAsm::STATUS_ACTIVE]);
    //                if (!$defaultContentProfileSite) {
    //                    continue;
    //                }
    //                /** Nếu content_profile này đã có trong targetSite thì thôi không xử lí */
    //                $targetContentProfileSite = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id, 'site_id' => $target_site_id, 'status' => ContentProfileSiteAsm::STATUS_ACTIVE]);
    //                if ($targetContentProfileSite) {
    //                    continue;
    //                }
    //                /** Get object content_priofile để xử lí*/
    //                $contentProfileDefault = $defaultContentProfileSite->contentProfile;
    //                /** Nếu là kiểu CDN thì mới xử lí */
    //                if ($contentProfileDefault->type != ContentProfile::TYPE_CDN) {
    //                    continue;
    //                }
    //                /** get lấy link CDN  */
    //                $res = ContentProfile::getCdnUrl($defaultContentProfileSite->url);
    //                if (!$res['success']) {
    //                    continue;
    //                }
    //                $arrCP                       = [];
    //                $arrCP['content_profile_id'] = $contentProfileDefault->id;
    //                $arrCP['content_link']       = $res['success'] ? $res['url'] : "";
    //                $arrCP['cdn_content_id']     = $defaultContentProfileSite->url;
    //                $arrCP['quality']            = $contentProfileDefault->quality;
    //                $item['qualities'][]         = $arrCP;
    //                $is_check                    = true;
    //
    //            }
    //            /** Nếu không có content_profile thì không truyền sang Downloader */
    //            if ($is_check) {
    //                /** Nếu chuyển được trạng thái sang STATUS_TRANSFERING thì mới đưa vào mảng */
    //                $contentSiteAsm = ContentSiteAsm::findOne(['content_id'=>$row->id, 'site_id'=>$target_site_id]);
    //                if(!$contentSiteAsm){
    //                    continue;
    //                }
    //                /** Chuyển trạng thái thành STATUS_TRANSFERING */
    //                $contentSiteAsm->status = ContentSiteAsm::STATUS_TRANSFERING;
    //                if(!$contentSiteAsm->save()){
    //                    continue;
    //                }
    //
    //                /** Thỏa mãn mọi điều kiện, đưa vào mảng để truyền sang Downloader */
    //                unset($item['default_site_id']);
    //                $arrItems[] = $item;
    //
    //            }
    //
    //        }
    //
    //        $data['items'] = $arrItems; echo "count: ".count($arrItems);exit;
    //        /** json_encode data trước khi truyền */
    //        $json_data = json_encode($data);return $json_data;
    //
    //        $ch = curl_init();
    //        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //        curl_setopt($ch, CURLOPT_URL, $url);
    //        curl_setopt($ch, CURLOPT_HEADER, true);
    //        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    //        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json;charset=UTF-8"));
    //        curl_setopt($ch, CURLOPT_POST, 1);
    //        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30s timeout
    //        Yii::info('#### Post to Downloader: ' . $json_data);
    //        $response = curl_exec($ch);
    //        if ($response === false) {
    //            CUtils::log('#### Post to Downloader error: ' . curl_error($ch));
    //            $return['succes']  = false;
    //            $return['message'] = Message::MSG_FAIL;
    //        } else {
    //            CUtils::log('#### Return from Downloader: ' . $response);
    //            $return['succes']  = true;
    //            $return['message'] = Message::MSG_SYNC_DATA_TO_SITE_SUCCESS;
    //        }
    //        curl_close($ch);
    //        return $return;
    //
    //    }

    /**
     * @param $target_site_id
     * @param $content_id
     * @param $streaming_server_id
     * @return mixed
     */
    public static function syncContentToSite($target_site_id, $content_id, $streaming_server_id)
    {
        if (!is_numeric($target_site_id)) {
            $res['status']  = false;
            $res['message'] = CUtils::replaceParam(Message::MSG_NUMBER_ONLY, ['site_id']);
            return $res;
        }
        if (!is_numeric($streaming_server_id)) {
            $res['status']  = false;
            $res['message'] = CUtils::replaceParam(Message::MSG_NUMBER_ONLY, ['streaming_server_id']);
            return $res;
        }
        /** @var  $streamAsm SiteStreamingServerAsm */
        $streamAsm = SiteStreamingServerAsm::findOne(['streaming_server_id' => $streaming_server_id]);

        if (!$streamAsm) {
            $res['status']  = false;
            $res['message'] = Message::MSG_NOT_FOUND_STREAMING;
            return $res;
        }
        /** @var  $stream StreamingServer */
        $stream         = $streamAsm->streamingServer;
        $url            = $stream->content_api;
        $content_folder = $stream->content_path;

        /** Bỏ phần check theo trạng thái contentSite mà mình sẽ quét all */
        /** Update 20160721 check chỉ lấy những thằng Content có trạng thái STATUS_ACTIVE và ContentSiteAsm có trạng thái là  STATUS_NOT_TRANSFER, STATUS_TRANSFER_ERROR theo nội dung cuộc họp */
        $item = Content::find()
            ->andWhere(['content.id' => $content_id])
            ->innerJoin('content_site_asm', 'content.id=content_site_asm.content_id')
            ->andWhere(['content_site_asm.site_id' => $target_site_id, 'content_site_asm.status' => [ContentSiteAsm::STATUS_NOT_TRANSFER, ContentSiteAsm::STATUS_TRANSFER_ERROR]])
            ->andWhere(['content.status' => Content::STATUS_ACTIVE, 'content.type' => [
                Content::TYPE_VIDEO,
                Content::TYPE_MUSIC,
                Content::TYPE_NEWS,
                Content::TYPE_CLIP,
                Content::TYPE_KARAOKE,
                Content::TYPE_RADIO,
                Content::TYPE_LIVE_CONTENT,
            ]])
            ->one();
        /** Chỉ xử lí với thằng ở trạng thái STATUS_NOT_TRANSFER, STATUS_TRANSFER_ERROR */
        if (!$item) {
            $res['status']  = false;
            $res['message'] = Message::MSG_NOT_FOUND_CONTENT;
            return $res;
        }
        $data                   = [];
        $data['request_id']     = time();
        $data['site_id']        = $target_site_id;
        $data['content_folder'] = $content_folder;
        $data['token']          = md5(time() . $target_site_id);
        $arrItems               = [];
        /** convert sang mảng*/
        $arrItem = $item->getAttributes(['id', 'type', 'default_site_id'], ['tvod1_id']);
        /** Kiểm tra xem có site_default_id hay không */
        if (!$item->default_site_id) {
            $res['status']  = false;
            $res['message'] = CUtils::replaceParam(Message::MSG_NULL_VALUE, ['default_site_id']);
            return $res;
        }

        /** Lấy danh sách content_profile của content không cần map theo site */
        $contentProfiles = ContentProfile::find()
            ->andWhere(['content_id' => $item['id'], 'type' => ContentProfile::TYPE_CDN, 'status' => Content::STATUS_ACTIVE])
            ->all();
        /** Nếu tồn tại quality thì mới xử lí */
        if (!$contentProfiles) {
            $res['status']  = false;
            $res['message'] = Message::CONTENT_PROFILE_NOT_FOUND;
            return $res;
        }

        $is_check = false;
        /** @var  $contentProfile ContentProfile */
        foreach ($contentProfiles as $contentProfile) {
            /** Chỉ xử lí content_profile thuộc defaultSite */
            /** @var  $defaultContentProfileSite ContentProfileSiteAsm */
            $defaultContentProfileSite = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id, 'site_id' => $item['default_site_id'], 'status' => ContentProfileSiteAsm::STATUS_ACTIVE]);
            if (!$defaultContentProfileSite) {
                continue;
            }
            /** Nếu content_profile này đã có trong targetSite thì thôi không xử lí */
            $targetContentProfileSite = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id, 'site_id' => $target_site_id, 'status' => ContentProfileSiteAsm::STATUS_ACTIVE]);
            if ($targetContentProfileSite) {
                continue;
            }
            /** Get object content_priofile để xử lí*/
            $contentProfileDefault = $defaultContentProfileSite->contentProfile;

            /** Nếu là kiểu CDN thì mới xử lí */
            if ($contentProfileDefault->type != ContentProfile::TYPE_CDN) {
                continue;
            }
            /** get lấy link CDN  */
            $res = ContentProfile::getCdnUrl($defaultContentProfileSite->url);

            $arrCP                       = [];
            $arrCP['content_profile_id'] = $contentProfileDefault->id;
            $arrCP['content_link']       = $res['success'] ? $res['url'] : "";
            $arrCP['cdn_content_id']     = $defaultContentProfileSite->url;
            $arrCP['quality']            = $contentProfileDefault->quality;
            $arrItem['qualities'][]      = $arrCP;
            $is_check                    = true;

        }
        if ($is_check) {
            /** Nếu chuyển được trạng thái sang STATUS_TRANSFERING thì mới đưa vào mảng */
            $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $item->id, 'site_id' => $target_site_id]);
            if (!$contentSiteAsm) {
                $return['succes']  = false;
                $return['message'] = Message::MSG_NOT_FOUND_CONTENT;
            }
            /** Chuyển trạng thái thành STATUS_TRANSFERING */
            $contentSiteAsm->status = ContentSiteAsm::STATUS_TRANSFERING;
            if (!$contentSiteAsm->save()) {
                $return['succes']  = false;
                $return['message'] = Message::MSG_FAIL;
            }
            /** Thỏa mãn mọi điều kiện, đưa vào mảng để truyền sang Downloader */
            unset($item['default_site_id']);
            $arrItems[] = $arrItem;
        }
        /** Nếu không có content_profile nào thỏa mãn thì báo lỗi */
        if (count($arrItems) <= 0) {
            $return['succes']  = false;
            $return['message'] = Message::CONTENT_PROFILE_NOT_FOUND;
        }
        $data['items'] = $arrItems;
        /** json_encode data trước khi truyền */
        $json_data = json_encode($data); //return $json_data;
        $ch        = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json;charset=UTF-8"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30s timeout
        Yii::info('#### Post to Downloader: ' . $json_data);
        $response = curl_exec($ch);
        if ($response === false) {
            CUtils::log('#### Post to Downloader error: ' . curl_error($ch));
            $return['succes']  = false;
            $return['message'] = Message::MSG_FAIL;
        } else {
            CUtils::log('#### Return from Downloader: ' . $response);
            $return['succes']  = true;
            $return['message'] = Message::MSG_SYNC_DATA_TO_SITE_SUCCESS;
        }
        curl_close($ch);
        return $return;
    }

    public function getActors()
    {
        switch ($this->type) {
            case self::TYPE_VIDEO:
                return ArrayHelper::map(ActorDirector::findAll(['type' => ActorDirector::TYPE_ACTOR, 'content_type' => self::TYPE_VIDEO]), 'id', 'name');
                break;
            case self::TYPE_KARAOKE:
                return ArrayHelper::map(ActorDirector::findAll(['type' => ActorDirector::TYPE_ACTOR, 'content_type' => self::TYPE_KARAOKE]), 'id', 'name');
                break;
            default:
                return [];
                break;
        }
    }

    public function getDirectors()
    {
        switch ($this->type) {
            case self::TYPE_VIDEO:
                return ArrayHelper::map(ActorDirector::findAll(['type' => ActorDirector::TYPE_DIRECTOR, 'content_type' => self::TYPE_VIDEO]), 'id', 'name');
                break;
            case self::TYPE_KARAOKE:
                return ArrayHelper::map(ActorDirector::findAll(['type' => ActorDirector::TYPE_DIRECTOR, 'content_type' => self::TYPE_KARAOKE]), 'id', 'name');
                break;
            default:
                return [];
                break;
        }
    }

    public function saveActorDirectors()
    {
        if ($this->type != self::TYPE_VIDEO && $this->type != self::TYPE_KARAOKE) {
            return false;
        }

        $directorSaved = $actorSaved = false;

        ContentActorDirectorAsm::deleteAll(['content_id' => $this->id]);

        if (!empty($this->content_directors)) {
            foreach ($this->content_directors as $key => $value) {
                $newActorDirectorsAsm                    = new ContentActorDirectorAsm;
                $newActorDirectorsAsm->content_id        = $this->id;
                $newActorDirectorsAsm->actor_director_id = $value;
                $directorSaved                           = $newActorDirectorsAsm->save();
            }
        }

        if (!empty($this->content_actors)) {
            foreach ($this->content_actors as $key => $value) {
                $newActorDirectorsAsm                    = new ContentActorDirectorAsm;
                $newActorDirectorsAsm->content_id        = $this->id;
                $newActorDirectorsAsm->actor_director_id = $value;
                $actorSaved                              = $newActorDirectorsAsm->save();
            }
        }

        return $directorSaved && $actorSaved;
    }

    public function getContentDirectors()
    {
        $directors = ActorDirector::find()
            ->innerJoin('content_actor_director_asm', 'content_actor_director_asm.actor_director_id = actor_director.id')
            ->innerJoin('content', 'content_actor_director_asm.content_id = content.id')
            ->where(['content.id' => $this->id])
            ->andwhere(['actor_director.type' => ActorDirector::TYPE_DIRECTOR])
            ->all();

        return $this->content_directors = ArrayHelper::map($directors, 'name', 'id');
    }

    public function getContentActors()
    {
        $actors = ActorDirector::find()
            ->innerJoin('content_actor_director_asm', 'content_actor_director_asm.actor_director_id = actor_director.id')
            ->innerJoin('content', 'content_actor_director_asm.content_id = content.id')
            ->where(['content.id' => $this->id])
            ->andwhere(['actor_director.type' => ActorDirector::TYPE_ACTOR])
            ->all();

        return $this->content_actors = ArrayHelper::map($actors, 'name', 'id');
    }

    public function getContentSiteProvider()
    {
        return self::find()
            ->innerJoin('content_site_asm', 'content_site_asm.content_id = content.id')
            ->innerJoin('site', 'site.id = content_site_asm.site_id')
            ->select('site.name site_name, site.id site_id, content_site_asm.status content_site_asm_status')
            ->where(['content.id' => $this->id])
            ->all();
    }

    public static function checkQualityWhenDownload($content_id, $site_id)
    {
        /** Lấy content_profile của thằng gốc */
        $contentProfilesDefault = ContentProfile::find()
            ->andWhere(['content_id' => $content_id, 'type' => ContentProfile::TYPE_CDN, 'status' => Content::STATUS_ACTIVE])
            ->all();
        $totalCountDefault = count($contentProfilesDefault);
        $totalCountSuccess = 0;
        foreach ($contentProfilesDefault as $contentProfileDefault) {
            $contentProfileSiteAsm = ContentProfileSiteAsm::find()->andWhere(['content_profile_id' => $contentProfileDefault->id, 'site_id' => $site_id, 'status' => ContentProfileSiteAsm::STATUS_ACTIVE])->one();
            if ($contentProfileSiteAsm) {
                $totalCountSuccess++;
            }
        }
        if ($totalCountDefault == $totalCountSuccess) {
            return true;
        } else {
            return false;
        }

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
    public function getIsFree($site_id)
    {
        $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $this->id, 'site_id' => $site_id, 'status' => Content::STATUS_ACTIVE]);
        if (!$contentSiteAsm) {
            return Content::IS_FREE;
        }
        if (empty($contentSiteAsm->pricing_id)) {
            return Content::IS_FREE;
        }
        return Content::NOT_FREE;
    }

    /**
     * @param $site_id
     * @return int
     */
    public function getPriceCoin($site_id)
    {
        /** @var  $contentSiteAsm ContentSiteAsm*/
        $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $this->id, 'site_id' => $site_id, 'status' => Content::STATUS_ACTIVE]);
        if (!$contentSiteAsm->pricing_id) {
            return 0;
        }
        return $contentSiteAsm->pricing ? $contentSiteAsm->pricing->price_coin : 0;
    }

    /**
     * @param $site_id
     * @return float|int
     */
    public function getPriceSms($site_id)
    {
        /** @var  $contentSiteAsm ContentSiteAsm*/
        $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $this->id, 'site_id' => $site_id, 'status' => Content::STATUS_ACTIVE]);
        if (!$contentSiteAsm->pricing_id) {
            return 0;
        }
        return $contentSiteAsm->pricing ? $contentSiteAsm->pricing->price_sms : 0;
    }

    /**
     * @param $site_id
     * @return int
     */
    public function getWatchingPriod($site_id)
    {
        /** @var  $contentSiteAsm ContentSiteAsm*/
        $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $this->id, 'site_id' => $site_id, 'status' => Content::STATUS_ACTIVE]);
        if (!$contentSiteAsm->pricing_id) {
            return 0;
        }
        return $contentSiteAsm->pricing ? $contentSiteAsm->pricing->watching_period : 0;
    }
}
