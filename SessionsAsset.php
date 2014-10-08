<?php
namespace infoweb\analytics;

use yii\web\AssetBundle;
use yii\web\View;

class SessionsAsset extends AssetBundle
{
    public $sourcePath = '@infoweb/analytics/assets/';
    
    public $css = [
    ];
    
    public $js = [
        'js/sessions.js',
    ];

    public $depends = [
        'infoweb\analytics\AnalyticsAsset',
    ];
}