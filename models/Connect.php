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
        $client->setDeveloperKey('AIzaSyDitQlwUtlc-ar17NXRutYoYfmskHiCuS4');

        // Add this email address as a new user to your Google analytics property
        $serviceAccountName = '254715720101-b7kcs7a23oveeuhobb33o6clmrles2rt@developer.gserviceaccount.com';
        $scopes = ['https://www.googleapis.com/auth/analytics.readonly'];
        $key = file_get_contents(Yii::getAlias('@webroot') . '/certificate/API Project-67815eac03a5.p12');

        $cred = new \Google_Auth_AssertionCredentials(
            $serviceAccountName,
            $scopes,
            $key
        );

        $client->setAssertionCredentials($cred);

        $clientId = '254715720101-b7kcs7a23oveeuhobb33o6clmrles2rt.apps.googleusercontent.com';
        $client->setClientId($clientId);
        $client->setAccessType('offline_access');

        return $client;

    }

}
