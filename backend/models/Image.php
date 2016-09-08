<?php
/**
 * Created by PhpStorm.
 * User: qhuy
 * Date: 18/12/2014
 * Time: 21:59
 */

namespace backend\models;


use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\helpers\MyCurl;
use common\models\Content;
use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\image\ImageDriver;
use yii\validators\UrlValidator;
use yii\web\UploadedFile;

class Image extends Model
{


    public static $imageConfig = null;
    public $name;
    public $type;
    public $size;


    /**
     * @var $image UploadedFile
     */
    public $image;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'size'], 'integer'],
            [['name'], 'string', 'max' => 500],
        ];
    }


    /**
     * Return full path url
     */
    public function getImageUrl()
    {
        return Yii::getAlias('@web').'/'.Yii::getAlias('@content_images') . '/' . $this->name;
    }

    public function getImageType()
    {
        $listImageType = Content::getListImageType();
        if (isset($listImageType[$this->type])) {
            return $listImageType[$this->type];
        }
        return '';
    }

    public function getFilePath()
    {
        $validator = new UrlValidator();
        if ($validator->validate($this->url)) {
            return null;
        } else {
            if (preg_match('/(@[a-z]*)/', $this->url)) {
                return Yii::getAlias(str_replace('@web', '@webroot', $this->url));
            } else {
                $configImage = self::getImageConfig();
                $baseUrl = isset($configImage['base_url']) ? $configImage['base_url'] : Yii::getAlias('@webrootF');
                return $baseUrl . $this->url;
            }
        }
    }

    public function getNameImageFullSave()
    {
        return time() . '_' . CVietnameseTools::makeValidFileName($this->name);
    }

    public static function getImageConfig()
    {
        if (Image::$imageConfig == null) {
            Image::$imageConfig = Yii::$app->params['content-image'];
        }
        return Image::$imageConfig;
    }


}