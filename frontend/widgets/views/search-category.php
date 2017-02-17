<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/17/2017
 * Time: 8:26 AM
 */
?>
<div class="col-xs-7 col-sm-7 header-search-box">
    <form class="form-inline">
        <div class="form-group form-category">
            <select class="select-category">
                <option value="2"><?= Yii::t('app','Chọn Danh mục') ?></option>
                <?php if(isset($category)){ ?>
                <?php foreach($category as $item){ ?>
                <?php /** @var \common\models\Category $item */ ?>
                <option value="<?= $item->id ?>"><?= $item->display_name ?></option>
                <?php } ?>
                <?php } ?>
            </select>
        </div>
        <div class="form-group input-serach">
            <input type="text"  placeholder="<?= Yii::t('app','Tìm kiếm')?>...">
        </div>
        <button type="submit" class="pull-right btn-search"></button>
    </form>
</div>
