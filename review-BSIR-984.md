# Code Review: MR !687 — BSIR-984 Tooltip Decimal Formatting

## Summary

Instead of auto-detecting stacked percentage charts via heuristic (as described in the ticket), the approach makes tooltip formatting **editor-configurable** via the existing y-axis `prefix`, `suffix`, and `decimal_count` fields. These values are passed to JS via `drupalSettings` and used to format Chart.js tooltips.

---

## Bug — `_formatTooltip` will crash when no settings exist for a chart

**File:** `js/chart.js`, line 44

```js
const chartSettings = drupalSettings.bsi_charts[event.target.id];
```

If `drupalSettings.bsi_charts` is `undefined` (no chart on the page has tooltip settings configured) or `drupalSettings.bsi_charts[event.target.id]` is `undefined` (this specific chart has no settings), this will throw `TypeError: Cannot read properties of undefined`.

**Fix:**
```js
const chartSettings = drupalSettings.bsi_charts?.[event.target.id];
if (!chartSettings || Object.keys(chartSettings).length === 0) {
  return;
}
```

---

## Minor — `null` is not valid for `Intl.NumberFormat` fraction digit options

**File:** `js/chart.js`, lines 58–59

```js
minimumFractionDigits: chartSettings.decimal_count ?? null,
maximumFractionDigits: chartSettings.decimal_count ?? null,
```

When `decimal_count` is not set, passing `null` may behave differently across browsers. The `Intl.NumberFormat` spec expects a number or `undefined`. Better to use `undefined`:

```js
minimumFractionDigits: chartSettings.decimal_count ?? undefined,
maximumFractionDigits: chartSettings.decimal_count ?? undefined,
```

---

## Minor — `decimal_count` type casting

**File:** `src/Hook/ChartHooks.php`, line 42

```php
$element['#attached']['drupalSettings']['bsi_charts'][$element['#id']][$property] = $element['yaxis']['#' . $property];
```

The value from `$element['yaxis']['#decimal_count']` is passed as-is. If it comes from a form as a string (e.g. `"3"`), JS will receive a string. `Intl.NumberFormat` expects an integer for `minimumFractionDigits` / `maximumFractionDigits`.

Consider casting `decimal_count` to int:

```php
foreach (['prefix', 'suffix', 'decimal_count'] as $property) {
  if (strlen((string) $element['yaxis']['#' . $property]) > 0) {
    $value = $element['yaxis']['#' . $property];
    if ($property === 'decimal_count') {
      $value = (int) $value;
    }
    $element['#attached']['drupalSettings']['bsi_charts'][$element['#id']][$property] = $value;
  }
}
```

---

## Design consideration — manual vs. automatic

The ticket describes a heuristic-based approach to auto-detect stacked percentage charts:
- CSV filename ends with `-stacked`
- Metadata contains "Anteile in Prozent"
- Data range is 0–100
- All data series sum to ~100

This MR takes a different approach: editors manually configure `decimal count = 3` and `suffix = %` per chart. This is simpler and more flexible, but means editors must remember to set it for each stacked percentage chart.

Worth confirming with Julia/Nicole that the manual approach is acceptable instead of the automatic heuristic.

---

## What looks good

- Clean separation: PHP passes config via `drupalSettings`, JS handles formatting
- `Intl.NumberFormat` for locale-aware number formatting (respects page language)
- Refactoring event listeners registration into an array loop — cleaner
- Fixing the early return in `alterChartDefinition` so trendline logic no longer blocks settings attachment
- `Chart.php` type hint improvements (`const string`, return types)

---

## Verdict

**Approve with minor fixes** — the null-safety bug on line 44 must be fixed before merging (it will crash on any chart without tooltip settings configured). The other items are nice-to-haves.
