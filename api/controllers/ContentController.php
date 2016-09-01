<?php
/**
 * Created by PhpStorm.
 * User: VS9 X64Bit
 * Date: 23/05/2015
 * Time: 4:37 PM
 */
namespace api\controllers;


use api\helpers\Message;
use api\helpers\UserHelpers;
use common\helpers\CUtils;
use common\helpers\VNPHelper;
use common\models\ActorDirector;
use common\models\ActorDirectorSearch;
use common\models\ApiVersion;
use common\models\Category;
use common\models\Content;
use common\models\ContentAttribute;
use common\models\ContentAttributeValue;
use common\models\ContentFeedback;
use common\models\ContentProfile;
use common\models\ContentProfileSiteAsm;
use common\models\ContentSearch;
use common\models\ContentSiteAsm;
use common\models\ContentSiteStatusAsm;
use common\models\ContentViewLog;
use common\models\Languages;
use common\models\LiveProgram;
use common\models\LiveProgramSearch;
use common\models\Site;
use common\models\SiteApiCredential;
use common\models\Subscriber;
use Faker\Provider\zh_TW\DateTime;
use kartik\widgets\ActiveForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidValueException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\UnauthorizedHttpException;


class ContentController extends ApiController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'karaoke-search',
            'content-attributes',
            'karaoke',
            'catchup-channels',
            'list-days',
            'catchup',
            'feedbacks',
            'view',
            'search',
            'get-content',
            'get-sub-drama',
            'list-drama-film',
            'list-sub-drama',
            'detail',
            'related',
            'comments',
            'list-content-search',
            'test',
            'film-drama-detail',
            'add-view-count',
            'sugestion',
            'go-to-drama',
            'get-version-api',
            'get-adventisment',
            'sync-content-to-site',
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'list-content' => ['GET'],
            'detail' => ['GET'],
            'related' => ['GET'],
            'favorite' => ['GET'],
            'unfavorite' => ['GET'],
            'comment' => ['POST'],
            'comments' => ['GET'],
//            'list-content-search' => ['GET'],
            'test' => ['GET'],
//            'film-drama-detail' => ['GET'],
//            'add-view-count' => ['POST'],
            'sync-content-to-site' => ['POST'],
        ];
    }



    /**
     * HungNV edition: search contents by filter: type, category, key->name...
     * @param int $type
     * @param int $category
     * @param int $filter
     * @param string $keyword
     * @param string $language
     * @param int $order 0: newest, 1: mostview
     * @return \yii\data\ActiveDataProvider
     */
