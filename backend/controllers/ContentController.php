<?php

namespace backend\controllers;

use api\helpers\Message;
use common\components\ActionLogTracking;
use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\models\ActorDirector;
use common\models\ApiVersion;
use common\models\ApiVersionSearch;
use common\models\Category;
use common\models\Content;
use common\models\ContentCategoryAsm;
use common\models\ContentFeedback;
use common\models\ContentFeedbackSearch;
use common\models\ContentLog;
use common\models\ContentProfile;
use common\models\ContentSearch;
use common\models\ContentSiteAsm;
use common\models\LiveProgram;
use common\models\StreamingServer;
use common\models\User;
use common\models\UserActivity;
use kartik\form\ActiveForm;
use sp\models\Image;
use Yii;
use yii\console\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ContentController implements the CRUD actions for Content model.
 */
class ContentController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class' => ActionLogTracking::className(),
                'user' => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_PRICING,
            ],
        ]);
    }

    /**
     * Lists all Content models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['hasEditable'])) {
            // read your posted model attributes
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                // read or convert your posted information
                $cat = Content::findOne($post['editableKey']);
                $index = $post['editableIndex'];
                if ($cat) {
                    $cat->load($post['Content'][$index], '');
                    if ($cat->update()) {
                        // tao log
                        $description = 'UPDATE STATUS CONTENT';
                        $ip_address = CUtils::clientIP();
                        $cat->createContentLog(ContentLog::TYPE_EDIT, Yii::$app->user->id, $ip_address, ContentLog::STATUS_SUCCESS, $description, '', $cat->display_name);

                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
                        // tao log
                        $description = 'UPDATE STATUS CONTENT';
                        $ip_address = CUtils::clientIP();
                        $cat->createContentLog(ContentLog::TYPE_EDIT, Yii::$app->user->id, $ip_address, ContentLog::STATUS_FAIL, $description, '', $cat->display_name);

                        echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Dữ liệu không hợp lệ']);
                    }
                } else {
                    echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Danh mục không tồn tại']);
                }
            } // else if nothing to do always return an empty JSON encoded output
            else {
                echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
            }

            return;
        }
        $searchModel = new ContentSearch();
        $params = Yii::$app->request->queryParams;
        Yii::trace($params);
        $params['ContentSearch']['created_at'] = isset($params['ContentSearch']['created_at']) && $params['ContentSearch']['created_at'] !== '' ? strtotime($params['ContentSearch']['created_at']) : '';
        $dataProvider = $searchModel->filter($params);
        // $dataProvider->query->andFilterWhere(['in', 'content.status', [Content::STATUS_WAIT_TRANSCODE, Content::STATUS_ACTIVE, Content::STATUS_DRAFT, Content::STATUS_INVISIBLE]]);
        $searchModel->keyword = isset($params['ContentSearch']['keyword']) ? $params['ContentSearch']['keyword'] : '';
        /* @var  $userAccessed User */
        $selectedCats = isset($params['ContentSearch']['categoryIds']) ? explode(',', $params['ContentSearch']['categoryIds']) : [];
        // var_dump($dataProvider);die;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'selectedCats' => $selectedCats,
        ]);
    }

    /**
     * Displays a single Content model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id, $active = 1)
    {
        ini_set('memory_limit', '-1');
        if (isset($_POST['hasEditable'])) {
            // read your posted model attributes
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                // read or convert your posted information
                $feedback = ContentFeedback::findOne($post['editableKey']);
                $index = $post['editableIndex'];
                if ($feedback) {
                    $feedback->load($post['ContentFeedback'][$index], '');
                    if ($feedback->update()) {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Dữ liệu không hợp lệ']);
                    }
                } else {
                    echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Feedback không tồn tại']);
                }
            } // else if nothing to do always return an empty JSON encoded output
            else {
                echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
            }

            return;
        }
        $model = $this->findModel($id);
        //Images
        $imageModel = new Image();
        $images = $model->getImages();
        $imageProvider = new ArrayDataProvider([
            'key' => 'name',
            'allModels' => $images,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        //profile
        $profileModel = new ContentProfile();
        $profileModel->content_id = $model->id;
        $profile = $model->contentProfiles;
        $profileProvider = new ArrayDataProvider([
            'key' => 'id',
            'allModels' => $profile,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        // feed back
        $searchFeedbackModel = new ContentFeedbackSearch();
        $params = Yii::$app->request->queryParams;
        $feedbackProvider = $searchFeedbackModel->filter($params, $id);

        // episode
        $searchEpisodeModel = new ContentSearch();
        $episodeModel = new Content();
        $episodeModel->parent_id = $model->id;
        $episodeModel->type = $model->type;
        $params = Yii::$app->request->queryParams;
        $episodeProvider = $searchEpisodeModel->filterEpisode($params, $model->type, $model->created_user_id, $id);

        $livePrograms = LiveProgram::find()
            ->innerJoin('content', 'live_program.content_id = content.id')
            ->select('live_program.*, content.images')
            ->andWhere(['channel_id' => $id])
            ->orderBy('started_at DESC')
            ->all();

        $liveProvider = new ArrayDataProvider([
            'key' => 'id',
            'allModels' => $livePrograms,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $model->getContentAttr();
        $liveModel = new LiveProgram();
        $liveModel->channel_id = $id;

        $contentSiteProvider = new ArrayDataProvider([
            'key' => 'id',
            'allModels' => $model->contentSiteProvider,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        // var_dump($model->getContentRelatedAsms()->all());die;

        return $this->render('view', [
            'model' => $model,
            'id' => $id,
            'imageModel' => $imageModel,
            'imageProvider' => $imageProvider,
            'liveProvider' => $liveProvider,
            'liveModel' => $liveModel,
            'profileModel' => $profileModel,
            'profileProvider' => $profileProvider,
            'feedbackProvider' => $feedbackProvider,
            'feedbackSearch' => $searchFeedbackModel,
            'episodeProvider' => $episodeProvider,
            'contentSiteProvider' => $contentSiteProvider,
            'episodeSearch' => $searchEpisodeModel,
            'episodeModel' => $episodeModel,
            'active' => $active,
        ]);
    }

    /**
     * Creates a new Content model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($type, $parent = null)
    {
        $model = new Content();
        $model->loadDefaultValues();
        $model->type = $type;
        $model->code = time() . rand(0, 999);

        if ($type != Content::TYPE_LIVE_CONTENT) {
            $model->setScenario('adminModify');
        } else {
            $model->setScenario('adminModifyLiveContent');
            $model->is_free = 1;
        }

        // $model->site_id = Yii::$app->user->id;
        // $model->content_provider_id = $this->sp_user->content_provider_id;
        $model->created_user_id = Yii::$app->user->id;


//        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//
//            return $model;
//        }

        if ($model->load(Yii::$app->request->post())) {


            if (isset(Yii::$app->request->post()['Content']['assignment_sites'])) {
                $model->assignment_sites = Yii::$app->request->post()['Content']['assignment_sites'];
            }

            if (isset(Yii::$app->request->post()['Content']['content_related_asm'])) {
                $model->content_related_asm = Yii::$app->request->post()['Content']['content_related_asm'];
            }

            if (isset(Yii::$app->request->post()['Content']['contentAttr'])) {
                $model->contentAttr = Yii::$app->request->post()['Content']['contentAttr'];
            }

            if (isset(Yii::$app->request->post()['Content']['list_cat_id'])) {
                $model->list_cat_id = Yii::$app->request->post()['Content']['list_cat_id'];
            }

            // $model->status     = Content::STATUS_ACTIVE;
            $model->ascii_name = CVietnameseTools::makeSearchableStr($model->display_name);
            // $old_images        = Content::convertJsonToArray($model->images);
            // $model->images     = Json::encode($images);
            $tags = $model->tags;
            if (is_array($tags)) {
                $model->tags = implode(';', $tags);
            }
            /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - begin */
            $model->created_at = time();
            $model->updated_at = time();
            /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - end */
            $model->episode_order = $model->getEpisodeOrder();
            if ($model->save()) {
                if ($type == Category::TYPE_LIVE_CONTENT) {
                    $id = $model->id;
                    $this->saveLiveProgram(Yii::$app->request->post(), $id, false, $model->status);
                }

                $model->createCategoryAsm();
                $model->saveRelatedContent();
                $model->saveAttrValue();
                $model->saveActorDirectors();
                $model->setAssignment_sites();

                // tao log
                $description = 'CREATE CONTENT';
                $ip_address = CUtils::clientIP();
                $model->createContentLog(ContentLog::TYPE_CREATE, Yii::$app->user->id, $ip_address, ContentLog::STATUS_SUCCESS, $description, '', $model->display_name);

                \Yii::$app->getSession()->setFlash('success', 'Lưu Content thành công');
                if ($type == Content::TYPE_LIVE_CONTENT && $parent > 0) {
                    return $this->redirect(['view', 'id' => $parent]);
                } else {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                Yii::info($model->getErrors());

                \Yii::$app->getSession()->setFlash('error', 'Lưu Content thất bại');
            }
        }


        $selectedCats = explode(',', $model->list_cat_id);
        // get screenshoot
        $logoInit = [];
        $thumbnail_epgInit = [];
        $thumbnailInit = [];
        $screenshootInit = [];
        $slideInit = [];

        $logoPreview = [];
        $thumbnail_epgPreview = [];
        $thumbnailPreview = [];
        $screenshootPreview = [];
        $slidePreview = [];
        $tags = explode(';', $model->tags);
        $thumb_epg = [];
        $thumb = [];
        $screenshoot = [];


