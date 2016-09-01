<?php
/**
 * Created by PhpStorm.
 * User: bibon
 * Date: 3/22/2016
 * Time: 4:55 PM
 */

namespace console\controllers;


use common\helpers\CVietnameseTools;
use common\helpers\MyCurl;
use common\models\ActorDirector;
use common\models\Category;
use common\models\Content;
use common\models\ContentActorDirectorAsm;
use common\models\ContentCategoryAsm;
use common\models\ContentProfile;
use common\models\ContentProfileSiteAsm;
use common\models\ContentRelatedAsm;
use common\models\ContentSiteAsm;
use common\models\LiveProgram;
use common\models\MigrateStatus;
use common\models\Site;
use common\models\User;
use console\models\ChannelLive;
use console\models\FieldDataBody;
use console\models\FieldDataFieldCategory;
use console\models\FieldDataFieldContentId;
use console\models\FieldDataFieldDramaCatagory;
use console\models\FieldDataFieldDramaPicture;
use console\models\FieldDataFieldFeature;
use console\models\FieldDataFieldLiveCategories;
use console\models\FieldDataFieldNewsCategory;
use console\models\FieldDataFieldRelatedContent;
use console\models\FieldDataFieldResolution;
use console\models\FieldDataFieldSeriesPictureSlideshow;
use console\models\FieldDataFieldSeriesTitleVi;
use console\models\FieldDataFieldSubtitle;
use console\models\FieldDataFieldTime;
use console\models\FieldDataFieldTitleVi;
use console\models\FieldDataFieldType;
use console\models\FieldDataFieldVideoActor;
use console\models\FieldDataFieldVideoDirector;
use console\models\FieldDataFieldVideoManufacturer;
use console\models\FieldDataFieldVideoNational;
use console\models\FieldDataFieldVideoVersionRef;
use console\models\FileManaged;
use console\models\FieldDataFieldVideoPicture;
use console\models\FieldDataFieldPictureSlideshow;
use console\models\FieldDataFieldChannelPicture;
use console\models\MapLive;
use console\models\Node;
use console\models\TaxonomyTermData;
use console\models\tvcatchup\HistoryContent;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\VarDumper;


class MigrateContentController extends Controller
{

    const TIMEOUT = 43200000; // 12 HOURS

    public function actionRun()
    {
//        $this->clearAllData();

//        $this->fixLiveChannelQualities();

        $this->migrateContents();
        $this->migrateContentProfiles();
        $this->migrateLiveChannels();
        $this->migrateChannelPictureContents();
        /** Chỉ cần chạy 1 lần update lại field Content::default_category_id  phục vụ cho báo cáo*/
//        $this->actionUpdateDefaultCategoryIdContent();

//        $this->updateEnNames();
//        $this->fixLiveChannelsStatus();
//        $this->fixLiveChannelProfilesStatus();

        //        $this->migrateLostContentProfiles();
//        $this->migratePictureLiveContents();
//        $this->migratePictureContents();
//        $this->updateContentCategories();
//        $this->fixActorDirectorAsm();
//        $this->migrateQualityContents();
//        $this->fixVideoUrls();
//        $this->fixSeriesPictureContents();
//        $this->fixChannelPictureContents();
    }

    private function fixLiveChannelsStatus()
    {
        $channels = Content::find()
            ->where(['type' => Content::TYPE_LIVE])
            ->andWhere(['not', ['tvod1_id' => null]])
            ->all();
        foreach ($channels as $channel) {
            $channel->status = $this->getChannelStatus($channel->tvod1_id);
            $channel->save(false, ['status']);
        }
    }

    private function fixLiveChannelQualities()
    {
        $channels = Content::find()
            ->where(['type' => Content::TYPE_LIVE])
            ->andWhere(['not', ['tvod1_id' => null]])
            ->all();
        foreach ($channels as $channel) {
            $contentProfiles = ContentProfile::find()->where(['content_id' => $channel->id])->all();
            foreach ($contentProfiles as $contentProfile) {
                $node = MapLive::findOne(['version_id' => $contentProfile->tvod1_id]);
                if (!$node) {
                    continue;
                }
                echo "\nProfile " . $contentProfile->id . ': ' . $node->type;
                $contentProfile->quality = $this->getChannelQuality($node->type);
                echo " => " . $contentProfile->quality;
                if (!$contentProfile->save(false, ['quality'])) {
                    VarDumper::dump($contentProfile->errors);
                }
            }

        }
    }

    private function fixLiveChannelProfilesStatus()
    {
        $channels = Content::find()
            ->where(['type' => Content::TYPE_LIVE])
            ->andWhere(['not', ['tvod1_id' => null]])
            ->all();
        foreach ($channels as $channel) {
            $contentProfiles = ContentProfile::find()->where(['content_id' => $channel->id])->all();
            foreach ($contentProfiles as $contentProfile) {
                $node = Node::findOne(['nid' => $contentProfile->tvod1_id]);
                if (!$node) {
                    continue;
                }
                $contentProfile->status = $node->status == 1 ? ContentProfile::STATUS_ACTIVE : ContentProfile::STATUS_INACTIVE;
                $contentProfile->save(false, ['status']);
                $contentProfileSites = ContentProfileSiteAsm::find()->where(['content_profile_id' => $contentProfile->id])->all();
                foreach ($contentProfileSites as $contentProfileSite) {
                    $contentProfileSite->status = $contentProfile->status == ContentProfile::STATUS_ACTIVE ? ContentProfileSiteAsm::STATUS_ACTIVE : ContentProfileSiteAsm::STATUS_INACTIVE;
                    $contentProfileSite->save(false, ['status']);
                }
            }

        }
    }

    private function updateEnNames()
    {
        // Cap nhat so thu tu phim bo
        $contents = Content::find()
            ->where(['not', ['tvod1_id' => null]])
            ->all();
        foreach ($contents as $content) {
            $node = Node::findOne(['nid' => $content->tvod1_id]);
            if ($node) {
                $content->en_name = $node->title;
                $content->save(false, ['en_name']);
            }
        }
    }

    private function fixSeriesPictureContents()
    {
        echo "\n********** Begin get contents ***********\n";
        $batchQueryResult = Node::find()->where(['type' => ['drama']])->batch(1000);
        echo "\n********** Begin migrating nodes ***********\n";
        if ($batchQueryResult) {
            foreach ($batchQueryResult as $nodes) {
                foreach ($nodes as $node) {
                    if ($this->checkContentExisted($node->nid)) {
                        $content = Content::findOne(['tvod1_id' => $node->nid]);
                        $content->images = $this->getImages($node->nid, $node->type);
                        if (!$content->save(true, ['images'])) {
                            VarDumper::dump($content->errors);
                            echo "migratePictureContents Error: cannot save content $content->id\n";
                        } else {
                            echo "Excute $content->id\n";
                        }
                    }
                }
            }
        }
    }

    private function migrateChannelPictureContents()
    {
        $channels = Content::find()->where(['type' => 2, "images" => null])->all();
        foreach ($channels as $channel) {
            $contentProfile = ContentProfile::find()->where(['content_id' => $channel->id, 'quality' => ContentProfile::QUALITY_HD])
                ->one();
            if ($contentProfile) {
                $images = $this->getImages($contentProfile->tvod1_id, 'live_streaming');
                if ($images) {
                    $channel->images = $images;
                    $channel->save(true, ['images']);
                    continue;
                }
            }
            $contentProfile = ContentProfile::find()->where(['content_id' => $channel->id,
                'quality' => ContentProfile::QUALITY_SD])
                ->one();
            if ($contentProfile) {
                $images = $this->getImages($contentProfile->tvod1_id, 'live_streaming');
                if ($images) {
                    $channel->images = $images;
                    $channel->save(true, ['images']);
                    continue;
                }
            }
            if ($contentProfile) {
                $contentProfile = ContentProfile::find()->where(['content_id' => $channel->id,
                    'quality' => ContentProfile::QUALITY_MB])
                    ->one();
                $images = $this->getImages($contentProfile->tvod1_id, 'live_streaming');
                if ($images) {
                    $channel->images = $images;
                    $channel->save(true, ['images']);
                }
            }
        }

    }

