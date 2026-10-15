/**
 * @file
 * Behaviors interacting with chart.js.
 */
(function (Drupal, once) {

  'use strict';

  Drupal.behaviors.bsiCharts = {
    attach (context) {
      once('deduplicate-legend', '.charts-chartjs', context).forEach((element) => {
        element.addEventListener('drupalChartsConfigsInitialization', Drupal.behaviors.bsiCharts._deduplicateLegend);
      });
    },

    _deduplicateLegend(event) {
      const options = event.detail.options;
      const originalGenerateLabels = options.plugins.legend.labels.generateLabels
        ?? Chart.overrides[event.detail.type]?.plugins?.legend.labels.generateLabels
        ?? Chart.defaults.plugins.legend.labels.generateLabels;

      options.plugins.legend.labels.generateLabels = (chart) => {
        const items = originalGenerateLabels(chart);
        const seen = new Set();

        return items.filter((item) => {
          const key = Drupal.behaviors.bsiCharts._legendItemKey(item);
          return seen.has(key) ? false : (seen.add(key) && true);
        });
      };

      options.plugins.legend.onClick = (event, legendItem, legend) => {
        const chart = legend.chart;
        const legendItems = originalGenerateLabels(chart);
        const clickedKey = Drupal.behaviors.bsiCharts._legendItemKey(legendItem);

        legendItems
          .filter((item) =>
            Drupal.behaviors.bsiCharts._legendItemKey(item) === clickedKey
          )
          .forEach((item) => {
            if (item.datasetIndex !== undefined) {
              const visible = chart.isDatasetVisible(item.datasetIndex);
              chart.setDatasetVisibility(item.datasetIndex, !visible);
            }
            else if (item.index !== undefined) {
              chart.toggleDataVisibility(item.index);
            }
          });
          chart.update();
        };
    },
    _legendItemKey(item) {
      return JSON.stringify([ item.text, item.fillStyle ]);
    }
  };

} (Drupal, once));
