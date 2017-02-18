<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/18/2017
 * Time: 2:40 PM
 */
use yii\helpers\Url;

?>
<?php if(isset($cat_parent) && !empty($cat_parent)){ /** @var  \common\models\Category $cat_parent */ ?>
<span class="navigation-pipe">&nbsp;</span>
<a href="<?= Url::to(['category/index','id'=>$cat_parent->id]) ?>" title="<?= $cat_parent->display_name ?>">
    <?= $cat_parent->display_name ?>
</a>
    <?php if(isset($id_content)){ ?>
    <?= \frontend\widgets\FindBreadcrumb::getBreadcrumbChild1($id_content) ?>
    <?php } ?>
<?php
}
if(isset($content)){
    ?>
    <span class="navigation-pipe">&nbsp;</span>
    <a href="<?= Url::to(['content/detail','id'=>$content->id]) ?>" title="<?= $content->display_name ?>">
        <?= $content->display_name ?>
    </a>
    <?php
}
?>

