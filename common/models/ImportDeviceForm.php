<?php
/**
 * Created by PhpStorm.
 * User: bibon
 * Date: 5/13/2016
 * Time: 10:26 AM
 */

namespace common\models;


use Yii;
use yii\base\Model;

class ImportDeviceForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $templateFile = '/sp/web/ImportDevicesTemplate.xlsx';
    public $uploadedFile;
    public $errorFile;

    public function rules()
    {
        return [
            [['templateFile'], 'string'],
            [['uploadedFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx', 'maxFiles' => 1],
            [['errorFile'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uploadedFile' => Yii::t('app', 'File excel'),
            'errorFile' => Yii::t('app', 'Errors File'),
        ];
    }
}