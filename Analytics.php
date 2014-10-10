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
    public $startDate;
    public $endDate;
    public $dataType;
    const SESSIONS = 'sessions';
    const VISITORS = 'visitors';
    const COUNTRIES = 'countries';

    /**
     * Initializes the widget
     */
    public function init()
    {
        parent::init();

        // Get the current view
        $view = $this->getView();

        $connection = new Connect;

        // Set default values
        // @todo Find a better way to do this
        if (!isset($this->startDate)) {
            $connection->startDate = date('Y-m-d', strtotime('-1 month'));
        } else {
            $connection->startDate = $this->startDate;
        }

        if (!isset($this->endDate)) {
            $connection->endDate = date('Y-m-d');
        } else {
            $connection->endDate = $this->endDate;
        }

        // Get analytics data
        switch ($this->dataType) {

            case Analytics::SESSIONS:
                $data = $connection->getSessions();

                // Set javascript variable
                // @todo Find a better way to do this
                $view->registerJs("var sessionsData = {$data}", \yii\web\View::POS_HEAD);

                break;

            case Analytics::VISITORS:
                $data = $connection->getVisitors();

                // Set javascript variable
                // @todo Find a better way to do this
                $view->registerJs("var visitorsData = {$data}", \yii\web\View::POS_HEAD);

                break;

            case Analytics::COUNTRIES:
                $data = $connection->getCountries();

                // Set javascript variable
                // @todo Find a better way to do this
                $view->registerJs("var countriesData = {$data}", \yii\web\View::POS_HEAD);

                break;
        }

        // Register asssets
        AnalyticsAsset::register($this->view);

    }

    /**
     * Run the widget
     *
     * @return string
     */
    public function run()
    {
        return $this->render($this->dataType);

    }
}