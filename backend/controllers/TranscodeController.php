<?php
namespace backend\controllers;

use common\components\ActionPrivateFilter;
use common\models\ContentProfile;
use common\models\FileTranscoded;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class TranscodeController extends Controller
{
    const ERROR_PARAM_INVALID       = '101';
    const ERROR_VERSION_EXIST       = '102';
    const ERROR_PARAM_NOT_NULL      = '301';
    const ERROR_PARAM_UNKNOWN_ERROR = '999';

    // public function behaviors()
    // {
    //     return [
    //         'auth' => [
    //             'class'                 => ActionPrivateFilter::className(),
    //             'enable_authentication' => false,
    //         ],
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * "http://$tvod_cms/?id=$id&basedir=$insert_dir&title=$insert_name&type=$insert_type&content_id=$cdn_id&picture=$picture&duration=$insert_duration&resolution=$insert_resolution"
     * @return string
     */
    public function actionAddFileTranscoded($id, $type, $content_id, $picture, $title = '', $basedir = '', $duration = 0, $resolution = '')
    {
        $id         = trim($id);
        $file_exist = FileTranscoded::findOne(['title' => $id]);

        if ($file_exist) {
            return $this->responseTranscode(false, 'Version video exist on system', self::ERROR_VERSION_EXIST);
        }

        $file_transcoded             = new FileTranscoded();
        $file_transcoded->basedir    = $basedir;
        $file_transcoded->type       = $type;
        $file_transcoded->cdn_id     = $content_id;
        $file_transcoded->duration   = $duration;
        $file_transcoded->picture    = $picture;
        $file_transcoded->title      = $title;
        $file_transcoded->resolution = $resolution;

        if ($file_transcoded->save()) {
            return $this->responseTranscode(true);
        } else {
            Yii::error($file_transcoded->getErrors());
            return $this->responseTranscode(false, $file_transcoded->getErrors(), self::ERROR_PARAM_UNKNOWN_ERROR);
        }
    }

    private function responseTranscode($result, $message = '', $type = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($result) {
            return [
                'success' => $result,
            ];
        }
        return [
            'success' => $result,
            'reason'  => $message,
            'type'    => $type,
        ];
    }

    public function actionSearch($title = '', $content_id = null, $site_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $title = trim($title);

        $createdProfile = ContentProfile::find()
        ->innerJoin('content_profile_site_asm', 'content_profile_site_asm.content_profile_id = content_profile.id')
        ->select(['url'])
        ->where(['site_id' => $site_id])
        ->where(['content_id' => $content_id]);

        $searchResults = FileTranscoded::find()
            ->where(['LIKE', 'title', $title])
            ->andWhere(['NOT IN', 'cdn_id', $createdProfile])
            ->all();

        return $searchResults;
    }
}
