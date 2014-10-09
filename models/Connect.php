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
    public static function connectAnalytics() {

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

        return $client;

    }



}
