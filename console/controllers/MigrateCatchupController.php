<?php
/**
 * Created by PhpStorm.
 * User: bibon
 * Date: 4/18/2016
 * Time: 5:23 PM
 */

namespace console\controllers;


use common\helpers\CVietnameseTools;
use common\models\Content;
use common\models\ContentCategoryAsm;
use common\models\ContentProfile;
use common\models\ContentProfileSiteAsm;
use common\models\ContentSiteAsm;
use common\models\LiveProgram;
use common\models\MigrateStatus;
use common\models\Site;
use common\models\User;
use console\models\tvcatchup\FieldDataFieldChannelDate;
use console\models\tvcatchup\FieldDataFieldChannelIdTvodCms;
use console\models\tvcatchup\FieldDataFieldChannelReference;
use console\models\tvcatchup\FieldDataFieldEndTime;
use console\models\tvcatchup\FieldDataFieldFileFolder;
use console\models\tvcatchup\FieldDataFieldFileId;
use console\models\tvcatchup\FieldDataFieldProgramReference;
use console\models\tvcatchup\FieldDataFieldStartTime;
use console\models\tvcatchup\FieldDataFieldSuccessfulWrite;
use console\models\tvcatchup\Node;
use Yii;
use yii\console\Controller;
use yii\helpers\VarDumper;

class MigrateCatchupController extends Controller
{

//    private $init = true;

    public function actionRun()
    {
//        $migrateStatus = MigrateStatus::getLastSuccessMigration(MigrateStatus::TYPE_CATCHUP);
//        $this->init = !$migrateStatus;
        $this->migrateMappedLiveChannel();
        $this->migratePrograms();
//        if ($this->init) {
//            $this->migrateContentProfilesOnInit();
//            $this->migrateContentProfileSiteAsmsOnInit();
//        }
    }

