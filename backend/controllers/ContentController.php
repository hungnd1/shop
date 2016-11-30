<?php

namespace backend\controllers;

use api\helpers\Message;
use common\components\ActionLogTracking;
use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\models\Content;
use common\models\ContentSearch;
use common\models\User;
use common\models\UserActivity;
use kartik\form\ActiveForm;
use Yii;
use yii\console\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
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
                    'update-status-content'=> ['post'],
                ],
            ],
            [
                'class' => ActionLogTracking::className(),
                'user' => Yii::$app->user,
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

                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
                        // tao log
                        $description = 'UPDATE STATUS CONTENT';
                        $ip_address = CUtils::clientIP();

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
                $index = $post['editableIndex'];
            } // else if nothing to do always return an empty JSON encoded output
            else {
                echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
            }

            return;
        }
        $model = $this->findModel($id);
        //Images
        $imageModel = new \backend\models\Image();
        $images = $model->getImages();
        $imageProvider = new ArrayDataProvider([
            'key' => 'name',
            'allModels' => $images,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);


        return $this->render('view', [
            'model' => $model,
            'id' => $id,
            'imageModel' => $imageModel,
            'imageProvider' => $imageProvider,
            'active' => $active,
        ]);
    }

    /**
     * Creates a new Content model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($type)
    {
        $model = new Content();
        $model->loadDefaultValues();
        $model->type = $type;
        $model->service_provider_id = $this->cp_user->service_provider_id;
        $model->content_provider_id = $this->cp_user->content_provider_id;
        $model->created_user_id     = $this->cp_user->id;

        $model->setScenario('create');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        $selectedCats = [];
        // get screenshoot
        $logoInit = [];
        $thumbnailInit = [];
        $screenshootInit = [];
        $slideInit = [];

        $logoPreview = [];
        $thumbnailPreview = [];
        $screenshootPreview = [];
        $slidePreview = [];
        $tags = explode(';', $model->tags);
        $tag_models = TagManage::findAll(['service_provider_id' => $this->cp_user->service_provider_id]);
        $tagDatas = [];
        foreach ($tag_models as $tag) {
            $tagDatas[$tag->value] = $tag->value;
        }

        if ($model->load(Yii::$app->request->post())) {
            $thumbnails = UploadedFile::getInstances($model, 'thumbnail');
            $screenshoots = UploadedFile::getInstances($model, 'screenshoot');
            $slides = UploadedFile::getInstances($model, 'slide');
            $images = [];
            if (count($thumbnails) > 0) {

                foreach ($thumbnails as $image) {
                    $attribute = 'thumbnail';

                    $files = null;

                    $files = $_FILES['Content'];
                    $type_thumbnail = Content::IMAGE_TYPE_THUMBNAIL;
                    Yii::trace($type_thumbnail . '  ' . $attribute);
                    $file_type = '';
                    list($width, $height, $file_type, $attr) = getimagesize($files['tmp_name']["$attribute"][0]);
                    Yii::info($width . 'xxx' . $height);

                    Yii::info($files);
                    $new_file = [];
                    $size = $files['size']["$attribute"][0];
                    if($size > Content::MAX_SIZE_UPLOAD){
                        Yii::$app->session->setFlash('error', Yii::t('app','Thumbnail dung lượng tối đa cho phép 10MB'));
                        $model->list_cat_id = '';
                        return $this->render('create', [
                            'model' => $model,
                            'logoInit' => $logoInit,
                            'logoPreview' => $logoPreview,
                            'thumbnailInit' => $thumbnailInit,
                            'thumbnailPreview' => $thumbnailPreview,
                            'screenshootInit' => $screenshootInit,
                            'screenshootPreview' => $screenshootPreview,
                            'slideInit' => $slideInit,
                            'slidePreview' => $slidePreview,
                            'selectedCats' => $selectedCats,
                            'type' => $type,
                            'tags' => $tags,
                            'tagDatas' => $tagDatas,

                        ]);
                    }
//                    getExtension()
                    if($image->extension != 'jpg' && $image->extension  !=  'png' && $image->extension != 'gif' && $image->extension !='jpeg' ){
                        Yii::$app->session->setFlash('error', Yii::t('app','Chỉ các file với phần mở rộng sau đây được phép tải lên: png, jpg, jpeg, gif'));
                        $model->list_cat_id = '';
                        return $this->render('create', [
                            'model' => $model,
                            'logoInit' => $logoInit,
                            'logoPreview' => $logoPreview,
                            'thumbnailInit' => $thumbnailInit,
                            'thumbnailPreview' => $thumbnailPreview,
                            'screenshootInit' => $screenshootInit,
                            'screenshootPreview' => $screenshootPreview,
                            'slideInit' => $slideInit,
                            'slidePreview' => $slidePreview,
                            'selectedCats' => $selectedCats,
                            'type' => $type,
                            'tags' => $tags,
                            'tagDatas' => $tagDatas,

                        ]);
                    }
                    $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                    if ($image->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@content_images') . "/" . $file_name)) {
                        $new_file['name'] = $file_name;
                        $new_file['type'] = Content::IMAGE_TYPE_THUMBNAIL;
                        $new_file['size'] = $image->size;
                        $images[] = $new_file;
                    }
                }

            }else{
                if($model->images){
                    $check = Content::CheckTypeThumbnailTp($model->images);
//                    echo "<pre>"; print_r($check);die();
                    if($check == Content::IMAGE_TYPE_THUMBNAIL) {
                        foreach ($thumbnails as $image) {
                            $attribute = 'thumbnail';

                            $files = null;
                            $files = $_FILES['Content'];
                            $type_thumbnail = Content::IMAGE_TYPE_THUMBNAIL;
                            Yii::trace($type_thumbnail . '  ' . $attribute);
                            $file_type = '';
                            list($width, $height, $file_type, $attr) = getimagesize($files['tmp_name']["$attribute"][0]);
                            Yii::info($width . 'xxx' . $height);

                            Yii::info($files);
                            $new_file = [];
                            $size = $files['size']["$attribute"][0];
                            if ($size > Content::MAX_SIZE_UPLOAD) {
                                Yii::$app->session->setFlash('error', Yii::t('app', 'Thumbnail dung lượng tối đa cho phép 10MB'));
                                $model->list_cat_id = '';
                                return $this->render('create', [
                                    'model' => $model,
                                    'logoInit' => $logoInit,
                                    'logoPreview' => $logoPreview,
                                    'thumbnailInit' => $thumbnailInit,
                                    'thumbnailPreview' => $thumbnailPreview,
                                    'screenshootInit' => $screenshootInit,
                                    'screenshootPreview' => $screenshootPreview,
                                    'slideInit' => $slideInit,
                                    'slidePreview' => $slidePreview,
                                    'selectedCats' => $selectedCats,
                                    'type' => $type,
                                    'tags' => $tags,
                                    'tagDatas' => $tagDatas,

                                ]);
                            }
//                    getExtension()
                            if ($image->extension != 'jpg' && $image->extension != 'png' && $image->extension != 'gif' && $image->extension != 'jpeg') {
                                Yii::$app->session->setFlash('error', Yii::t('app', 'Chỉ các file với phần mở rộng sau đây được phép tải lên: png, jpg, jpeg, gif'));
                                $model->list_cat_id = '';
                                return $this->render('create', [
                                    'model' => $model,
                                    'logoInit' => $logoInit,
                                    'logoPreview' => $logoPreview,
                                    'thumbnailInit' => $thumbnailInit,
                                    'thumbnailPreview' => $thumbnailPreview,
                                    'screenshootInit' => $screenshootInit,
                                    'screenshootPreview' => $screenshootPreview,
                                    'slideInit' => $slideInit,
                                    'slidePreview' => $slidePreview,
                                    'selectedCats' => $selectedCats,
                                    'type' => $type,
                                    'tags' => $tags,
                                    'tagDatas' => $tagDatas,

                                ]);
                            }
                            $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                            if ($image->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@content_images') . "/" . $file_name)) {
                                $new_file['name'] = $file_name;
                                $new_file['type'] = Content::IMAGE_TYPE_THUMBNAIL;
                                $new_file['size'] = $image->size;
                                $images[] = $new_file;
                            }
                        }
                    }else{
                        Yii::$app->session->setFlash('error', Yii::t('app', 'Thumbnail không được để trống'));
                        $model->list_cat_id = '';
                        return $this->render('create', [
                            'model' => $model,
                            'logoInit' => $logoInit,
                            'logoPreview' => $logoPreview,
                            'thumbnailInit' => $thumbnailInit,
                            'thumbnailPreview' => $thumbnailPreview,
                            'screenshootInit' => $screenshootInit,
                            'screenshootPreview' => $screenshootPreview,
                            'slideInit' => $slideInit,
                            'slidePreview' => $slidePreview,
                            'selectedCats' => $selectedCats,
                            'type' => $type,
                            'tags' => $tags,
                            'tagDatas' => $tagDatas,

                        ]);
                    }
                }else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Thumbnail không được để trống'));
                    $model->list_cat_id = '';
                    return $this->render('create', [
                        'model' => $model,
                        'logoInit' => $logoInit,
                        'logoPreview' => $logoPreview,
                        'thumbnailInit' => $thumbnailInit,
                        'thumbnailPreview' => $thumbnailPreview,
                        'screenshootInit' => $screenshootInit,
                        'screenshootPreview' => $screenshootPreview,
                        'slideInit' => $slideInit,
                        'slidePreview' => $slidePreview,
                        'selectedCats' => $selectedCats,
                        'type' => $type,
                        'tags' => $tags,
                        'tagDatas' => $tagDatas,

                    ]);
                }
            }
            if (count($screenshoots) > 0) {
                foreach ($screenshoots as $image) {
                    $attribute = 'screenshoot';

                    $files = null;

                    $files = $_FILES['Content'];
                    $type_thumbnail = Content::IMAGE_TYPE_SCREENSHOOT;
                    Yii::trace($type_thumbnail . '  ' . $attribute);
                    $file_type = '';
                    list($width, $height, $file_type, $attr) = getimagesize($files['tmp_name']["$attribute"][0]);
                    Yii::info($width . 'xxx' . $height);

                    Yii::info($files);
                    $new_file = [];
                    $size = $files['size']["$attribute"][0];
                    if($size > Content::MAX_SIZE_UPLOAD){
                        Yii::$app->session->setFlash('error', Yii::t('app','Screenshoot dung lượng tối đa cho phép 10MB'));
                        $model->list_cat_id = '';
                        return $this->render('create', [
                            'model' => $model,
                            'logoInit' => $logoInit,
                            'logoPreview' => $logoPreview,
                            'thumbnailInit' => $thumbnailInit,
                            'thumbnailPreview' => $thumbnailPreview,
                            'screenshootInit' => $screenshootInit,
                            'screenshootPreview' => $screenshootPreview,
                            'slideInit' => $slideInit,
                            'slidePreview' => $slidePreview,
                            'selectedCats' => $selectedCats,
                            'type' => $type,
                            'tags' => $tags,
                            'tagDatas' => $tagDatas,

                        ]);
                    }

                    if($image->extension != 'jpg' && $image->extension  !=  'png' && $image->extension != 'gif' && $image->extension !='jpeg' ){
                        Yii::$app->session->setFlash('error', Yii::t('app','Chỉ các file với phần mở rộng sau đây được phép tải lên: png, jpg, jpeg, gif'));
                        $model->list_cat_id = '';
                        return $this->render('create', [
                            'model' => $model,
                            'logoInit' => $logoInit,
                            'logoPreview' => $logoPreview,
                            'thumbnailInit' => $thumbnailInit,
                            'thumbnailPreview' => $thumbnailPreview,
                            'screenshootInit' => $screenshootInit,
                            'screenshootPreview' => $screenshootPreview,
                            'slideInit' => $slideInit,
                            'slidePreview' => $slidePreview,
                            'selectedCats' => $selectedCats,
                            'type' => $type,
                            'tags' => $tags,
                            'tagDatas' => $tagDatas,

                        ]);
                    }
                    $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                    if ($image->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@content_images') . "/" . $file_name)) {
                        $new_file['name'] = $file_name;
                        $new_file['type'] = Content::IMAGE_TYPE_SCREENSHOOT;
                        $new_file['size'] = $image->size;
                        $images[] = $new_file;
                    }
                }
            }
            if (count($slides) > 0) {
                foreach ($slides as $image) {
                    $attribute = 'slide';

                    $files = null;

                    if (empty($_FILES['Content'])) {
                        return Yii::$app->session->setFlash('error', Yii::t('app','Không được để trống '));
                    }

                    $files = $_FILES['Content'];
                    $type_thumbnail = Content::IMAGE_TYPE_SLIDE;
                    Yii::trace($type_thumbnail . '  ' . $attribute);
                    $file_type = '';
                    list($width, $height, $file_type, $attr) = getimagesize($files['tmp_name']["$attribute"][0]);
                    Yii::info($width . 'xxx' . $height);

                    Yii::info($files);
                    $new_file = [];
                    $size = $files['size']["$attribute"][0];
                    if($size > Content::MAX_SIZE_UPLOAD){
                        Yii::$app->session->setFlash('error', Yii::t('app','Slide dung lượng tối đa cho phép 10MB'));
                        $model->list_cat_id = '';
                        return $this->render('create', [
                            'model' => $model,
                            'logoInit' => $logoInit,
                            'logoPreview' => $logoPreview,
                            'thumbnailInit' => $thumbnailInit,
                            'thumbnailPreview' => $thumbnailPreview,
                            'screenshootInit' => $screenshootInit,
                            'screenshootPreview' => $screenshootPreview,
                            'slideInit' => $slideInit,
                            'slidePreview' => $slidePreview,
                            'selectedCats' => $selectedCats,
                            'type' => $type,
                            'tags' => $tags,
                            'tagDatas' => $tagDatas,

                        ]);
                    }

                    if($image->extension != 'jpg' && $image->extension  !=  'png' && $image->extension != 'gif' && $image->extension !='jpeg' ){
                        Yii::$app->session->setFlash('error', Yii::t('app','Chỉ các file với phần mở rộng sau đây được phép tải lên: png, jpg, jpeg, gif'));
                        $model->list_cat_id = '';
                        return $this->render('create', [
                            'model' => $model,
                            'logoInit' => $logoInit,
                            'logoPreview' => $logoPreview,
                            'thumbnailInit' => $thumbnailInit,
                            'thumbnailPreview' => $thumbnailPreview,
                            'screenshootInit' => $screenshootInit,
                            'screenshootPreview' => $screenshootPreview,
                            'slideInit' => $slideInit,
                            'slidePreview' => $slidePreview,
                            'selectedCats' => $selectedCats,
                            'type' => $type,
                            'tags' => $tags,
                            'tagDatas' => $tagDatas,

                        ]);
                    }
                    $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                    if ($image->saveAs(Yii::getAlias('@webroot') . "/" . Yii::getAlias('@content_images') . "/" . $file_name)) {
                        $new_file['name'] = $file_name;
                        $new_file['type'] = Content::IMAGE_TYPE_SLIDE;
                        $new_file['size'] = $image->size;
                        $images[] = $new_file;
                    }
                }
                $model->is_slide = 1;
            }
            if($model->list_cat_id == null){
                Yii::$app->session->setFlash('error', Yii::t('app','Category không được để trống'));
                return $this->render('create', [
                    'model' => $model,
                    'logoInit' => $logoInit,
                    'logoPreview' => $logoPreview,
                    'thumbnailInit' => $thumbnailInit,
                    'thumbnailPreview' => $thumbnailPreview,
                    'screenshootInit' => $screenshootInit,
                    'screenshootPreview' => $screenshootPreview,
                    'slideInit' => $slideInit,
                    'slidePreview' => $slidePreview,
                    'selectedCats' => $selectedCats,
                    'type' => $type,
                    'tags' => $tags,
                    'tagDatas' => $tagDatas,
                ]);
            }
            $model->status = Content::STATUS_PENDING;
            $model->episode_order = $model->episode_order ? $model->episode_order : 0;
            $model->ascii_name = CVietnameseTools::makeSearchableStr($model->display_name);
            $old_images = Content::convertJsonToArray($model->images);
            $model->images = Json::encode(ArrayHelper::merge($old_images, $images));
            $tags = $model->tags;
            if (is_array($tags)) {
                TagManage::addTags($tags, $this->cp_user->service_provider_id);
                $model->tags = implode(';', $tags);
            }
            $model->loadHonor();
//            ContentCategoryAsm::deleteAll(['content_id' => $this->id]);

//            if (empty($this->list_cat_id)) {
//                Yii::$app->getSession()->setFlash('error', 'Category không được để trống');
//            } else {
            if ($model->save()) {

                $model->createCategoryAsm();

                // tao log
//                $description = 'CREATE CONTENT';
//                $ip_address = CUtils::clientIP();
//                $model->createContentLog(ContentLog::TYPE_CREATE, Yii::$app->user->id, $ip_address, ContentLog::STATUS_SUCCESS, $description, '', $model->display_name);

                \Yii::$app->getSession()->setFlash('success', 'Lưu Content thành công');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::info($model->getErrors());

                \Yii::$app->getSession()->setFlash('error', 'Lưu Content thất bại');
            }

//            }
        }


        //end screenshoot

        return $this->render('create', [
            'model' => $model,
            'logoInit' => $logoInit,
            'logoPreview' => $logoPreview,
            'thumbnailInit' => $thumbnailInit,
            'thumbnailPreview' => $thumbnailPreview,
            'screenshootInit' => $screenshootInit,
            'screenshootPreview' => $screenshootPreview,
            'slideInit' => $slideInit,
            'slidePreview' => $slidePreview,
            'selectedCats' => $selectedCats,
            'type' => $type,
            'tags' => $tags,
            'tagDatas' => $tagDatas,
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
        $model->setScenario('adminModify');

        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $model->expired_at = Yii::$app->formatter->asDate($model->expired_at, "dd-M-yyyy");
        if ($model->load(Yii::$app->request->post())) {

            $model->ascii_name = CVietnameseTools::makeSearchableStr($model->display_name);
            if (isset(Yii::$app->request->post()['Content']['list_cat_id'])) {
                $model->list_cat_id = Yii::$app->request->post()['Content']['list_cat_id'];
            }

            $model->expired_at = strtotime($model->expired_at);
            if(!$model->honor){
                $model->honor = 0;
            }
            $model->updated_at = time();
            if ($model->save()) {

                $model->createCategoryAsm();

                // tao log

                \Yii::$app->getSession()->setFlash('success', 'Cập nhật Content thành công');

                return $this->redirect(['view', 'id' => $model->id]);

            } else {
                \Yii::$app->getSession()->setFlash('error', 'Cập nhật Content thất bại');

            }
        }
        // get screenshoot
        $images = Content::convertJsonToArray($model->images);

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

        $thumb_epg = [];
        $thumb = [];
        $slide = [];
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
                case Content::IMAGE_TYPE_SLIDE:
                    $slidePreview[] = $preview;
                    $slideInit[] = $value;
                    $slide[] = $name;
                    break;
            }

            //end screenshoot
        }
        $model->thumbnail = $thumb;
        $model->slide = $slide;
        $model->screenshoot = $screenshoot;

        $selectedCats = $model->getListCatIds();
        $model->list_cat_id = implode(',', $selectedCats);

        Yii::trace($selectedCats);
//        var_dump($screenshootInit);
        //        var_dump($screenshootPreview);exit;
        return $this->render('update', [
            'model' => $model,
            'logoInit' => $logoInit,
            'logoPreview' => $logoPreview,
            'thumbnail_epgPreview' => $thumbnail_epgPreview,
            'thumbnail_epgInit' => $thumbnail_epgInit,
            'thumbnailInit' => $thumbnailInit,
            'slideInit' => $slideInit,
            'slidePreview' => $slidePreview,
            'thumbnailPreview' => $thumbnailPreview,
            'screenshootInit' => $screenshootInit,
            'screenshootPreview' => $screenshootPreview,
            'selectedCats' => $selectedCats,
        ]);
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
        }elseif ($type == Content::IMAGE_TYPE_SLIDE) {
            $old_value = Yii::$app->request->post('slide_old');
            $attribute = 'slide';
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



}
