<?php
/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 12/20/2016
 * Time: 5:06 PM
 */
use frontend\widgets\MenuTop;
use yii\helpers\Url;

?>
<div id="main-menu" class="col-sm-9 main-menu">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#">MENU</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="<?= Url::to(['site/index']) ?>">Home</a></li>
                    <?php
                    if(isset($menu)){
                        echo MenuTop::showCategories($menu);
                    }
                    ?>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>
</div>