//    public function actionListContentSearch($type = 0, $category = 0, $filter = 0, $order = Content::ORDER_NEWEST, $language = '')
//    {
//        $keyword = $this->getParameter('keyword', '');
//        return Content::getListContentSearch($this->site->id, $type, $category, $filter, $keyword, $order, $language);
//    }


    /**
     * API get content dùng cho cả search, list video, list phim bộ
     * @return \yii\data\ActiveDataProvider
     */
    public function actionSearch()
    {
//        UserHelpers::manualLogin();
        $searchModel = new ContentSearch();

        $param = Yii::$app->request->queryParams;
        $searchModel->site_id = $this->site->id;
//        $searchModel->id= isset($param['id'])?($param['id']):0;
        $searchModel->type = isset($param['type']) ? ($param['type']) : 0;
        /** Bổ sung trường catchup để lấy danh sách kênh live catchup */
        $searchModel->is_catchup = isset($param['is_catchup']) ? ($param['is_catchup']) : Content::NOT_CATCHUP;
        $searchModel->category_id = isset($param['category_id']) ? ($param['category_id']) : 0;
        $searchModel->honor = isset($param['honor']) ? ($param['honor']) : Content::HONOR_NOTHING;
        $searchModel->keyword = isset($param['keyword']) ? ($param['keyword']) : "";
        $searchModel->order = isset($param['order']) ? ($param['order']) : Content::ORDER_NEWEST;
        $searchModel->status = Content::STATUS_ACTIVE;
        $searchModel->language = isset($param['language']) ? ($param['language']) : "";
        $searchModel->is_series = isset($param['is_series']) ? ($param['is_series']) : Content::IS_MOVIES;

        if (!$searchModel->validate()) {
            $error = $searchModel->firstErrors;
            $message = "";
            foreach ($error as $key => $value) {
                $message .= $value;
                break;
            }
            throw new InvalidValueException($message);
        }
        $dataProvider = $searchModel->search($param);
        return $dataProvider;
    }

    /**
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function actionView($id,$status =0)
    {
        UserHelpers::manualLogin();
        if (!is_numeric($id)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['id']));
        }
        if (!is_numeric($status)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['status']));
        }
        if($status){
//            $content = \api\models\Content::findOne(['tvod1_id' => $id, 'status' => Content::STATUS_ACTIVE]);
            $content = \api\models\Content::findOne(['tvod1_id' => $id]);
        }else{
            $content = \api\models\Content::findOne(['id' => $id]);
        }

        if (!$content) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }
        return $content;
    }

    public function actionGetSubDrama($id)
    {
        UserHelpers::manualLogin();
        $content = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $id,'is_series' => Content::IS_SERIES, 'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$content) {
            throw new InvalidValueException("Not the drama.");
        }

        $searchModel = new ContentSearch();
        $param = Yii::$app->request->queryParams;
        $searchModel->site_id = $this->site->id;
        $searchModel->type = Content::TYPE_VIDEO;
        $searchModel->order = Content::ORDER_EPISODE;
        $searchModel->status = Content::STATUS_ACTIVE;
        $searchModel->language = isset($param['language']) ? ($param['language']) : "";
        $searchModel->parent_id = isset($param['id']) ? ($param['id']) : 0;

        /** Validate đầu vào */
        if (!$searchModel->validate()) {
            $error = $searchModel->firstErrors;
            $message = "";
            foreach ($error as $key => $value) {
                $message .= $value;
                break;
            }
            throw new InvalidValueException($message);
        }

        $dataProvider = $searchModel->search($param);

        return $dataProvider;
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionSuggestion()
    {
        $searchModel = new ContentSearch();
        $param = Yii::$app->request->queryParams;
        $searchModel->site_id = $this->site->id;
        $searchModel->type = isset($param['type']) ? ($param['type']) : 0;
        $searchModel->category_id = isset($param['category_id']) ? ($param['category_id']) : 0;
        $searchModel->honor = isset($param['honor']) ? ($param['honor']) : Content::HONOR_NOTHING;
        $searchModel->keyword = isset($param['keyword']) ? ($param['keyword']) : "";
        $searchModel->order = isset($param['order']) ? ($param['order']) : Content::ORDER_NEWEST;
        $searchModel->status = Content::STATUS_ACTIVE;
        $searchModel->language = isset($param['language']) ? ($param['language']) : "";
        $searchModel->is_series = isset($param['is_series']) ? ($param['is_series']) : Content::IS_MOVIES;

        if (!$searchModel->validate()) {
            $error = $searchModel->firstErrors;
            $message = "";
            foreach ($error as $key => $value) {
                $message .= $value;
                break;
            }
            throw new InvalidValueException($message);
        }
        $dataProvider = $searchModel->suggestion($param);

        return $dataProvider;
    }

    /**
     * @param $id
     * @return ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function actionRelated($id)
    {
        UserHelpers::manualLogin();
        $searchModel = new ContentSearch();
        $param = Yii::$app->request->queryParams;
        $searchModel->site_id = $this->site->id;
        $searchModel->content_id = $id;

        if (!$searchModel->validate()) {
            $error = $searchModel->firstErrors;
            $message = "";
            foreach ($error as $key => $value) {
                $message .= $value;
                break;
            }
            throw new InvalidValueException($message);
        }
        $dataProvider = $searchModel->search($param);
        if (!$dataProvider->getModels()) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }

        return $dataProvider;
    }

    /**
     * @param $id
     * @param int $status
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionGoToDrama($id, $status = Content::NEXT_VIDEO)
    {
        UserHelpers::manualLogin();
        /** Check validate input */
        if (!is_numeric($id) || !is_numeric($status)) {
            throw new InvalidValueException("Id or Status must be an integer.");
        }
        /** Check xem ID có phải là movie không */
        /** @var  $episode Content */
