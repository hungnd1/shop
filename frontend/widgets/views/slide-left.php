<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 2/20/2017
 * Time: 9:31 AM
 */
use common\models\Content;

?>
<?php if(isset($product_hots)){ ?>
    <div class="col-left-slide left-module">
        <ul class="owl-carousel owl-style2" data-loop="true" data-nav = "false" data-margin = "0" data-autoplayTimeout="1000" data-autoplayHoverPause = "true" data-items="1" data-autoplay="true">
            <?php foreach($product_hots as $item){ /** @var  Content $item */?>
                <li><a href="<?= \yii\helpers\Url::to(['content/detail','id'=>$item->id]) ?>"><img style="height: 346px" src="<?= $item->getFirstImageLinkFE() ?>" alt="<?= $item->display_name ?>"></a></li>
            <?php  }?>
        </ul>
    </div>
<?php } ?>
