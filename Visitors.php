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
    public $startDate;
    public $endDate;
    public $analyticsId;

    /**
     * Initializes the widget
     */
    public function init()
    {
        parent::init();

        // Get the current view
        $view = $this->getView();

        // Set default values
        if (!isset($this->startDate)) {
            $this->startDate = date('Y-m-d', strtotime('-1 month'));
        }

        if (!isset($this->endDate)) {
            $this->endDate = date('Y-m-d');
        }

        // Your analytics profile id. (Admin -> Profile Settings -> Profile ID)
        $this->analyticsId = Yii::$app->params['analytics']['analyticsId'];

        // Connect to Google Api
        $this->client = Connect::connectAnalytics();

        // Get analytics data
        $data = $this->getData();

        // Set javascript variable
        // @todo Find a better way to do this
        $view->registerJs("var visitorsData = {$data}", \yii\web\View::POS_HEAD);

        // Register asssets
        VisitorsAsset::register($this->view);

    }

    /**
     * Get the Analytics data
     *
     * @return string
     */
    public function getData() {

        $analytics = new \Google_Service_Analytics($this->client);

        try {

            $results['returningVisitors'] = $analytics->data_ga->get($this->analyticsId, $this->startDate, $this->endDate, 'ga:sessions', ['segment' => 'gaid::-3'])->getTotalsForAllResults();
            $results['newVisitors'] = $analytics->data_ga->get($this->analyticsId, $this->startDate, $this->endDate, 'ga:sessions', ['segment' => 'gaid::-2'])->getTotalsForAllResults();

            $data[] = ['Title', 'Total'];

            $data[] = ['Returning visitor', (int)$results['returningVisitors']['ga:sessions']];
            $data[] = ['New visitor', (int)$results['newVisitors']['ga:sessions']];

        } catch(Exception $e) {
            // @todo Yii exception
            echo 'There was an error : - ' . $e->getMessage();
        }

        return json_encode($data);
    }

    /**
     * Run the widget
     *
     * @return string
     */
    public function run()
    {
        return $this->render('visitors');

    }
}