//        $episode = Content::findOne(['id' => $id, 'status' => Content::STATUS_ACTIVE, 'type' => Content::TYPE_VIDEO]);
//        if (!$episode) {
//            throw new InvalidValueException("Not found movie.");
//        }
        $episode = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $id,'type' => Content::TYPE_VIDEO,'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$episode) {
            throw new InvalidValueException("Not found movie.");
        }
        /** @var  $drama Content */
//        $drama = Content::findOne(['id' => $episode->parent_id, 'is_series' => Content::IS_SERIES, 'status' => Content::STATUS_ACTIVE]);
//        if (!$drama) {
//            throw new InvalidValueException("Not the episode.");
//        }
        $drama = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $episode->parent_id,'is_series' => Content::IS_SERIES,'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$drama) {
            throw new InvalidValueException("Not the episode.");
        }

        $episodeCount = $drama->episode_count ? $drama->episode_count : 1;
        $episodeOrder = $episode->episode_order ? $episode->episode_order : 1;

        if ($status == Content::NEXT_VIDEO) {
            if ($episodeCount == $episodeOrder) {
                throw new NotFoundHttpException("Limit to episode + ");
            }
            /** Dùng \api\models\Content để trả thêm att cho client/ */
//            $nextContent = \api\models\Content::find()->where(['parent_id' => $drama->id, 'status' => Content::STATUS_ACTIVE])
//                ->andWhere('episode_order >:episode_order', [':episode_order' => $episodeOrder])
//                ->orderBy('episode_order')->one();

            $nextContent = \api\models\Content::find()
                ->joinWith('contentSiteAsms')
                ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
                ->andWhere(['content.status' => Content::STATUS_ACTIVE])
                ->andWhere(['parent_id' => $drama->id])
                ->andWhere('episode_order >:episode_order', [':episode_order' => $episodeOrder])
                ->orderBy('episode_order')->one();

            if ($nextContent) {
                return $nextContent;
            }
            throw new NotFoundHttpException("Cannot go to drama -");
        } else {
            if ($episodeOrder == 1) {
                throw new NotFoundHttpException("Limit to episode -");
            }
            /** Dùng \api\models\Content để trả thêm att cho client/ */
            $previousContent = \api\models\Content::find()
                ->joinWith('contentSiteAsms')
                ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
                ->andWhere(['content.status' => Content::STATUS_ACTIVE])
                ->andWhere(['parent_id' => $drama->id])
                ->andWhere('episode_order <:episode_order', [':episode_order' => $episodeOrder])
                ->orderBy('episode_order DESC')->one();
            if ($previousContent) {
                return $previousContent;
            }
            throw new NotFoundHttpException("Cannot go to drama -");
        }

    }

//    public function actionListDramaFilm()
//    {
//        $res = Content::getListDrama(Content::TYPE_VIDEO, 1);
//        return $res;
//    }

//    public function actionListSubDrama($film_id)
    /**
     * @return ActiveDataProvider
     */
//    public function actionLiveDramas()
//    {
//        $res = Content::getLiveDrama(Content::TYPE_LIVE, 1, null);
//        return $res;
//    }

    /*
        public function actionSubLiveDrama($id)
        {
            $id = $this->getParameter('id');
            $res = Content::getLiveDrama(Content::TYPE_LIVE, 0, null);
            return $res;
        }
    */
//    public function actionFilmDramaDetail($id)
//    {
//
//        $data['items'] = Content::getListContentDetail($this->site->id, $id);
//
//        return $data;
//    }

    /**
     * HungNV edition: 14/03/16
     * @param $id
     * @param string $getURL
     * @return \api\models\ListContent
     */