//        var_dump($model->image_tmp);exit;
        $images = Content::convertJsonToArray($model->images);

        foreach ($images as $key => $row) {
            $key = $key + 1;
            $urlDelete = Yii::$app->urlManager->createAbsoluteUrl(['/content/delete-image', 'name' => $row['name'], 'type' => $row['type'], 'content_id' => $model->id]);
            $name = $row['name'];
            $type = $row['type'];
            $value = ['caption' => $name, 'width' => '120px', 'url' => $urlDelete, 'key' => $key];
            $host_file = ((strpos($row['name'], 'http') !== false) || (strpos($row['name'], 'https') !== false)) ? $row['name'] : Yii::getAlias('@web') . DIRECTORY_SEPARATOR . Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $row['name'];
            $preview = Html::img($host_file, ['class' => 'file-preview-image']);
            switch ($row['type']) {
                case Content::IMAGE_TYPE_LOGO:
                    $logoPreview[] = $preview;
                    $logoInit[] = $value;
                    break;
                case Content::IMAGE_TYPE_THUMBNAIL_EPG:
                    $thumbnail_epgInit[] = $value;
                    $thumbnail_epgPreview[] = $preview;
                    $thumb_epg[] = $name;
                    break;
                case Content::IMAGE_TYPE_SCREENSHOOT:
                    $screenshootPreview[] = $preview;
                    $screenshootInit[] = $value;
                    $screenshoot[] = $name;
                    break;
                case Content::IMAGE_TYPE_THUMBNAIL:
                    $thumbnailPreview[] = $preview;
                    $thumbnailInit[] = $value;
                    $thumb[] = $name;
                    break;
            }

            //end screenshoot
        }
        $model->thumbnail_epg = $thumb_epg;
        $model->thumbnail = $thumb;
        $model->screenshoot = $screenshoot;

        return $this->render('create', [
            'model' => $model,
            'logoInit' => $logoInit,
            'logoPreview' => $logoPreview,
            'thumbnail_epgPreview'=>$thumbnail_epgPreview,
            'thumbnail_epgInit'=>$thumbnail_epgInit,
            'thumbnailInit' => $thumbnailInit,
            'thumbnailPreview' => $thumbnailPreview,
            'screenshootInit' => $screenshootInit,
            'screenshootPreview' => $screenshootPreview,
            'slideInit' => $slideInit,
            'slidePreview' => $slidePreview,
            'selectedCats' => $selectedCats,
            'type' => $type,
            'tags' => $tags,
            'site_id' => Yii::$app->user->id,
            'parent' => $parent,
        ]);
    }

    /**
     * Updates an existing Content model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->getRelatedContents();
        $model->getContentAttr();
        $model->getAssignment_sites();
        if ($model->type != Content::TYPE_LIVE_CONTENT) {
            $model->setScenario('adminModify');
        } else {
            $model->setScenario('adminModifyLiveContent');
        }

        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $liveProgram = LiveProgram::findOne(['content_id' => $id]);

        if ($model->load(Yii::$app->request->post())) {
            $thumbnails_epg = UploadedFile::getInstance($model, 'thumbnail_epg');
            $thumbnails = UploadedFile::getInstances($model, 'thumbnail');
            $screenshoots = UploadedFile::getInstances($model, 'screenshoot');
            $slides = UploadedFile::getInstances($model, 'slide');
            $images = [];

            $model->ascii_name = CVietnameseTools::makeSearchableStr($model->display_name);
            if (isset(Yii::$app->request->post()['Content']['assignment_sites'])) {
                $model->assignment_sites = Yii::$app->request->post()['Content']['assignment_sites'];
            }
            if (isset(Yii::$app->request->post()['Content']['content_related_asm'])) {
                $model->content_related_asm = Yii::$app->request->post()['Content']['content_related_asm'];
            }
            if (isset(Yii::$app->request->post()['Content']['contentAttr'])) {
                $model->contentAttr = Yii::$app->request->post()['Content']['contentAttr'];
            }
            if (isset(Yii::$app->request->post()['Content']['list_cat_id'])) {
                $model->list_cat_id = Yii::$app->request->post()['Content']['list_cat_id'];
            }

            $recorded = ContentProfile::findOne(['content_id' => $id]) ? true : false;
            // var_dump($model->images);die;
            /** cuongvm 20160725 - phải insert updated_at bằng tay, không dùng behaviors - begin */
            $model->updated_at = time();
            /** cuongvm 20160725 - phải insert updated_at bằng tay, không dùng behaviors - end */
            if ($model->save()) {
                if ($liveProgram) {
                    $this->saveLiveProgram(Yii::$app->request->post(), $id, $recorded, $model->status);
                }
                $model->createCategoryAsm();
                $model->saveRelatedContent();
                $model->saveAttrValue();
                $model->saveActorDirectors();
                $model->setAssignment_sites();

                // tao log
                $description = 'EDIT CONTENT ' . $model->display_name;
                $ip_address = CUtils::clientIP();
                $model->createContentLog(ContentLog::TYPE_EDIT, Yii::$app->user->id, $ip_address, ContentLog::STATUS_SUCCESS, $description, '', $model->display_name);

                \Yii::$app->getSession()->setFlash('success', 'Cập nhật Content thành công');

                return $this->redirect(['view', 'id' => $model->id]);

            } else {
                \Yii::$app->getSession()->setFlash('error', 'Cập nhật Content thất bại');

            }
        }
        // get screenshoot
        $images = Content::convertJsonToArray($model->images);
        // var_dump($model->images);die;
        $logoInit = [];
        $thumbnail_epgInit = [];
        $thumbnailInit = [];
        $screenshootInit = [];

        $logoPreview = [];
        $thumbnail_epgPreview = [];
        $thumbnailPreview = [];
        $screenshootPreview = [];
        $thumb_epg = [];
        $thumb = [];
        $screenshoot = [];

        foreach ($images as $key => $row) {
            $key = $key + 1;
            $urlDelete = Yii::$app->urlManager->createAbsoluteUrl(['/content/delete-image', 'name' => $row['name'], 'type' => $row['type'], 'content_id' => $model->id]);
            $name = $row['name'];
            $type = $row['type'];
            $value = ['caption' => $name, 'width' => '120px', 'url' => $urlDelete, 'key' => $key];
            $host_file = ((strpos($row['name'], 'http') !== false) || (strpos($row['name'], 'https') !== false)) ? $row['name'] : Yii::getAlias('@web') . DIRECTORY_SEPARATOR . Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $row['name'];
            $preview = Html::img($host_file, ['class' => 'file-preview-image']);
            switch ($row['type']) {
                case Content::IMAGE_TYPE_LOGO:
                    $logoPreview[] = $preview;
                    $logoInit[] = $value;
                    break;
                case Content::IMAGE_TYPE_THUMBNAIL_EPG:
                    $thumbnail_epgInit[] = $value;
                    $thumbnail_epgPreview[] = $preview;
                    $thumb_epg[] = $name;
                    break;
                case Content::IMAGE_TYPE_SCREENSHOOT:
                    $screenshootPreview[] = $preview;
                    $screenshootInit[] = $value;
                    $screenshoot[] = $name;
                    break;
                case Content::IMAGE_TYPE_THUMBNAIL:
                    $thumbnailPreview[] = $preview;
                    $thumbnailInit[] = $value;
                    $thumb[] = $name;
                    break;
            }

            //end screenshoot
        }
        $model->thumbnail_epg = $thumb_epg;
        $model->thumbnail = $thumb;
        $model->screenshoot = $screenshoot;

        $selectedCats = $model->getListCatIds();
        $model->list_cat_id = implode(',', $selectedCats);

        if ($liveProgram) {
            $model->live_channel = $liveProgram->channel_id;
            $model->started_at = date('d-m-Y H:i:s', $liveProgram->started_at);
            $model->ended_at = date('d-m-Y H:i:s', $liveProgram->ended_at);
        }
        Yii::trace($selectedCats);
        $model->getContentActors();
        $model->getContentDirectors();