    private function fixVideoUrls()
    {
        $contentProfiles = ContentProfile::find()
            ->where(['not', ['tvod1_id' => null]])
            ->all();
        foreach ($contentProfiles as $contentProfile) {
            $contentProfileSiteAsm = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id]);
            $contentProfileSiteAsm->url = $this->getContentProfileUrl($contentProfile->tvod1_id);
            $contentProfileSiteAsm->save(false, ['url']);
        }
    }

    private function migrateQualityContents()
    {
        echo "\n********** Begin updating quality contents ***********\n";
        $contentProfilesBatch = ContentProfile::find()
//            ->where(['quality' => null])
            ->batch(1000);
        foreach ($contentProfilesBatch as $contentProfiles) {
            foreach ($contentProfiles as $contentProfile) {
                $contentProfile->quality = $this->getProfileQuality($contentProfile->tvod1_id);
                $contentProfile->save(false, ['quality']);
            }
        }
        echo "\n********** End updating quality contents  ***********\n";
    }

    private function clearAllData()
    {
        ContentProfileSiteAsm::deleteAll();
        ContentSiteAsm::deleteAll();
        ContentCategoryAsm::deleteAll();
        ContentProfile::deleteAll();
        LiveProgram::deleteAll();
        Content::deleteAll();
    }

    private function fixActorDirectorAsm()
    {
        $contentsBatch = Content::find()
            ->batch(1000);
        foreach ($contentsBatch as $contents) {
            foreach ($contents as $content) {
                if (!$content->tvod1_id) {
                    continue;
                }
                $actorIds = $this->getActors($content->tvod1_id, $content->type);
                if ($actorIds) {
                    foreach ($actorIds as $actorId) {
                        $contentActorAsm = new ContentActorDirectorAsm();
                        $contentActorAsm->content_id = $content->id;
                        $contentActorAsm->actor_director_id = $actorId;
                        $contentActorAsm->created_at = time();

                        $contentActorDirectorAsmRows[] = $contentActorAsm->attributes;
                    }
                }

                $directorIds = $this->getDirectors($content->tvod1_id, $content->type);
                if ($directorIds) {
                    foreach ($directorIds as $directorId) {
                        $contentDirectorAsm = new ContentActorDirectorAsm();
                        $contentDirectorAsm->content_id = $content->id;
                        $contentDirectorAsm->actor_director_id = $directorId;
                        $contentDirectorAsm->created_at = time();

                        $contentActorDirectorAsmRows[] = $contentDirectorAsm->attributes;
                    }
                }
            }
            if ($contentActorDirectorAsmRows) {
                Yii::$app->db->createCommand()->batchInsert(ContentActorDirectorAsm::tableName(), (new ContentActorDirectorAsm())->attributes(), $contentActorDirectorAsmRows)->execute();
            }
            $contentActorDirectorAsmRows = null;
        }
    }

    private function migrateRelatedContents($maxId = 0)
    {
        $relatedContents = FieldDataFieldRelatedContent::find()
            ->select(['entity_id', 'field_related_content_nid'])
            ->where(['>', 'entity_id', $maxId])
            ->distinct(true)
            ->all();
        foreach ($relatedContents as $relatedContent) {
            $content1 = Content::findOne(['tvod1_id' => $relatedContent->entity_id]);
            if ($content1) {
                $content2 = Content::findOne(['tvod1_id' => $relatedContent->field_related_content_nid]);
                if ($content2) {
                    $existed = ContentRelatedAsm::find()->where(['content_id' => $content1->id, 'content_related_id' => $content2->id])
//                        ->orWhere(['content_id' => $content2->id, 'content_related_id' => $content1->id])
                        ->one();
                    if (!$existed) {
                        $contentRelatedAsm = new ContentRelatedAsm();
                        $contentRelatedAsm->content_id = $content1->id;
                        $contentRelatedAsm->content_related_id = $content2->id;
                        $contentRelatedAsm->created_at = time();
                        $contentRelatedAsm->updated_at = time();
                        $contentRelatedAsm->save();
                    }
                }
            }
        }
    }

    private function migratePictureLiveContents()
    {
        echo "\n********** Begin get contents ***********\n";
        $batchQueryResult = Node::find()->where(['type' => ['live_streaming']])->batch(1000);
        echo "\n********** Begin migrating nodes ***********\n";
        if ($batchQueryResult) {
            foreach ($batchQueryResult as $nodes) {
                foreach ($nodes as $node) {
                    if ($this->checkContentExisted($node->nid)) {
                        $content = Content::findOne(['tvod1_id' => $node->nid]);
                        $content->images = $this->getLiveImages($node->nid);
                        if (!$content->save(true, ['images'])) {
                            VarDumper::dump($content->errors);
                            echo "migratePictureContents Error: cannot save content $content->id\n";
                        } else {
                            echo "Excute $content->id\n";
                        }
                    }
                }
            }
        }
    }

    private function migratePictureContents()
    {
        echo "\n********** Begin get contents ***********\n";
        $batchQueryResult = Node::find()->where(['type' => ['drama', 'video', 'news', 'embedded_video']])->batch(1000);
        echo "\n********** Begin migrating nodes ***********\n";
        if ($batchQueryResult) {
            foreach ($batchQueryResult as $nodes) {
                foreach ($nodes as $node) {
                    if ($this->checkContentExisted($node->nid)) {
                        $content = Content::findOne(['tvod1_id' => $node->nid]);
                        $content->images = $this->getImages($node->nid, $node->type);
                        if (!$content->save(true, ['images'])) {
                            VarDumper::dump($content->errors);
                            echo "migratePictureContents Error: cannot save content $content->id\n";
                        } else {
                            echo "Excute $content->id\n";
                        }
                    }
                }
            }
        }
    }

    private function migrateContents()
    {
        echo "\n********** Begin migrating contents ***********\n";

        $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_VIDEO);
        if ($migrateStatus) {
            if ($migrateStatus->started_at > time() - self::TIMEOUT) {
                echo 'Existed running content migration process';
                return;
            } else {
                $migrateStatus->finish(MigrateStatus::STATUS_FAIL, 0, 'Timeout');
            }
        }

        $migrateStatus = MigrateStatus::getLastSuccessMigration(MigrateStatus::TYPE_VIDEO);
        $newMaxId = $maxId = $migrateStatus ? $migrateStatus->max_id : 0;
        $lastMigratedAt = $migrateStatus ? $migrateStatus->started_at : 0;

        MigrateStatus::createOrFinish(MigrateStatus::TYPE_VIDEO);

        try {
            $batchQueryResult = Node::find()
                ->where(['type' => ['drama', 'video', 'news', 'embedded_video']])
                ->andWhere(['>', 'changed', $lastMigratedAt])
                ->batch(1000);
            $siteId = $this->getSiteId();
            if (!$siteId) {
                echo 'Error: NOT FOUND Site';
                return;
            }
            $adminId = $this->getAdminId();
            if (!$adminId) {
                echo 'Error: NOT FOUND Admin';
                return;
            }

            if ($batchQueryResult) {
                foreach ($batchQueryResult as $nodes) {
                    foreach ($nodes as $node) {
                        $content = $this->migrateContent($node, $adminId, $siteId);
                        if ($content && $content->tvod1_id > $newMaxId) {
                            $newMaxId = $content->tvod1_id;
                        }
                    }
                }
            }
            $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_VIDEO);
            $migrateStatus->finish(MigrateStatus::STATUS_SUCCESS, $newMaxId);
            $this->updateIsSeries();
            $this->migrateRelatedContents($maxId);
            echo "\n********** Migrated contents successfully ***********\n";
        } catch (Exception $e) {
            echo "\n********** Migrated contents failed: " . $e->getMessage() ." ***********\n";
            $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_VIDEO);
            if ($migrateStatus) {
                $migrateStatus->finish(MigrateStatus::STATUS_FAIL, 0, $e->getMessage());
            }
        }
    }

    private function migrateLiveChannels()
    {
        echo "\n********** Begin migrating live channels ***********\n";

        $adminId = $this->getAdminId();
        $siteId = $this->getSiteId();
        $liveChannels = ChannelLive::find()->all();

        $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_CHANNEL_VERSION);
        if ($migrateStatus) {
            if ($migrateStatus->started_at > time() - self::TIMEOUT) {
                echo 'Existed running live channels migration process';
                return;
            } else {
                $migrateStatus->finish(MigrateStatus::STATUS_FAIL, 0, 'Timeout');
            }
        }

        $migrateStatus = MigrateStatus::getLastSuccessMigration(MigrateStatus::TYPE_CHANNEL_VERSION);
        $newMaxId = $maxId = $migrateStatus ? $migrateStatus->max_id : 0;
        $lastMigratedAt = $migrateStatus ? $migrateStatus->started_at : 0;

        MigrateStatus::createOrFinish(MigrateStatus::TYPE_CHANNEL_VERSION);

        try {
            foreach ($liveChannels as $channel) {
                $content = $this->migrateChannel($channel, $adminId, $siteId);
                if (!$content) {
                    continue;
                }
                $newMaxId = $this->migrateChannelProfiles($content, $siteId, $lastMigratedAt, $newMaxId);
            }
            $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_CHANNEL_VERSION);
            $migrateStatus->finish(MigrateStatus::STATUS_SUCCESS, $newMaxId);
            echo "\n********** Migrated live channels successfully ***********\n";
        } catch (Exception $e) {
            echo "\n********** Migrated live channels failed: " . $e->getMessage() ." ***********\n";
            $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_CHANNEL_VERSION);
            if ($migrateStatus) {
                $migrateStatus->finish(MigrateStatus::STATUS_FAIL, 0, $e->getMessage());
            }
        }
    }


    private function getChannelStatus($channel_id)
    {
        $versions = MapLive::find()->where(['channel_id' => $channel_id])->all();
        foreach ($versions as $version) {
            $node = Node::findOne(['nid' => $version->version_id]);
            if (!$node) {
                continue;
            }
            if ($node->status == 1) {
                return Content::STATUS_ACTIVE;
            }
        }
        return Content::STATUS_INVISIBLE;
    }

    private function getChannelQuality($type)
    {
        if ($type == 1) {
            return ContentProfile::QUALITY_MB;
        }
        if ($type == 3) {
            return ContentProfile::QUALITY_HD;
        }
        if ($type == 4) {
            return ContentProfile::QUALITY_H265;
        }
        return ContentProfile::QUALITY_SD;
    }

    private function migrateChannelProfiles($content, $siteId, $lastMigratedAt = 0, $newMaxId = 0)
    {
        $channelProfiles = MapLive::find()->where(['channel_id' => $content->tvod1_id])->all();
        foreach ($channelProfiles as $channelProfile) {
            $node = Node::find()
                ->where(['nid' => $channelProfile->version_id])
                ->andWhere(['>', 'changed', $lastMigratedAt])
                ->one();
            if (!$node) {
                continue;
            }

            $contentProfile = $this->getContentProfileByTvod1Id($node->nid);
            $new = !$contentProfile;
            if (!$new && $contentProfile->updated_at == $node->changed) {
                echo 'Not any change';
                continue;
            }

            if ($new) {
                $contentProfile = new ContentProfile();
                $contentProfile->content_id = $content->id;
                $contentProfile->tvod1_id = $channelProfile->version_id;
                $contentProfile->name = $channelProfile->live_title;
                $contentProfile->type = ContentProfile::TYPE_CDN;
                $contentProfile->quality = $this->getChannelQuality($channelProfile->type);
                $contentProfile->created_at = $node->created;
//                if (!$content->images) {
//                    $content->images = $this->getImages($channelProfile->version_id, 'live_streaming');
//                }
            }
            $contentProfile->status = $node->status == 1 ? ContentProfile::STATUS_ACTIVE : ContentProfile::STATUS_INACTIVE;
            $contentProfile->updated_at = $node->changed;
            $contentProfile->save(false);

            $contentProfileSiteAsm = $this->createContentProfileSiteAsm($contentProfile, $siteId);
            $contentProfileSiteAsm->save(false);

            if ($contentProfile && $contentProfile->tvod1_id > $newMaxId) {
                $newMaxId = $contentProfile->tvod1_id;
            }
        }
        return $newMaxId;

    }

    private function getLiveChannelCategory()
    {
        return Category::findOne(['type' => Category::TYPE_LIVE, 'status' => Category::STATUS_ACTIVE, 'parent_id' => null]);
    }

    private function migrateChannel($channel, $adminId, $siteId)
    {
        echo "\n\n\n=======Migrating channel: $channel->title =========\n";

        $content = Content::findOne(["type" => Content::TYPE_LIVE, "tvod1_id" => $channel->id]);

        if ($content) {
            return $content;
        }

        $category = $this->getLiveChannelCategory();
        if (!$category) {
            echo 'Error: NOT FOUND Category';
            return;
        }

        $content = new Content();
        $content->tvod1_id = $channel->id;
        $content->code = 'MSLC' . $channel->id; // To Do: bo sung quy luat sinh ma
        $content->display_name = $channel->title;
        $content->ascii_name = CVietnameseTools::removeSigns($content->display_name);
        $content->type = Content::TYPE_LIVE;
        $content->images = $this->getLiveImages($channel->id);

        $content->status = $this->getChannelStatus($channel->id);

        $content->created_at = time();
        $content->updated_at = time();
        $content->approved_at = time();
        $content->created_user_id = $adminId;

        $content->view_count = 0;
        $content->download_count = 0;
        $content->like_count = 0;
        $content->episode_count = 0;
        $content->dislike_count = 0;
        $content->favorite_count = 0;
        $content->rating_count = 0;
        $content->comment_count = 0;
        $content->rating = 0.0;

        $content->default_site_id = $siteId;
        $content->default_category_id = $category->id;

        if (!$content->save()) {
            Yii::error($content->errors);
            echo "Error: cannot save content $content->id \n";
            VarDumper::dump($content->errors);
            return;
        } else {

            $contentRows[] = $content->attributes;

            $contentCategoryAsm = new ContentCategoryAsm();
            $contentCategoryAsm->category_id = $category->id;
            $contentCategoryAsm->content_id = $content->id;
            $content->created_at = time();
            $contentCategoryAsmRows[] = $contentCategoryAsm->attributes;

            $contentSiteAsm = new ContentSiteAsm();
            $contentSiteAsm->site_id = $siteId;
            $contentSiteAsm->content_id = $content->id;
            $contentSiteAsm->status = $content->status == Content::STATUS_ACTIVE ? ContentSiteAsm::STATUS_ACTIVE : ContentSiteAsm::STATUS_INACTIVE;
            $contentSiteAsm->created_at = time();
            $contentSiteAsm->updated_at = time();
            $contentSiteAsmRows[] = $contentSiteAsm->attributes;
        }

        Yii::$app->db->createCommand()->batchInsert(ContentCategoryAsm::tableName(), (new ContentCategoryAsm())->attributes(), $contentCategoryAsmRows)->execute();
        Yii::$app->db->createCommand()->batchInsert(ContentSiteAsm::tableName(), (new ContentSiteAsm())->attributes(), $contentSiteAsmRows)->execute();

        return $content;
    }

    private function checkLiveChannelExisted($channelId)
    {
        return Content::find()->where(["type" => Content::TYPE_LIVE, "tvod1_id" => $channelId])->count() > 0;
    }

    private function updateContentCategories()
    {
        echo "\n********** Begin update categories contents ***********\n";
        $batchQueryResult = Content::find()
            ->leftJoin('content_category_asm asm', 'content.id=asm.content_id')
            ->where(['not', ['content.tvod1_id' => null]])
            ->andWhere(['asm.id' => null])
            ->batch(1000);
        echo "\n********** Begin migrating nodes ***********\n";
        if ($batchQueryResult) {
            foreach ($batchQueryResult as $contents) {
                foreach ($contents as $content) {
                    $node = Node::findOne(['nid' => $content->tvod1_id]);
                    $categoryId = $this->getCategoryId($node->nid, $node->type);
                    $contentCategoryAsm = new ContentCategoryAsm();
                    $contentCategoryAsm->category_id = $categoryId;
                    $contentCategoryAsm->content_id = $content->id;
                    $content->created_at = time();
                    $contentCategoryAsmRows[] = $contentCategoryAsm->attributes;
                }
                Yii::$app->db->createCommand()->batchInsert(ContentCategoryAsm::tableName(), (new ContentCategoryAsm())->attributes(), $contentCategoryAsmRows)->execute();
                $contentCategoryAsmRows = null;
            }
        }
    }

    private function migrateContent($node, $adminId, $siteId)
    {
        echo "\n\n\n=======Migrating content node: $node->nid =========\n";
        $nodeId = $node->nid;
        $parentNodeId = $this->getParentNodeId($nodeId);
        $parentId = null;
        if ($parentNodeId) {
            if ($this->checkContentExisted($parentNodeId)) {
                $parent = $this->getContentByTvod1Id($parentNodeId);
            } else {
                $parentNode = Node::findOne($parentNodeId);
                $parent = $this->migrateContent($parentNode, $adminId, $siteId);
            }
            if ($parent) {
                $parentId = $parent->id;
            }
        }
        $categoryId = $this->getCategoryId($nodeId, $node->type);
        if (!$categoryId) {
            echo 'Error: NOT FOUND Category';
            return;
        }

        $content = $this->getContentByTvod1Id($node->nid);
        $new = !$content;
        if (!$new && $content->updated_at == $node->changed) {
            echo 'Not any change';
            return $content;
        }
        $content = $this->createContentFromNode($content, $node, $categoryId, $adminId, $siteId, $parentId);

        if (!$content->save()) {
            Yii::error($content->errors);
            echo "Error: cannot save content $content->id \n";
            VarDumper::dump($content->errors);
            return;
        }
        if ($new) {
            $contentCategoryAsm = new ContentCategoryAsm();
            $contentCategoryAsm->category_id = $categoryId;
            $contentCategoryAsm->content_id = $content->id;
            $content->created_at = time();
            $contentCategoryAsmRows[] = $contentCategoryAsm->attributes;

            $contentSiteAsm = new ContentSiteAsm();
            $contentSiteAsm->site_id = $siteId;
            $contentSiteAsm->content_id = $content->id;
            $contentSiteAsm->status = $content->status == Content::STATUS_ACTIVE ? ContentSiteAsm::STATUS_ACTIVE : ContentSiteAsm::STATUS_INACTIVE;
            $contentSiteAsm->created_at = time();
            $contentSiteAsm->updated_at = time();
            $contentSiteAsmRows[] = $contentSiteAsm->attributes;

            $actorIds = $this->getActors($nodeId, $content->type);
            if ($actorIds) {
                foreach ($actorIds as $actorId) {
                    $contentActorAsm = new ContentActorDirectorAsm();
                    $contentActorAsm->content_id = $content->id;
                    $contentActorAsm->actor_director_id = $actorId;
                    $contentActorAsm->created_at = time();

                    $contentActorDirectorAsmRows[] = $contentActorAsm->attributes;
                }
            }

            $directorIds = $this->getDirectors($nodeId, $content->type);
            if ($directorIds) {
                foreach ($directorIds as $directorId) {
                    $contentDirectorAsm = new ContentActorDirectorAsm();
                    $contentDirectorAsm->content_id = $content->id;
                    $contentDirectorAsm->actor_director_id = $directorId;
                    $contentDirectorAsm->created_at = time();

                    $contentActorDirectorAsmRows[] = $contentDirectorAsm->attributes;
                }
            }

            Yii::$app->db->createCommand()->batchInsert(ContentCategoryAsm::tableName(), (new ContentCategoryAsm())->attributes(), $contentCategoryAsmRows)->execute();
            Yii::$app->db->createCommand()->batchInsert(ContentSiteAsm::tableName(), (new ContentSiteAsm())->attributes(), $contentSiteAsmRows)->execute();

            if (isset($contentActorDirectorAsmRows)) {
                Yii::$app->db->createCommand()->batchInsert(ContentActorDirectorAsm::tableName(), (new ContentActorDirectorAsm())->attributes(), $contentActorDirectorAsmRows)->execute();
            }
        }

        return $content;
    }

    private function createContentFromNode($content, $node, $categoryId, $adminId, $siteId, $parentId = null)
    {
        $new = false;
        if (!$content) {
            $content = new Content();
            $new = true;
        }
        $content->parent_id = $parentId;
        if ($new) {
            $content->type = $this->getHardCodeTypeByCategory($categoryId);
            $content->tvod1_id = $node->nid;
            $content->code = 'MS' . $node->nid; // To Do: bo sung quy luat sinh ma
            $content->created_at = $node->created;
            $content->created_user_id = $adminId;
            $content->view_count = 0;
            $content->download_count = 0;
            $content->like_count = 0;
            $content->episode_count = 0;
            $content->dislike_count = 0;
            $content->favorite_count = 0;
            $content->rating_count = 0;
            $content->comment_count = 0;
            $content->rating = 0.0;
            $content->default_site_id = $siteId;
            $content->default_category_id = $categoryId;
        }
        $titleVi = $this->getTitleVi($node->nid, $node->type);
        $content->display_name = $titleVi ? $titleVi : $node->title;
        $content->ascii_name = CVietnameseTools::removeSigns($content->display_name);
        $content->en_name = $node->title;
        $content->images = $this->getImages($node->nid, $node->type);
        $content->tags = $this->getTag($node->nid);
        $content->content = $this->getHTMLContent($node->nid);
        $content->honor = $this->isHonor($adminId) ? Content::HONOR_FEATURED : Content::HONOR_NOTHING;

        $content->updated_at = $node->changed;

        $status = $node->status == 1 ? Content::STATUS_ACTIVE : Content::STATUS_INVISIBLE;
        if ($status == Content::STATUS_ACTIVE && !$new && $content->status != $status) {
            $content->approved_at = $node->changed;
        } else {
            $content->approved_at = $node->created;
        }
        $content->status = $status;

        $content->author = $this->getAuthor($node->nid);
        $content->country = $this->getCountry($node->nid);

        return $content;
    }

    private function migrateContentProfile($node, $siteId)
    {
        echo "\n\n\n=======Migrating video version node: $node->nid =========\n";

        $contentProfile = $this->getContentProfileByTvod1Id($node->nid);
        $new = !$contentProfile;
        if (!$new && $contentProfile->updated_at == $node->changed) {
            echo 'Not any change';
            return $contentProfile;
        }
        $contentProfile = $this->createContentProfileFromNode($contentProfile, $node);

        if (!$contentProfile) {
            return;
        }

        if (!$contentProfile->save(false)) {
            Yii::error($contentProfile->errors);
            echo "Error: cannot save content profile $contentProfile->id \n";
            VarDumper::dump($contentProfile->errors);
            return;
        }

        $contentProfileSiteAsm = $this->createContentProfileSiteAsm($contentProfile, $siteId);

        if (!$contentProfileSiteAsm->save(false)) {
            Yii::error($contentProfileSiteAsm->errors);
            echo "Error: cannot save content profile asm $contentProfileSiteAsm->id \n";
            VarDumper::dump($contentProfileSiteAsm->errors);
            return;
        }

        return $contentProfile;
    }

    private function getVideoNodeOfVideoVersion($vvid)
    {
        return FieldDataFieldVideoVersionRef::findOne(['field_video_version_ref_nid' => $vvid]);
    }

    private function createContentProfileSiteAsm($contentProfile, $siteId)
    {
        $contentProfileSiteAsm = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id]);
        if (!$contentProfileSiteAsm) {
            $contentProfileSiteAsm = new ContentProfileSiteAsm();
            $contentProfileSiteAsm->content_profile_id = $contentProfile->id;
            $contentProfileSiteAsm->site_id = $siteId;
            $contentProfileSiteAsm->created_at = time();
            $contentProfileSiteAsm->updated_at = time();
            $contentProfileSiteAsm->url = $this->getContentProfileUrl($contentProfile->tvod1_id);
        }
        $contentProfileSiteAsm->status = $contentProfile->status == ContentProfile::STATUS_ACTIVE ? ContentProfileSiteAsm::STATUS_ACTIVE : ContentProfileSiteAsm::STATUS_INACTIVE;
        return $contentProfileSiteAsm;
    }

    private function createContentProfileFromNode($contentProfile, $node)
    {
        $new = false;
        if (!$contentProfile) {
            $contentProfile = new ContentProfile();
            $new = true;
        }
        if (!$contentProfile->content_id) {
            $videoNode = $this->getVideoNodeOfVideoVersion($node->nid);
            if (!$videoNode) {
                echo 'Cannot find video node of video version: ' . $node->nid;
                return;
            }
            $content = $this->getContentByTvod1Id($videoNode->entity_id);
            if (!$content) {
                echo 'Cannot find content of video version: ' . $node->nid;
                return;
            }
            $contentProfile->content_id = $content->id;
        } else {
            $content = Content::findOne(['id' => $contentProfile->content_id]);
            if (!$content) {
                echo 'Cannot find content of video version: ' . $node->nid;
                return;
            }
        }

        if ($new) {
            $contentProfile->name = $node->title;
            $contentProfile->type = ContentProfile::TYPE_CDN;
            $resolution = $this->getVideoResolution($node->nid);
            if (!$resolution) {
                $resolutionArr = explode("x", $resolution);
                $contentProfile->width = $resolutionArr[0];
                $contentProfile->height = $resolutionArr[1];
            }
            $contentProfile->quality = $this->getProfileQuality($node->nid);
            $contentProfile->created_at = $node->created;

            if (!$content->duration) {
                $content->duration = $this->getDuration($node->nid);
                $content->save(false, ['duration']);
            }

            $subtitle = $this->getVideoSubtitle($node->nid);
            if ($subtitle) {
                $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $content->id]);
                if ($contentSiteAsm && !$contentSiteAsm->subtitle) {
                    $contentSiteAsm->subtitle = $subtitle;
                    $contentSiteAsm->update(false, ['subtitle']);
                }
            }
        }

        $contentProfile->updated_at = $node->changed;

        $status = $node->status == 1 ? Content::STATUS_ACTIVE : Content::STATUS_INVISIBLE;
        $contentProfile->status = $status;

        return $contentProfile;
    }

    private function migrateContentProfiles()
    {
        $migrateStatus = MigrateStatus::getLastSuccessMigration(MigrateStatus::TYPE_VIDEO_VERSION);
        if (!$migrateStatus) {
            echo "\n********** Begin Migrating content profiles ***********\n";
            MigrateStatus::createOrFinish(MigrateStatus::TYPE_VIDEO_VERSION);
            try {
                $this->migrateContentProfilesOnInit();
                $this->insertContentProfileSiteAsmsOnInit();
                $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_VIDEO_VERSION);
                $maxId = ContentProfile::find()->where(['not', ['tvod1_id' => null]])->select('tvod1_id')->max('tvod1_id');
                if ($maxId) {
                    $migrateStatus->finish(MigrateStatus::STATUS_SUCCESS, $maxId);
                }
                echo "\n********** Migrated content profiles successfully ***********\n";
            } catch (Exception $e) {
                $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_VIDEO_VERSION);
                if ($migrateStatus) {
                    $migrateStatus->finish(MigrateStatus::STATUS_FAIL, 0, $e->getMessage());
                }
                echo $e->__toString();
                echo "\n********** Migrated content profiles failed ***********\n";
            }
        } else {
            $this->migrateContentProfilesPeriodically();
        }
    }

    private function migrateContentProfilesPeriodically()
    {
        echo "\n********** Begin Migrating content profiles ***********\n";

        $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_VIDEO_VERSION);
        if ($migrateStatus) {
            if ($migrateStatus->started_at > time() - self::TIMEOUT) {
                echo 'Existed running content profiles migration process';
                return;
            } else {
                $migrateStatus->finish(MigrateStatus::STATUS_FAIL, 0, 'Timeout');
            }
        }

        $migrateStatus = MigrateStatus::getLastSuccessMigration(MigrateStatus::TYPE_VIDEO_VERSION);
        $newMaxId = $maxId = $migrateStatus ? $migrateStatus->max_id : 0;
        $lastMigratedAt = $migrateStatus ? $migrateStatus->started_at : 0;

        MigrateStatus::createOrFinish(MigrateStatus::TYPE_VIDEO_VERSION);

        try {
            $batchQueryResult = Node::find()
                ->where(['type' => ['video_version']])
                ->andWhere(['>', 'changed', $lastMigratedAt])
                ->batch(1000);
            $siteId = $this->getSiteId();
            if (!$siteId) {
                echo 'Error: NOT FOUND Site';
                return;
            }
            if ($batchQueryResult) {
                foreach ($batchQueryResult as $nodes) {
                    foreach ($nodes as $node) {
                        $contentProfile = $this->migrateContentProfile($node, $siteId);
                        if ($contentProfile && $contentProfile->tvod1_id > $newMaxId) {
                            $newMaxId = $contentProfile->tvod1_id;
                        }
                    }
                }
            }
            $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_VIDEO_VERSION);
            $migrateStatus->finish(MigrateStatus::STATUS_SUCCESS, $newMaxId);
            echo "\n********** Migrated content profiles successfully ***********\n";
        } catch (Exception $e) {
            $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_VIDEO_VERSION);
            if ($migrateStatus) {
                $migrateStatus->finish(MigrateStatus::STATUS_FAIL, 0, $e->getMessage());
            }
            echo $e->__toString();
            echo "\n********** Migrated content profiles failed ***********\n";
        }
    }

    private function migrateContentProfilesOnInit()
    {
        $batchQueryResult = Node::find()->where(['type' => 'video'])->batch(1000);
        if ($batchQueryResult) {
            foreach ($batchQueryResult as $videoNodes) {
                foreach ($videoNodes as $videoNode) {
                    $content = Content::findOne(['tvod1_id' => $videoNode->nid]);
                    if (!$content) {
                        continue;
                    }
                    $this->migrateContentProfilesOfVideo($videoNode->nid, $content);
                }
            }
        }
    }

    private function migrateLostContentProfiles()
    {
        $contentsBatchResult = Content::find()
            ->select("content.*")
            ->leftJoin('content_profile', 'content.id=content_profile.content_id')
            ->where(['not', ['content.tvod1_id' => null]])
            ->andWhere(['content_profile.id' => null])
            ->batch(1000);

        if ($contentsBatchResult) {
            foreach ($contentsBatchResult as $contents) {
                foreach ($contents as $content) {
                    $this->migrateContentProfilesOfVideo($content->tvod1_id, $content);
                }
            }
        }
    }

    private function getContentProfileUrl($nodeId)
    {

        $contentIdRow = FieldDataFieldContentId::find()->where(['entity_id' => $nodeId])->one();
        if ($contentIdRow) {
//            return $this->getVideoUrl($contentIdRow->field_content_id_value);
            return $contentIdRow->field_content_id_value;
        }
        return null;
    }

    private function getVideoUrl($contentId)
    {

//        http://api.cdn.tvod.com.vn/getURL?contentId=&cpName=TVoD&reqId=&euip=&token=
        $ip = getHostByName(getHostName());;
        $reqId = time();
        $cpName = 'TVoD';

        $token = md5($contentId . $cpName . $reqId . 'aslk02938');

        $ch = new MyCurl();
        $response = $ch->get('http://api.cdn.tvod.com.vn/getURL', array(
            'contentId' => $contentId,
            'cpName' => 'TVoD',
            'reqId' => time(),
            "euip" => $ip,
            "token" => $token,
        ));

////        echo "Gettting videoUrl of contentId: $contentId\n";
////        $contentUrl = $response->body;
////        echo "response: $contentUrl";
//
        $result = json_decode($response->body, true);

//        if ($result) {
////            $contentUrl = $result[0]['contentUrl'];
//            VarDumper::dump($result);
        return $result['contentURL'];
//            echo "\ncontentUrl: $contentURL";
////            echo "contentUrl: $contentUrl";
//
//        }

        return null;
    }

    private function migrateContentProfilesOfVideo($videoNodeId, $content)
    {
        $videoVersionsBatchResult = Node::find()
            ->select("node.*")
            ->innerJoin('field_data_field_video_version_ref as vvf', 'node.nid = vvf.field_video_version_ref_nid')
            ->where(['vvf.entity_id' => $videoNodeId])
            ->batch(1000);
        if ($videoVersionsBatchResult) {
            foreach ($videoVersionsBatchResult as $videoVersions) {
                foreach ($videoVersions as $videoVersion) {
                    $status = $videoVersion->status == 1 ? ContentProfile::STATUS_ACTIVE : ContentProfile::STATUS_INACTIVE;
                    $contentProfile = new ContentProfile();
                    $contentProfile->content_id = $content->id;
                    $contentProfile->tvod1_id = $videoVersion->nid;
                    $contentProfile->name = $videoVersion->title;
                    $contentProfile->type = ContentProfile::TYPE_CDN;
                    $contentProfile->status = $status;
                    $resolution = $this->getVideoResolution($videoVersion->nid);
                    if (!$resolution) {
                        $resolutionArr = explode("x", $resolution);
                        $contentProfile->width = $resolutionArr[0];
                        $contentProfile->height = $resolutionArr[1];
                    }
                    $contentProfile->quality = $this->getProfileQuality($videoVersion->nid);
                    $contentProfile->created_at = $videoVersion->created;
                    $contentProfile->updated_at = $videoVersion->changed;
                    $contentProfileRows[] = $contentProfile;

                    if (!$content->duration) {
                        $content->duration = $this->getDuration($videoNodeId);
                        $content->save(false, ['duration']);
                    }
                    $subtitle = $this->getVideoSubtitle($videoVersion->nid);
                    if ($subtitle) {
                        $contentSiteAsm = ContentSiteAsm::findOne(['content_id' => $content->id]);
                        if ($contentSiteAsm && !$contentSiteAsm->subtitle) {
                            $contentSiteAsm->subtitle = $subtitle;
                            $contentSiteAsm->update(false, ['subtitle']);
                        }
                    }
                }
                if ($contentProfileRows) {
                    Yii::$app->db->createCommand()->batchInsert(ContentProfile::tableName(), (new ContentProfile())->attributes(), $contentProfileRows)->execute();
                }
                $contentProfileRows = null;
            }
        }
    }

    private function isHD($channelName)
    {
        return strtoupper(substr($channelName, strlen($channelName) - 2, 2)) == 'HD';
    }

    private function insertContentProfileSiteAsmsOnInit()
    {
        $siteId = $this->getSiteId();
        if (!$siteId) {
            echo 'Error: NOT FOUND Site';
            return;
        }
        $contentProfilesBatch = ContentProfile::find()
            ->select('content_profile.id, content_profile.status, content_profile.tvod1_id')
            ->leftJoin('content_profile_site_asm as asm', 'content_profile.id=asm.content_profile_id')
            ->where(['not', ['content_profile.tvod1_id' => null]])
            ->andWhere(['asm.id' => null])
            ->batch(1000);
        foreach ($contentProfilesBatch as $contentProfiles) {
            foreach ($contentProfiles as $contentProfile) {
                $contentProfileSiteAsm = new ContentProfileSiteAsm();
                $contentProfileSiteAsm->content_profile_id = $contentProfile->id;
                $contentProfileSiteAsm->site_id = $siteId;
                $contentProfileSiteAsm->status = $contentProfile->status == ContentProfile::STATUS_ACTIVE ? ContentProfileSiteAsm::STATUS_ACTIVE : ContentProfileSiteAsm::STATUS_INACTIVE;
                $contentProfileSiteAsm->created_at = time();
                $contentProfileSiteAsm->updated_at = time();
                $contentProfileSiteAsm->url = $this->getContentProfileUrl($contentProfile->tvod1_id);

                $contentProfileSiteAsmRows[] = $contentProfileSiteAsm;

            }
            if (isset($contentProfileSiteAsmRows)) {
                Yii::$app->db->createCommand()->batchInsert(ContentProfileSiteAsm::tableName(), (new ContentProfileSiteAsm())->attributes(), $contentProfileSiteAsmRows)->execute();
            }
            $contentProfileSiteAsmRows = null;
        }
    }

    private function getVideoResolution($nodeId)
    {
        $videoResolution = FieldDataFieldResolution::find()->where(['entity_id' => $nodeId])->one();
        if ($videoResolution) {
            return $videoResolution->field_resolution_value;
        }

    }

    private function getVideoSubtitle($nodeId)
    {
        $subtitle = FieldDataFieldSubtitle::findOne(['entity_id' => $nodeId]);
        if ($subtitle) {
            $fid = $subtitle->field_subtitle_fid;
            if ($fid) {
                $file = FileManaged::find()
                    ->select(['uri', 'filesize'])
                    ->where(['fid' => $fid])
                    ->one();
                if ($file) {
//                    echo "\n" . str_replace("public://subtitle/", "", $file->uri);
                    return str_replace("public://subtitle/", "", $file->uri);
                }
            }
        }

    }

    private function getTitleVi($nodeId, $nodeType)
    {
        if ($nodeType == 'drama') {
            $titleRow = FieldDataFieldSeriesTitleVi::find()->where(['entity_id' => $nodeId])->one();
            if ($titleRow) {
                return $titleRow->field_series_title_vi_value;
            }
        } else {
            $titleRow = FieldDataFieldTitleVi::find()->where(['entity_id' => $nodeId])->one();
            if ($titleRow) {
                return $titleRow->field_title_vi_value;
            }
        }
    }

    private function getHardCodeTypeByCategory($catId)
    {
        $cat = Category::findOne($catId);
        switch ($cat->type) {
            case Category::TYPE_NEWS:
                return Content::TYPE_NEWS;
//            case Category::TYPE_LIVE:
//                return Content::TYPE_LIVE;
            case Category::TYPE_MUSIC:
                return Content::TYPE_MUSIC;
            case Category::TYPE_KARAOKE:
                return Content::TYPE_KARAOKE;
            case Category::TYPE_CLIP:
                return Content::TYPE_CLIP;
            case Category::TYPE_RADIO:
                return Content::TYPE_RADIO;
//            case Category::TYPE_LIVE_CONTENT:
//                return Content::TYPE_LIVE_CONTENT;
            case  Category::TYPE_FILM:
                return Content::TYPE_VIDEO;
        }
        return 0;
    }

    private function getParentNodeId($nodeId)
    {
        $parent = Node::find()
            ->innerJoin('field_data_field_video_ref', '`field_data_field_video_ref`.`entity_id`=`node`.`nid`')
            ->where(["`field_data_field_video_ref`.`field_video_ref_nid`" => $nodeId])
            ->one();
        if ($parent) {
            return $parent->nid;
        }
    }

    private function getContentByTvod1Id($tvod1_id)
    {
        return Content::find()->where(["tvod1_id" => $tvod1_id])->andWhere(['not', ['type' => Content::TYPE_LIVE]])->one();
    }

    private function checkContentExisted($nodeId)
    {
        return Content::find()->where(["tvod1_id" => $nodeId])->andWhere(['not', ['type' => Content::TYPE_LIVE]])->count() > 0;
    }

    private function getContentProfileByTvod1Id($tvod1_id)
    {
        return ContentProfile::find()->where(["tvod1_id" => $tvod1_id])->one();
    }

    private function checkContentProfileExisted($nodeId)
    {
        return ContentProfile::find()->where(["tvod1_id" => $nodeId])->count() > 0;
    }

    private function getSiteId()
    {
        $site = Site::findOne(['status' => Site::STATUS_ACTIVE]);
        if ($site) {
            return $site->id;
        }
    }

    private function getAdminId()
    {
        $admin = User::findOne(['status' => User::STATUS_ACTIVE, 'type' => User::USER_TYPE_ADMIN]);
        if ($admin) {
            return $admin->id;
        }
    }

    private function getCategoryId($nodeId, $nodeType)
    {
        $cat = null;
        if ($nodeType == 'drama') {
            $oldCat = FieldDataFieldDramaCatagory::find()->where(['entity_id' => $nodeId])->one();
            if ($oldCat) {
                $oldId = $oldCat->field_drama_catagory_tid;
                $cat = Category::find()->where(['tvod1_id' => $oldId])->one();
            }
        } else if ($nodeType == 'news') {
            $oldCat = FieldDataFieldNewsCategory::find()->where(['entity_id' => $nodeId])->one();
            if ($oldCat) {
                $oldId = $oldCat->field_news_category_tid;
                $cat = Category::find()->where(['tvod1_id' => $oldId])->one();
            }
        } else if ($nodeType == 'live_streaming') {
            $oldCat = FieldDataFieldLiveCategories::find()->where(['entity_id' => $nodeId])->one();
            if ($oldCat) {
                $oldId = $oldCat->field_live_categories_tid;
                $cat = Category::find()->where(['tvod1_id' => $oldId])->one();
            }
        } else {
            $oldCat = FieldDataFieldCategory::find()->where(['entity_id' => $nodeId])->one();
            if ($oldCat) {
                $oldId = $oldCat->field_category_tid;
                $cat = Category::find()->where(['tvod1_id' => $oldId])->one();
            }
        }
        if ($cat) {
            return $cat->id;
        }
    }

    private function getProfileQuality($nodeId)
    {
        $quality = FieldDataFieldType::findOne(['entity_id' => $nodeId]);
        if ($quality) {
            // Luu y: hang so quality trong model Content cua tvod2 co gia tri bang voi du lieu tvod1
            // neu ko phai viet function de convert
            return $quality->field_type_value;
        }
        return ContentProfile::QUALITY_SD;
    }

    private function getTag($nodeId)
    {
        $tags = '';
        $nodeTags = TaxonomyTermData::find()
            ->innerJoin('field_data_field_tag as tag_ref', 'taxonomy_term_data.tid=tag_ref.field_tag_tid')
            ->where(['tag_ref.entity_id' => $nodeId])
            ->all();
        if ($nodeTags) {
            foreach ($nodeTags as $nodeTag) {
                if ($tags) {
                    $tags = $tags . ',';
                }
                $tags = $tags . $nodeTag->name;
            }
        }
        return $tags;
    }

    private function getHTMLContent($nodeId)
    {
        $body = FieldDataBody::find()
            ->where(['entity_id' => $nodeId])->one();
        if ($body) {
            return $body->body_value;
        }
    }

    private function getDuration($nodeId)
    {
        $duration = FieldDataFieldTime::find()
            ->where(['entity_id' => $nodeId])->one();

        if ($duration) {
            return $duration->field_time_value;
        }
    }

    private function isHonor($nodeId)
    {
        $feature = FieldDataFieldFeature::findOne(['entity_id' => $nodeId]);
        return $feature && $feature->field_feature_value == 1;
    }

    private function getUrl($nodeId)
    {
        // To Do
    }

    private function getLiveImages($nodeId)
    {
        $images = '[';

        //Anh dai dien
        $fid = FieldDataFieldChannelPicture::find()
            ->select(['field_channel_picture_fid'])
            ->where(['entity_id' => $nodeId])
            ->one();

        if ($fid) {
            $file = FileManaged::find()
                ->select(['uri', 'filesize'])
                ->where(['fid' => $fid->field_channel_picture_fid])
                ->one();
            if ($file) {
                $images = $images . '{"' . 'name":"' . str_replace("public://", "", $file->uri) . '","type":"2","size":' . $file->filesize . '}';
            }
        }
        $images = $images . ']';
        return $images;
    }

    private function getImages($nodeId, $nodeType)
    {
        $images = '[';
        if ($nodeType == 'drama') {
            //Anh dai dien
            $fid = FieldDataFieldDramaPicture::find()
                ->select(['field_drama_picture_fid'])
                ->where(['entity_id' => $nodeId])
                ->one();

            if ($fid) {
                $file = FileManaged::find()
                    ->select(['uri', 'filesize'])
                    ->where(['fid' => $fid->field_drama_picture_fid])
                    ->one();
                if ($file) {
                    $images = $images . '{"' . 'name":"' . str_replace("public://", "", $file->uri) . '","type":"2","size":' . $file->filesize . '},';
                }
            }

            //Anh slideshow
            $fid = FieldDataFieldSeriesPictureSlideshow::find()
                ->select(['field_series_picture_slideshow_fid'])
                ->where(['entity_id' => $nodeId])
                ->one();

            if ($fid) {
                $file = FileManaged::find()
                    ->select(['uri', 'filesize'])
                    ->where(['fid' => $fid->field_series_picture_slideshow_fid])
                    ->one();
                if ($file) {
                    $images = $images . '{"' . 'name":"' . str_replace("public://", "", $file->uri) . '","type":"3","size":' . $file->filesize . '}';
                }
            }
        } else if ($nodeType == 'live_streaming') {
            //Anh dai dien
            $fid = FieldDataFieldChannelPicture::find()
                ->select(['field_channel_picture_fid'])
                ->where(['entity_id' => $nodeId])
                ->one();

            if ($fid) {
                $file = FileManaged::find()
                    ->select(['uri', 'filesize'])
                    ->where(['fid' => $fid->field_channel_picture_fid])
                    ->one();
                if ($file) {
                    $images = $images . '{"' . 'name":"' . str_replace("public://", "", $file->uri) . '","type":"2","size":' . $file->filesize . '}';
                }
            }
        } else {
            //Anh dai dien
            $fid = FieldDataFieldVideoPicture::find()
                ->select(['field_video_picture_fid'])
                ->where(['entity_id' => $nodeId])
                ->one();

            if ($fid) {
                $file = FileManaged::find()
                    ->select(['uri', 'filesize'])
                    ->where(['fid' => $fid->field_video_picture_fid])
                    ->one();
                if ($file) {
                    $images = $images . '{"' . 'name":"' . str_replace("public://", "", $file->uri) . '","type":"2","size":' . $file->filesize . '},';
                }
            }

            //Anh slideshow
            $fid = FieldDataFieldPictureSlideshow::find()
                ->select(['field_picture_slideshow_fid'])
                ->where(['entity_id' => $nodeId])
                ->one();

            if ($fid) {
                $file = FileManaged::find()
                    ->select(['uri', 'filesize'])
                    ->where(['fid' => $fid->field_picture_slideshow_fid])
                    ->one();
                if ($file) {
                    $images = $images . '{"' . 'name":"' . str_replace("public://", "", $file->uri) . '","type":"3","size":' . $file->filesize . '}';
                }
            }
        }

        $images = $images . ']';
        return $images;
    }

    private function getAuthor($nodeId)
    {
        $authors = FieldDataFieldVideoManufacturer::find()
            ->where(['entity_id' => $nodeId])->all();
        $strAuthors = '';
        if ($authors) {
            foreach ($authors as $author) {
                if ($strAuthors) {
                    $strAuthors = $strAuthors . ', ';
                }
                $strAuthors = $strAuthors . $author->field_video_manufacturer_value;
            }
            return $strAuthors;
        }
    }

    private function getActors($nodeId, $contentType)
    {
        $actors = FieldDataFieldVideoActor::find()
            ->where(['entity_id' => $nodeId])->all();
        $actorIds = [];
        if ($actors) {
            foreach ($actors as $actor) {
                $actorNamesArr = explode(",", $actor->field_video_actor_value);
                foreach ($actorNamesArr as $actorName) {
                    $actorIds[] = $this->findAndCreateActorDirector(trim($actorName), ActorDirector::TYPE_ACTOR, $contentType);
                }
            }
        }
        return array_unique($actorIds);
    }

    private function getDirectors($nodeId, $contentType)
    {
        $directors = FieldDataFieldVideoDirector::find()
            ->where(['entity_id' => $nodeId])->all();
        $directorIds = [];
        if ($directors) {
            foreach ($directors as $director) {
                $directorNamesArr = explode(",", $director->field_video_director_value);
                foreach ($directorNamesArr as $directorName) {
                    $directorIds[] = $this->findAndCreateActorDirector(trim($directorName), ActorDirector::TYPE_DIRECTOR, $contentType);
                }
            }
        }
        return array_unique($directorIds);
    }

    private function findAndCreateActorDirector($name, $type, $contentType)
    {
        $actor = ActorDirector::findOne(['name' => $name, 'type' => $type, 'content_type' => $contentType, 'status' => ActorDirector::STATUS_ACTIVE]);
        if (!$actor) {
            $actor = new ActorDirector();
            $actor->name = $name;
            $actor->type = $type;
            $actor->status = ActorDirector::STATUS_ACTIVE;
            $actor->content_type = $contentType;
            $actor->save();
        }
        return $actor->id;
    }

    private function getCountry($nodeId)
    {
        $country = FieldDataFieldVideoNational::find()
            ->where(['entity_id' => $nodeId])->one();
        if ($country) {
            return $country->field_video_national_value;
        }
    }

    private function updateIsSeries()
    {
        Yii::$app->db->createCommand('update content a join content b on a.id = b.parent_id set a.is_series = 1 WHERE a.is_series = 0 AND a.type = ' . Content::TYPE_VIDEO)->execute();

        // Cap nhat so tap phim bo
        $sql = 'UPDATE `content` a JOIN 
                (SELECT parent_id, COUNT(*) AS episode_count FROM content
                  WHERE status = ' . Content::STATUS_ACTIVE . ' GROUP BY parent_id
                 ) b
                ON a.id = b.parent_id
                SET a.episode_count = b.episode_count';

        Yii::$app->db->createCommand($sql)->execute();

        // Cap nhat so thu tu phim bo
        $series = Content::find()
            ->where([
                'is_series' => 1,
                'status' => Content::STATUS_ACTIVE
            ])
            ->andWhere(['not', ['tvod1_id' => null]])
            ->all();
        foreach ($series as $parent) {
            $films = Content::find()
                ->where(['parent_id' => $parent->id, 'status' => Content::STATUS_ACTIVE])
                ->orderBy('en_name')
                ->all();
            for ($i = 0; $i < count($films); $i++) {
                $film = $films[$i];
                if ($film->episode_order != $i + 1) {
                    $film->episode_order = $i + 1;
                    $film->save(false, ['episode_order']);
                }
            }
        }
    }


    /**
     *
     */
    public function actionUpdateDefaultCategoryIdContent()
    {
        ini_set("memory_limit", "2048M");
        $contents = Content::find()->andWhere('type in (1,2,3,4,5,6,7) and default_category_id is null')->all();
        $count = 0;
        echo '***** BEFORE ***** total: ' . count($contents);
        /** @var  $content Content */
        foreach ($contents as $content) {
            $contentCategoryAsms = $content->contentCategoryAsms;
            if (count($contentCategoryAsms) <= 0) {
                echo "   -content_id:" . $content->id;
                continue;
            }
            foreach ($contentCategoryAsms as $contentCategoryAsm) {
                $content->default_category_id = $contentCategoryAsm->category_id;
                /** Nếu save được thì chuyển thoát khỏi vòng for này */
                if ($content->save()) {
                    $count++;
                    break;
                } else {
                    echo "   -category_id:" . $contentCategoryAsm->category_id;
                }
            }

        }
        echo ' ***** AFTER ***** success: ' . $count;
    }

//    /**
//     *
//     */
//    public function actionUpdateDefaultCategoryIdContent()
//    {
//        ini_set("memory_limit", "2048M");
//        $contents = Content::find()->andWhere('default_category_id is null')->all();
//        $count = 0;
//        echo '***** BEFORE ***** total: ' . count($contents);
//        /** @var  $content Content */
//        foreach ($contents as $content) {
//            $contentCategoryAsms = $content->contentCategoryAsms;
//            if (count($contentCategoryAsms) <= 0) {
//                continue;
//            }
//            foreach ($contentCategoryAsms as $contentCategoryAsm) {
//                $content->default_category_id = $contentCategoryAsm->category_id;
//                /** Nếu save được thì chuyển thoát khỏi vòng for này */
//                if ($content->save()) {
//                    $count++;
//                    break;
//                }
//            }
//
//        }
//        echo ' ***** AFTER ***** success: ' . $count;
//    }

}