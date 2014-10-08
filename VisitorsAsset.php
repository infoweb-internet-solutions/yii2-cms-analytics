<?php
namespace infoweb\analytics;

use yii\web\AssetBundle;
use yii\web\View;

class VisitorsAsset extends AssetBundle
{
    public $sourcePath = '@infoweb/analytics/assets/';
    
    public $css = [
    ];
    
    public $js = [
        'js/visitors.js',
    ];

    public $depends = [
        'infoweb\analytics\AnalyticsAsset',
    ];
}