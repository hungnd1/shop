<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "dealer".
 *
 * @property integer $id
 * @property integer $site_id
 * @property string $code
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property integer $created_at
 * @property string $description
 * @property integer $status
 * @property integer $updated_at
 * @property integer $user_admin_id
 * @property string $logo
 *
 * @property Site $site
 * @property DealerSubscriberAsm[] $dealerSubscriberAsms
 * @property ReportMonthlyDealerRevenue[] $reportMonthlyDealerRevenues
 * @property ReportMonthlyDealerRevenueDetail[] $reportMonthlyDealerRevenueDetails
 * @property SubscriberTransaction[] $subscriberTransactions
 * @property SumContent[] $sumContents
 * @property SumContentDownload[] $sumContentDownloads
 * @property SumContentUpload[] $sumContentUploads
 * @property SumContentView[] $sumContentViews
 * @property SumViewPartner[] $sumViewPartners
 * @property User[] $users
 * @property UserActivity[] $userActivities
 * @property User $userAdmin
 */
class Dealer extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = -1;

    public static $cp_status = [
        self::STATUS_ACTIVE => 'Hoạt động',
        self::STATUS_INACTIVE => 'Không hoạt động',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dealer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'code', 'name'], 'required'],
            [['code'], 'validateUnique'],
            [['site_id', 'created_at', 'status', 'updated_at', 'user_admin_id'], 'integer'],
            [['description', 'phone', 'address'], 'string'],
            [['email'], 'email'],
            [['code'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 200],
            [['logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => 'Site ID',
            'code' => 'Mã đại lý',
            'name' => 'Tên đại lý',
            'created_at' => 'Ngày tạo',
            'description' => 'Mô tả',
            'status' => 'Trạng thái',
            'updated_at' => 'Ngày cập nhật',
            'user_admin_id' => 'User Admin ID',
            'logo' => 'Logo',
            'email' => 'Email',
            'address' => 'Địa chỉ',
            'phone' => 'Số điện thoại',
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
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealerSubscriberAsms()
    {
        return $this->hasMany(DealerSubscriberAsm::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportMonthlyDealerRevenues()
    {
        return $this->hasMany(ReportMonthlyDealerRevenue::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportMonthlyDealerRevenueDetails()
    {
        return $this->hasMany(ReportMonthlyDealerRevenueDetail::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberTransactions()
    {
        return $this->hasMany(SubscriberTransaction::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSumContents()
    {
        return $this->hasMany(SumContent::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSumContentDownloads()
    {
        return $this->hasMany(SumContentDownload::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSumContentUploads()
    {
        return $this->hasMany(SumContentUpload::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSumContentViews()
    {
        return $this->hasMany(SumContentView::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSumViewPartners()
    {
        return $this->hasMany(SumViewPartner::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserActivities()
    {
        return $this->hasMany(UserActivity::className(), ['dealer_id' => 'id']);
    }

    public function getUserAdmin()
    {
        return $this->hasOne(User::className(), ['id' => 'user_admin_id']);
    }

    public function validateUnique($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($attribute == 'code') {
                $obj = static::find()
                    ->where(['code' => strtoupper($this->code)])
                    ->andWhere(['not', ['status'=>Dealer::STATUS_DELETED]])
                    ->andWhere(['not', ['id' => $this->id]])
                    ->one();
            }

            if ($obj) {
                $this->addError($attribute, $attribute . ' đã tồn tại.');
            }
        }
    }


    public function getStatusName()
    {
        $listStatus = self::$cp_status;
        if (isset($listStatus[$this->status])) {
            return $listStatus[$this->status];
        }
        return '';
    }

    public function getUsersFullName() {
        $users = User::find()->where(['dealer_id'=>$this->id])->all();
        $usersFullname = '';
        if ($users) {
            foreach($users as $user) {
                if ($usersFullname != '') {
                    $usersFullname = $usersFullname . ', ';
                }
                $usersFullname = $usersFullname . $user->fullname;
            }
        }
        return $usersFullname;
    }

    public static function createDealerEmpty($site_id) {
        $dealer = new Dealer();
        $dealer->site_id = $site_id;
        $dealer->id = null;
        $dealer->status = Dealer::STATUS_ACTIVE;
        return $dealer;
    }

    public static function listDealer($site_id)
    {
        $objs = Dealer::find()->andWhere(['status' => Dealer::STATUS_ACTIVE,'site_id'=>$site_id])->all();
        $lst = [];
        foreach ($objs as $obj) {
            $lst[$obj->id] = $obj->name;
        }
        return $lst;

    }
}