//    public function actionDetail($id, $getURL = '')
//    {
//        /** @var \api\models\ListContent $content */
//        /** @var  $video Content */
//        $protocol = ContentProfile::STREAMING_HLS;
//        $content = [];
//        $content['content'] = Content::findOne(['id' => $id, 'status' => Content::STATUS_ACTIVE]);
//        if (isset($content)) {
//            $content['site'] = Site::findOne(['id' => $content['content']->created_user_id])->name;
//            if ($getURL == 'get-url') {
//                $video = new ContentProfile();
//                $content['content']->urls = $video->getStreamUrl($protocol, true);
//            }
//            return $content;
//        } else {
//            throw new InvalidValueException("Nội dung không tồn tại");
//        }
//    }


    /**
     * ------> Same function actionRelated
     *
     * @param $id
     * @return ActiveDataProvider
     */
    /*
    public function actionRelatedContent($id)
    {
        return Content::getRelated($this->site->id, $id);
    }

    public
    function actionFavorite($content_id)
    {
        $msisdn = VNPHelper::getMsisdn(false, true);
        $subscriber = null;
        if ($msisdn) {
            $subscriber = \api\models\Subscriber::findByMsisdn($msisdn, $this->site->id);
            return ['message' => $subscriber->favorite($content_id, $this->site->id)];
        }
    }

    public
    function actionUnfavorite($content_id)
    {
        $msisdn = VNPHelper::getMsisdn(false, true);
        $subscriber = null;
        if ($msisdn) {
            $subscriber = \api\models\Subscriber::findByMsisdn($msisdn, $this->site->id);
            return ['message' => $subscriber->unfavorite($content_id, $this->site->id)];
        }
    }
    */
    public function actionComment($id)
    {
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }
        $content = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $id,'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$content) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }

        $params = Yii::$app->request->getBodyParams();
        $title = isset($params['title']) ? ($params['title']) : '';
        $cmt = isset($params['content']) ? ($params['content']) : '';
        $like = isset($params['like']) ? ($params['like']) : 0;
        $rate = isset($params['rate']) ? ($params['rate']) : '';
        $comment = ContentFeedback::createFeedback($content, $subscriber, $title, $cmt, $like, $rate);
        if (!$comment) {
            throw new InvalidValueException(Message::MSG_FAIL);
        }
        return [
            'status' => true,
            'message' => Message::MSG_SUCCESS,
        ];
    }

    /*
    public
    function actionComments($content_id)
    {
        $msisdn = VNPHelper::getMsisdn(false, true);
        $subscriber = null;
        if ($msisdn) {
            $subscriber = \api\models\Subscriber::findByMsisdn($msisdn, $this->site->id);
            return $subscriber->comments($this->site->id, $content_id);
        } else {
            return ['message' => 'NO COMMENT'];
        }
    }

    public
    function actionAddViewCount()
    {
        $content_id = $this->getParameterPost('content_id', 0);
        $view_count = $this->getParameterPost('count_view', 0);
        $result = Content::addViewCount($content_id, $view_count);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    */
    /**
     * @param $id
     * @param $quality
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionGetUrl($id, $quality)
    {
        //Validate
        if (!is_numeric($id)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['id']));
        }

        if (!is_numeric($quality)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['quality']));
        }
        /** @var $subscriber Subscriber */
        $subscriber = Yii::$app->user->identity;
        if (!$subscriber) {
            throw new InvalidValueException(Message::MSG_ACCESS_DENNY);
        }

        /** @var $content Content */
        $content = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $id,'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$content) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }

        /** check video is_free or not */
        if($content->getIsFree($this->site->id) == Content::NOT_FREE){
            $purchase = Subscriber::validatePurchasing($subscriber->id, $content->id);
            if (!$purchase) {
                throw new NotFoundHttpException(Message::MSG_EXPIRED_SERVICE);
            }
        }

        /** get link */
        /** @var $contentProfile ContentProfile */
        $contentProfile = ContentProfile::findOne(['content_id'=>$id,'quality' => $quality ,'status' => ContentProfile::STATUS_ACTIVE]);
        if (!$contentProfile) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT_PROFILE);
        }

        $res = Content::getUrl($contentProfile, $this->site->id);
        if (!$res['success']) {
            throw new NotFoundHttpException($res['message']);
        }
        /** Tăng View Count */
        $content->view_count++;
        $content->update();

        $data['items'] = $res;
        return $data;
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionGetActor(){
        $searchModel = new ActorDirectorSearch();
        $param = Yii::$app->request->queryParams;
        $searchModel->type = ActorDirector::TYPE_ACTOR;
        $searchModel->status = ActorDirector::STATUS_ACTIVE;
        $searchModel->content_type = ActorDirector::TYPE_KARAOKE;

        $dataProvider = $searchModel->search($param);
        return $dataProvider;
    }

    public function actionGetDirector(){
        $searchModel = new ActorDirectorSearch();
        $param = Yii::$app->request->queryParams;
        $searchModel->type = ActorDirector::TYPE_DIRECTOR;
        $searchModel->status = ActorDirector::STATUS_ACTIVE;
        $searchModel->content_type = ActorDirector::TYPE_KARAOKE;

        $dataProvider = $searchModel->search($param);
        return $dataProvider;
    }

    /**
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function actionGetVersionApi(){
        $model = ApiVersion::findOne(['type'=>ApiVersion::TYPE_KARAOKE,'site_id' =>$this->site->id]);
        if(!$model){
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }
        return $model;
    }


    public function actionGetAdventisment(){
        return ["message" => Message::MSG_ADVENTISMENT];
    }

//    /**
//     * HungNV
//     * @param $id
//     * @return array
//     * @throws NotFoundHttpException
//     */
//    public
//    function actionGetQualities($id)
//    {
//        //validate
//        if (!is_numeric($id)) {
//            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['id']));
//        }
//        /** @var $content */
//        $content = Content::findOne(['id' => $id, 'status' => Content::STATUS_ACTIVE]);
//        if (!$content) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }
//        /** get all available content profiles of a content */
//        $profile = ContentProfile::findAll(['content_id' => $id, 'status' => ContentProfile::STATUS_ACTIVE]);
//        if (!$profile) {
//            return [
//                'message' => Message::CONTENT_PROFILE_NOT_FOUND,
//            ];
//        }
//        foreach ($profile as $value) {
//            $arr['content_id'] = $value['content_id'];
//            $arr['quality'] = $value['quality'];
//            $arr['display'] = ContentProfile::$stream_quality[$value['quality']];
////            $arr['subtitle'] = $value['sub_url'];
//            $data[] = $arr;
//        }
//        $res['items'] = $data;
//        return $res;
//    }

    /**
     * HungNV edition: 31/03/16: GET LIST OF LIVES & SUB CONTENT LIVES (parent)
     * @return \yii\data\ActiveDataProvider
     */
