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

        $view->registerJs("var sessionsData = {$data}", \yii\web\View::POS_HEAD);
        SessionsAsset::register($this->view);

    }

    public function getData() {

        $analytics = new \Google_Service_Analytics($this->client);

        // Your analytics profile id. (Admin -> Profile Settings -> Profile ID)
        $analyticsId    = Yii::$app->params['analytics']['analyticsId'];
        $startDate      = date('Y-m-d', strtotime('-1 month'));
        $endDate        = date('Y-m-d');

        try {
            $results = $analytics->data_ga->get($analyticsId, $startDate, $endDate, 'ga:sessions', ['dimensions' => 'ga:date']);

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

    public function run()
    {
        return $this->render('sessions');

    }
}