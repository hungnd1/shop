<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/15/2017
 * Time: 10:16 PM
 */
?>
<div class="container">
    <div class="service ">
        <?php if(isset($sp)){ ?>
        <?php foreach($sp as $item){ ?>
        <?php /** @var \common\models\ProgramSuppost $item */?>
        <div class="col-xs-6 col-sm-3 service-item">
            <div class="icon">
                <img alt="services" src="<?= $item->getImageLink() ?>" />
            </div>
            <div class="info">
                <a href="#"><h3><?= $item->name ?></h3></a>
                <span><?= $item->short_des ?></span>
            </div>
        </div>
        <?php } ?>
        <?php } ?>
    </div>
</div>
