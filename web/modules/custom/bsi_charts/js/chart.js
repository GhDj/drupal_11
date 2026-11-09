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
          Drupal.behaviors.bsiCharts._adjustAxes,
          Drupal.behaviors.bsiCharts._adjustLegend,
          Drupal.behaviors.bsiCharts._adjustChartWidth,
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
      if (Object.keys(chartSettings).length === 0 && event.detail.type !== 'scatter') {
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
          minimumFractionDigits: chartSettings.decimal_count ?? 0,
          // Need to provide or defaults to 3 digits.
          maximumFractionDigits: chartSettings.decimal_count ?? 100,
        }
      );

      let value = context.raw;
      if (context.dataset.type === 'scatter') {
        value = context.raw.y;
      }
      return `${context.dataset.label}: ${chartSettings.prefix ?? ''}${numberFormat.format(value)}${chartSettings.suffix ?? ''}`
    },

    _adjustAxes(event) {
      const options = event.detail.options;
      options.scales.x.ticks = options.scales.x.ticks ?? {};
      // Render labels as configured in the field, no number formatting.
      options.scales.x.ticks.callback = function (value, index, ticks) {
        return event.detail.data.labels[value] ?? null;
      };
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

      if (window.innerWidth < 768) {
        options.aspectRatio = 1.5;
        options.plugins.legend.labels.boxWidth = 12;
        options.plugins.legend.labels.boxHeight = 8;
        options.plugins.legend.labels.padding = 8;
      }
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
    _adjustChartWidth(event) {
      if (window.innerWidth >= 768) {
        return;
      }

      const canvas = document.getElementById(event.target.id);
        if (window.innerWidth >= 768) {
         return;
        }

        const labelCount = event.detail.data?.labels?.length ?? 0;
        if (labelCount <= 10) {
          return;
        }
      if (!canvas) {
        return;
      }

      const figure = canvas.closest('.charts-figure');

      if (figure) {
        figure.style.minWidth = `${labelCount * 80}px`;

        console.log('width gesetzt', figure.style.minWidth);
      }

      document.querySelectorAll('.charts-figure').forEach((figure) => {
        if (!figure.parentElement.classList.contains('charts-wrapper')) {
          const wrapper = document.createElement('div');
          wrapper.className = 'bsi-charts-wrapper bsi-grid';

          figure.parentNode.insertBefore(wrapper, figure);
          wrapper.appendChild(figure);
        }
      });
    },
    _legendItemKey(item) {
      return JSON.stringify([ item.text, item.fillStyle ]);
    }
  };

} (Drupal, once, drupalSettings));