    private function migrateContentProfileSiteAsmsOnInit()
    {
        $siteId = $this->getSiteId();
        if (!$siteId) {
            echo 'Error: NOT FOUND Site';
            return;
        }
        $contentProfilesBatch = ContentProfile::find()
            ->select('content_profile.id, content_profile.status, content.catchup_id')
            ->innerJoin('content', 'content_profile.content_id=content.id')
            ->leftJoin('content_profile_site_asm as asm', 'content_profile.id=asm.content_profile_id')
            ->where(['content.type' => Content::TYPE_LIVE_CONTENT])
            ->andwhere(['not', ['content.catchup_id' => null]])
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
                $contentProfileSiteAsm->url = $this->getContentProfileUrl($contentProfile);

                $contentProfileSiteAsmRows[] = $contentProfileSiteAsm;

            }
            if (isset($contentProfileSiteAsmRows)) {
                Yii::$app->db->createCommand()->batchInsert(ContentProfileSiteAsm::tableName(), (new ContentProfileSiteAsm())->attributes(), $contentProfileSiteAsmRows)->execute();
            }
            $contentProfileSiteAsmRows = null;
        }
    }

    private function migrateContentProfilesOnInit()
    {
        $siteId = $this->getSiteId();
        if (!$siteId) {
            echo 'Error: NOT FOUND Site';
            return;
        }

        $contentsBatchResult = Content::find()
            ->select("content.*")
            ->leftJoin('content_profile', 'content.id=content_profile.content_id')
            ->where(['not', ['content.catchup_id' => null]])
            ->andWhere(['content.type' => Content::TYPE_LIVE_CONTENT])
            ->andWhere(['content_profile.id' => null])
            ->batch(1000);

        if ($contentsBatchResult) {
            foreach ($contentsBatchResult as $catchupVideos) {
                $contentProfileRows = null;
                $contentProfileSiteAsmRows = null;
                foreach ($catchupVideos as $catchupContent) {
                    $contentId = $catchupContent['id'];
                    echo "\n==== migrate content: $contentId ===";
                    if ($this->checkProfileExisted($catchupContent['id'])) {
                        echo '\nEXISTED';
                        continue;
                    }
                    $contentProfile = $this->migrateContentProfile($catchupContent);
                    if (!$contentProfile) {
                        continue;
                    }
                    $contentProfileRows[] = $contentProfile;

                    $contentProfileSiteAsm = new ContentProfileSiteAsm();
                    $contentProfileSiteAsm->site_id = $siteId;
                    $contentProfileSiteAsm->content_profile_id = $contentProfile['id'];
                    $contentProfileSiteAsm->status = $catchupContent['status'] == Content::STATUS_ACTIVE ? ContentProfileSiteAsm::STATUS_ACTIVE : ContentProfileSiteAsm::STATUS_INACTIVE;
                    $contentProfileSiteAsm->url = $this->getContentProfileUrl($catchupContent);
                    $contentProfileSiteAsm->created_at = time();
                    $contentProfileSiteAsm->updated_at = time();
                    $contentProfileSiteAsmRows[] = $contentProfileSiteAsm->attributes;
                }
                if ($contentProfileRows) {
                    Yii::$app->db->createCommand()->batchInsert(ContentProfile::tableName(), (new ContentProfile())->attributes(), $contentProfileRows)->execute();
                    Yii::$app->db->createCommand()->batchInsert(ContentProfileSiteAsm::tableName(), (new ContentProfileSiteAsm())->attributes(), $contentProfileSiteAsmRows)->execute();
                }
                $contentProfileRows = null;
                $contentProfileSiteAsmRows = null;
            }
        }
    }

    private function getContentProfileUrl($catchupContent)
    {
        $folder = $this->getFileFolder($catchupContent->catchup_id);
        if (!$folder) {
            echo 'ERROR: NOT FOUND FOLDER';
            return null;
        }
        $fileId = $this->getFileId($catchupContent->catchup_id);
        if (!$fileId) {
            echo 'ERROR: NOT FOUND FILE';
            return null;
        }
        return $folder . '/' . $fileId;
    }

    private function migrateContentProfile($catchupContent)
    {

        $contentProfile = new ContentProfile();
        $contentProfile->content_id = $catchupContent->id;
        $contentProfile->name = $catchupContent->display_name;
        $contentProfile->type = ContentProfile::TYPE_STREAM;
        $contentProfile->status = $catchupContent->status == Content::STATUS_ACTIVE ? ContentProfile::STATUS_ACTIVE : ContentProfile::STATUS_INACTIVE;;
        $contentProfile->quality = $this->isHD($catchupContent) ? ContentProfile::QUALITY_HD : ContentProfile::QUALITY_SD;
        $contentProfile->created_at = time();
        $contentProfile->updated_at = time();
        if (!$contentProfile->save()) {

        }
    }

    private function isHD($catchupContent)
    {
        $parentChannel = Content::findOne($catchupContent->parent_id);
        $channelName = $parentChannel->display_name;
        return strtoupper(substr($channelName, strlen($channelName) - 2, 2)) == 'HD';
    }

    private function getFileFolder($nodeId)
    {
        $folder = FieldDataFieldFileFolder::findOne(['entity_id' => $nodeId]);
        if ($folder) {
            return $folder->field_file_folder_value;
        }
    }

    private function getFileId($nodeId)
    {
        $fileId = FieldDataFieldFileId::findOne(['entity_id' => $nodeId]);
        if ($fileId) {
            return $fileId->field_file_id_value;
        }
    }

    private function checkProfileExisted($contentId)
    {
        return !!ContentProfile::findOne(['content_id' => $contentId]);
    }

    private function migrateMappedLiveChannel()
    {

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

        $channelsTvodId = FieldDataFieldChannelIdTvodCms::find()
            ->innerJoin('node', 'node.nid=field_data_field_channel_id_tvod_cms.entity_id')
            ->all();

        $count = count($channelsTvodId);
        echo "$count mapped channels";

        foreach ($channelsTvodId as $channelTvodId) {
            $contentProfile = ContentProfile::findOne(['tvod1_id' => $channelTvodId->field_channel_id_tvod_cms_value]);
            if (!$contentProfile) {
                continue;
            }
            $node = Node::findOne(['nid' => $channelTvodId->entity_id]);
            $this->updateCatchupId($channelTvodId->entity_id, $contentProfile->content_id, $node->status);
        }

    }

    private function updateCatchupId($catchupId, $id, $catchupStatus)
    {
        if ($catchupStatus == 1) {
            Content::updateAll(['catchup_id' => $catchupId, 'is_catchup' => 1], ['id' => $id]);
        } else {
            Content::updateAll(['catchup_id' => $catchupId, 'is_catchup' => 0], ['id' => $id]);
        }
    }

    private function checkContentExisted($nodeId)
    {
        return Content::find()->where(["catchup_id" => $nodeId])->count() > 0;
    }

    private function getContentByCatchupId($catchup_id)
    {
        return Content::findOne(["catchup_id" => $catchup_id]);
    }

    private function migratePrograms()
    {

        echo "\n********** Begin migrating programs ***********\n";

        $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_CATCHUP);
        if ($migrateStatus) {
            if ($migrateStatus->started_at > time() - self::TIMEOUT) {
                echo 'Existed running content migration process';
                return;
            } else {
                $migrateStatus->finish(MigrateStatus::STATUS_FAIL, 0, 'Timeout');
            }
        }

        $migrateStatus = MigrateStatus::getLastSuccessMigration(MigrateStatus::TYPE_CATCHUP);
        $newMaxId = $maxId = $migrateStatus ? $migrateStatus->max_id : 0;
        $lastMigratedAt = $migrateStatus ? $migrateStatus->started_at : 0;

        MigrateStatus::createOrFinish(MigrateStatus::TYPE_CATCHUP);
        $minTime = time() - 30 * 24 * 60 * 60;
        try {
            $batchQueryResult = Node::find()
                ->where(['type' => 'program'])
                ->andWhere(['>', 'created', $minTime])
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
                    foreach ($nodes as $program) {
                        $content = $this->migrateProgram($program, $adminId, $siteId);
                        if ($content && $content->catchup_id > $newMaxId) {
                            $newMaxId = $content->catchup_id;
                        }
                    }
                }
            }
            $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_CATCHUP);
            $migrateStatus->finish(MigrateStatus::STATUS_SUCCESS, $newMaxId);
            echo "\n********** Migrated programs successfully ***********\n";
        } catch (Exception $e) {
            $migrateStatus = MigrateStatus::getRunningMigration(MigrateStatus::TYPE_CATCHUP);
            if ($migrateStatus) {
                $migrateStatus->finish(MigrateStatus::STATUS_FAIL, 0, $e->getMessage());
            }
            echo $e->__toString();
            echo "\n********** Migrated programs failed ***********\n";
        }
    }

    private function getCategoryByParent($parentId)
    {
        $contentCatAsm = ContentCategoryAsm::findOne(['content_id' => $parentId]);
        return $contentCatAsm->category_id;
    }

    private function migrateProgram($node, $adminId, $siteId)
    {
        echo "\n\n\n=======Migrating node: $node->nid =========\n";
        $nodeId = $node->nid;
        $parentId = $this->getParentId($nodeId);
        if (!$parentId) {
            echo 'Error: NOT FOUND Parent Channel';
            return;
        }
        $categoryId = $this->getCategoryByParent($parentId);
        if (!$categoryId) {
            echo 'Error: NOT FOUND Category';
            return;
        }


        $content = $this->getContentByCatchupId($node->nid);
        $new = !$content;
        if (!$new && $content->updated_at == $node->changed) {
            echo 'Not any change';
            return $content;
        }

        if ($new) {
            $startTime = $this->getStartTime($nodeId);
            if (!$startTime) {
                $startTime = 0;
            }
            $endTime = $this->getEndTime($nodeId);
            if (!$endTime) {
                $endTime = 0;
            }
        } else {
            $startTime = 0;
            $endTime = 0;
        }

        $content = $this->createContentFromNode($content, $node, $categoryId, $adminId, $siteId, $parentId, $endTime - $startTime);

        if (!$content->save()) {
            Yii::error($content->errors);
            echo "Error: cannot save content $content->id \n";
            VarDumper::dump($content->errors);
            return;
        }
        if ($new) {

            $contentRows[] = $content->attributes;

            $contentCategoryAsm = new ContentCategoryAsm();
            $contentCategoryAsm->category_id = $categoryId;
            $contentCategoryAsm->content_id = $content->id;
            $contentCategoryAsm->created_at = time();
            $contentCategoryAsm->save();

            $contentSiteAsm = new ContentSiteAsm();
            $contentSiteAsm->site_id = $siteId;
            $contentSiteAsm->content_id = $content->id;
            $contentSiteAsm->status = $content->status == Content::STATUS_ACTIVE ? ContentSiteAsm::STATUS_ACTIVE : ContentSiteAsm::STATUS_INACTIVE;
            $contentSiteAsm->created_at = time();
            $contentSiteAsm->updated_at = time();
            $contentSiteAsm->save();
        }
        $liveProgram = LiveProgram::findOne(['content_id' => $content->id]);
        if (!$liveProgram) {
            $liveProgram = new LiveProgram();
            $liveProgram->channel_id = $parentId;
            $liveProgram->content_id = $content->id;
            $liveProgram->created_at = $node->created;
        }
        $liveProgram->updated_at = $node->changed;
        $liveProgram->name = $content->display_name;
        $liveProgram->started_at = $startTime;
        $liveProgram->ended_at = $endTime;
        $liveProgram->status = $content->status == Content::STATUS_ACTIVE ? LiveProgram::READY : LiveProgram::NOT_RECORDED;
        $liveProgram->save();

//        if (!$this->init) {

            $contentProfile = ContentProfile::findOne(['content_id' => $content->id]);
            if (!$contentProfile) {
                $contentProfile = new ContentProfile();
                $contentProfile->content_id = $content->id;
                $contentProfile->type = ContentProfile::TYPE_STREAM;
                $contentProfile->quality = $this->isHD($content) ? ContentProfile::QUALITY_HD : ContentProfile::QUALITY_SD;
                $contentProfile->created_at = $node->created;
            }
            $contentProfile->name = $content->display_name;
            $contentProfile->status = $content->status == Content::STATUS_ACTIVE ? ContentProfile::STATUS_ACTIVE : ContentProfile::STATUS_INACTIVE;;
            $contentProfile->updated_at = $node->changed;
            $contentProfile->save();

            $contentProfileSiteAsm = ContentProfileSiteAsm::findOne(['content_profile_id' => $contentProfile->id]);
            if (!$contentProfileSiteAsm) {
                $contentProfileSiteAsm = new ContentProfileSiteAsm();
                $contentProfileSiteAsm->content_profile_id = $contentProfile->id;
                $contentProfileSiteAsm->site_id = $siteId;
                $contentProfileSiteAsm->created_at = $node->created;
            }
            $contentProfileSiteAsm->updated_at = $node->changed;
            $contentProfileSiteAsm->status = $contentProfile->status == ContentProfile::STATUS_ACTIVE ? ContentProfileSiteAsm::STATUS_ACTIVE : ContentProfileSiteAsm::STATUS_INACTIVE;
            $contentProfileSiteAsm->url = $this->getContentProfileUrl($content);
            $contentProfileSiteAsm->save();

//        }

        return $content;
    }

    private function createContentFromNode($content, $node, $categoryId, $adminId, $siteId, $parentId = null, $duration = 0)
    {
        $new = false;
        if (!$content) {
            $content = new Content();
            $new = true;
        }
        $content->parent_id = $parentId;
        if ($new) {
            $content->type = Content::TYPE_LIVE_CONTENT;
            $content->catchup_id = $node->nid;
            $content->code = 'MSCU' . $node->nid; // To Do: bo sung quy luat sinh ma
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
            $content->duration = $duration;
        }
        $content->display_name = $node->title;
        $content->ascii_name = CVietnameseTools::removeSigns($content->display_name);
        $content->en_name = $node->title;

        $content->updated_at = $node->changed;

        $status = $status = $this->getStatus($node->nid);
        $content->status = $status;

        return $content;
    }

    private function getStatus($nodeId)
    {
        $successfullWrite = FieldDataFieldSuccessfulWrite::findOne(['entity_id' => $nodeId]);
        if ($successfullWrite && $successfullWrite->field_successful_write_value == 1) {
            return Content::STATUS_ACTIVE;
        }
        return Content::STATUS_INVISIBLE;
    }

    private function getStartTime($nodeId)
    {
        $dateStr = $this->getDate($nodeId);
        if (!$dateStr) {
            echo 'Error NOT FOUND DATE';
            return null;
        }
        $startTimeStr = $this->getStartTimeStr($nodeId);
        if (!$startTimeStr) {
            echo 'Error NOT FOUND START TIME';
            return null;
        }
        $fullTimeStr = trim($dateStr) . ' ' . trim($startTimeStr);
        echo "fullTimeStr: $fullTimeStr";
        $startTime = date_create_from_format('Ymd H:i', trim($dateStr) . ' ' . trim($startTimeStr));
        if (!$startTime) {
            echo 'Error PARSE START TIME';
            return null;
        }


        return $startTime->getTimestamp();
    }

    private function getEndTime($nodeId)
    {
        $dateStr = $this->getDate($nodeId);
        if (!$dateStr) {
            echo 'Error NOT FOUND DATE';
            return null;
        }
        $endTimeStr = $this->getEndTimeStr($nodeId);
        if (!$endTimeStr) {
            echo 'Error NOT FOUND END TIME';
            return null;
        }
        $endTime = date_create_from_format('Ymd H:i', trim($dateStr) . ' ' . trim($endTimeStr));

//        echo "endTime: $endTime";

        if (!$endTime) {
            echo 'Error PARSE END TIME';
            return null;
        }

        return $endTime->getTimestamp();
    }

    private function getDate($nodeId)
    {
        $channelProRef = FieldDataFieldProgramReference::findOne(['field_program_reference_nid' => $nodeId]);
        if (!$channelProRef) {
            return null;
        }
        $channelDate = FieldDataFieldChannelDate::findOne(['entity_id' => $channelProRef->entity_id]);
        if (!$channelDate) {
            return null;
        }
        echo "Date: $channelDate->field_channel_date_value";
        return $channelDate->field_channel_date_value;
    }

    private function getStartTimeStr($nodeId)
    {
        $startTime = FieldDataFieldStartTime::find()
            ->where(['entity_id' => $nodeId])->one();
        if ($startTime) {
            return $startTime->field_start_time_value;
        }
    }

    private function getEndTimeStr($nodeId)
    {
        $endTime = FieldDataFieldEndTime::find()
            ->where(['entity_id' => $nodeId])->one();
        if ($endTime) {
            return $endTime->field_end_time_value;
        }
    }

    private function getParentId($nodeId)
    {
        $channelProRef = FieldDataFieldProgramReference::findOne(['field_program_reference_nid' => $nodeId]);
        if (!$channelProRef) {
            return null;
        }
        $channelDateRef = FieldDataFieldChannelReference::findOne(['entity_id' => $channelProRef->entity_id]);
        if (!$channelDateRef) {
            return null;
        }
        $parentContent = Content::findOne(['catchup_id' => $channelDateRef->field_channel_reference_nid]);
        if (!$parentContent) {
            return null;
        }
        return $parentContent->id;
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
}