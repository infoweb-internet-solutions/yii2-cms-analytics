<?php

namespace infoweb\analytics;

use Yii;
use infoweb\analytics\SessionsAsset;
use infoweb\analytics\models\Connect;

/**
 * This is just an example.
 */
class Sessions extends \yii\base\Widget
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
        $view->registerJs("var sessionsData = {$data}", \yii\web\View::POS_HEAD);

        // Register asssets
        SessionsAsset::register($this->view);

    }

    /**
     * Get the Analytics data
     *
     * @return string
     */
    public function getData() {

        // Connect to the analytics api
        $analytics = new \Google_Service_Analytics($this->client);

        try {
            $results = $analytics->data_ga->get($this->analyticsId, $this->startDate, $this->endDate, 'ga:sessions', ['dimensions' => 'ga:date']);

            $data[] = ['Day', 'Sessions'];

            foreach ($results['rows'] as $result)
            {
                $data[] = [date('d-m-Y', strtotime($result[0])), (int)$result[1]];
            }

        } catch(Exception $e) {
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
        return $this->render('sessions');

    }
}