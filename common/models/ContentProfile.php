<?php

namespace common\models;

use common\helpers\CVietnameseTools;
use common\helpers\MyCurl;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\helpers\FileHelper;
use yii\validators\UrlValidator;

/**
 * This is the model class for table "{{%content_profile}}".
 *
 * @property integer $id
 * @property integer $content_id
 * @property string $name
 * @property string $description
 * @property integer $type
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $bitrate
 * @property integer $width
 * @property integer $height
 * @property integer $quality
 * @property double $progress
 * @property integer $tvod1_id
 *
 * @property Content $content
 * @property ContentProfileSiteAsm[] $contentProfileSiteAsms
 */
class ContentProfile extends \yii\db\ActiveRecord
{

    private static $cpName    = 'TVoD';
    private static $secretKey = 'aslk02938';

    const URL_SERVER_CDN_TVOD = "http://api.cdn.tvod.com.vn";
    const URL_SERVER_CATCHUP  = "http://tvod-hwu3a.static.cdn.tvod.com.vn/";

    const LOCATION_BACKEND = 1;
    const LOCATION_STORAGE = 2;

    const TYPE_RAW    = 1;
    const TYPE_STREAM = 2;
    const TYPE_CDN    = 3;

    /**
     * "SD") type="1" ;;
    "HD") type="2" ;;
    "MB") type="3" ;;
    "AD") type="4" ;;
    "SU") type="5" ;;
    "FP") type="6" ;;
     */
    // const QUALITY_LOW = 1;
    // const QUALITY_NORMAL = 2;
    // const QUALITY_HIGH = 3;
    // const QUALITY_HD = 4;
    // const QUALITY_FHD = 5;
    // const QUALITY_4K = 10;

    const QUALITY_SD   = 1;
    const QUALITY_HD   = 2;
    const QUALITY_MB   = 3;
    const QUALITY_AD   = 4;
    const QUALITY_SU   = 5;
    const QUALITY_FP   = 6;
    const QUALITY_H265 = 7;

    const STATUS_ACTIVE           = 10;
    const STATUS_INACTIVE         = 0;
    const STATUS_TEST             = 3;
    const STATUS_TRANCODED        = 4; // DA TRANSCOE
    const STATUS_TRANCODE_PENDING = 5; // DANG TRANSCOE, Khoa
    const STATUS_RAW              = 6; // raw chua transcode
    const STATUS_RAW_ERROR        = 7; // raw error
    const STATUS_UPLOADING        = 8; // raw error

    const STREAMING_HTTP = 0;
    const STREAMING_RTSP = 1;
    const STREAMING_HLS  = 2;
    const STREAMING_RTMP = 3;
    const STREAMING_MMS  = 4;

    public static $stream_quality = [
        self::QUALITY_SD   => 'SD',
        self::QUALITY_HD   => 'HD',
        self::QUALITY_MB   => 'MB',
        self::QUALITY_AD   => 'AD',
        self::QUALITY_SU   => 'SU',
        self::QUALITY_FP   => 'FP',
        self::QUALITY_H265 => 'H256',
    ];

    public static $types = [
        self::TYPE_RAW    => 'Raw',
        self::TYPE_STREAM => 'Stream',
        self::TYPE_CDN    => 'Cdn',
    ];

    public static $stream_status = [
        self::STATUS_ACTIVE           => 'Active',
        self::STATUS_INACTIVE         => 'In Active',
        self::STATUS_TEST             => 'Test',
        self::STATUS_TRANCODE_PENDING => 'Transcode pending',
        self::STATUS_TRANCODED        => 'Transcoded',
        self::STATUS_RAW              => 'NOT TRANSCODE',
        self::STATUS_RAW_ERROR        => 'RAW ERROR',
        self::STATUS_UPLOADING        => 'Uploading',
    ];

    public static $stream_protocol = [
        self::STREAMING_HTTP => 'http',
        self::STREAMING_RTSP => 'rtsp',
        self::STREAMING_HLS  => 'hls',
        self::STREAMING_RTMP => 'rtmp',
        self::STREAMING_MMS  => 'mms',
    ];

    public $detail = 'Chi tiết luồng';

    public $file_sub;
    public $url;
    public $subtitle;