//    public function actionGetLives()
//    {
//        $parent_id = $this->getParameter('parent', '');
//        $live = Content::getLives(Content::TYPE_LIVE, Content::ORDER_NEWEST, $parent_id);
//        return $live;
//    }

    /**
     * HungNV
     * @return ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function actionComments($id)
    {
        /**
         * comments under channel or content
         */
        if (!is_numeric($id)) {
            throw new InvalidValueException(Message::MSG_NUMBER_ONLY);
        }
        /** @var $content Content */
//        $content = Content::findOne(['id' => $id, 'status' => Content::STATUS_ACTIVE]);
//        if (!$content) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }

        $content = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $id,'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$content) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }
        $comment = $content->getContentFeedbacks();
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = $comment;
        return $dataProvider;
//        $query = new Query();
//        $query->select(['subscriber.msisdn','content.display_name', 'content_feedback.*'])
//            ->from('content_feedback')
//            ->innerJoin('content', 'content.id = content_feedback.content_id')
//            ->innerJoin('subscriber', 'subscriber.id = content_feedback.subscriber_id');
//        if ($id) {
//            $content = Content::findOne(['id' => $id]);
//            if (!$content) {
//                throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//            }
//            $query->andWhere(['content.id' => $id]);
//        }
//        $query->andWhere(['IS', 'content.parent_id', null])
//            ->orderBy('content_feedback.created_at');
//        $query->all();
//
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//            'sort' => [],
//            'pagination' => [
//                'defaultPageSize' => 10,
//            ]
//        ]);
//        return $dataProvider;
    }

    /**
     * HungNV
     * @return array|\yii\db\ActiveRecord[]
     */
//    public function actionCatchupChannels()
//    {
//        $channel = new LiveProgram();
//        $res = $channel->getChannels();
//        if (!$res['status']) {
//            throw new InvalidValueException($res['message']);
//        }
//        $data['items'] = $res['items'];
//        return $data;
//
//    }

    /**
     * @param $channel_id
     * @param int $days
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionListDays($channel_id, $days = 7)
    {
//        $channel = Content::find()
//            ->innerJoin('user', 'user.id = content.created_user_id')
//            ->andWhere(['user.site_id' => $this->site->id])
//            ->andWhere(['content.id' => $channel_id])
//            ->andWhere(['content.status' => Content::STATUS_ACTIVE])
//            ->one();
//        if (!$channel) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }

        $content = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $this->site->id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $channel_id,'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$content) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }

        /** @var $beginTime \DateTime */
