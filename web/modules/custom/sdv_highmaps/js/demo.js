/**
 * Provides the highmaps button activation script.
 */
(function ($, Drupal) {
  Drupal.behaviors.highmaps = {
    attach: function attach(context) {

      var config = JSON.parse(drupalSettings.highmaps.config);
      console.log(drupalSettings.highmaps);

      var dataset = drupalSettings.highmaps.dataset;
      // console.log('dataset');
      console.log(dataset);

      // dataset = [
      //   ['NL', 5.5],
      //   ['BE', 7.5],
      // ];

      console.log(dataset);

      var map = drupalSettings.highmaps.map;
console.log(map);

      // Create the chart
      Highcharts.mapChart('container', {
        chart: {
          map: config.map
        },
        credits: config.credits,
        title: {
          text: config.title
        },
        subtitle: {
          text: config.subtitle
        },
        mapNavigation: {
          enabled: config.mapNavigation.enabled,
          buttonOptions: {
            verticalAlign: 'bottom',
          }
        },
        colorAxis: config.colorAxis,
        series: [{
          data: dataset,
          joinBy: [map.join, 'hc-key'],
          name: config.series.name,
          states: {
            hover: {
              color: config.series.color
            }
          },
          dataLabels: {
            enabled: true,
            format: '{point.name}'
          }
        }]
      });



    }
  };
})(jQuery, Drupal);
