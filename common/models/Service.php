<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * This is the model class for table "{{%service}}".
 *
 * @property integer $id
 * @property integer $site_id
 * @property string $name
 * @property string $display_name
 * @property integer $pricing_id
 * @property string $description
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $free_download_count
 * @property integer $free_duration
 * @property integer $free_view_count
 * @property integer $free_gift_count
 * @property integer $period
 * @property integer $auto_renew
 * @property integer $free_days
 * @property integer $max_daily_retry
 * @property integer $max_day_failure_before_cancel
 * @property string $admin_note
 * @property string $full_types
 * @property integer $day_register_again
 * @property integer $root_service_id
 * @property integer $type
 *
 * @property Site $site
 * @property Pricing $pricing
 * @property ServiceCategoryAsm[] $serviceCategoryAsms
 * @property ServiceGroupAsm[] $serviceGroupAsms
 * @property SmsMoSyntax[] $smsMoSyntaxes
 * @property SubscriberServiceAsm[] $subscriberServiceAsms
 * @property SubscriberTransaction[] $subscriberTransactions
 * @property Service $rootService
 * @property Service $tempService
 * @property SumService[] $sumServices
 * @property SumServiceAmount[] $sumServiceAmounts
 */
class Service extends \yii\db\ActiveRecord
{
    public static $service_autorenew = [1 => 'Tự động gia hạn', 0 => 'Không gia hạn'];
    public $price_coin;
    public $watching_period;
    public $full_services = [];

    public $full_video        = 0;
    public $full_live         = 0;
    public $full_music        = 0;
    public $full_news         = 0;
    public $full_clip         = 0;
    public $full_karaoke      = 0;
    public $full_radio        = 0;
    public $full_live_content = 0;

    const STATUS_REMOVE   = -1;
    const STATUS_INACTIVE = 0;
    const STATUS_TEMP     = 1;
    const STATUS_PENDING  = 2;
    const STATUS_PAUSE    = 3;
    const STATUS_ACTIVE   = 10;

    const TYPE_AUTO_RENEW = 1;
    const TYPE_NOT_RENEW  = 0;

    const SERVICE_TYPE_PRODUCTION = 1;
    const SERVICE_TYPE_TEMP       = 0;

    const SCOPE_SP    = 1;
    const SCOPE_ADMIN = 2;

    public static $service_status = [
//        self::STATUS_REMOVE   => 'Xóa',
        self::STATUS_INACTIVE => 'Từ chối',
        self::STATUS_TEMP     => 'Nháp',
        self::STATUS_PENDING  => 'Chờ duyệt',
        self::STATUS_PAUSE    => 'Tạm dừng',
        self::STATUS_ACTIVE   => 'Đã duyệt',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'site_id',
                    'name',
                    'display_name',
                    'pricing_id',
                    'period',
                    'max_daily_retry',
                    'max_day_failure_before_cancel',
                    'free_days',
                    'auto_renew',
                    'status',
                ],
                'required',
                // 'on' => 'create_update'
            ],
            [
                [
                    'site_id',
                    'type',
                    'day_register_again',
                    'root_service_id',
                    'status',
                    'created_at',
                    'updated_at',
                    'free_download_count',
                    'free_duration',
                    'free_view_count',
                    'free_gift_count',
                    'period',
                    'auto_renew',
                    'free_days',
                    'pricing_id',
                    'max_daily_retry',
                    'max_day_failure_before_cancel',
                ],
                'integer',
            ],
            [['description', 'admin_note'], 'string'],
            [['name'], 'string', 'max' => 32],
            [['name'], 'unique', 'message' => Yii::t('app','Mã gói cước đã tồn tại. Vui lòng nhập lại.')],
            [['full_services'], 'safe'],
            [['display_name', 'admin_note', 'full_types'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                            => Yii::t('app', 'ID'),
            'site_id'                       => Yii::t('app', 'Service Provider ID'),
            'name'                          => Yii::t('app', 'Mã gói'),
            'display_name'                  => Yii::t('app', 'Tên gói'),
            'description'                   => Yii::t('app', 'Mô tả'),
            'status'                        => Yii::t('app', 'Trạng thái'),
            'created_at'                    => Yii::t('app', 'Ngày tạo'),
            'updated_at'                    => Yii::t('app', 'Ngày cập nhật'),
            'free_download_count'           => Yii::t('app', 'Free Download Count'),
            'free_duration'                 => Yii::t('app', 'Free Duration'),
            'free_view_count'               => Yii::t('app', 'Free View Count'),
            'free_gift_count'               => Yii::t('app', 'Free Gift Count'),
            'price'                         => Yii::t('app', 'Giá'),
            'period'                        => Yii::t('app', 'Chu kỳ'),
            'auto_renew'                    => Yii::t('app', 'Cơ chế gia hạn'),
            'free_days'                     => Yii::t('app', 'Số ngày miễn phí'),
            'max_daily_retry'               => Yii::t('app', 'Số lần gia hạn tối đa trong ngày'),
            'max_day_failure_before_cancel' => Yii::t('app', 'Số ngày gia hạn'),
            'admin_note'                    => Yii::t('app', 'Admin note'),
            'day_register_again'            => 'Số ngày được tính lại miễn phí lần đầu đăng ký',
            'pricing_id'                    => 'Giá',
        ];
    }

    public function getStatusClassCss()
    {
        switch ($this->status) {
            case self::STATUS_REMOVE:
                return 'default';
            case self::STATUS_INACTIVE:
                return 'danger';
            case self::STATUS_TEMP:
                return 'info';
            case self::STATUS_PENDING:
                return 'warning';
            case self::STATUS_PAUSE:
                return "primary";
            case self::STATUS_ACTIVE:
                return "success";
        }
    }

    /**
     * Nếu condition = false có nghĩa là trường hợp lấy gói cước cho transaction: Lấy toàn bộ gói cước đã tồn tại trong hệ thống
     * Nếu condition = true có nghĩa là lấy toàn bộ danh sách gói cước đang active, trong thời gian hiệu lực, còn thời gian hiệu lực
     */
