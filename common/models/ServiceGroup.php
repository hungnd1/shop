<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "service_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $site_id
 * @property string $icon
 * @property integer $type
 *
 * @property ServiceGroupAsm[] $serviceGroupAsms
 * @property Service[] $services
 * @property Site $site
 */
class ServiceGroup extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 10;
    public $list_service_id;
    public $service_id;
//    public $services;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_group';
    }

    public static $list_status = [
        self::STATUS_ACTIVE   => 'Hoạt động',
        self::STATUS_INACTIVE => 'Tạm dừng',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'site_id', 'type', 'list_service_id'], 'required'],
            [['status', 'created_at', 'updated_at', 'site_id', 'type'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['display_name'], 'string', 'max' => 200],
            [['icon'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => 10 * 1024 * 1024],
            // [['description'], 'string', 'max' => 1024],
            [['list_service_id'], 'safe'],
            [['services'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'name'         => 'Mã nhóm gói cước',
            'display_name' => 'Tên hiển thị',
            'description'  => 'Mô tả',
            'status'       => 'Trạng thái',
            'created_at'   => 'Ngày tạo',
            'updated_at'   => 'Ngày cập nhật',
            'site_id'      => 'Site',
            'services'     => 'services',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceGroupAsms()
    {
        return $this->hasMany(ServiceGroupAsm::className(), ['service_group_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::className(), ['id' => 'service_id'])
            ->via('serviceGroupAsms');
    }

    public static function getCheckBoxListService($sp_id)
    {
        $data = Service::find()
            ->select(['id', 'name'])
            ->andWhere(['site_id' => $sp_id])
            ->andWhere(['status' => Service::STATUS_ACTIVE])->asArray()->all();
        return ArrayHelper::map($data, 'id', 'name');
    }

    public function createServiceGroupAsm()
    {
        ServiceGroupAsm::deleteAll(['service_group_id' => $this->id]);
        if ($this->list_service_id) {

            if (is_array($this->list_service_id) && count($this->list_service_id) > 0) {
                foreach ($this->list_service_id as $service_id) {
                    $asm                   = new ServiceGroupAsm();
                    $asm->service_group_id = $this->id;
                    $asm->service_id       = $service_id;
                    $asm->save();
                }
            }
            return true;
        }
        return true;
    }

    public function getServiceProvider()
    {
        $query = Service::find()->select("*, service.id as id");
        $query->innerJoin('service_group_asm', 'service_group_asm.service_id= service.id');
        $query->innerJoin('pricing', 'pricing.id= service.pricing_id');
        $query->andWhere(['service_group_asm.service_group_id' => $this->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }

    public function getImageLink()
    {
        return $this->icon ? Url::to('@web/' . Yii::getAlias('@service_group_icon') . DIRECTORY_SEPARATOR . $this->icon, true) : '';
    }

    /**
     * @param $site_id
     * @param null $content_id
     * @param null $subscriber
     * @return array
     */
    public static function getServiceGroup($site_id, $content_id = null, $subscriber = null)
    {
        $result          = [];
        $arrService          = [];
        $arrGroupService          = [];
        $list_my_service = [];
        if ($subscriber) {
            $listService = $subscriber->services;
            foreach ($listService as $row) {
                $list_my_service[] = $row->id;
            }
        }
        $serviceGroups = ServiceGroup::find()
            ->select(['id', 'name', 'display_name', 'icon', 'description'])
            ->andWhere(['status' => ServiceGroup::STATUS_ACTIVE])
            ->andWhere(['site_id' => $site_id])
            ->all();
        /** Lấy danh sách các gói cước theo  nhóm gói cước */
        /** @var  $row  ServiceGroup */
        foreach ($serviceGroups as $row) {
            $group_tmp             = $row->getAttributes(null, ['site_id', 'type', 'updated_at', 'created_at', 'status']);
            $group_tmp['icon']     = $row->getImageLink();
            $group_tmp['services'] = [];
            $serviceAsms           = $row->serviceGroupAsms;
            /** @var  $asm ServiceGroupAsm */
            foreach ($serviceAsms as $asm) {
                /** @var  $service Service */
                $service = $asm->service;
                /** Kiểm tra trạng thái gói cước đã active chưa? Nếu chưa active thì bỏ qua k xét */
                if($service->status != Service::STATUS_ACTIVE){
                    continue;
                }
                $isCheck = true;
                if ($content_id) {
                    $isCheck = self::checkContentInService($content_id, $service->id); //cuongvm them vao de check theo noi dung
                }
                if ($isCheck) {
                    $service_arr                  = [];
                    $service_arr['id']            = $service->id;
                    $service_arr['name']          = $service->name;
                    $service_arr['display_name']  = $service->display_name;
                    $service_arr['price_coin']    = $service->pricing ? $service->pricing->price_coin : -1;
                    $service_arr['price_sms']     = $service->pricing ? $service->pricing->price_sms : -1;
                    $service_arr['period']        = $service->period;
                    $service_arr['description']   = $service->description;
                    $service_arr['is_my_package'] = in_array($service->id, $list_my_service);
                    $group_tmp['services'][]      = $service_arr;
                }
            }
//            $result[] = $group_tmp;
            $arrGroupService[] = $group_tmp;
        }

        /** Lấy các gói cước lẻ */
        $services = Service::findAll(['status' => Service::STATUS_ACTIVE,'site_id' => $site_id]);

        foreach ($services as $service){
            if(count($service->serviceGroupAsms) >0){
                continue;
            }
//            $tmp             = $row->getAttributes(null, ['root_service_id']);
            $isCheck = true;
            if ($content_id) {
                $isCheck = self::checkContentInService($content_id, $service->id); //cuongvm them vao de check theo noi dung
                if(!$isCheck){
                    continue;
                }
            }
//            var_dump($service->pricing);exit;
            $s                  = [];
            $s['id']            = $service->id;
            $s['name']          = $service->name;
            $s['display_name']  = $service->display_name;
            $s['price_coin']    = $service->pricing ? $service->pricing->price_coin : -1;
            $s['price_sms']     = $service->pricing ? $service->pricing->price_sms : -1;
            $s['period']        = $service->period;
            $s['description']   = $service->description;
            $s['is_my_package'] = in_array($service->id, $list_my_service);

            $arrService[] = $s;

        }

        $result['group_service'] = $arrGroupService;
        $result['services'] = $arrService;

        return $result;
    }

    public static function getListServiceGroup($site_id, $content_id)
    {
        $result          = [];
        $list_my_service = [];
//        Yii::info($list_my_service);
        $serviceGroups = ServiceGroup::find()
            ->select(['id', 'name', 'display_name', 'icon', 'description'])
            ->andWhere(['status' => ServiceGroup::STATUS_ACTIVE])
            ->andWhere(['site_id' => $site_id])
            ->all();
        /** @var  $row  ServiceGroup */
        foreach ($serviceGroups as $row) {
            $group_tmp             = $row->getAttributes(null, ['site_id', 'updated_at', 'created_at', 'status']);
            $group_tmp['icon']     = $row->getImageLink();
            $group_tmp['services'] = [];
            $serviceAsms           = $row->serviceGroupAsms;
            /** @var  $asm ServiceGroupAsm */
            foreach ($serviceAsms as $asm) {
                /** @var  $service Service */
                $service = $asm->service;

                $isCheck = self::checkContentInService($content_id, $service->id); //cuongvm them vao de check theo noi dung
                if ($isCheck) {
                    $service_arr                  = [];
                    $service_arr['id']            = $service->id;
                    $service_arr['name']          = $service->name;
                    $service_arr['display_name']  = $service->display_name;
                    $service_arr['price_coin']    = $service->pricing ? $service->pricing->price_coin : -1;
                    $service_arr['price_sms']     = $service->pricing ? $service->pricing->price_sms : -1;
                    $service_arr['period']        = $service->period;
                    $service_arr['description']   = $service->description;
                    $service_arr['is_my_package'] = in_array($service->id, $list_my_service);
                    $group_tmp['services'][]      = $service_arr;

                }

            }
            $result[] = $group_tmp;
        }
        return $result;
    }

    /**
     * Ban basic cu
     */
    public static function getListServiceGroup_($sp_id, $sub = null)
    {
        $result          = [];
        $list_my_service = [];
        /**
         * @var $sub Subscriber
         */
        if ($sub) {
            $listService = $sub->services;

            foreach ($listService as $row) {
                $list_my_service[] = $row->id;
            }
        }
        Yii::info($list_my_service);
        $servicegroups = ServiceGroup::find()
            ->select(['id', 'name', 'display_name', 'icon', 'description'])
            ->andWhere(['status' => ServiceGroup::STATUS_ACTIVE])
            ->andWhere(['site_id' => $sp_id])
            ->all();
        /** @var  $row  ServiceGroup */
        foreach ($servicegroups as $row) {
            $group_tmp             = $row->getAttributes(null, ['site_id', 'updated_at', 'created_at', 'status']);
            $group_tmp['icon']     = $row->getImageLink();
            $group_tmp['services'] = [];
            $serviceAsms           = $row->serviceGroupAsms;
            /** @var  $asm ServiceGroupAsm */
            foreach ($serviceAsms as $asm) {
                /** @var  $service Service */
                $service = $asm->service;

                $service_arr                 = [];
                $service_arr['id']           = $service->id;
                $service_arr['name']         = $service->name;
                $service_arr['display_name'] = $service->display_name;
//                $service_arr['price'] = $service->price;
                $service_arr['period']        = $service->period;
                $service_arr['description']   = $service->description;
                $service_arr['is_my_package'] = in_array($service->id, $list_my_service);
                $group_tmp['services'][]      = $service_arr;

            }
            $result[] = $group_tmp;
        }
        return $result;
    }

    public static function getFirstPackage($sp_id, $type)
    {
        $msisdn     = \common\helpers\VNPHelper::getMsisdn(false, true);
        $controller = \Yii::$app->requestedAction->controller;
        /**
         * @var Site $sp
         */
        $sp = (isset($controller->serviceProvider)) ? $controller->serviceProvider : null;
        \Yii::info($sp);
        $subscriber_id = null;
        if ($msisdn && $sp) {
            /** @var  $subscriber Subscriber */
            $subscriber = Subscriber::findByMsisdn($msisdn, $sp->id);
            if ($subscriber) {
                $subscriber_id = $subscriber->id;
            }
        }
        $group = ServiceGroup::findOne(['site_id' => $sp_id, 'type' => $type]);
        if (!$group) {
            return null;
        }
        $asms    = $group->getServiceGroupAsms();
        $service = $asms->select(['service.id', 'service.price', 'service.display_name', 'service.price', 'service.period', 'service.description', 'service.name'])
            ->innerJoin('service', 'service.id=service_group_asm.service_id')
            ->orderBy('service.price asc')
            ->asArray()
            ->one();
        if (count($service) > 0) {
            if ($subscriber_id == null) {
                $service['is_my_package'] = false;
            } else {
                $purchaseService = $subscriber->getServices()->andWhere(['id' => $service['id']])->one();
                if ($purchaseService) {
                    $service['is_my_package'] = true;
                }
            }
        }
        return $service;

    }

//    public static function checkServviceInGroup($service_id){
//        $serviceGroup = ServiceGroup::find()->where([''])
//    }

    public static function checkServiceInGroupByContent($content_id, $service_group_id)
    {
        /** @var  $serviceGroup ServiceGroup*/
        $serviceGroup = ServiceGroup::findOne($service_group_id);
        /** Nếu không tồn tại serviceGroup thì out */
        if (!$serviceGroup) {
            return false;
        }
        /** Nếu  serviceGroup không chứa gói cước nào thì cũng out*/
        if (!$serviceGroup->services) {
            return false;
        }
        /** Check các gói cước trong serviceGroup có được map với nội dung không */
        /** @var  $service Service*/
        foreach ($serviceGroup->services as $service) {
            /** @var  $lstCategory ServiceCategoryAsm*/
            $lstCategory = ServiceCategoryAsm::find()->select('category_id')->distinct()
                ->andWhere(['service_id' => $service->id])
                ->asArray()->all();
            foreach ($lstCategory as $category) {
                $lstConCatAsm = ContentCategoryAsm::findAll(['content_id' => $content_id, 'category_id' => $category['category_id']]);
                if (count($lstConCatAsm) > 0) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function getServiceInGroupByContent($content_id, $service_group_id)
    {
        $lst      = [];
        $services = ServiceGroupAsm::find()->select('service_id')->andWhere(['service_group_id' => $service_group_id])->asArray()->all();
        foreach ($services as $service) {
            $status = self::checkContentInService($content_id, $service['service_id']);
            if ($status) {
                $s = \api\models\Service::findOne(['id' => $service['service_id'], 'status' => Service::STATUS_ACTIVE]);
                if ($s) {
                    array_push($lst, $s);
                }
            }
        }
        return $lst;
    }
    public static function checkContentInService($content_id, $service_id)
    {
        $lstCategory = ServiceCategoryAsm::find()->select('category_id')->distinct()
            ->andWhere(['service_id' => $service_id])
            ->asArray()->all();
        foreach ($lstCategory as $category) {
            $lstConCatAsm = ContentCategoryAsm::findAll(['content_id' => $content_id, 'category_id' => $category['category_id']]);
            if (count($lstConCatAsm) > 0) {
                return true;
            }
        }
        return false;
    }
}
