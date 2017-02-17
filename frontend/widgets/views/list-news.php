<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/16/2017
 * Time: 1:46 PM
 */
?>
<div id="content-wrap">
    <div class="container">
        <div id="hot-categories" class="row">
            <div class="col-sm-12 group-title-box">
                <h2 class="group-title ">
                    <span><?= Yii::t('app','Tin tức') ?></span>
                </h2>
            </div>
            <?php if(isset($news)){ ?>
            <?php foreach($news as  $item){ ?>
            <?php /** @var  \common\models\Content $item*/ ?>
            <div class="col-sm-6 col-lg-3 cate-box">
                <div class="cate-tit" >
                    <div class="div-1" style="width: 46%;">
                        <div class="cate-name-wrap">
                            <p class="cate-name"><?= $item->display_name ?></p>
                        </div>
                        <a href="" class="cate-link link-active" data-ac="flipInX" ><span><?= Yii::t('app','Xem ngay') ?></span></a>
                    </div>
                    <div class="div-2" >
                        <a href="#">
                            <img src="<?= $item->getFirstImageLinkFE() ?>" alt="<?= $item->display_name ?>" class="hot-cate-img" />
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php } ?>
        </div>
    </div> <!-- /.container -->
</div>