//    public static function listPackage($condition = true){
    //        if($condition){
    //            $objs =  ContentPackage::find()->where(['status'=>ContentPackage::STATUS_ACTIVE])
    //                ->andWhere('effective_at IS NULL OR effective_at <=:effective_at',[':effective_at' => time()] )
    //                ->andWhere('expired_at IS NULL OR expired_at >=:expired_at',[':expired_at' => time()] )
    //                ->all();
    //            $objs= Service::find()->where(['status'=>Service::STATUS_ACTIVE])
    //
    //        }else{
    //            $objs = ContentPackage::find()->all();
    //        }
    //
    //
    //
    //        $lst = [];
    //        foreach ($objs as $obj) {
    //            $lst[$obj->id] = $obj->name;
    //        }
    //        return $lst;
    //
    //    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (is_array($this->full_services) && count($this->full_services) > 0) {
                $this->loadFullServices();
                $this->full_types = json_encode([
                    'full_video'        => $this->full_video,
                    'full_live'         => $this->full_live,
                    'full_live_content' => $this->full_live_content,
                    'full_clip'         => $this->full_clip,
                    'full_karaoke'      => $this->full_karaoke,
                    'full_music'        => $this->full_music,
                    'full_news'         => $this->full_news,
                    'full_radio'        => $this->full_radio,
                ]);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        # return parent::find()->where(['store_id' => User::getStoreID()]);
        return parent::find()->where('service.status != :status', ['status' => self::STATUS_REMOVE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPricing()
    {
        return $this->hasOne(Pricing::className(), ['id' => 'pricing_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRootService()
    {
        return $this->hasOne(Service::className(), ['id' => 'root_service_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTempService()
    {
        return $this->hasOne(Service::className(), ['root_service_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCategoryAsms()
    {
        return $this->hasMany(ServiceCategoryAsm::className(), ['service_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSmsMoSyntaxes()
    {
        return $this->hasMany(SmsMoSyntax::className(), ['service_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberServiceAsms()
    {
        return $this->hasMany(SubscriberServiceAsm::className(), ['service_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceGroupAsms()
    {
        return $this->hasMany(ServiceGroupAsm::className(), ['service_id' => 'id']);
    }

    public static function getListPrices($site = null)
    {
        /**
         * @var $prices Pricing[]
         */
        if ($site) {
            $prices = Pricing::findAll(['type' => Pricing::TYPE_SERVICE, 'site_id' => $site->id]);
        } else {
            $prices = Pricing::findAll(['type' => Pricing::TYPE_SERVICE]);
        }
        $list = [];
        foreach ($prices as $price) {
            $list[$price->id] = $price->getPriceInfo();
        }
        return $list;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberTransactions()
    {
        return $this->hasMany(SubscriberTransaction::className(), ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSumServices()
    {
        return $this->hasMany(SumService::className(), ['service_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSumServiceAmounts()
    {
        return $this->hasMany(SumServiceAmount::className(), ['service_id' => 'id']);
    }

    public function getVodCategories()
    {
        $listCategory = '';
        /**
         * @var ServiceCategoryAsm[] $vod_category_asm
         */
        $vod_category_asm = $this->getServiceCategoryAsms()->where(['type' => Category::TYPE_FILM])->all();
        foreach ($vod_category_asm as $asm) {
            $listCategory .= ' ' . $asm->category_id . ', ';
        }
        return $listCategory;
    }

    public function getLiveCategories()
    {
        $listCategory = '';
        /**
         * @var ServiceCategoryAsm[] $live_category_asm
         */
        $live_category_asm = $this->getServiceCategoryAsms()->where(['type' => Category::TYPE_LIVE])->all();
        foreach ($live_category_asm as $asm) {
            $listCategory .= ' ' . $asm->category_id . ', ';
        }
        return $listCategory;
    }

    public function getMusicCategories()
    {
        $listCategory = '';
        /**
         * @var ServiceCategoryAsm[] $live_category_asm
         */
        $live_category_asm = $this->getServiceCategoryAsms()->where(['type' => Category::TYPE_MUSIC])->all();
        foreach ($live_category_asm as $asm) {
            $listCategory .= ' ' . $asm->category_id . ', ';
        }
        return $listCategory;
    }

    public function getNewCategories()
    {
        $listCategory = '';
        /**
         * @var ServiceCategoryAsm[] $live_category_asm
         */
        $live_category_asm = $this->getServiceCategoryAsms()->where(['type' => Category::TYPE_NEWS])->all();
        foreach ($live_category_asm as $asm) {
            $listCategory .= ' ' . $asm->category_id . ', ';
        }
        return $listCategory;
    }

    public function getClipCategories()
    {
        $listCategory = '';
        /**
         * @var ServiceCategoryAsm[] $live_category_asm
         */
        $live_category_asm = $this->getServiceCategoryAsms()->where(['type' => Category::TYPE_CLIP])->all();
        foreach ($live_category_asm as $asm) {
            $listCategory .= ' ' . $asm->category_id . ', ';
        }
        return $listCategory;
    }

    public function getKaraokeCategories()
    {
        $listCategory = '';
        /**
         * @var ServiceCategoryAsm[] $live_category_asm
         */
        $live_category_asm = $this->getServiceCategoryAsms()->where(['type' => Category::TYPE_KARAOKE])->all();
        foreach ($live_category_asm as $asm) {
            $listCategory .= ' ' . $asm->category_id . ', ';
        }
        return $listCategory;
    }

    public function getRadioCategories()
    {
        $listCategory = '';
        /**
         * @var ServiceCategoryAsm[] $live_category_asm
         */
        $live_category_asm = $this->getServiceCategoryAsms()->where(['type' => Category::TYPE_RADIO])->all();
        foreach ($live_category_asm as $asm) {
            $listCategory .= ' ' . $asm->category_id . ', ';
        }
        return $listCategory;
    }

    /**
     * @return array
     */
    public static function listStatus()
    {
        $lst = [
            self::STATUS_ACTIVE   => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_REMOVE   => 'Remove',
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getStatusName()
    {
        $lst = self::listStatus();
        if (array_key_exists($this->status, $lst)) {
            return $lst[$this->status];
        }
        return $this->status;
    }

    /**
     * @return array
     */
    public static function listType()
    {
        $lst = [
            self::TYPE_AUTO_RENEW => 'Auto renew',
            self::TYPE_NOT_RENEW  => 'Not renew',
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getTypeName()
    {
        $lst = self::listType();
        if (array_key_exists($this->auto_renew, $lst)) {
            return $lst[$this->auto_renew];
        }
        return $this->auto_renew;
    }

//TODO implement sau
    public function getAddTimeTelcoCatID()
    {
        return '';
    }

//TODO implement sau
    public function getAddTimeTelcoContentID()
    {
        return '';
    }

    public function isReadOnly($scope = self::SCOPE_SP)
    {
        return false;

        // sua flow hoat dong, o duoi la flow cu

        if ($scope == self::SCOPE_ADMIN) {
            return true;
        }

        if ($this->status == self::STATUS_TEMP || $this->status == self::STATUS_INACTIVE) {
            return false;
        } else {
            return true;
        }
    }

    public function getRoot()
    {
        $rootService = $this->rootService;
        if ($rootService && $rootService->status > self::STATUS_INACTIVE) {
            return $rootService;
        } else {
            return $this;
        }
    }

    public function validateServiceCycle($nextStatus, $scope)
    {
        if ($nextStatus == Service::STATUS_REMOVE) {
            /**
             * Change to status remove when current status in temp, only SP
             */
            if ($this->status == self::STATUS_TEMP && $scope == self::SCOPE_SP) {
                return true;
            } else {
                return false;
            }
        } elseif ($nextStatus == Service::STATUS_INACTIVE) {
            /**
             * Change to status INACTIVE (Tu choi) when current status in (PENDING, ACTIVE), only Admin
             */
            if (($this->status == self::STATUS_PENDING || $this->status == self::STATUS_ACTIVE) && $scope == self::SCOPE_ADMIN) {
                return true;
            } else {
                return false;
            }
        } elseif ($nextStatus == Service::STATUS_TEMP) {
            /**
             * Change to status TEMP (Nhap) when current status in (PENDING), only SP
             */
            if (($this->status == self::STATUS_PENDING && $scope == self::SCOPE_SP) || ($this->status >= self::STATUS_PENDING && $scope == self::SCOPE_ADMIN)) {
                return true;
            } else {
                return false;
            }
        } elseif ($nextStatus == Service::STATUS_PENDING) {
            /**
             * Change to status PENDING (Cho duyet) when current status in (TEMP), only SP
             */
            if ($this->status == self::STATUS_TEMP && $scope == self::SCOPE_SP) {
                return true;
            } else {
                return false;
            }
        } elseif ($nextStatus == Service::STATUS_PAUSE) {
            /**
             * Change to status PAUSE (Tam dung) when current status in (ACTIVE), only SP
             */
            if ($this->status == self::STATUS_ACTIVE && $scope == self::SCOPE_SP) {
                return true;
            } else {
                return false;
            }
        } elseif ($nextStatus == Service::STATUS_ACTIVE) {
            /**
             * Change to status ACTIVE (Da duyet) when current status in (PENDING - admin, PAUSE-sp), only SP
             */
            if (($this->status == self::STATUS_PAUSE && $scope == self::SCOPE_SP) || ($this->status == self::STATUS_PENDING && $scope == self::SCOPE_ADMIN)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function mergeRoot()
    {
        $rootService = $this->rootService;
        if ($rootService) {
            $rootService->name                          = $this->name;
            $rootService->display_name                  = $this->display_name;
            $rootService->description                   = $this->description;
            $rootService->free_download_count           = $this->free_download_count;
            $rootService->free_duration                 = $this->free_duration;
            $rootService->free_view_count               = $this->free_view_count;
            $rootService->free_gift_count               = $this->free_gift_count;
            $rootService->pricing_id                    = $this->pricing_id;
            $rootService->period                        = $this->period;
            $rootService->auto_renew                    = $this->auto_renew;
            $rootService->free_days                     = $this->free_days;
            $rootService->max_daily_retry               = $this->max_daily_retry;
            $rootService->max_day_failure_before_cancel = $this->max_day_failure_before_cancel;
            $rootService->status                        = self::STATUS_ACTIVE;
            $rootService->type                          = self::SERVICE_TYPE_PRODUCTION;
            $rootService->full_types                    = $this->full_types;

            if ($rootService->update()) {
                ServiceCategoryAsm::deleteAll(['service_id' => $rootService->id]);

                foreach ($this->serviceCategoryAsms as $mapping) {
                    $serviceMapping              = new ServiceCategoryAsm();
                    $serviceMapping->service_id  = $rootService->id;
                    $serviceMapping->category_id = $mapping->category_id;
                    $serviceMapping->type        = $mapping->type;
                    $serviceMapping->save();
                }
                ServiceCategoryAsm::deleteAll(['service_id' => $this->id]);
                $this->delete();
                return $rootService;
            }
            Yii::error($rootService->getErrors());
            return false;
        } else {
            Yii::error("Service (" . $this->id . ") Not have Root service");
            return false;
        }
    }

    /**
     * Validate xac dinh xem co duoc tao new temp service khong
     * Duoc tao temp khi: service o trang thai > TEMP, va khong co version temp nao
     *
     */
    public function validateCreateNewTemp()
    {
        if ($this->status > self::STATUS_PENDING && !$this->tempService) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Tao ban temp
     * @return Service
     */
    public function createTemp()
    {
        $temp                                = new Service();
        $temp->name                          = $this->name;
        $temp->display_name                  = $this->display_name;
        $temp->description                   = $this->description;
        $temp->free_download_count           = $this->free_download_count;
        $temp->free_duration                 = $this->free_duration;
        $temp->free_view_count               = $this->free_view_count;
        $temp->free_gift_count               = $this->free_gift_count;
        $temp->period                        = $this->period;
        $temp->auto_renew                    = $this->auto_renew;
        $temp->free_days                     = $this->free_days;
        $temp->max_daily_retry               = $this->max_daily_retry;
        $temp->max_day_failure_before_cancel = $this->max_day_failure_before_cancel;
        $temp->root_service_id               = $this->id;
        $temp->site_id                       = $this->site_id;
        $temp->full_types                    = $this->full_types;
        return $temp;
    }

    /**
     * Kiem tra xem goi cuoc nay co nam trong dien cho khuyen mai dang ky lan dau ko
     * @param $package
     * @return bool
     */
    public static function freeFirst($package)
    {
        return false;
    }

    /**
     * Thuc hien merge thong tin chuyen goi cuoc production ve temp
     */
    public function mergeTemp()
    {
        $tempService = $this->tempService;
        if ($tempService) {
            $this->name                          = $tempService->name;
            $this->display_name                  = $tempService->display_name;
            $this->description                   = $tempService->description;
            $this->free_download_count           = $tempService->free_download_count;
            $this->free_duration                 = $tempService->free_duration;
            $this->free_view_count               = $tempService->free_view_count;
            $this->free_gift_count               = $tempService->free_gift_count;
            $this->period                        = $tempService->period;
            $this->auto_renew                    = $tempService->auto_renew;
            $this->free_days                     = $tempService->free_days;
            $this->max_daily_retry               = $tempService->max_daily_retry;
            $this->max_day_failure_before_cancel = $tempService->max_day_failure_before_cancel;
            $this->status                        = self::STATUS_TEMP;
            $this->type                          = self::SERVICE_TYPE_TEMP;
            $this->full_types                    = $tempService->full_types;

            if ($this->update()) {
                ServiceCategoryAsm::deleteAll(['service_id' => $this->id]);

                foreach ($tempService->serviceCategoryAsms as $mapping) {
                    $serviceMapping              = new ServiceCategoryAsm();
                    $serviceMapping->service_id  = $this->id;
                    $serviceMapping->category_id = $mapping->category_id;
                    $serviceMapping->type        = $mapping->type;
                    $serviceMapping->save();
                }
                ServiceCategoryAsm::deleteAll(['service_id' => $tempService->id]);
                $tempService->delete();
                return true;
            } else {
                Yii::error($this->getErrors());
                return false;
            }
        } else {
            Yii::error("Service (" . $this->id . ") Not have temp service");
            return false;
        }
    }

    /**
     * HungNV edition: 05/04/16: mapping services and pricing to get price for other service
     *
     * @param $sp_id
     * @return ActiveDataProvider
     */
    public static function getListService($id = null, $sp_id)
    {
        $services = new Query();
        $services->select('*')
            ->from('service')
            ->innerJoin('pricing', 'pricing.id = service.pricing_id')
            ->andWhere(['service.status' => Service::STATUS_ACTIVE]);
        if ($id) {
            $services->andWhere(['service.id' => $id]);
        }
        $services->andWhere(['service.site_id' => $sp_id])
            ->all();
        /*
        $services= Service::find()
        ->select(['id','name','display_name','price','period','description'])
        ->andWhere(['status'=>Service::STATUS_ACTIVE])
        ->andWhere(['site_id'=>$sp_id])
        ->asArray();
         */
        $dataProvider = new ActiveDataProvider([
            'query'      => $services,
            'sort'       => [],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    public function getPackageOnGroup($is_promotion = false)
    {
        $service_ids = [];
        $mappings    = $this->serviceGroupAsms;
        foreach ($mappings as $map) {
            /**
             * @var $group ServiceGroup
             */
            $group = $map->serviceGroup;
            if ($group) {
                $services = $group->services;
                /**
                 * @var Service[] $services
                 */
                foreach ($services as $service) {
                    if ($service->period == 30 && $is_promotion) {
                        continue;
                    }

                    $service_ids[] = $service->id;
                }
            }

        }
        return $service_ids;
    }

    /**
     * @param $id
     * @param $site_id
     * @return ActiveDataProvider
     */
    public static function getDetail($id, $site_id)
    {
        $query = new Query();
        $query->select(['service.*', 'pricing.*'])
            ->from('service')
            ->innerJoin('pricing', 'service.pricing_id = pricing.id')
            ->andWhere(['service.id' => $id])
            ->andWhere(['service.site_id' => $site_id])
            ->andWhere(['service.status' => Service::STATUS_ACTIVE])
            ->all();
//        $res = Service::find()
        //            ->andWhere(['service.id' => $id])
        //            ->andWhere(['service.site_id' => $site_id])
        //            ->andWhere(['service.status' => Service::STATUS_ACTIVE]);
        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    /**
     * Parse ngay tu vnp gui sang
     * @param $str
     * @return int
     */
    public function parseDay($str)
    {
        $day = 0;
        preg_match("/(.*)(.$)/", $str, $output_array);
        $last   = $output_array[2];
        $number = $output_array[1];

        switch ($last) {
            case 'c':
                $day = $number * $this->period;
                break;
            case 'd':
                $day = $number;
                break;
            case 'w':
                $day = $number * 7;
                break;
            case 'm':
                $day = $number * 30;
                break;
            default:
                $day = intval($str);

        }
        return $day;
    }

    public function parseFullServices()
    {
        if (empty($this->full_types)) {
            return [];
        }

        $types = json_decode($this->full_types, true);
        $i     = 0;
        foreach ($types as $key => $value) {
            $this->$key = $value;
            if ($value == 1) {
                $this->full_services[] = $key;
            }
            $i++;
        }
    }

    public function loadFullServices()
    {
        foreach ($this->full_services as $full) {
            $this->$full = 1;
        }
    }

    public function isFullService($type)
    {
        $this->parseFullServices();
        switch ($type) {
            case Category::TYPE_FILM:
                return $this->full_video;
            case Category::TYPE_LIVE:
                return $this->full_live;
            case Category::TYPE_LIVE_CONTENT:
                return $this->full_live_content;
            case Category::TYPE_CLIP:
                return $this->full_clip;
            case Category::TYPE_NEWS:
                return $this->full_news;
            case Category::TYPE_RADIO:
                return $this->full_radio;
            case Category::TYPE_KARAOKE:
                return $this->full_karaoke;
            case Category::TYPE_MUSIC:
                return $this->full_music;
        }
    }

    public function getFullTypeServices()
    {
        $description = "";
        if ($this->isFullService(Category::TYPE_FILM)) {
            $description .= "Full Film | ";
        }
        if ($this->isFullService(Category::TYPE_LIVE)) {
            $description .= "Full Live | ";
        }
        if ($this->isFullService(Category::TYPE_LIVE_CONTENT)) {
            $description .= "Full Live Content | ";
        }
        if ($this->isFullService(Category::TYPE_CLIP)) {
            $description .= "Full Clip | ";
        }
        if ($this->isFullService(Category::TYPE_NEWS)) {
            $description .= "Full News | ";
        }
        if ($this->isFullService(Category::TYPE_RADIO)) {
            $description .= "Full Radio | ";
        }
        if ($this->isFullService(Category::TYPE_KARAOKE)) {
            $description .= "Full karaoke | ";
        }
        if ($this->isFullService(Category::TYPE_MUSIC)) {
            $description .= "Full music ";
        }
        if (empty($description)) {
            $description = 'None';
        }
        return $description;
    }

    public static function findServiceBySite($site_id)
    {
        return Service::find()->andWhere(['status' => Service::STATUS_ACTIVE, 'site_id' => $site_id])->all();
    }

    public static function createServiceEmpty($site_id) {
        $service = new Service();
        $service->site_id = $site_id;
        $service->id = null;
        $service->status = Service::STATUS_ACTIVE;
        return $service;
    }
}
