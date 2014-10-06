<?php

namespace infoweb\analytics;

use Yii;
use infoweb\analytics\AnalyticsAsset;
use infoweb\analytics\models\Connect;

/**
 * This is just an example.
 */
class Analytics extends \yii\base\Widget
{

    public $client;
    public $dataType;
    const SESSIONS = 'sessions';
    const VISITORS = 'visitors';

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

        switch ($this->dataType) {
            case Analytics::SESSIONS:
                $data = $this->getSessions();
                break;
            case Analytics::VISITORS:
                $data = $this->getVisitors();
                break;
        }

        $view->registerJs("var analyticsData = {$data}", \yii\web\View::POS_HEAD);
        AnalyticsAsset::register($this->view);


    }

    public function getSessions() {

        // @todo move to connection class

        $analytics = new \Google_Service_Analytics($this->client);
        // We have finished setting up the connection,
        // now get some data and output the number of visits this week.

        // Your analytics profile id. (Admin -> Profile Settings -> Profile ID)
        $analyticsId    = 'ga:';
        $lastWeek       = date('Y-m-d', strtotime('-1 month'));
        $today          = date('Y-m-d');

        try {
            $results = $analytics->data_ga->get($analyticsId, $lastWeek, $today, 'ga:visits', ['dimensions' => 'ga:date']);

            $data[] = ['Day', 'Visits'];

            foreach ($results['rows'] as $result)
            {
                $data[] = [date('d-m-Y', strtotime($result[0])), (int)$result[1]];
            }


        } catch(Exception $e) {
            echo 'There was an error : - ' . $e->getMessage();
        }

        return json_encode($data);
    }

    public function getVisitors() {

        $analytics = new \Google_Service_Analytics($this->client);
        // We have finished setting up the connection,
        // now get some data and output the number of visits this week.

        // Your analytics profile id. (Admin -> Profile Settings -> Profile ID)
        $analyticsId    = 'ga:';
        $lastWeek       = date('Y-m-d', strtotime('-1 month'));
        $today          = date('Y-m-d');

        try {

            $data = [];
            $data['returningVisitors'] = $analytics->data_ga->get($analyticsId, $lastWeek, $today, 'ga:sessions', ['segment' => 'gaid::-3']);
            $data['newVisitors'] = $analytics->data_ga->get($analyticsId, $lastWeek, $today, 'ga:sessions', ['segment' => 'gaid::-2']);

        } catch(Exception $e) {
            // @todo Yii exception
            echo 'There was an error : - ' . $e->getMessage();
        }

        print_r($data); exit();
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
        return $this->render($this->dataType);

    }
}