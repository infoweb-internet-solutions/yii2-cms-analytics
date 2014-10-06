<?php

namespace infoweb\analytics;

use Yii;
use infoweb\analytics\AnalyticsAsset;

/**
 * This is just an example.
 */
class Graph extends \yii\base\Widget
{
    /**
     * Initializes the widget
     */
    public function init()
    {
        parent::init();

        $view = $this->getView();

        $data = $this->getdata();

        $view->registerJs("var analyticsData = {$data}", \yii\web\View::POS_HEAD);
        AnalyticsAsset::register($view);

    }

    public function getdata() {

        require_once Yii::getAlias('@google/api') . '/Google/Client.php';
        require_once Yii::getAlias('@google/api') . '/Google/Service/Analytics.php';

        $client = new \Google_Client();
        $client->setApplicationName("API Project");
        $client->setDeveloperKey('AIzaSyDitQlwUtlc-ar17NXRutYoYfmskHiCuS4');

        $serviceAccountName = '254715720101-b7kcs7a23oveeuhobb33o6clmrles2rt@developer.gserviceaccount.com';

        $key = file_get_contents(Yii::getAlias('@webroot') . '/certificate/API Project-67815eac03a5.p12');

        $cred = new \Google_Auth_AssertionCredentials(
        // Replace this with the email address from the client.
            $serviceAccountName,
            // Replace this with the scopes you are requesting.
            array('https://www.googleapis.com/auth/analytics.readonly'),
            $key
        );

        $client->setAssertionCredentials($cred);

        // Get this from the Google Console, API Access page
        $clientId = '254715720101-b7kcs7a23oveeuhobb33o6clmrles2rt.apps.googleusercontent.com';

        $client->setClientId($clientId);
        $client->setAccessType('offline_access');

        $analytics = new \Google_Service_Analytics($client);

        // We have finished setting up the connection,
        // now get some data and output the number of visits this week.

        // Your analytics profile id. (Admin -> Profile Settings -> Profile ID)
        $analyticsId    = 'ga:76903014';
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
        return $this->render('graph', [
            //'data' => json_encode($data),
        ]);

    }
}