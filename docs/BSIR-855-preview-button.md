## BSIR-855 — Preview Button Fix

### Problem

The node form "Preview" button (Vorschau) shows the wrong moderation state ("Current state") after a preview round-trip. When you change the moderation state dropdown (e.g. Draft → In Release), click Preview, then return to the edit form, the "Current state" label reflects the previewed state rather than the actual saved state. This makes the status display incomplete/incorrect and can confuse editors about available transitions.

This is a known Drupal core issue ([#2914873](https://www.drupal.org/project/drupal/issues/2914873), [#2940701](https://www.drupal.org/project/drupal/issues/2940701)) where the content moderation widget's "from state" gets changed during preview.

### What was done

1. **Fixed the "Current state" display** in `bsi_custom/src/Hook/FieldWidgetHooks.php`:
   - Added `hook_field_widget_single_element_moderation_state_default_form_alter`
   - The hook always loads the persisted moderation state from the database
   - Overrides the "Current state" label with the actual saved state, regardless of form/preview state
   - This ensures the "Current state" is always correct, even after a Preview → Back to editing cycle

2. **Kept `preview_mode: 1`** for the `page` content type:
   - The Preview button works for field value changes — editors can preview text/field edits before saving
   - The Layout Builder layout renders correctly in preview (it's a stored field, not affected by unsaved form values)
   - Only the moderation state display was wrong, which is now fixed

### About using a module vs. LB instant preview

After investigation, **no additional module is needed**. The project already has a working mechanism to preview the latest unreleased revision:

#### "Latest version" tab (already configured)

- **Route:** `/node/{nid}/latest`
- **Permission:** `view latest version` — already granted to all authenticated users
- **How it works:** When a node has a forward revision (e.g. Draft or In Release on top of Published), the "Latest version" tab automatically appears on the node page. It renders the full page including the Layout Builder layout.
- **Blocks:** The `bsi_bund_opener` block is already configured to display on this route.

| Preview method | Where | Works with LB? | Status |
|---|---|---|---|
| Node form "Preview" button | Node edit form | ✅ Fixed | Shows field changes before saving |
| LB "Show content preview" toggle | `/node/{nid}/layout` | ✅ Edit mode only | Toggles block rendering in LB |
| **"Latest version" tab** | `/node/{nid}/latest` | **✅ Full page render** | Shows latest unpublished revision |

### Recommendation

- **For previewing edits before saving:** Use the node form "Preview" button (now fixed)
- **For viewing the latest unreleased version as it would appear to visitors:** Use the "Latest version" tab
- **For sharing preview links with external reviewers** (future need): Consider the [`preview_link`](https://www.drupal.org/project/preview_link) module (stable, Drupal 11 compatible, security covered)

### Branch

`feature/BSIR-855-preview-button`

### Changed files

- `web/modules/custom/bsi_custom/src/Hook/FieldWidgetHooks.php` — Added moderation state widget alter
- `docs/BSIR-855-preview-button.md` — This documentation
