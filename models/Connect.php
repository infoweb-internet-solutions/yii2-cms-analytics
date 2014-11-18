<?php

namespace infoweb\analytics\models;

use Yii;
use yii\base\Exception as BaseException;

/**
 *
 * Google API Connection class
 *
 */
class Connect extends \yii\base\Action
{

    public $startDate;
    public $endDate;
    public $connection = null;

    public function __construct()
    {
        parent::init();

        // Read the connection from cache
        $connection = $this->readFromCache('infoweb/analytics/connection');
        
        if (!$connection) {
            $this->connectToAnalytics(); 
        } else {
            $this->connection = $connection;       
        }
    }

    public function connectToAnalytics()
    {
        if ($this->connection == null) {
            require_once Yii::getAlias('@google/api') . '/Google/Client.php';
            require_once Yii::getAlias('@google/api') . '/Google/Service/Analytics.php';
    
            $client = new \Google_Client(); //'../config.php'
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
    
            $this->connection = $analytics;
            
            // Cache the connection
            $this->cacheData('infoweb/analytics/connection', $this->connection);
        }

        return $this->connection;
    }

    /**
     * Get the sessions data
     *
     * @return string
     */
    public function getSessions()
    {
        // Read the data from cache
        $data = $this->readFromCache('infoweb/analytics/data/sessions');
        
        if (!$data) {               
            try {
                // You can find your analytics profile id here: Admin -> Profile Settings -> Profile ID
                $results = $this->connection->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['dimensions' => 'ga:date']);
    
                $data[] = [Yii::t('infoweb/analytics', 'Day'), Yii::t('infoweb/analytics', 'Sessions')];
    
                foreach ($results['rows'] as $result)
                {
                    $data[] = [date('d-m-Y', strtotime($result[0])), (int)$result[1]];
                }
                
                $this->cacheData('infoweb/analytics/data/sessions', $data);
    
            } catch(Exception $e) {
                throw new BaseException(Yii::t('infoweb/analytics', 'Error while fetching data from Google Analytics'));
            }
        }