//        $beginTime = new \DateTime();
//        $beginTime->setTime(0, 0, 0);
//        $endTime = new \DateTime();
//        $endTime->setTime(23, 59, 59);
        $today = time();
        for ($i = -$days; $i < 0; $i++) {
            $days = $today + 86400 * ($i + 1);
//            $begin = $beginTime->getTimestamp() + 86400 * ($i + 1);
//            $end = $endTime->getTimestamp() + 86400 * ($i + 1);
            $day = date("D", $days);
            $ngay = '';
            switch ($day) {
                case 'Mon':
                    $ngay = "Thứ hai";
                    break;
                case 'Tue':
                    $ngay = "Thứ ba";
                    break;
                case 'Wed':
                    $ngay = "Thứ tư";
                    break;
                case 'Thu':
                    $ngay = "Thứ năm";
                    break;
                case 'Fri':
                    $ngay = "Thứ sáu";
                    break;
                case 'Sat':
                    $ngay = "Thứ bảy";
                    break;
                case 'Sun':
                    $ngay = "Chủ nhật";
                    break;
            }
            $res[] = [
                'id' => $content->id,
                'display_name' => $content->display_name,
                'day' => $ngay,
                'datetime' => date("Y-m-d", $days),
//                'beginTime' => $begin,
//                'endTime' => $end,
            ];
        }
        $data['items'] = $res;
        return $data;
    }

    /**
     * @param $channel_id
     * @param null $date
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCatchup($channel_id, $date = null){
        $site_id = $this->site->id;
        if (!is_numeric($channel_id)) {
            throw new InvalidValueException($this->replaceParam(Message::MSG_NUMBER_ONLY, ['channel_id']));
        }
        $channel = Content::find()
            ->joinWith('contentSiteAsms')
            ->andWhere(['content_site_asm.site_id' => $site_id,'content_site_asm.status'=>ContentSiteAsm::STATUS_ACTIVE])
            ->andWhere(['content.id' => $channel_id,'content.status' => Content::STATUS_ACTIVE])
            ->one();
        if (!$channel) {
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }
        if ($date) {
            if(!CUtils::validateDate($date)){
                throw new InvalidValueException(Message::MSG_NOT_VALIDATE_DATE);
            }
            $begin = new \DateTime($date);
            $begin->setTime(0, 0, 0);
            $fromTimeDefault = $begin->getTimestamp();
            $end = new \DateTime($date);
            $end->setTime(23, 59, 59);
            $toTimeDefault = $end->getTimestamp();
        } else {
            $begin = new \DateTime("today");
            $begin->setTime(0, 0, 0);
            $fromTimeDefault = $begin->getTimestamp();
            $end = new \DateTime("today");
            $end->setTime(23, 59, 59);
            $toTimeDefault = $end->getTimestamp();
        }
        $res = LiveProgram::getEpg($channel_id,$fromTimeDefault,$toTimeDefault,$site_id);
        if(count($res)<=0){
            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }
        return $res;


    }


    /**
     * @param $content_id
     * @return array
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionSyncContentToSite(){
        $data = Yii::$app->request->post('data', null);
        $json_data = json_decode($data);
        $request_id =  $json_data->request_id;
        $content_id =  $json_data->content_id;
        $site_id =  $json_data->site_id;
        $contentProfiles =  $json_data->data;
        $token =  $json_data->token;

        $tokent_validate = md5($request_id.$site_id);
        /** Kiểm tra tokent */
        if($token !== $tokent_validate){
            throw new ServerErrorHttpException(Message::MSG_VERIFY_TOKEN_WRONG);
        }

        $count = 0;
        foreach($contentProfiles as $contentProfile){
            if($contentProfile->success){
                $count++;
            }
            ContentProfileSiteAsm::createContentProfileSiteAsm($contentProfile->content_profile_id,$contentProfile->cdn_content_id,$site_id,$contentProfile->success?ContentProfileSiteAsm::STATUS_ACTIVE:ContentProfileSiteAsm::STATUS_INACTIVE);
        }
        if($count == 0){
            throw new ServerErrorHttpException(Message::MSG_FAIL);
        }

        /** Nếu thành công hết cả các contentProfile thì mới update  ContentSiteAsm STATUS_ACTIVE */
        /** @var  $cssa ContentSiteAsm*/
        $cssa = ContentSiteAsm::findOne(['content_id'=>$content_id, 'site_id'=>$site_id]);
        if(!$cssa){
            throw new ServerErrorHttpException(Message::MSG_NOT_FOUND_CONTENT);
        }

        $is_check = Content::checkQualityWhenDownload($content_id,$site_id);

        if($is_check){
            $cssa->status = ContentSiteAsm::STATUS_ACTIVE;
            if(!$cssa->save()){
                throw new ServerErrorHttpException(Message::MSG_ERROR_SYSTEM);
            }
        }else{
            $cssa->status = ContentSiteAsm::STATUS_TRANSFER_ERROR;
            if(!$cssa->save()){
                throw new ServerErrorHttpException(Message::MSG_ERROR_SYSTEM);
            }
            /** Hiển thị message update lỗi chưa phân phổi đủ chất lượng video */
            throw new ServerErrorHttpException(Message::MSG_TRANSFER_ERROR);
        }
        /** Tất cả các bản ghi thành công thì báo là thành công */
        return ['message' => Message::MSG_SUCCESS];
    }

