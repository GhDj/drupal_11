/**
 * @file
 * Behaviors interacting with chart.js.
 */
(function (Drupal, once, drupalSettings) {

  'use strict';

  Drupal.behaviors.bsiCharts = {
    attach (context) {
      once('deduplicate-legend', '.charts-chartjs', context).forEach((element) => {
        [
          Drupal.behaviors.bsiCharts._deduplicateLegend,
          Drupal.behaviors.bsiCharts._formatTooltip,
          Drupal.behaviors.bsiCharts._lightboxOverrides,
          Drupal.behaviors.bsiCharts._adjustLegend,
        ].forEach(callback => {
          element.addEventListener('drupalChartsConfigsInitialization', callback);
        });
      });
    },

    _lightboxOverrides(event) {
      if (!this.closest('.bsi-chart-lightbox')) {
        return;
      }
      const options = event.detail.options;

      // Force legend below the chart so the source text aligns properly.
      options.plugins.legend = options.plugins.legend ?? {};
      options.plugins.legend.position = 'bottom';

      // Symmetric left/right padding for even spacing.
      options.layout = options.layout ?? {};
      options.layout.padding = options.layout.padding ?? {};
      if (typeof options.layout.padding === 'number') {
        options.layout.padding = { top: options.layout.padding, right: options.layout.padding, bottom: options.layout.padding, left: options.layout.padding };
      }
      options.layout.padding.left = options.layout.padding.left ?? 16;
      options.layout.padding.right = options.layout.padding.right ?? 16;
    },

    _formatTooltip(event) {
      const chartSettings = (drupalSettings.bsi_charts ?? {})[event.target.id] ?? {};
      if (Object.keys(chartSettings).length === 0) {
        return;
      }

      const options = event.detail.options;
      options.plugins.tooltip ??= {};
      options.plugins.tooltip.callbacks ??= {};
      options.plugins.tooltip.callbacks.label = (context) => Drupal.behaviors.bsiCharts.formatTooltipValue(context, chartSettings);
    },
    formatTooltipValue(context, chartSettings) {
      const numberFormat = new Intl.NumberFormat(
        document.getElementsByTagName('html')[0].lang,
        {
          minimumFractionDigits: chartSettings.decimal_count ?? null,
          maximumFractionDigits: chartSettings.decimal_count ?? null,
        }
      );
      return `${context.dataset.label}: ${chartSettings.prefix ?? ''}${numberFormat.format(context.raw)}${chartSettings.suffix ?? ''}`
    },

    _adjustLegend(event) {
      const options = event.detail.options;
      options.plugins.legend = options.plugins.legend ?? {};
      options.plugins.legend.labels = options.plugins.legend.labels ?? {};

      // Increase spacing between chart area and legend.
      options.plugins.legend.labels.padding = 16;

      // Match Figma legend color box dimensions.
      options.plugins.legend.labels.boxWidth = 24;
      options.plugins.legend.labels.boxHeight = 12;
    },

    _deduplicateLegend(event) {
      const options = event.detail.options;

      if (event.detail.type === 'bar') {
        options.interaction = options.interaction ?? {};

        if (options.indexAxis === 'y') {
          options.interaction.mode = 'y';
        }
        else {
          options.interaction.mode = 'x';
        }

        options.interaction.intersect = false;
      }

      const originalGenerateLabels = options.plugins.legend.labels?.generateLabels
        ?? Chart.overrides[event.detail.type]?.plugins?.legend.labels.generateLabels
        ?? Chart.defaults.plugins.legend.labels.generateLabels;

      options.plugins.legend.labels = options.plugins.legend.labels ?? {};
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

} (Drupal, once, drupalSettings));
