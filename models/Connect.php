<?php

namespace infoweb\analytics\models;

use Yii;

/**
 *
 * Google API Connection class
 *
 */
class Connect extends \yii\base\Action
{

    public $startDate;
    public $endDate;

    public function __construct() {
        parent::init();
    }

    public function connectAnalytics() {

        require_once Yii::getAlias('@google/api') . '/Google/Client.php';
        require_once Yii::getAlias('@google/api') . '/Google/Service/Analytics.php';

        $client = new \Google_Client();
        $client->setApplicationName("API Project");
        $client->setDeveloperKey(Yii::$app->params['analytics']['developerKey']);

        $cred = new \Google_Auth_AssertionCredentials(
        // Add this email address as a new user to your Google analytics property
            Yii::$app->params['analytics']['serviceAccountName'],
            ['https://www.googleapis.com/auth/analytics.readonly'],
            file_get_contents(Yii::getAlias('@app') . '/assets/certificate/certificate.p12')
        );

        $client->setAssertionCredentials($cred);

        $client->setClientId(Yii::$app->params['analytics']['clientId']);
        $client->setAccessType('offline_access');

        // Connect to the analytics api
        $analytics = new \Google_Service_Analytics($client);

        return $analytics;

    }

    /**
     * Get the sessions data
     *
     * @return string
     */
    public function getSessions() {

        $analytics = $this->connectAnalytics();

        try {
            // You can find your analytics profile id here: Admin -> Profile Settings -> Profile ID
            $results = $analytics->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['dimensions' => 'ga:date']);

            $data[] = ['Day', 'Sessions'];

            foreach ($results['rows'] as $result)
            {
                $data[] = [date('d-m-Y', strtotime($result[0])), (int)$result[1]];
            }

        } catch(Exception $e) {
            // @todo Yii exception
            echo 'There was an error : - ' . $e->getMessage();
        }

        return json_encode($data);
    }

    /**
     * Get the visitors data
     *
     * @return string
     */
    public function getVisitors() {

        $analytics = $this->connectAnalytics();

        try {

            $results['returningVisitors'] = $analytics->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['segment' => 'gaid::-3'])->getTotalsForAllResults();
            $results['newVisitors'] = $analytics->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['segment' => 'gaid::-2'])->getTotalsForAllResults();

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
     * Get the countries data
     *
     * @return string
     */
    public function getCountries() {

        $analytics = $this->connectAnalytics();

        try {

            //$results = $analytics->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['sort' => '-ga:sessions', 'max-results' => 10]);
            $results = $analytics->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['dimensions' => 'ga:country', 'sort' => '-ga:sessions', 'max-results' => 10]);

            $data['cols'] = [
                ['id' => 'countries', 'label' => 'Countries', 'type' => 'string'],
                ['id' => 'sessions', 'label' => 'Sessions', 'type' => 'number'],
            ];

            foreach ($results['rows'] as $result)
            {
                $data['rows'][] = ['c' => [
                    ['v' => $result[0]],
                    ['v' => (int)$result[1]]],
                ];
            }

        } catch(Exception $e) {
            // @todo Yii exception
            echo 'There was an error : - ' . $e->getMessage();
        }

        return json_encode($data);
    }

    /**
     * Get total sessions data
     *
     * @return string
     */
    public function getTotalSessions() {

        $analytics = $this->connectAnalytics();

        try {
            $results = $analytics->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions')->getTotalsForAllResults();

            $data = number_format($results['ga:sessions'], 0, ',', '.');

        } catch(Exception $e) {
            // @todo Yii exception
            echo 'There was an error : - ' . $e->getMessage();
        }

        return json_encode($data);
    }

    /**
     * Get total users data
     *
     * @return string
     */
    public function getTotalUsers() {

        $analytics = $this->connectAnalytics();

        try {
            $results = $analytics->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:users')->getTotalsForAllResults();

            $data = number_format($results['ga:users'], 0, ',', '.');

        } catch(Exception $e) {
            // @todo Yii exception
            echo 'There was an error : - ' . $e->getMessage();
        }

        return json_encode($data);
    }

    /**
     * Get total page views data
     *
     * @return string
     */
    public function getTotalPageViews() {

        $analytics = $this->connectAnalytics();

        try {
            $results = $analytics->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:pageviews')->getTotalsForAllResults();

            $data = number_format($results['ga:pageviews'], 0, ',', '.');

        } catch(Exception $e) {
            // @todo Yii exception
            echo 'There was an error : - ' . $e->getMessage();
        }

        return json_encode($data);
    }

    /**
     * Get average session length data
     *
     * @return string
     */
    public function getAverageSessionLength() {

        $analytics = $this->connectAnalytics();

        try {
            $results = $analytics->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:avgSessionDuration')->getTotalsForAllResults();

            $data = gmdate('H:i:m', $results['ga:avgSessionDuration']);


        } catch(Exception $e) {
            // @todo Yii exception
            echo 'There was an error : - ' . $e->getMessage();
        }

        return json_encode($data);
    }


}