//        var_dump($screenshootInit);
        //        var_dump($screenshootPreview);exit;
        return $this->render('update', [
            'model' => $model,
            'logoInit' => $logoInit,
            'logoPreview' => $logoPreview,
            'thumbnail_epgPreview'=>$thumbnail_epgPreview,
            'thumbnail_epgInit'=>$thumbnail_epgInit,
            'thumbnailInit' => $thumbnailInit,
            'thumbnailPreview' => $thumbnailPreview,
            'screenshootInit' => $screenshootInit,
            'screenshootPreview' => $screenshootPreview,
            'type' => $model->type,
            'selectedCats' => $selectedCats,
            'site_id' => Yii::$app->user->id,
        ]);
    }

    private function saveLiveProgram($request, $content_id, $recorded, $status)
    {
        $channel_id = $request['Content']['live_channel'];
        $name = $request['Content']['display_name'];
        $description = $request['Content']['description'];
        $started_at = strtotime($request['started_at-content-started_at']);
        $ended_at = strtotime($request['ended_at-content-ended_at']);
        $status = LiveProgram::initStatus($recorded, $status);

        $checkLiveProgram = LiveProgram::findOne(['content_id' => $content_id]);
        if (!$checkLiveProgram) {
            $newLive = new LiveProgram();
            $newLive->channel_id = $channel_id;
            $newLive->content_id = $content_id;
            $newLive->name = $name;
            $newLive->description = $description;
            $newLive->started_at = $started_at;
            $newLive->ended_at = $ended_at;
            $newLive->status = $status;

            return $newLive->insert();
        } else {
            $checkLiveProgram->channel_id = intval($channel_id);
            $checkLiveProgram->name = $name;
            $checkLiveProgram->description = $description;
            $checkLiveProgram->started_at = $started_at;
            $checkLiveProgram->ended_at = $ended_at;
            $checkLiveProgram->status = $status;

            return $checkLiveProgram->update();
        }
    }

    /**
     * Deletes an existing Content model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Content::STATUS_DELETE;
        /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - begin */
        $model->updated_at = time();
        /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - end */
        $model->save();

        return $this->redirect(['index', 'type' => $model->type]);
    }

    /**
     * Finds the Content model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Content the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Content::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUploadFile($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Content();
        $type = Yii::$app->request->post('type');
        $allowExt = ['png', 'jpg', 'jpeg', 'gif'];
        if ($type == Content::IMAGE_TYPE_THUMBNAIL_EPG) {
            $old_value = Yii::$app->request->post('thumbnail_epg_old');
            $attribute = 'thumbnail_epg';
        } else if ($type == Content::IMAGE_TYPE_THUMBNAIL) {
            $old_value = Yii::$app->request->post('thumbnail_old');
            $attribute = 'thumbnail';
        } elseif ($type == Content::IMAGE_TYPE_SCREENSHOOT) {
            $old_value = Yii::$app->request->post('screenshot_old');
            $attribute = 'screenshoot';
        } else {
            $old_value = Yii::$app->request->post('logo_old');
            $attribute = 'logo';
        }
        $model->load(Yii::$app->request->post());

        $files = null;

        if (empty($_FILES['Content'])) {
            return []; // or process or throw an exception
        }

        $files = $_FILES['Content'];
        Yii::trace($type . '  ' . $attribute);
        $file_type = '';
        list($width, $height, $file_type, $attr) = getimagesize($files['tmp_name']["$attribute"][0]);
        Yii::info($width . 'xxx' . $height);

        Yii::info($files);
        $new_file = [];
        $size = $files['size']["$attribute"][0];
        $ext = explode('.', basename($files['name']["$attribute"][0]));
        $checkExt = $ext[max(array_keys($ext))];
        $file_name = uniqid() . time() . '.' . array_pop($ext);
        $uploadPath = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . Yii::getAlias('@content_images');
        $target = $uploadPath . DIRECTORY_SEPARATOR . $file_name;
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777);
        }

        if (!in_array($checkExt, $allowExt)) {
            return ['success' => false, 'error' => "Ảnh không đúng định dạng"];
        }

        if ($size > Content::MAX_SIZE_UPLOAD) {
            return ['success' => false, 'error' => "Ảnh vượt quá dung lượng cho phép"];
        }

        if (move_uploaded_file($files['tmp_name']["$attribute"][0], $target)) {
            $success = true;
            $new_file['name'] = $file_name;
            $new_file['type'] = $type;
            $new_file['size'] = $size;
        } else {
            $success = false;
        }
        // neu tao file thanh cong. tra ve danh sach file moi
        if ($success) {
            if ($id === null) {
                $output = ['success' => $success, 'output' => json_encode($new_file)];

                return $output;
            }

            $oldImages = Content::findOne($id);
            // var_dump(json_decode($oldImages->images, true));die;

            if ($type == Content::IMAGE_TYPE_THUMBNAIL_EPG) {
                $imgs = Content::convertJsonToArray($oldImages->images, true) !== null ? Content::convertJsonToArray($oldImages->images, true) : [];
                $imgs = array_filter($imgs, function ($v) {
                    return $v['type'] != Content::IMAGE_TYPE_THUMBNAIL_EPG;
                });

                $oldImages->images = json_encode(array_merge($imgs, [$new_file]));
            } else if ($type == Content::IMAGE_TYPE_THUMBNAIL) {
                $imgs = Content::convertJsonToArray($oldImages->images, true) !== null ? Content::convertJsonToArray($oldImages->images, true) : [];
                $imgs = array_filter($imgs, function ($v) {
                    return $v['type'] != Content::IMAGE_TYPE_THUMBNAIL;
                });

                $oldImages->images = json_encode(array_merge($imgs, [$new_file]));
            } else {
                $oldImages->images = json_encode(array_merge(Content::convertJsonToArray($oldImages->images, true) !== null ? Content::convertJsonToArray($oldImages->images, true) : [], [$new_file]));
            }

            $success = $oldImages->update();

            $old_value = Content::convertJsonToArray($old_value);
            $old_value[] = $new_file;
        }
        $output = ['success' => $success, 'output' => $oldImages->images];

        return $output;
    }

    public function actionDeleteImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $content_id = Yii::$app->request->get('content_id');
        $name = Yii::$app->request->get('name');

        if (!$content_id || !$name) {
            return [
                'success' => false,
                'message' => 'Thiếu tham số!',
                'error' => 'Thiếu tham số!',
            ];
        }
        $content = $this->findModel($content_id);
        if (!$content) {
            return [
                'success' => false,
                'message' => 'Không thấy nội dung!',
                'error' => 'Không thấy nội dung!',
            ];
        } else {
            $index = -1;
            $images = Content::convertJsonToArray($content->images);
            $thumb = [];
            $thumb_epg = [];
            $screenshoot = [];

            Yii::trace($images);
            foreach ($images as $key => $row) {
                if ($row['name'] == $name) {
                    $index = $key;
                }
                if ($row['type'] == Content::IMAGE_TYPE_THUMBNAIL_EPG) {
                    $thumb_epg[] = $row['name'];
                }
                if ($row['type'] == Content::IMAGE_TYPE_THUMBNAIL) {
                    $thumb[] = $row['name'];
                }
                if ($row['type'] == Content::IMAGE_TYPE_SCREENSHOOT) {
                    $screenshoot[] = $row['name'];
                }
            }
            if ($index == -1) {
                return [
                    'success' => false,
                    'error' => 'Không thấy ảnh!',
                ];
            } else {
                array_splice($images, $index, 1);
                Yii::trace($images);
                $content->images = Json::encode($images);
                $content->thumbnail = $thumb;
                $content->thumbnail_epg = $thumb_epg;
                $content->screenshoot = $screenshoot;
                $selectedCats = $content->getListCatIds();
                $content->list_cat_id = implode(',', $selectedCats);
                $content->assignment_sites = ContentSiteAsm::getSiteList(['content_id' => $content_id], ['id', 'site_id']);

                if ($content->save()) {
                    return [
                        'success' => true,
                        'result' => $content->images,
                    ];
                } else {
                    return $content->getErrors();
                }
            }
        }
    }

    public function actionCreateEpisode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Content();
        $model->loadDefaultValues();
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
            return ActiveForm::validate($model);
        }
        /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - begin */
        $model->created_at = time();
        $model->updated_at = time();
        /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - end */
        if ($model->load($post) && $model->save()) {
            // tao log
            $description = 'CREATE CONTENT';
            $ip_address = CUtils::clientIP();
            $model->createContentLog(ContentLog::TYPE_CREATE, Yii::$app->user->id, $ip_address, ContentLog::STATUS_SUCCESS, $description);

            return [
                'success' => true,
                'message' => 'Tạo episode cho thành công!',
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Tạo episode cho content không thành công',
            ];
        }
    }

    public function actionCreateEpisodeModal()
    {
        $id = Yii::$app->request->get('parent_id');
        $model = $this->findModel($id);
        $episodeModel = new Content();
        $episodeModel->loadDefaultValues();
        $episodeModel->parent_id = $model->id;
        $episodeModel->type = $model->type;
        $episodeModel->created_user_id = $model->created_user_id;
        // $episodeModel->content_provider_id = $model->content_provider_id;

        if ($model) {
            $data = $this->renderAjax('_create_episode_form', [
                'episode' => $episodeModel,
            ]);
            $success = true;
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ['success' => $success, 'data' => $data];
        } else {
            $success = false;
            $message = 'Content không tồn tại';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['success' => $success, 'data' => $message];
    }

    public function actionApprove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $cp = Yii::$app->user->id;
        $sp_id = Yii::$app->user->id;
        $newStatus = Content::STATUS_ACTIVE;
        if (isset($post['ids'])) {
            $ids = $post['ids'];
            $contents = Content::findAll($ids);
            $count = 0;
            foreach ($contents as $content) {
                if ($content->spUpdateStatus($newStatus, $sp_id)) {
                    ++$count;
                }
            }

            return [
                'success' => true,
                'message' => 'Duyệt ' . $count . ' content thành công!',
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy content trên hệ thống',
            ];
        }
    }

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        // $oldStatus = $model->status;
        // if ($oldStatus == Content::STATUS_PENDING) {
        //     echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Bạn không có quyền cập nhật trạng thái chờ duyệt']);
        //     return;
        // }
        if (isset($_POST['hasEditable'])) {
            // read your posted model attributes
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                // read or convert your posted information
                $content = Content::findOne($post['editableKey']);
                $index = $post['editableIndex'];
                if ($content || $model->id != $content->id) {
                    $content->load($post['Content'][$index], '');
                    /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - begin */
                    $model->updated_at = time();
                    /** cuongvm 20160725 - phải insert created_at, updated_at bằng tay, không dùng behaviors - end */
                    if ($content->update()) {
                        // tao log
                        $description = 'UPDATE STATUS CONTENT';
                        $ip_address = CUtils::clientIP();
                        $content->createContentLog(ContentLog::TYPE_EDIT, Yii::$app->user->id, $ip_address, ContentLog::STATUS_SUCCESS, $description, '', $content->display_name);

                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
                        // tao log
                        $description = 'UPDATE STATUS CONTENT';
                        $ip_address = CUtils::clientIP();
                        $content->createContentLog(ContentLog::TYPE_EDIT, Yii::$app->user->id, $ip_address, ContentLog::STATUS_FAIL, $description, '', $content->display_name);

                        echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Dữ liệu không hợp lệ']);
                    }
                } else {
                    echo \yii\helpers\Json::encode(['output' => '', 'message' => 'Dữ liệu không tồn tại']);
                }
            } // else if nothing to do always return an empty JSON encoded output
            else {
                echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
            }

            return;
        }
    }

    public function actionUpdateStatusContent()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $cp = Yii::$app->user->id;

        if (isset($post['ids']) && isset($post['newStatus'])) {
            $ids = $post['ids'];
            $newStatus = $post['newStatus'];
            $contents = Content::findAll($ids);
            $count = 0;

            foreach ($contents as $content) {
                if ($content->spUpdateStatus($newStatus, $cp)) {
                    ++$count;
                }
            }

            $successMess = $newStatus == Content::STATUS_DELETE ? 'Xóa' : 'Cập nhật';

            return [
                'success' => true,
                'message' => $successMess . ' ' . $count . ' content thành công!',
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không thành công. Vui lòng thử',
            ];
        }
    }

    public function actionRelatedList($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'display_name' => '']];
        if (!is_null($q)) {
            $data = Content::find()->where(['LIKE', 'display_name', $q])->asArray()->all();
            $out['results'] = $data;
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'display_name' => Content::find()
                ->where(['IN', 'content_related_asm.id', $id])
                ->display_name];
        }

        return $out;
    }

    /**
     * @return string
     */
    public function actionReleaseStaticData()
    {
        $searchModel = new ApiVersionSearch();

        $params = Yii::$app->request->queryParams;
        $site_id = Yii::$app->request->get('site_id', 0);
        $searchModel->type = ApiVersion::TYPE_KARAOKE;
        $dataProvider = $searchModel->search($params);

        return $this->render('release_static_data', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'site_id' => $site_id,
        ]);
    }


    public function actionExportDataToFile(){
        $params = Yii::$app->request->post();
        if (!isset($params['site_id'])) {
            return $this->redirect(['release-static-data']);
        }
        $site_id = $params['site_id'];
        try{
//            $message=shell_exec("nohup ./export_karaoke.sh $site_id 2>&1 &");
//            $message=shell_exec("nohup /usr/bin/php ./export_karaoke.sh $site_id &");
            $message=shell_exec("/usr/bin/nohup  ./export_karaoke.sh $site_id > /dev/null 2>&1 &");
//            $message=shell_exec("export_karaoke.sh $site_id");
//            Yii::$app->getSession()->setFlash('success', Message::MSG_SUCCESS);
            Yii::$app->getSession()->setFlash('success', Message::MSG_EXPORT_DATA_TO_FILE_SUCCESS);
        }catch (Exception $ex){
//            $err = $ex->getMessage();
            Yii::$app->getSession()->setFlash('success', Message::MSG_FAIL);
        }

        return $this->redirect(['release-static-data', 'site_id' => $site_id]);
    }
    /**
     * @return Response
     */
