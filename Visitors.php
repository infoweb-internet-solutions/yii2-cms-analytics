<?php

namespace infoweb\analytics;

use Yii;
use infoweb\analytics\VisitorsAsset;
use infoweb\analytics\models\Connect;

/**
 * This is just an example.
 */
class Visitors extends \yii\base\Widget
{

    public $client;

    /**
     * Initializes the widget
     */
    public function init()
    {
        parent::init();

        // Get the current view
        $view = $this->getView();

        // Connect to Google Api
        $this->client = Connect::connectAnalytics();

        $data = $this->getData();

        $view->registerJs("var visitorsData = {$data}", \yii\web\View::POS_HEAD);
        VisitorsAsset::register($this->view);

    }

    public function getData() {

        $analytics = new \Google_Service_Analytics($this->client);

        // Your analytics profile id. (Admin -> Profile Settings -> Profile ID)
        $analyticsId    = Yii::$app->params['analytics']['analyticsId'];
        $startDate      = date('Y-m-d', strtotime('-1 month'));
        $endDate        = date('Y-m-d');

        try {
            $data = [];
            $data['returningVisitors'] = $analytics->data_ga->get($analyticsId, $startDate, $endDate, 'ga:sessions', ['segment' => 'gaid::-3'])->getTotalsForAllResults();
            $data['newVisitors'] = $analytics->data_ga->get($analyticsId, $startDate, $endDate, 'ga:sessions', ['segment' => 'gaid::-2'])->getTotalsForAllResults();

        } catch(Exception $e) {
            // @todo Yii exception
            echo 'There was an error : - ' . $e->getMessage();
        }

        //echo $data['returningVisitors']['ga:sessions']; exit();

        return json_encode($data);
    }

    /**
     * Registers the needed assets

    public function registerAssets()
    {
        $view = $this->getView();
        AnalyticsAsset::register($view);
        //$this->registerPlugin('checkboxX');
    }
     */
    public function run()
    {
        return $this->render('visitors');

    }
}