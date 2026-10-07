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
        ?? Chart.defaults.plugins.legend.labels.generateLabels;

      options.plugins.legend.labels.generateLabels = (chart) => {
        const items = originalGenerateLabels(chart);
        const seen = new Set();

        return items.filter((item) => {
          const dataset = chart.data.datasets[item.datasetIndex];
          const key = Drupal.behaviors.bsiCharts._datasetKey(dataset);

          return seen.has(key) ? false : (seen.add(key) && true);
        });
      };

      options.plugins.legend.onClick = (event, legendItem, legend) => {
        const chart = legend.chart;
        const sourceDataset = chart.data.datasets[legendItem.datasetIndex];
        const visible = chart.isDatasetVisible(legendItem.datasetIndex);

        chart.data.datasets.forEach((dataset, index) => {
          if (Drupal.behaviors.bsiCharts._datasetKey(dataset) === Drupal.behaviors.bsiCharts._datasetKey(sourceDataset)) {
            chart.setDatasetVisibility(index, !visible);
          }
        });
        chart.update();
      };
    },
    _datasetKey(dataset) {
      return JSON.stringify([dataset.label, dataset.backgroundColor]);
    }
  };

} (Drupal, once));