//    public function actionExportDataToFile()
//    {
//        $params = Yii::$app->request->post();
//        if (!isset($params['site_id'])) {
//            return $this->redirect(['release-static-data']);
//        }
//        $site_id = $params['site_id'];
//        $lst = [];
//        $items = Content::find()
//            ->joinWith('contentSiteAsms')
//            ->andWhere(['content_site_asm.site_id' => $site_id, 'content_site_asm.status' => ContentSiteAsm::STATUS_ACTIVE])
//            ->andWhere(['content.type' => Content::TYPE_KARAOKE, 'content.status' => Content::STATUS_ACTIVE])
//            ->all();
//        /** Nếu không có dữ liệu thì return */
//        if (count($items) <= 0) {
//            Yii::$app->getSession()->setFlash('error', Message::MSG_NOT_FOUND_CONTENT);
//            return $this->redirect(['release-static-data', 'site_id' => $site_id]);
//        }
//        /** @var  $item Content */
//        foreach ($items as $item) {
//            $group_tmp = $item->getAttributes(['id', 'display_name', 'ascii_name', 'short_description'], ['created_user_id']);
//            $tempCat = "";
////            $categoryAsms = ContentCategoryAsm::find()->andWhere(['content_id'=>$item->id])->all();
//            $categoryAsms = $item->contentCategoryAsms;
//            if (count($categoryAsms) > 0) {
//                foreach ($categoryAsms as $asm) {
//                    /** @var $asm ContentCategoryAsm */
//                    $tempCat .= $asm->category->id . ',';
//                }
//            }
//
//            /** Cắt xâu */
//            if (strlen($tempCat) >= 2) {
//                $tempCat = substr($tempCat, 0, -1);
//            }
//            $group_tmp['categories'] = $tempCat;
//            $tempA = "";
//            $tempD = "";
//            $contentActorDirectorAsms = $item->contentActorDirectorAsms;
////            $contentActorDirectorAsms = ContentActorDirectorAsm::find()->andWhere(['content_id'=>$item->id])->all();
//
//            if ($contentActorDirectorAsms) {
//                foreach ($contentActorDirectorAsms as $asm) {
//                    if ($asm->actorDirector->type == ActorDirector::TYPE_ACTOR) {
//                        /** @var $asm ContentCategoryAsm */
//                        $tempA .= $asm->actorDirector->id . ',';
//                    }
//                    if ($asm->actorDirector->type == ActorDirector::TYPE_DIRECTOR) {
//                        /** @var $asm ContentCategoryAsm */
//                        $tempD .= $asm->actorDirector->id . ',';
//                    }
//                }
//            }
//            /** Cắt xâu */
//            if (strlen($tempA) >= 2) {
//                $tempA = substr($tempA, 0, -1);
//            }
//            /** Cắt xâu */
//            if (strlen($tempD) >= 2) {
//                $tempD = substr($tempD, 0, -1);
//            }
//            $group_tmp['actors'] = $tempA;
//            $group_tmp['directors'] = $tempD;
//
//            $strQuality = "";
////            $qualities  = $item->contentProfiles;
//            $qualities = ContentProfile::find()->andWhere(['content_id' => $item->id, 'type' => ContentProfile::TYPE_CDN])->all();
//            if ($qualities) {
//                foreach ($qualities as $quality) {
//                    $strQuality .= $quality->quality . ',';
//                }
//            }
//            /** Cắt xâu */
//            if (strlen($strQuality) >= 2) {
//                $strQuality = substr($strQuality, 0, -1);
//            }
//
//            $group_tmp['qualities'] = $strQuality;
//            $group_tmp['shortname'] = CUtils::parseTitleToKeyword($item->display_name);
//
//            array_push($lst, $group_tmp);
//        }
//
//        $res = [
//            'success' => true,
//            'message' => Message::MSG_SUCCESS,
//            'totalCount' => count($lst),
//            'time_update' => time(),
//            "date_expired" => "01/01/2018",
//        ];
//        $res['items'] = $lst;
//        $resJson = json_encode($res);
//        $path = 'staticdata/data' . $site_id . '.json';
//        $save2File = CUtils::writeFile($resJson, $path);
//        if ($save2File) {
//            $r = ApiVersion::createApiVersion("karaoke", "version karaoke", $site_id, ApiVersion::TYPE_KARAOKE);
//            if ($r['success']) {
//                Yii::$app->getSession()->setFlash('success', Message::MSG_SUCCESS);
//            } else {
//                Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
//            }
//
//        } else {
//            Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
//        }
//        return $this->redirect(['release-static-data', 'site_id' => $site_id]);
//
//    }

