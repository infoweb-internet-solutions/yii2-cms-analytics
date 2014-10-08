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

    };

    Analytics.renderSessionsGraph = function() {

        var google = window.google;

        var data = google.visualization.arrayToDataTable(window.sessionsData);

        var options = {
            hAxis: {
                textStyle: {
                    fontName: 'Arial',
                    fontSize: '11',
                    color: '#444444'
                },
                slantedText: false,
                showTextEvery: 7,
                //minValue: new Date('2014-09-07')
                textPosition: 'out'
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
            series: {
                0: { areaOpacity: 0.1, color: '#058dc7'}
            },
            legend: 'none',
            pointSize: 7,
            lineWidth: 4,
            chartArea: {
                width: '100%',
                height: '80%'
            },
            titleTextStyle: {
                fontName: 'Arial, sans-serif',
                fontSize: '13',
                color: '#444444',
                bold: false
            },
            tooltip: {
                textStyle: {
                    fontName: 'Arial',
                    color: '#444444',
                    fontSize: '12'
                }
            }
        };

        var chart = new google.visualization.AreaChart(document.getElementById('analytics-sessions'));
        chart.draw(data, options);
    };

    Analytics.renderVisitorsGraph = function() {

        var google = window.google;

        //var data = google.visualization.arrayToDataTable(window.visitorsData);

        var data = google.visualization.arrayToDataTable([
            ['Title', 'Total'],
            ['Returning visitor', 11450],
            ['New visitor', 6643],
        ]);

        var options = {
            alignment: 'center',
            legend: {
                position: 'top',
                textStyle: {
                    fontName: 'Arial',
                    fontSize: '13',
                    color: '#444444'
                },
                alignment: 'center'
            },
            pieSliceTextStyle: {
                textStyle: {
                    fontName: 'Arial',
                    fontSize: '13',
                    color: '#444444'
                }
            },
            tooltip: {
                textStyle: {
                    fontName: 'Arial',
                    color: '#444444',
                    fontSize: '12'
                }
            },
            chartArea: {
                width: '100%',
                height: '80%',
                top: 40
            },
            slices: {
                0: { color: '#058dc7' },
                1: { color: '#50b432' }
            }
        };

        var chart = new google.visualization.PieChart(document.getElementById('analytics-visitors'));

        chart.draw(data, options);
    };

    return Analytics;    
});