    public $catchup_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_profile}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $type_raw = function ($model) {
            return $model->type == self::TYPE_RAW;
        };
        return [
            // [['content_id', 'url'], 'required'],
            [['content_id', 'type', 'status', 'created_at', 'updated_at', 'bitrate', 'width', 'height', 'quality', 'tvod1_id'], 'integer'],
            [['progress'], 'number'],
            [['name'], 'string', 'max' => 200],
            [['url'], 'string', 'max' => 1000],
            [['description'], 'string', 'max' => 4000],
            [['subtitle'], 'file', 'extensions' => ['txt', 'smi', 'srt', 'ssa', 'sub', 'ass', 'style'], 'checkExtensionByMimeType' => false, 'maxSize' => 1024 * 1024 * 10],
        ];
    }

    public function validateRaw($attribute, $params)
    {
        if ($this->isNewRecord) {
            $file_raw = ContentProfile::find()->andWhere(['content_id' => $this->content_id, 'type' => ContentProfile::TYPE_RAW])->one();
            if ($file_raw != null) {
                $this->addError('url', 'Only have one file RAW');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'content_id'  => Yii::t('app', 'Content ID'),
            'name'        => Yii::t('app', 'Name'),
            'url'         => Yii::t('app', 'Url'),
            'description' => Yii::t('app', 'Description'),
            'type'        => Yii::t('app', 'Loại'),
            'status'      => Yii::t('app', 'Trạng thái'),
            'created_at'  => Yii::t('app', 'Created At'),
            'updated_at'  => Yii::t('app', 'Updated At'),
            'bitrate'     => Yii::t('app', 'Bitrate'),
            'width'       => Yii::t('app', 'Width'),
            'height'      => Yii::t('app', 'Height'),
            'quality'     => Yii::t('app', 'Chất lượng'),
            'progress'    => Yii::t('app', 'Progress'),
            'subtitle'    => Yii::t('app', 'Phụ đề'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentProfileSiteAsms()
    {
        return $this->hasMany(ContentProfileSiteAsm::className(), ['content_profile_id' => 'id']);
    }

    public function getProfileUrl($siteId = null)
    {
        $siteId    = $siteId ? $siteId : Content::findOne($this->content_id)->default_site_id;
        $url       = ContentProfileSiteAsm::findOne(['content_profile_id' => $this->id, 'site_id' => $siteId]);
        $this->url = $url['url'];
        return;
    }

    public function getTypeName()
    {
        if (isset(self::$types[$this->type])) {
            return self::$types[$this->type];
        }
        return '';
    }

    public function getQualityName()
    {
        if (isset(self::$stream_quality[$this->quality])) {
            return self::$stream_quality[$this->quality];
        }
        return '';
    }

    public function getStatusName()
    {
        if (isset(self::$stream_status[$this->status])) {
            return self::$stream_status[$this->status];
        }
        return '';
    }

    public static function getCpName()
    {
        return self::$cpName;
    }

    public static function getSecretKey()
    {
        return self::$secretKey;
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        if ($this->save()) {
            return [
                'error'   => 0,
                'message' => 'Cập nhật thành công',
            ];
        }
        return [
            'error'   => 1,
            'message' => 'Cập nhật thất bại',
        ];
    }

    public function getFilePath($location = self::LOCATION_BACKEND)
    {
        $path = '';
        if ($location == self::LOCATION_STORAGE) {
            $path = Yii::getAlias('@storage_location') . DIRECTORY_SEPARATOR . 0 . DIRECTORY_SEPARATOR . $this->name;
        } else {
            $path = Yii::getAlias('@backend') . DIRECTORY_SEPARATOR . 0 . DIRECTORY_SEPARATOR . $this->name;
        }
        return $path;
    }

    public function getSubPath($file = '')
    {
        return Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . Yii::getAlias('@subtitle') . DIRECTORY_SEPARATOR . $file;
    }

    public static function getFileDir($cp_id = 0, $location = self::LOCATION_BACKEND)
    {
        $path = '';
        if ($cp_id == null) {
            $cp_id = 0;
        }

        if ($location == self::LOCATION_STORAGE) {
            $path = Yii::getAlias('@storage_location') . DIRECTORY_SEPARATOR . $cp_id;
        } else {
            $path = Yii::getAlias('@backend') . DIRECTORY_SEPARATOR . $cp_id;
            if (!is_dir($path)) {
                FileHelper::createDirectory($path);
            }
        }
        return $path;
    }

    public function saveSubFile()
    {
        if ($this->subtitle) {
            $file_save = time() . '-' . CVietnameseTools::makeValidFileName($this->subtitle->name);

            if ($this->validate()) {
                $this->subtitle->saveAs($this->getSubPath($file_save));
                $this->subtitle = $file_save;

                return true;
            } else {
                return false;
            }
        }
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this->type == self::TYPE_RAW) {
                if (!empty($this->url)) {
                    $path = $this->getFilePath();
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                if (!empty($this->subtitle)) {
                    $path = $this->getSubPath();
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    private function getPrefixFile()
    {
        preg_match("/(.*)_(.*)(\.[.a-zA-Z0-9]*)$/", $this->url, $output_array);
        return isset($output_array[1]) ? $output_array[1] : $this->url;
    }

    private function getMiddleFile()
    {
        preg_match("/(.*)_(.*)(\.[.a-zA-Z0-9]*)$/", $this->url, $output_array);
        return isset($output_array[2]) ? $output_array[2] : $this->url;
    }

    private function getPostfixFile()
    {
        preg_match("/(.*)_(.*)(\.[.a-zA-Z0-9]*)$/", $this->url, $output_array);
        return isset($output_array[3]) ? $output_array[3] : $this->url;
    }

    /**
     * Create new profile from raw file
     * @param $bitrate
     * @param $width
     * @param $height
     * @param $url
     */
    public function createNewProfile($bitrate, $width, $height, $url, $quality = 1)
    {
        //Check xem co profile nao cung url chua
        $profile = ContentProfile::findOne(['content_id' => $this->content_id, 'url' => $url, 'type' => self::TYPE_STREAM]);
        if (!$profile) {
            $profile             = new ContentProfile();
            $profile->content_id = $this->content_id;
            $profile->type       = self::TYPE_STREAM;
            $profile->status     = self::STATUS_ACTIVE;
            $profile->quality    = $quality;
        }

        $profile->bitrate = $bitrate;
        $profile->width   = $width;
        $profile->height  = $height;
        // $profile->url     = $url;
        $profile->quality = $quality;

        if ($profile->save()) {
            $cps                     = new ContentProfileSiteAsm();
            $cps->content_profile_id = $profile->id;
            $cps->url                = $url;
            $cps->site_id            = Content::findOne($this->content_id)->default_site_id;
            $cps->status             = ContentProfileSiteAsm::STATUS_ACTIVE;

            return $cps->insert();
        }

        return false;
    }

    /**
     * Create adaptive url only for HLS
     * @param ContentProfile[] $streams
     */
    public static function generateAdaptiveUrl($streams)
    {
        // TODO
        $urls = [];
        foreach ($streams as $key => $value) {
            $urls[] = [
                'quality' => $streams[$key]->name,
                'url'     => $streams[$key]->url,
            ];
        }
        return $urls;
        $url = self::getBaseStreamingUrl(ContentProfile::STREAMING_HLS);
        if (empty($url)) {
            return $url;
        }

        $url_validator = new UrlValidator();

        for ($i = 0; $i < count($streams); $i++) {
            $stream = $streams[$i];
            if ($url_validator->validate($stream->url)) {
                return $stream->url;
            }
            if ($i == 0) {
                $url .= $stream->getPrefixFile() . '_';
            }
            $url .= ',' . $stream->getMiddleFile();
            if ($i == (count($streams) - 1)) {
                $url .= ',' . $stream->getPostfixFile() . '.urlset';
            }
        }
        return $url . DIRECTORY_SEPARATOR . 'master.m3u8';
    }

    public static function getStreamUrl($url)
    {
        $url        = ContentProfile::URL_SERVER_CATCHUP . $url . '.mp4/index.m3u8';
        return $res = ['success' => true, 'url' => $url];
    }
//    /**
    //     * @param $object ContentProfile
    //     * @param $quality
    //     * @param $type_stream
    //     * @return array|bool
    //     */
    //    public static function getStreamUrl($object, $quality = null, $type_stream)
    //    {
    //        /**
    //         *  blocked older code by HungNV
    //         *
    //         * $url = self::getBaseStreamingUrl($protocol);
    //         * if (empty($url)) return $url;
    //         * $url_validator = new UrlValidator();
    //         * if ($url_validator->validate($this->url)) {
    //         * return $this->url;
    //         * }
    //         * switch ($protocol) {
    //         * case ContentProfile::STREAMING_HLS:
    //         * return $url . $this->url . DIRECTORY_SEPARATOR . 'index.m3u8';
    //         * case ContentProfile::STREAMING_HTTP:
    //         * return $url . $this->url;
    //         * case ContentProfile::STREAMING_MMS:
    //         * return $url . $this->url . DIRECTORY_SEPARATOR . 'manifest';
    //         * case ContentProfile::STREAMING_RTSP:
    //         * return $url . $this->url;
    //         * default:
    //         * return $url . $this->url;
    //         * }
    //         */
    //
    //        if ($quality == null) {
    //            $profiles = ContentProfile::findOne(['content_id' => $object->content_id, 'type' => $type_stream, 'status' => ContentProfile::STATUS_ACTIVE]);
    //            if (count($profiles) != 1) {
    //                foreach ($profiles as $key => $value) {
    //                    //TODO
    //                }
    //            } else {
    //                $profile = $profiles;
    //            }
    //        } else {
    //            $profile = ContentProfile::findOne(['content_id' => $object->content_id, 'quality' => $quality, 'status' => ContentProfile::STATUS_ACTIVE]);
    //        }
    //        if (!$profile) {
    //            return false;
    //        }
    //        return $profile;
    //    }

    public static function getBaseStreamingUrl($protocol)
    {
        // TODO
        $streaming_config = isset(Yii::$app->params['streaming-server']) ? Yii::$app->params['streaming-server'] : [];
        $url              = '';
        switch ($protocol) {
            case ContentProfile::STREAMING_HLS:
                return isset($streaming_config['hls']) ? $streaming_config['hls'] : '';
            case ContentProfile::STREAMING_HTTP:
                return isset($streaming_config['http']) ? $streaming_config['http'] : '';
            case ContentProfile::STREAMING_MMS:
                return isset($streaming_config['mms']) ? $streaming_config['mms'] : '';
            case ContentProfile::STREAMING_RTSP:
                return isset($streaming_config['rtsp']) ? $streaming_config['rtsp'] : '';
            default:
                return isset($streaming_config['http']) ? $streaming_config['http'] : '';
        }
    }

    /**
     * HungNV creation 15-April
     *
     * @param $id
     * @return bool
     */
    public static function getCdnUrl($id)
    {
        /** access cpName and secretKey through method getCpName and getSecretKey */
        $cpName    = ContentProfile::getCpName();
        $secretKey = ContentProfile::getSecretKey();
        $contentId = $id;
        $repId     = time();
        $euip      = $_SERVER['REMOTE_ADDR'];
//        $euip     = "113.190.240.238";
        $toString = $contentId . $cpName . $repId . $secretKey;
        $en_token = md5($toString);
        $request  = ContentProfile::URL_SERVER_CDN_TVOD . '/getURL?reqId=' . $repId . '&cpName=' . $cpName . '&euip=' . $euip . '&contentId=' . $contentId . '&token=' . $en_token;

        $ch       = new MyCurl();
        $response = $ch->get($request, null);
        $data     = json_decode($response->body);
        if ($data->success) {
            return [
                'success' => true,
                'url'     => $data->contentURL,
            ];
        } else {
            return [
                'success'   => false,
                'codeError' => $data->errorCode,
                'message'   => $data->reason,
            ];
        }
    }
//
    //
    //    /**
    //     * HungNV
    //     * @param $id
    //     * @return array
    //     * @throws NotFoundHttpException
    //     */
    //    public static function getQualities($id)
    //    {
    //        //validate
    //        if (!is_numeric($id)) {
    //            throw new InvalidValueException(Message::MSG_NUMBER_ONLY, ['id']);
    //        }
    //        /** @var $content */
    //        $content = Content::findOne(['id' => $id, 'status' => Content::STATUS_ACTIVE]);
    //        if (!$content) {
    //            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
    //        }
    //        /** get all available content profiles of a content */
    //        $profile = ContentProfile::findAll(['content_id' => $id, 'status' => ContentProfile::STATUS_ACTIVE]);
    //        if (!$profile) {
    //            $data = null;
    //        }
    //        if (count($profile) == 1) {
    //            $data = $profile['quality'];
    //        } else {
    //            $arr = '';
    //            foreach ($profile as $value) {
    //                $arr .= $value['quality'] . ',';
    //            }
    //            $data = $arr;
    //        }
    //        return $data;
    //    }
    //
    public function saveSiteContentProfile()
    {
        $siteId = Content::findOne($this->content_id)->default_site_id;

        ContentProfileSiteAsm::deleteAll(['content_profile_id' => $this->id, 'site_id' => $siteId]);
        ContentSiteAsm::deleteAll(['content_id' => $this->content_id, 'site_id' => $siteId]);

        $contentProfileSite = new ContentProfileSiteAsm();
        $contentSite        = new ContentSiteAsm();

        $contentProfileSite->content_profile_id = $this->id;
        $contentProfileSite->site_id            = $siteId;
        $contentProfileSite->url                = $this->url;
        $contentProfileSite->status             = self::STATUS_ACTIVE;

        $contentSite->content_id = $this->content_id;
        $contentSite->site_id    = $siteId;
        $contentSite->subtitle   = $this->subtitle;
        $contentSite->status     = self::STATUS_ACTIVE;

        return $contentProfileSite->insert() && $contentSite->insert();
    }

    public function updateUrl()
    {
        $cps = ContentProfileSiteAsm::find()
            ->innerJoin('content_profile', 'content_profile_site_asm.content_profile_id = content_profile.id')
            ->innerJoin('content', '(content_profile.content_id = content.id AND content_profile_site_asm.site_id = content.default_site_id)')
            ->where(['content_profile_id' => $this->id])
            ->one();

        $cps->url = $this->url;

        return $cps->update();
    }

}
