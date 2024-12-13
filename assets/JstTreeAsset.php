<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class JstTreeAsset extends AssetBundle
{

    public $sourcePath = '@app/assets';

    public $css = [
        'vakata-jstree-3.3.17/dist/themes/default/style.min.css',
    ];
    public $js = [
        'vakata-jstree-3.3.17/dist/jstree.min.js',
        'js/elintest.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
        'yii\web\JqueryAsset'
    ];
}
