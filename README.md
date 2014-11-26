Google analytics
================
Google analytics

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist infoweb-internet-solutions/yii2-cms-analytics "*"
```

or add

```
"infoweb-internet-solutions/yii2-cms-analytics": "*"
```

to the require section of your `composer.json` file.



Go to the [Google developers console](https://console.developers.google.com)

Create a new project (or use an existing project)

Open the project

Go to 'API & Auth -> API's' and enable the 'Analytics API'

Go to 'API & Auth -> Credentials' and under 'OAuth' click 'Create new Client ID'

Choose 'Service account' and click 'Create client id'

Save the certificate to 'backend\assets\certificate\certificate.p12' (don't forget to rename the file)

Write down the 'private key's password' somewhere

Add the credentials to your backend params

```php
return [
    ...
    'analytics' => [
        'developerKey' => '', // Public key fingerprints
        'serviceAccountName' => 'xxx@developer.gserviceaccount.com', // Email address
        'clientId' => 'xxx.apps.googleusercontent.com', // Client ID
    ],
];
```

Go to [Google analytics](https://www.google.com/analytics/), open your property and get your 'Profile ID'

(It is the number at the end of the URL starting with p: https://www.google.com/analytics/web/#home/a11345062w43527078pXXXXXXXX/)

Add the 'Profile ID' to your params

```php
return [
    ...
    'analytics' => [
        ...
        'analyticsId' => 'ga:XXXXXXXX',
    ],
];
```


Add the serviceAccountName (xxx@developer.gserviceaccount.com) as a new user to your Analyics property


Create the alias '@google/api' in the bootstrap file in common/config like so:
```
Yii::setAlias('google/api', dirname(dirname(__DIR__)) . '/vendor/google/apiclient/src');
```

Import the translations and use category 'infoweb/analytics':
```
yii i18n/import @infoweb/analytics/messages
```

If you can't access the /tmp folder on your server (shared hosting), change line 94 in vendor\google\apiclient\src\Google\
```
'directory' => dirname(Yii::getAlias('@webroot')) . '/runtime/Google_Client'
```

Usage
-----

Once the extension is installed, simply use it in your code by :

```php
use infoweb\analytics\Analytics;
```

```php
<div class="row">
    <div class="col-lg-12">
        <span class="pull-right"><strong><?= Yii::t('app', 'From') ?>&nbsp;<?= date('d-m-Y', strtotime('-1 month')); ?>&nbsp;<?= Yii::t('app', 'to') ?>&nbsp;<?= date('d-m-Y') ?></strong></span>
        <h1 class="page-header">Dashboard</h1>
    </div>
</div>

<div class="row">
    <?= Analytics::widget(['dataType' => Analytics::TOTAl_SESSIONS]); ?>
    <?= Analytics::widget(['dataType' => Analytics::TOTAL_USERS]); ?>
    <?= Analytics::widget(['dataType' => Analytics::TOTAL_PAGE_VIEWS]); ?>
    <?= Analytics::widget(['dataType' => Analytics::AVERAGE_SESSION_LENGTH]); ?>
</div>

<div class="row">
    <?= Analytics::widget(['dataType' => Analytics::SESSIONS]); ?>
</div>
<div class="row">
    <?= Analytics::widget(['dataType' => Analytics::VISITORS]); ?>
    <?= Analytics::widget(['dataType' => Analytics::COUNTRIES]); ?>
</div>
```

Useful links
------------

[Google Analytics Query Explorer 2](https://ga-dev-tools.appspot.com/explorer/)  
[Google API Php Client](https://github.com/google/google-api-php-client)  
[Developer Documentation](https://developers.google.com/api-client-library/php)  
[Google Charts](https://developers.google.com/chart/?hl=nl)  