//    public function actionExportDataToFile()
    //    {
    //        $params            = Yii::$app->request->post();
    //        if(!isset($params['site_id'])){
    //            return $this->redirect(['release-static-data']);
    //        }
    //        $site_id = $params['site_id'];
    //        $items   = Content::find()
    //            ->joinWith('contentSiteAsms')->andWhere(['content_site_asm.site_id' => $site_id, 'content_site_asm.status' => ContentSiteAsm::STATUS_ACTIVE])
    //            ->andWhere(['content.type' => Content::TYPE_KARAOKE, 'content.status' => Content::STATUS_ACTIVE])
    //            ->all();
    ////        echo count($items);exit;
    //        $lst = [];
    //        /** @var  $item Content*/
    //        foreach ($items as $item) {
    //            $group_tmp = $item->getAttributes(['id', 'display_name', 'ascii_name', 'short_description'], ['created_user_id']);
    //            $tempCat   = "";
    //
    //            $categoryAsms = $item->contentCategoryAsms;
    //            if ($categoryAsms) {
    //                foreach ($categoryAsms as $asm) {
    //                    /** @var $asm ContentCategoryAsm */
    //                    $tempCat .= $asm->category->id . ',';
    //                }
    //            }
    ////            /** Cắt xâu */
    ////            if (strlen($tempCat) >= 2) {
    ////                $tempCat = substr($tempCat, 0, -1);
    ////            }
    //            $group_tmp['categories']  = $tempCat;
    //            $tempA                    = "";
    //            $tempD                    = "";
    //            $contentActorDirectorAsms = $item->contentActorDirectorAsms;
    //            if ($contentActorDirectorAsms) {
    //                foreach ($contentActorDirectorAsms as $asm) {
    //                    if ($asm->actorDirector->type == ActorDirector::TYPE_ACTOR) {
    //                        /** @var $asm ContentCategoryAsm */
    //                        $tempA .= $asm->actorDirector->id . ',';
    //                    }
    //                    if ($asm->actorDirector->type == ActorDirector::TYPE_DIRECTOR) {
    //                        /** @var $asm ContentCategoryAsm */
    //                        $tempD .= $asm->actorDirector->id . ',';
    //                    }
    //                }
    //            }
    ////            /** Cắt xâu */
    ////            if (strlen($tempA) >= 2) {
    ////                $tempA = substr($tempA, 0, -1);
    ////            }
    ////            /** Cắt xâu */
    ////            if (strlen($tempD) >= 2) {
    ////                $tempD = substr($tempD, 0, -1);
    ////            }
    //            $group_tmp['actors']    = $tempA;
    //            $group_tmp['directors'] = $tempD;
    //
    //            $strQuality = "";
    //            $qualities  = $item->contentProfiles;
    //            foreach ($qualities as $quality) {
    //                $strQuality .= $quality->quality . ',';
    //            }
    ////            /** Cắt xâu */
    ////            if (strlen($strQuality) >= 2) {
    ////                $strQuality = substr($strQuality, 0, -1);
    ////            }
    //
    //            $group_tmp['qualities'] = $strQuality;
    //            $group_tmp['shortname'] = CUtils::parseTitleToKeyword($item->display_name);
    //
    //            array_push($lst, $group_tmp);
    //        }
    //        echo count($lst);exit;
    //        $res = [
    //            'success'      => true,
    //            'message'      => Message::MSG_SUCCESS,
    //            'totalCount'   => count($lst),
    //            'time_update'  => time(),
    //            "date_expired" => "01/01/2018",
    //        ];
    //        $res['items'] = $lst;
    //        $resJson      = json_encode($res);
    //        $path         = 'staticdata/data' . $site_id . '.json';
    //        $save2File    = CUtils::writeFile($resJson, $path);
    //        if ($save2File) {
    //            $c = ApiVersion::createApiVersion("karaoke","version karaoke",$site_id,ApiVersion::TYPE_KARAOKE);
    //            if($c['success']){
    //                Yii::$app->getSession()->setFlash('success', Message::MSG_SUCCESS);
    //            }
    //            Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
    //        } else {
    //            Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
    //        }
    //        return $this->redirect(['release-static-data']);
    //
    //    }

    public function actionUpdateVersionApi()
    {
//        $site_id = Yii::$app->user->identity->site_id;
        $params = Yii::$app->request->queryParams;
        $site_id = $params['site_id'];
        $type = $params['type'];
        if (!$site_id || !$type) {
            Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
            return $this->redirect(['release-static-data', 'site_id' => $site_id]);
        }
        /** @var  $model ApiVersion */
        $model = ApiVersion::findOne(['type' => $type, 'site_id' => $site_id]);
        if (!$model) {
            $model = new ApiVersion();
            $model->name = "karaoke";
            $model->version = 1;
            $model->type = ApiVersion::TYPE_KARAOKE;
            $model->site_id = $site_id;

            $model->description = "version karaoke";
        } else {
            $model->version++;
        }

        if ($model->save()) {
            Yii::$app->getSession()->setFlash('success', Message::MSG_SUCCESS);
        } else {
            Yii::$app->getSession()->setFlash('error', Message::MSG_FAIL);
        }
        return $this->redirect(['release-static-data', 'site_id' => $site_id]);

    }

    public function actionCreateEpg()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $params = Yii::$app->request->post();

        $count = 0;
        foreach ($params['title'] as $key => $value) {
            $started_at = explode('-', $params['time'][$key])[0];
            $ended_at = explode('-', $params['time'][$key])[1];

            $content = new Content();
            $content->code = time() . rand(0, 999);
            $content->created_user_id = Yii::$app->user->id;
            $content->type = Content::TYPE_LIVE_CONTENT;
            $content->display_name = $value;
            $content->is_free = 1;

            if ($content->insert()) {
                $liveProgram = new LiveProgram();

                $liveProgram->name = $value;
                $liveProgram->channel_id = $params['channel'];
                $liveProgram->content_id = $content->id;
                $liveProgram->started_at = strtotime($params['date'] . ' ' . $started_at . ':00', time());
                $liveProgram->ended_at = strtotime($params['date'] . ' ' . $ended_at . ':00', time());
                if ($liveProgram->insert()) {
                    $count++;
                }
            } else {
                var_dump($content->getErrors(0));
                die;

            }
        }

        return [
            "success" => "",
            "message" => "Thêm thành công $count chương trình",
        ];
    }

    public function actionChangeLiveprogramStatus($lp_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->post();

        $live = LiveProgram::findOne($lp_id);
        $live->status = $params['LiveProgram'][$params['editableIndex']]['status'];

        if ($live->update()) {
            return [
                'output' => '',
                'message' => '',
            ];
        } else {
            return [
                'output' => '',
                'message' => $live->getErrors(),
            ];
        }
    }

    public function actionModalStreamingServer($site_id, $content_id = null)
    {
        $streamingServer = StreamingServer::find()
            ->innerJoin('site_streaming_server_asm', 'streaming_server.id = site_streaming_server_asm.streaming_server_id')
            ->andWhere(['site_streaming_server_asm.site_id' => $site_id, 'status' => StreamingServer::STATUS_ACTIVE])
            ->all();

        $streamingServers = new ArrayDataProvider([
            'key' => 'name',
            'allModels' => $streamingServer,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        if ($content_id !== null) {
            $action = \yii\helpers\Url::to(['sync-content-to-site', 'site_id' => $site_id, 'id' => $content_id]);
        } else {
            $action = \yii\helpers\Url::to(['sync-data-to-site', 'site_id' => $site_id]);
        }

        return $this->renderAjax('_streaming_server', [
            'countSS' => count($streamingServer),
            'streamingServers' => $streamingServers,
            'action' => $action,
        ]);
    }

    public function actionSyncContentToSite($site_id, $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->post();
        $servers = empty($params['selection']) ? [] : $params['selection'];

        if (empty($servers)) {
            Yii::$app->getSession()->setFlash('error', 'Phân phối nội dung không thành công. Yêu cầu chọn ít nhất một máy chủ');
        } else {
            foreach ($servers as $server) {
                $sync = Content::syncContentToSite($site_id, $id, $server);
                Yii::info($server, 'sync-log');
                Yii::info($sync, 'sync-log2');
            }
            Yii::$app->getSession()->setFlash('success', 'Đang tiến hành phân phối nội dung.  Tiến trình này sẽ mất một khoảng thời gian');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionSyncDataToSite($site_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->post();
        $servers = empty($params['selection']) ? [] : $params['selection'];

        if (empty($servers)) {
            Yii::$app->getSession()->setFlash('error', 'Phân phối nội dung không thành công. Yêu cầu chọn ít nhất một máy chủ');
        } else {
            foreach ($servers as $server) {
                Content::syncDataToSite($site_id, $server);
            }
            Yii::$app->getSession()->setFlash('success', 'Đang tiến hành phân phối nội dung.  Tiến trình này sẽ mất một khoảng thời gian');
        }
        return $this->redirect(Yii::$app->request->referrer);

    }

    public function actionChangeContentSiteStatus($site_id, $content_id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $content = ContentSiteAsm::findOne(['site_id' => $site_id, 'content_id' => $content_id]);
        $content->status = $status;
        if ($content->update()) {
            return [
                "success" => true,
                "message" => '',
            ];
        } else {
            return [
                "success" => false,
                "message" => '',
            ];
        }
    }

}
