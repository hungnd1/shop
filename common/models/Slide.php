<?php

namespace common\models;

use api\models\Content;
use api\models\ListContentSlider;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "slide".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $content_id
 * @property string $name
 * @property string $open_url
 * @property string $description
 * @property integer $weight
 * @property integer $open_count
 * @property double $rating
 * @property integer $rating_count
 * @property string $banner
 * @property integer $status
 * @property integer $is_visible
 * @property integer $is_visible_ios
 * @property integer $is_visible_android
 * @property integer $is_visible_wp
 * @property integer $service_provider_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Content $content
 * @property ServiceProvider $serviceProvider
 */
class Slide extends \yii\db\ActiveRecord
{

    const STATUS_INACTIVE= 0;
    const STATUS_ACTIVE = 1;

    public static $slide_status = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => "Disable"
    ];

    const SLIDE_TYPE_CONTENT = 1;
    const SLIDE_TYPE_BANNER = 2;

    public static function getListType(){
        return [
            self::SLIDE_TYPE_BANNER => Yii::t('app','Banner'),
            self::SLIDE_TYPE_CONTENT => Yii::t('app','Load from content'),
        ];
    }

    public $file_banner;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'slide';
    }

    public static function getContents($limit = 5)
    {
        $query = static::find();

        $query->andWhere(['status'=> self::STATUS_ACTIVE]);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'weight' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => $limit,
            ]
        ]);
        return $provider;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'content_id','created_at','status','updated_at'], 'integer'],
            [['content_id'], 'required', 'message' => 'Không được để trống sản phẩm làm Banner'],
            ['content_id', 'validateContent'],
            [['des'], 'string', 'max' => 4000],
        ];
    }
    public function validateContent($attribute, $params)
    {
        /**
         * @var $content Content
         */
        Yii::info('Validate content');
        $content = Content::findOne($this->content_id);
        if (!$content) {
            $this->addError($attribute, 'Không tồn tại sản phẩm này');
        }else{
            $images = $content->getImages();
            if(count($images) <= 0){
                $this->addError($attribute, 'Sản phẩm không có hình ảnh');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'content_id' => Yii::t('app', 'Content ID'),
            'des' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceProvider(){
        return $this->hasOne(ServiceProvider::className(), ['id' => 'service_provider_id']);
    }

    public static function getMaxWeigh(){
        $max = Slide::find()->orderBy('weight DESC')->one();
        if($max){
            return $max->weight + 1;
        }
        return 1;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }

    public function saveBannerFile(){
        if($this->type == self::SLIDE_TYPE_BANNER && $this->file_banner != null){
            /**
             * Xoa file cu
             */
            if($this->file_banner && !$this->isNewRecord){
                $this->deleteBannerFile();
            }
            $ext = end(explode(".", $this->file_banner->name));
            $file_save = Yii::$app->security->generateRandomString().".{$ext}";
            $folder = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . Yii::getAlias('@content_banner') . DIRECTORY_SEPARATOR;
            if (!is_dir($folder)) {
                FileHelper::createDirectory($folder);
            }
            $path = $folder.$file_save;
            $this->file_banner->saveAs($path);
//            $this->banner = '@content_banner'.DIRECTORY_SEPARATOR.$file_save;
            $this->banner = $file_save;
            $this->update(false);
        }
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->deleteBannerFile();
            return true;
        } else {
            return false;
        }
    }

    public function getBannerUrl()
    {
        if(empty($this->banner)){
            return Yii::t('app', 'Banner url not found');
        }
        return Yii::getAlias('@web') . '/' . Yii::getAlias('@content_banner') . '/'.$this->banner;
    }

    private function deleteBannerFile()
    {
        if(empty($this->banner)) return;
        $path = Yii::getAlias('@web') . '/' . Yii::getAlias('@content_banner') . '/'.$this->banner;
        if(file_exists($path)){
            unlink($path);
        }
    }

    public function getPlatforms()
    {
        $platforms = ($this->is_visible_android)?'Android OS, ':'';
        $platforms .= ($this->is_visible_ios)?'iOS, ':'';
        $platforms .= ($this->is_visible_wp)?'Windown Phone ':'';
        if(empty($platforms)){
            $platforms = Yii::t('app', 'Invisible');
        }
        return $platforms;
    }

    public function getViewContent()
    {
        $content = '';
        if($this->type == self::SLIDE_TYPE_BANNER){
            $content = Html::img($this->getBannerUrl(),['style' => 'height:120px']);
        }else{
            $content = Html::a($this->content->display_name,['content/view', 'id' => $this->content->id], ['class' => 'label label-primary']);
        }
        return $content;
    }

    public function getUrl(){
        /**
         * TODO: Get link cua slide tuy theo loai
         * - Content: thi generate frontend content
         * - URL: thi lay thang openurl luon
         */
       return $this->type == Slide::SLIDE_TYPE_CONTENT ? $this->content->getViewUrl() : $this->open_url;
    }

    public function getSlideImage(){
        /**
         * TODO: Neu loai la content thi viet ham lay image link image slide
         */
        return $this->type == Slide::SLIDE_TYPE_CONTENT ? $this->content->getFirstImageLink() : $this->getBannerUrl() ;
    }
    public static function getSlider($sp){
        $query = ListContentSlider::find()
            ->andWhere(['slide_content.service_provider_id'=>$sp])
            //phim moi nhat
            ->andWhere(['slide_content.status'=>Slide::STATUS_ACTIVE]);
        $orderDefault = [];
        $orderDefault['weight'] = SORT_DESC;
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => $orderDefault
            ],
            'pagination' => [
                'defaultPageSize' => 8,
            ]
        ]);
        return $provider;
    }
}
