<?php
namespace infoweb\analytics;

use yii\web\AssetBundle;
use yii\web\View;

class AnalyticsAsset extends AssetBundle
{
    public $sourcePath = '@infoweb/analytics/assets/';
    
    public $css = [
        'css/analytics.css'
    ];
    
    public $js = [
        'https://www.google.com/jsapi',
        'js/analytics.js',
        'js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}