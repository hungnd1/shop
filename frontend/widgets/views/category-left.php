<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/17/2017
 * Time: 8:29 PM
 */
?>
<div class="block left-module">
    <p class="title_block"><?= Yii::t('app','Danh mục')?></p>
    <div class="block_content">
        <!-- layered -->
        <div class="layered layered-category">
            <div class="layered-content">
                <ul class="tree-menu">
                    <?php
                    if(isset($menu)){$i=0;foreach($menu as $item){ /** @var \common\models\Category $item */
                    ?>
                    <li class="<?= $i==0?'active':'' ?>">
                        <span></span><a href="#"><?= $item->display_name?></a>
                        <ul>
                            <li><span></span><a href="#">T-shirts</a></li>
                            <li><span></span><a href="#">Dresses</a></li>
                            <li><span></span><a href="#">Casual</a></li>
                            <li><span></span><a href="#">Evening</a></li>
                            <li><span></span><a href="#">Summer</a></li>
                            <li><span></span><a href="#">Bags & Shoes</a></li>
                            <li><span></span><a href="#"><span></span>Blouses</a></li>
                        </ul>
                    </li>
                    <?php $i++;} }?>
                </ul>
            </div>
        </div>
        <!-- ./layered -->
    </div>
</div>
