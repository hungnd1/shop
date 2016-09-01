<?php

namespace common\models;

//use api\models\Content;
use common\helpers\CVietnameseTools;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * ContentSearch represents the model behind the search form about `\common\models\Content`.
 */
class ContentSearch extends Content
{
    public $keyword;
    public $categoryIds;
    public $listCatIds;
    public $cp_id;
    public $site_id;
    public $category_id;
    public $order;
    public $content_id;
    public $pricing_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'version_code', 'view_count', 'download_count', 'like_count', 'dislike_count', 'rating_count', 'comment_count', 'favorite_count', 'status', 'created_at', 'updated_at', 'honor', 'approved_at', 'order'], 'integer'],
            [['display_name', 'ascii_name', 'tags', 'language', 'cp_id', 'short_description', 'categoryIds', 'description', 'content', 'version', 'images', 'keyword'], 'safe'],
            [['site_id', 'category_id', 'order', 'content_id'], 'integer'],
            [['rating'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
//                $query = Content::find();
        $query = \api\models\Content::find();

        $orderDefault = [];
        if ($this->order == self::ORDER_MOSTVIEW) {
            $orderDefault['view_count'] = SORT_DESC;
        } else if ($this->order == self::ORDER_NEWEST) {
            $orderDefault['updated_at'] = SORT_DESC;
        }  else if ($this->order == self::ORDER_TITLE) {
            $orderDefault['display_name'] = SORT_ASC;
        } else if ($this->order == self::ORDER_ID) {
            $orderDefault['id'] = SORT_DESC;
        }else if ($this->order == self::ORDER_ORDER) {
            $orderDefault['order'] = SORT_DESC;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => $orderDefault,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        /** Tạm fixcode cho Thái test */
        if ($this->type == Content::TYPE_LIVE) {
            /** Không lấy những thằng đã xóa */
            $query->andWhere('tvod1_id  is not null');
        }
        /** Không lấy những thằng đã xóa */
        $query->andWhere(['<>', 'content.status', Content::STATUS_DELETE]);

        /* Bắt đầu xử lí các điều kiện lọc, nếu điều kiện nào truyền vào thì mới xử lí */
        /* Lấy phim liên quan */
        if ($this->content_id) {
            $query->joinWith('contentRelatedAsms0 as a');
            $query->andWhere(['a.content_id' => $this->content_id]);
        }

        if ($this->id) {
            $query->andWhere(['content.id' => $this->id]);
        }

        if ($this->honor) {
            $query->andWhere(['content.honor' => $this->honor]);
        }

        if ($this->type) {
            $query->andWhere(['content.type' => $this->type]);
        }
        /** Điều kiện là kênh catchup */
        if ($this->is_catchup) {
            $query->andWhere(['content.is_catchup' => $this->is_catchup]);
        }


        /* Lấy toàn bộ SubDrama */
        if ($this->parent_id) {
            $query->andWhere(['content.parent_id' => $this->parent_id]);
        }else{
            /** Lấy những thằng phim mà không thuộc phim bộ nếu lấy phim lẻ */
            $query->andWhere('content.parent_id  IS NULL');
        }

        if ($this->category_id) {
            $query->joinWith('contentCategoryAsms');
            $query->andWhere(['category_id' => $this->category_id]);
        }

        if ($this->status) {
            $query->joinWith('contentSiteAsms');
            $query->andWhere(['content_site_asm.status' => $this->status]);
            $query->andWhere(['content.status' => $this->status]);
        }

//        if ($this->honor) {
//            $query->andWhere(['content.honor' => $this->honor]);
//        }

        if ($this->site_id) {
            $query->joinWith('contentSiteAsms');
            $query->andWhere(['site_id' => $this->site_id]);
        }
        if ($this->language) {
            $query->andFilterWhere(['=', 'language', $this->language]);
        }

        if ($this->keyword) {
            $query->andFilterWhere(['or',
                ['like', 'display_name', $this->keyword],
                ['like', 'ascii_name', $this->keyword],
            ]);
        }
//        $query->andFilterWhere(['or',
        //            ['like', 'display_name', $this->keyword],
        //            ['like', 'ascii_name', $this->keyword],
        //        ]);

        return $dataProvider;
    }

    public function suggestion($params)
    {
        //        $query = Content::find();
        $query = \api\models\ContentSugestion::find();

        $orderDefault = [];
        $orderDefault = [];
        if ($this->order == self::ORDER_MOSTVIEW) {
            $orderDefault['view_count'] = SORT_DESC;
        } else if ($this->order == self::ORDER_NEWEST) {
            $orderDefault['updated_at'] = SORT_DESC;
        }else if ($this->order == self::ORDER_TITLE) {
            $orderDefault['display_name'] = SORT_ASC;
        } else if ($this->order == self::ORDER_ID) {
            $orderDefault['id'] = SORT_DESC;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => $orderDefault,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        /** Tạm fixcode cho Thái test */
        if ($this->type == Content::TYPE_LIVE) {
            /** Không lấy những thằng đã xóa */
            $query->andWhere('tvod1_id  is not null');
        }
        /** Không lấy những thằng đã xóa */
        $query->andWhere(['<>', 'content.status', Content::STATUS_DELETE]);

        /* Bắt đầu xử lí các điều kiện lọc, nếu điều kiện nào truyền vào thì mới xử lí */
        if ($this->honor) {
            $query->andWhere(['content.honor' => $this->honor]);
        }

        if ($this->type) {
            $query->andWhere(['content.type' => $this->type]);
        }


        /* Lấy toàn bộ SubDrama */
        if ($this->parent_id) {
            $query->andWhere(['content.parent_id' => $this->parent_id]);
        }else{
            /** Lấy những thằng phim mà không thuộc phim bộ nếu lấy phim lẻ */
            $query->andWhere('content.parent_id  IS NULL');
        }

        if ($this->category_id) {
            $query->joinWith('contentCategoryAsms');
            $query->andWhere(['category_id' => $this->category_id]);
        }

        if ($this->status) {
            $query->joinWith('contentSiteAsms');
            $query->andWhere(['content_site_asm.status' => $this->status]);
            $query->andWhere(['content.status' => $this->status]);
        }

//        if ($this->honor) {
//            $query->andWhere(['content.honor' => $this->honor]);
//        }

        if ($this->site_id) {
            $query->joinWith('contentSiteAsms');
            $query->andWhere(['site_id' => $this->site_id]);
        }
        if ($this->language) {
            $query->andFilterWhere(['=', 'language', $this->language]);
        }

        $query->andFilterWhere(['or',
            ['like', 'display_name', $this->keyword],
            ['like', 'ascii_name', $this->keyword],
        ]);

        return $dataProvider;
    }

    public function filter($params)
    {
        $query            = Content::find();
        $inactiveCategory = ArrayHelper::map(Category::findAll(['status' => Category::STATUS_INACTIVE]), 'ascii_name', 'id');
        // var_dump($inactiveCategory);die;
            $query->innerJoin('content_category_asm', 'content.id = content_category_asm.content_id');
            $query->andWhere(['NOT IN', 'content_category_asm.category_id', $inactiveCategory]);
            $query->andWhere('content.status!= :p_status_delete', [':p_status_delete' => Content::STATUS_DELETE]);

        // $query->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->categoryIds) {
            $categoryIds = explode(',', $this->categoryIds);
            //     $this->listCatIds = $categoryIds;

            // $query->distinct();

            $query->andWhere(['IN', 'content_category_asm.category_id', $categoryIds]);
        }

        $query->andFilterWhere(['=', 'content_provider_id', $this->cp_id]);

        $query->andFilterWhere(['like', 'content.display_name', $this->keyword]);
        $query->andFilterWhere(['like', 'content.display_name', $this->display_name]);
        $query->andFilterWhere(['=', 'content.status', $this->status]);
        $query->andFilterWhere(['=', 'content.pricing_id', $this->pricing_id]);
        $query->andFilterWhere(['=', 'content.language', $this->language]);
        if ($this->created_at !== '') {
            $create_at     = $this->created_at;
            $create_at_end = $this->created_at + (60 * 60 * 24);
            $query->andFilterWhere(['>=', 'content.created_at', $create_at]);
            $query->andFilterWhere(['<=', 'content.created_at', $create_at_end]);
        }
        // var_dump($query->createCommand()->rawSql);die;
        return $dataProvider;
    }

    public function fillContent($type, $params, $sp)
    {
        $dataProvider = new ArrayDataProvider([
            'key'       => 'id',
            'allModels' => Content::findAll(['type' => $type]),
        ]);
    }

    public function filterEpisode($params, $type, $sp_id, $parent_id)
    {
        $query = Content::find();
        // $query->andWhere(['created_user_id' => $sp_id]);
        $query->andWhere(['parent_id' => $parent_id]);
        $query->andWhere(['type' => $type]);
        $query->andWhere(['!=', 'status', Content::STATUS_DELETE]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        Yii::trace($this->getAttributes());
        $fullCat     = array_keys(Category::getTreeCategories($type, $this->created_user_id));
        $categoryIds = [];
        if (!$this->categoryIds) {
            //            $this->categoryIds = $fullCat;
            $categoryIds = $fullCat;
        } else {
            $categoryIds = explode(',', $this->categoryIds);
        }
        if (count($categoryIds) != count($fullCat)) {
            $this->listCatIds = $categoryIds;
            $query->distinct();
            $query->innerJoinWith(['contentCategoryAsms' => function ($query) {
                $query->andWhere(['IN', 'category_id', $this->listCatIds]);
            }]);
        }
        $query->andFilterWhere(['=', 'content_provider_id', $this->cp_id]);
        $query->andFilterWhere(['like', 'ascii_name', CVietnameseTools::makeSearchableStr($this->keyword)]);
        $query->andFilterWhere(['!=', 'status', Content::STATUS_DELETE]);

        return $dataProvider;
    }

    public static function validateDate($date, $format = "YYYY-MM-DD")
    {
        if (!preg_match("/^[-]+$/", substr($date, 4, 1)) || !preg_match("/^[-]+$/", substr($date, 7, 1))) {
            return false;
        }
        switch ($format) {
            case "YYYY-MM-DD":
                list($y, $m, $d) = preg_split('/[-\.\/ ]/', $date);
                break;
            default:
                return false;
        }
        return checkdate($m, $d, $y);
    }

    public static function filterValues($attributeName, $type, $params = null)
    {

        /** @var $attr ContentAttribute */
        $attr = ContentAttribute::findOne(['name' => $attributeName, 'content_type' => $type]);
        if (!$attr) {
            return false;
        }
        /** find at least one existed record */
        $content = ContentAttributeValue::find()
            ->innerJoin('content', 'content.id = content_attribute_value.content_id')
            ->where(['content_attribute_id' => $attr->id])
            ->andWhere(['content.status' => Content::STATUS_ACTIVE])
            ->andWhere(['content.type' => Content::TYPE_KARAOKE])
            ->one();
        if (!$content) {
            return false;
        }
        /** @var $query \api\models\Content */
        $query = \api\models\Content::find()
            ->innerJoin('content_attribute_value', 'content_attribute_value.content_id = content.id')
            ->andWhere(['content_attribute_value.content_attribute_id' => $attr->id]);
        if ($params) {
            $query->andWhere(['LIKE', 'content_attribute_value.value', $params]);
        }
        $query->andWhere(['content.type' => $type])
            ->andWhere(['content.status' => Content::STATUS_ACTIVE])
            ->orderBy('content.ascii_name ASC');
        $dataPrivider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'defaultPageSize' => 20,
            ],
        ]);
        return $dataPrivider;
    }
}
