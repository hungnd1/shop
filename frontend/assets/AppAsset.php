<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'lib/bootstrap/css/bootstrap.min.css',
        'lib/font-awesome/css/font-awesome.min.css',
        'lib/select2/css/select2.min.css',
        'lib/jquery.bxslider/jquery.bxslider.css',
        'lib/owl.carousel/owl.carousel.css',
        'lib/fancyBox/jquery.fancybox.css',
        'lib/jquery-ui/jquery-ui.css',
        'css/animate.css',
        'css/reset.css',
        'css/style.css',
        'css/responsive.css',
        'css/site.css',
    ];
    public $js = [
        'lib/jquery/jquery-1.11.2.min.js',
        'lib/bootstrap/js/bootstrap.min.js',
        'lib/select2/js/select2.min.js',
        'lib/jquery.bxslider/jquery.bxslider.min.js',
        'lib/owl.carousel/owl.carousel.min.js',
        'lib/jquery.countdown/jquery.countdown.min.js',
        'lib/jquery.elevatezoom.js',
        'lib/jquery-ui/jquery-ui.min.js',
        'lib/fancyBox/jquery.fancybox.js',
        'js/jquery.actual.min.js',
        'js/theme-script.js',
        'js/tp.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
}