//    public function actionSyncContentToSite(){
//        $data = Yii::$app->request->post('data', null);
//        $json_data = json_decode($data);
//        $request_id =  $json_data->request_id;
//        $content_id =  $json_data->content_id;
//        $site_id =  $json_data->site_id;
//        $contentProfiles =  $json_data->data;
//        $token =  $json_data->token;
//        /** Khởi tạo biến với mục đích check */
//        $content_profile_id = 0;
//
//        $tokent_validate = md5($request_id.$site_id);
//        /** Kiểm tra tokent */
//        if($token !== $tokent_validate){
//            throw new ServerErrorHttpException(Message::MSG_VERIFY_TOKEN_WRONG);
//        }
//
//        $count = 0;
//        foreach($contentProfiles as $contentProfile){
//            if($contentProfile->success){
//                $count++;
//            }
//            /** lấy giá trị content_profile_id vì data trả thằng này ở trong mảng, chỉ cần lấy 1 lần, kiểm tra nếu !=0 thì mới lấy*/
//            if(!$content_profile_id){
//                $content_profile_id = $contentProfile->content_profile_id;
//            }
//            ContentProfileSiteAsm::createContentProfileSiteAsm($contentProfile->content_profile_id,$contentProfile->cdn_content_id,$site_id,$contentProfile->success?ContentProfileSiteAsm::STATUS_ACTIVE:ContentProfileSiteAsm::STATUS_INACTIVE);
//        }
//        if($count == 0){
//            throw new ServerErrorHttpException(Message::MSG_FAIL);
//        }
//        /** Lấy total ContentProfileSiteAsm để kiểm tra xem đã download xong hết chưa */
//        $totalCount = ContentProfileSiteAsm::find()->andWhere(['content_profile_id'=>$content_profile_id, 'site_id'=>$site_id])->count();
//        $totalCountSuccess = ContentProfileSiteAsm::find()->andWhere(['content_profile_id'=>$content_profile_id, 'site_id'=>$site_id,'status'=>ContentProfileSiteAsm::STATUS_ACTIVE])->count();
//        /** Nếu thành công hết cả các contentProfile thì mới update  ContentSiteAsm STATUS_ACTIVE */
//        /** @var  $cssa ContentSiteAsm*/
//        $cssa = ContentSiteAsm::findOne(['content_id'=>$content_id, 'site_id'=>$site_id]);
//        if(!$cssa){
//            throw new ServerErrorHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }
//        if($totalCountSuccess == $totalCount){
//            $cssa->status = ContentSiteAsm::STATUS_ACTIVE;
//            if(!$cssa->save()){
//                throw new ServerErrorHttpException(Message::MSG_FAIL);
//            }
//        }else{
//            $cssa->status = ContentSiteAsm::STATUS_TRANSFER_ERROR;
//            if(!$cssa->save()){
//                throw new ServerErrorHttpException(Message::MSG_FAIL);
//            }
//            /** Hiển thị message update lỗi chưa phân phổi đủ chất lượng video */
//            throw new ServerErrorHttpException(Message::MSG_TRANSFER_ERROR);
//        }
//        /** Chỉ cần có 1 bản ghi thành công thì báo là thành công */
//        return ['message' => Message::MSG_SUCCESS];
//    }

    public function actionSyncDataToSite(){
        $site_id = $this->site->id;
        $lst = Content::syncDataToSite(6,24);
//        $lst = Content::syncContentToSite(6,35304,24);
        var_dump($lst);
//        echo $lst;
    }


    /**
     * @param $channel_id
     * @param $from
     * @param $to
     * @param null $date
     * @return bool|ActiveDataProvider
     * @throws NotFoundHttpException
     */
