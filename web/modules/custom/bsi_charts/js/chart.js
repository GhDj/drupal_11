/**
 * @file
 * Behaviors interacting with chart.js.
 */
(function (Drupal, once, drupalSettings) {

  'use strict';

  Drupal.behaviors.bsiCharts = {
    attach (context) {
      Chart.defaults.font.family = 'BundesSansWeb, Helvetica, Arial, sans-serif';
      Chart.defaults.font.size = 17;

      once('bsi-charts', '.charts-chartjs', context).forEach((element) => {
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

      const modalChart = document.querySelector(
        '#drupal-modal .charts-chartjs'
      );

      if (!modalChart || modalChart.id !== event.target.id) {
        return;
      }

      const options = event.detail.options;


      // Be sure pie charts do not get to big, especially with caption
      if (['pie', 'doughnut'].includes(event.detail.type)) {
        requestAnimationFrame(() => {
          const canvas = document.getElementById(event.target.id);

          if (canvas) {
            canvas.style.height = '450px';
          }
        });
      }
      // Force legend below the chart so the source text aligns properly.
      if (!['pie', 'doughnut'].includes(event.detail.type)) {
        options.plugins.legend = options.plugins.legend ?? {};
        options.plugins.legend.position = 'bottom';
      }
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
      const options = event.detail.options;
      const isPie = ['pie', 'doughnut'].includes(event.detail.type);

      if (isPie) {
        options.plugins.tooltip ??= {};
        options.plugins.tooltip.callbacks ??= {};
        options.plugins.tooltip.mode = 'dataset';
        options.plugins.tooltip.intersect = true;
        options.plugins.tooltip.position = 'nearest';
        options.plugins.tooltip.filter = (item) => item.chart.getDataVisibility(item.dataIndex);
        options.plugins.tooltip.callbacks.title = () => '';
        options.plugins.tooltip.callbacks.label = (context) => {
          const label = [context.label].flat().join(' ');
          const value = Object.keys(chartSettings).length === 0
            ? context.formattedValue
            : Drupal.behaviors.bsiCharts.formatNumber(context.raw, context.chart, chartSettings);
          return `${label}: ${value}`;
        };
        return;
      }

      options.plugins.tooltip ??= {};
      options.plugins.tooltip.callbacks ??= {};
      options.plugins.tooltip.callbacks.title = Drupal.behaviors.bsiCharts.formatTooltipTitle;
      options.plugins.tooltip.callbacks.label = (context) => Drupal.behaviors.bsiCharts.formatTooltipValue(context, chartSettings);

      if (Object.keys(chartSettings).length === 0 && event.detail.type !== 'scatter') {
        return;
      }
    },

    formatTooltipTitle(tooltipItems) {
      const item = tooltipItems[0];
      if (!item) {
        return '';
      }
      const label = item.chart.data.labels?.[item.dataIndex];
      return Array.isArray(label) ? label.join(' ') : item.label;
    },

    formatTooltipValue(context, chartSettings) {
      let value = context.raw;
      if (context.dataset.type === 'scatter') {
        value = context.raw.y;
      }
      return `${context.dataset.label}: ${Drupal.behaviors.bsiCharts.formatNumber(value, context.chart, chartSettings)}`;
    },
    formatNumber(value, chart, chartSettings) {
      const numberFormat = new Intl.NumberFormat(
        chart.options.locale,
        {
          minimumFractionDigits: chartSettings.decimal_count ?? 0,
          // Need to provide or defaults to 3 digits.
          maximumFractionDigits: chartSettings.decimal_count ?? 100,
        }
      );

      return `${chartSettings.prefix ?? ''}${numberFormat.format(value)}${chartSettings.suffix ?? ''}`;
    },

    _adjustAxes(event) {
      const options = event.detail.options;

      if (!options.scales?.x) {
        return;
      }

      // Render labels as configured in the field, no number formatting.
      options.scales.x.ticks = options.scales.x.ticks ?? {};
      options.scales.x.ticks.callback = Drupal.behaviors.bsiCharts.formatAxisTick;

      //show all y labels
      if (options.scales.y) {
        options.scales.y.ticks = options.scales.y.ticks ?? {};
        options.scales.y.ticks.autoSkip = false;
      }
    },

    formatAxisTick(value, index, ticks) {
      return this.type === 'category'
        ? (this.chart.data.labels[value] ?? null)
        : Chart.Ticks.formatters.numeric.call(this, value, index, ticks);
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

      const datasets = event.detail.data?.datasets ?? [];
      // Handle area charts, display one tooltip for everything
      const isArea = event.detail.type === 'line' && datasets.some((dataset) => dataset.fill === 'origin');
      if (isArea || event.detail.type === 'bar' || datasets.some((dataset) => dataset.type === 'bar')) {
        options.interaction = options.interaction ?? {};

        if (options.indexAxis === 'y') {
          options.interaction.mode = 'y';
        }
        else {
          options.interaction.mode = 'x';
        }

        options.interaction.intersect = false;
        options.plugins.tooltip ??= {};
        options.plugins.tooltip.mode = 'index';
        options.plugins.tooltip.axis = options.interaction.mode;
        options.plugins.tooltip.intersect = false;
      }

      const originalGenerateLabels = options.plugins.legend.labels?.generateLabels
        ?? Chart.overrides[event.detail.type]?.plugins?.legend.labels.generateLabels
        ?? Chart.defaults.plugins.legend.labels.generateLabels;

      options.plugins.legend.labels = options.plugins.legend.labels ?? {};
      options.plugins.legend.labels.generateLabels = (chart) => {
        const items = originalGenerateLabels(chart);
        const seen = new Set();

        // Join legend into one line
        items.forEach((item) => {
          item.text = Drupal.behaviors.bsiCharts._joinLabel(item.text);
        });

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
      const canvas = document.getElementById(event.target.id);

      if (!canvas) {
        return;
      }

      const figure = canvas.closest('.charts-figure');

      if (figure &&  ['pie', 'doughnut'].includes(event.detail.type)) {
        figure.classList.add('bsi-chart__pie');
      }

      if (window.innerWidth >= 768) {
        return;
      }

      const labelCount = event.detail.data?.labels?.length ?? 0;
      if (labelCount <= 10) {
        return;
      }

      if (figure) {
        figure.style.minWidth = `${labelCount * 80}px`;
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
    },
    _joinLabel(label) {
      return Array.isArray(label) ? label.join(' ') : label;
    }
  };

} (Drupal, once, drupalSettings));
