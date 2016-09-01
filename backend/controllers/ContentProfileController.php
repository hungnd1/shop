<?php

namespace backend\controllers;

use api\models\Content;
use common\components\ActionLogTracking;
use common\components\FileUploadLarge;
use common\components\FileValidator;
use common\components\FragmentFileSystem;
use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\models\ContentProfile;
use common\models\ContentProfileSearch;
use common\models\ContentProfileSiteAsm;
use common\models\UserActivity;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * ContentProfileController implements the CRUD actions for ContentProfile model.
 */
class ContentProfileController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class'              => ActionLogTracking::className(),
                'user'               => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_PRICING,
            ],
        ]);
    }

    /**
     * Lists all ContentProfile models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new ContentProfileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ContentProfile model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ContentProfile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model                      = new ContentProfile();
        $post                       = Yii::$app->request->post();
        // if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
        //     return ActiveForm::validate($model);
        // }

        if ($model->load($post)) {
            $model->subtitle = UploadedFile::getInstance($model, 'subtitle');

            $model->saveSubFile();

            if ($model->save()) {
                $model->saveSiteContentProfile();
                $model->refresh();

                return [
                    'success' => true,
                    'message' => 'Tạo profile cho content ' . $model->content->display_name . ' thành công!',
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => $model->getErrors(),
            ];
        }
    }

    /**
     * Updates an existing ContentProfile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model                      = $this->findModel($id);
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post())) {
            $model->subtitle = UploadedFile::getInstance($model, 'subtitle');
            $model->saveSubFile();

            if ($model->save()) {
                $model->refresh();
                return ['success' => 'true', 'message' => 'Cập nhật thành công'];
            } else {
                Yii::trace($model->getErrors());

                return ['success' => 'false', 'message' => Yii::t('app', 'Cập nhật thất bại')];
            }
        }

        return ['success' => 'false', 'message' => Yii::t('app', 'Cập nhật thất bại')];
    }

    /**
     * Deletes an existing ContentProfile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model                      = $this->findModel($id);

        if ($model) {
            ContentProfileSiteAsm::deleteAll(['content_profile_id' => $id]);
            if ($model->delete()) {
                return [
                    'success' => true,
                    'message' => 'Xóa profile thành công!',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không xóa được profile này',
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'profile không tồn tại',
            ];
        }
    }

    /**
     * Finds the ContentProfile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return ContentProfile the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContentProfile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionEditable($profile_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post                       = Yii::$app->request->post();
        if ($post['editableKey']) {
            // read or convert your posted information
            $model = $this->findModel($profile_id);
            $index = $post['editableIndex'];
            if ($model) {
                $model->load($post['ContentProfile'][$index], '');
                if ($post['hasEditable'] == 0) {
                    return CUtils::errorForEditable(ActiveForm::validate($model), $index);
                } else {
                    if (!empty($model->url)) {
                        $model->updateUrl();
                    }

                    if ($model->update()) {
                        return ['output' => '', 'message' => ""];
                    } else {
                        return ['output' => '', 'message' => 'System error'];
                    }
                }
            } else {
                return ['output' => '', 'message' => ""];
            }
        } // else if nothing to do always return an empty JSON encoded output
        else {
            return ['output' => '', 'message' => 'ko hop le'];
        }
    }

    /**
     * load data for modal.
     */
    public function actionViewModalData()
    {
        $id    = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model) {
            $data = $this->renderAjax('view', [
                'model' => $model,
            ]);
            $success                    = true;
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ['success' => $success, 'data' => $data];
        } else {
            $success = false;
            $message = 'Profile không tồn tại';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success, 'data' => $message];
    }

    /**
     * load data for modal.
     */
    public function actionUpdateModalData()
    {
        $id    = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model) {
            $data = $this->renderAjax('update', [
                'model' => $model,
            ]);
            $success                    = true;
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ['success' => $success, 'data' => $data];
        } else {
            $success = false;
            $message = 'Profile không tồn tại';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success, 'data' => $message];
    }

    public function actionUploadVideo($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $content                    = $this->findContentModel($id);
        /**
         * Check xem model hien tai da co file raw chua
         * @var $file_raw ContentProfile
         * Ko check nua: Do co the update file raw cho phim hoac cho trailer
         */
//        $file_raw = ContentProfile::find()->andWhere(['content_id' => $content->id, 'type' => ContentProfile::TYPE_RAW])->one();

        $model = new ContentProfile();
        // Simple validation (max file size 10GB and only two allowed mime types)
        $validator = new FileValidator(1024 * 1024 * 1024 * 10, [
//                'video/mp4',
            //                'video/avi',
            //                'video/mpeg',
            //                'video/3gpp',
            //                'video/ogg',
            //                'video/quicktime',
            //                'video/webm',
            //                'video/x-matroska',
            //                'video/x-ms-wmv',
            //                'video/x-flv',
            //                'application/octet-stream'
        ]);

        // Simple path resolver, where uploads will be put
        $pathresolver = new \FileUpload\PathResolver\Simple(ContentProfile::getFileDir());

        // The machine's filesystem
        $filesystem = new FragmentFileSystem();

        // FileUploader itself
        $file = UploadedFile::getInstance($model, 'url');

        $fileupload = new FileUploadLarge([
            'name'     => $file->name,
            'type'     => $file->type,
            'tmp_name' => $file->tempName,
            'error'    => $file->error,
            'size'     => $file->size,
        ], $_SERVER);
        // Adding it all together. Note that you can use multiple validators or none at all
        $fileupload->setPathResolver($pathresolver);
        $fileupload->setFileSystem($filesystem);
        $fileupload->addValidator($validator);
        list($files, $headers) = $fileupload->processAll();
        $results               = [];
        foreach ($files as $profile_raw) {
            $profile = ContentProfile::findOne(['content_id' => $content->id, 'name' => $profile_raw->name]);
            if ($profile == null) {
                $profile             = new ContentProfile();
                $profile->name       = $profile_raw->name;
                $profile->type       = ContentProfile::TYPE_RAW;
                $profile->content_id = $content->id;
                $profile->status     = ContentProfile::STATUS_UPLOADING;
            }
            $profile->progress = $fileupload->getProcess($profile_raw->name);

            if (isset($profile_raw->path)) {
                $profile->status   = ContentProfile::STATUS_RAW;
                $profile->progress = 100;
            }
            if (!$profile->save()) {
                Yii::error($profile->getErrors());
            }
            $profile_raw->profile_id = $profile->id;
            $results[]               = ArrayHelper::toArray($profile_raw);
        }
        return $this->fileUpdateResponse($results, true);
    }

    public function actionUploadInfo($id, $file)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $file_info                  = [
            'file' => [
                'name' => $file,
                'size' => 0,
            ],
        ];
        /**
         * @var $content Content
         */
        $content = $this->findContentModel($id);

        // Simple path resolver, where uploads will be put
        $pathresolver = new \FileUpload\PathResolver\Simple(ContentProfile::getFileDir());
        $file_path    = $pathresolver->getUploadPath(CVietnameseTools::makeValidFileName($file));
        // The machine's filesystem
        $filesystem = new \FileUpload\FileSystem\Simple();
        if ($filesystem->isFile($file_path)) {
            $file_info['file']['size'] = $filesystem->getFilesize($file_path);
        }

        return $file_info;
    }

    protected function findContentModel($id)
    {
        if (($model = Content::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function fileUpdateResponse($fileInfo, $list = false)
    {
        if ($list) {
            $data = [
                'files' => $fileInfo,
            ];
        } else {
            $data = [
                'files' => [$fileInfo],
            ];
        }
        if (is_array($fileInfo)) {
            foreach ($fileInfo as $info) {
                if (!empty($info['error'])) {
                    Yii::$app->response->setStatusCode(403, $info['error']);
                }
            }
        } else {
            if (!empty($fileInfo['error'])) {
                Yii::$app->response->setStatusCode(403, $fileInfo['error']);
            }
        }
        return $data;
    }
}