        return json_encode($data);
    }

    /**
     * Get the visitors data
     *
     * @return string
     */
    public function getVisitors()
    {
        // Read the data from cache
        $data = $this->readFromCache('infoweb/analytics/data/visitors');
        
        if (!$data) { 
            try {
    
                $results['returningVisitors'] = $this->connection->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['segment' => 'gaid::-3'])->getTotalsForAllResults();
                $results['newVisitors'] = $this->connection->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['segment' => 'gaid::-2'])->getTotalsForAllResults();
    
                $data[] = [Yii::t('infoweb/analytics', 'Title'), Yii::t('infoweb/analytics', 'Total')];
    
                $data[] = [Yii::t('infoweb/analytics', 'Returning visitor'), (int)$results['returningVisitors']['ga:sessions']];
                $data[] = [Yii::t('infoweb/analytics', 'New visitor'), (int)$results['newVisitors']['ga:sessions']];
                
                $this->cacheData('infoweb/analytics/data/visitors', $data);
            } catch(Exception $e) {
                throw new BaseException(Yii::t('infoweb/analytics', 'Error while fetching data from Google Analytics'));
            }
        }

        return json_encode($data);
    }

    /**
     * Get the countries data
     *
     * @return string
     */
    public function getCountries()
    {
        // Read the data from cache
        $data = $this->readFromCache('infoweb/analytics/data/countries');
        
        if (!$data) { 
            try {
    
                //$results = $this->connection->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['sort' => '-ga:sessions', 'max-results' => 10]);
                $results = $this->connection->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions', ['dimensions' => 'ga:country', 'sort' => '-ga:sessions', 'max-results' => 10]);
    
                $data['cols'] = [
                    ['id' => 'countries', 'label' => Yii::t('infoweb/analytics', 'Countries'), 'type' => 'string'],
                    ['id' => 'sessions', 'label' => Yii::t('infoweb/analytics', 'Sessions'), 'type' => 'number'],
                ];
    
                foreach ($results['rows'] as $result)
                {
                    $data['rows'][] = ['c' => [
                        ['v' => $result[0]],
                        ['v' => (int)$result[1]]],
                    ];
                }
                
                $this->cacheData('infoweb/analytics/data/countries', $data);
    
            } catch(Exception $e) {
                throw new BaseException(Yii::t('infoweb/analytics', 'Error while fetching data from Google Analytics'));
            }
        }

        return json_encode($data);
    }

    /**
     * Get total sessions data
     *
     * @return string
     */
    public function getTotalSessions()
    {
        // Read the data from cache
        $data = $this->readFromCache('infoweb/analytics/data/totalSessions');
        
        if (!$data) { 
            try {
                $results = $this->connection->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:sessions')->getTotalsForAllResults();
    
                $data = number_format($results['ga:sessions'], 0, ',', '.');
                
                $this->cacheData('infoweb/analytics/data/totalSessions', $data);
            } catch(Exception $e) {
                throw new BaseException(Yii::t('infoweb/analytics', 'Error while fetching data from Google Analytics'));
            }
        }

        return json_encode($data);
    }

    /**
     * Get total users data
     *
     * @return string
     */
    public function getTotalUsers()
    {
        // Read the data from cache
        $data = $this->readFromCache('infoweb/analytics/data/totalUsers');
        
        if (!$data) { 
            try {
                $results = $this->connection->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:users')->getTotalsForAllResults();
    
                $data = number_format($results['ga:users'], 0, ',', '.');
                
                $this->cacheData('infoweb/analytics/data/totalUsers', $data);
            } catch(Exception $e) {
                throw new BaseException(Yii::t('infoweb/analytics', 'Error while fetching data from Google Analytics'));
            }
        }

        return json_encode($data);
    }

    /**
     * Get total page views data
     *
     * @return string
     */
    public function getTotalPageViews()
    {
        // Read the data from cache
        $data = $this->readFromCache('infoweb/analytics/data/totalPageViews');
        
        if (!$data) { 
            try {
                $results = $this->connection->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:pageviews')->getTotalsForAllResults();
    
                $data = number_format($results['ga:pageviews'], 0, ',', '.');
                
                $this->cacheData('infoweb/analytics/data/totalPageViews', $data);
            } catch(Exception $e) {
                throw new BaseException(Yii::t('infoweb/analytics', 'Error while fetching data from Google Analytics'));
            }
        }

        return json_encode($data);
    }

    /**
     * Get average session length data
     *
     * @return string
     */
    public function getAverageSessionLength()
    {
        // Read the data from cache
        $data = $this->readFromCache('infoweb/analytics/data/averageSessionLength');
        
        if (!$data) { 
            try {
                $results = $this->connection->data_ga->get(Yii::$app->params['analytics']['analyticsId'], $this->startDate, $this->endDate, 'ga:avgSessionDuration')->getTotalsForAllResults();
    
                $data = gmdate('H:i:m', $results['ga:avgSessionDuration']);
    
                $this->cacheData('infoweb/analytics/data/averageSessionLength', $data);
            } catch(Exception $e) {
                throw new BaseException(Yii::t('infoweb/analytics', 'Error while fetching data from Google Analytics'));
            }
        }

        return json_encode($data);
    }

    /**
     * Caches data in a session variable
     * 
     * @param   string  $key        The key that has to be used in the session var
     * @param   mixed   $data
     * @return  boolean
     */
    public function cacheData($key = '', $data = [])
    {
        $session = Yii::$app->session;
        
        $session[$key] = serialize($data);
        
        return true;    
    }
    
    /**
     * Reads the data from cache
     * 
     * @param   string  $key
     * @return  mixed
     */
    public function readFromCache($key)
    {
        $session = Yii::$app->session;
        
        return ($session->has($key)) ? unserialize($session[$key]) : [];     
    }
}
