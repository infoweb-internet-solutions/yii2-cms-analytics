(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory;
    } else {
        root.Analytics = factory();
    }    
})(this, function () {

    'use strict';
    
    var Analytics = {};
    
    // Module initialization
    Analytics.init = function() {
        Analytics.renderSessionsTable();
    };

    Analytics.renderSessionsTable = function() {

        var google = window.google;

        var dataTable = google.visualization.arrayToDataTable(window.analyticsData);

        var options = {
            //title: 'Bezoeken',
            hAxis: {
                textStyle: {
                    fontName: 'Arial',
                    fontSize: '11',
                    color: '#444444'
                },
                slantedText: false,
                showTextEvery: 7,
                //minValue: new Date('2014-09-07')
                textPosition: 'out',
                format: '%a %m %Y'
            },
            vAxis: {
                minValue: 0,
                ticks: [500, 1000],
                textPosition: 'in',
                textStyle: {
                    fontName: 'Arial',
                    fontSize: '11',
                    color: '#444444'
                },
                gridlines: {
                    color: '#f1f1f1'
                }
            },

            animation: {
                duration: 1000,
                easing: 'in'
            },
            series: {
                0: { areaOpacity: 0.1, color: '#058dc7'}

            },
            legend: 'none',
            pointSize: 7,
            lineWidth: 4,
            //theme: 'maximized'
            chartArea: {width: '100%', height: '80%'},
            titleTextStyle: {
                fontName: 'Arial, sans-serif',
                fontSize: '13',
                color: '#444444',
                bold: false
            },
            tooltip: {
                textStyle: {
                    color: '#444444',
                    fontSize: '12'
                }
            }
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(dataTable, options);
    };

    return Analytics;    
});