//    public function actionCatchup($channel_id, $date = null)
//    {
//        /**
//         * Url available or not differentiated by status (ex: 2 is recorded and available)
//         */
//        if ($channel_id) {
//            $channel = Content::find()
//                ->innerJoin('user', 'user.id = content.created_user_id')
//                ->andWhere(['user.site_id' => $this->site->id])
//                ->andWhere(['content.id' => $channel_id])
//                ->andWhere(['content.status' => Content::STATUS_ACTIVE])
//                ->one();
//            if (!$channel) {
//                throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//            }
//        }
//
//        if ($date) {
//            /**
//             * validate $date
//             */
//            if (!ContentSearch::validateDate($date, "YYYY-MM-DD")) {
//                throw new InvalidValueException("Invalid date format");
//            }
//
//            $begin = new \DateTime($date);
//            $begin->setTime(0, 0, 0);
//            $fromTimeDefault = $begin->getTimestamp();
//            $end = new \DateTime($date);
//            $end->setTime(23, 59, 59);
//            $toTimeDefault = $end->getTimestamp();
//            $from = $fromTimeDefault;
//            $to = $toTimeDefault;
//        } else {
//            $begin = new \DateTime("today");
//            $begin->setTime(0, 0, 0);
//            $fromTimeDefault = $begin->getTimestamp();
//            $end = new \DateTime("today");
//            $end->setTime(23, 59, 59);
//            $toTimeDefault = $end->getTimestamp();
//            $from = $fromTimeDefault;
//            $to = $toTimeDefault;
//        }
////        echo $to;exit;
//        /** @var $catch LiveProgram */
//        $catch = LiveProgram::getListCatchup($channel_id, null, Content::ORDER_NEWEST, $from, $to);
//        /** validate */
//        if (!$catch) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }
//        return $catch;
//    }

    /**
     * TODO - cần xem lại..thiết kế db - using to list all singer or author KARAOKE
     *
     * @param $name
     * @return mixed
     * @throws NotFoundHttpException
     */
//    public function actionContentAttributes($name)
//    {
//        $data = ContentAttributeValue::getListByFilter($name);
//        if (!$data['status']) {
//            throw new NotFoundHttpException($data['message']);
//        }
//        return $data['dataProvider'];
//    }

//    /**
//     * @param $param
//     * @return ActiveDataProvider
//     * @throws NotFoundHttpException
//     */
//    public function actionKaraokeSearch($type = Content::IS_SINGER, $name = null)
//    {
//        $res = ContentSearch::filterValues($type, Content::TYPE_KARAOKE, $name);
//        if (!$res) {
//            throw new NotFoundHttpException(Message::MSG_NOT_FOUND_CONTENT);
//        }
//        return $res;
//    }

//    /**
//     * @return ActiveDataProvider
//     */
//    public function actionKaraoke()
//    {
//        $params = Yii::$app->request->queryParams;
//        $searchModel = new ContentSearch();
//        $searchModel->type = Content::TYPE_KARAOKE;
//        $searchModel->actor = isset($params['actor']) ? ($params['actor']) : '';
//        $searchModel->author = isset($params['author']) ? ($params['author']) : '';
//        if (!$searchModel->validate()) {
//            $errors = $searchModel->firstErrors;
//            $message = '';
//            foreach ($errors as $value) {
//                $message = $value;
//                break;
//            }
//            throw new InvalidValueException($message);
//        }
//        $dataProvider = $searchModel->search($params);
//        return $dataProvider;
//    }


    public function actionTest()
    {
        //
    }
}