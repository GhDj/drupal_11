## BSIR-855 — Preview Button Fix

### What was done

The node form "Preview" button (Vorschau) was **disabled for the `page` content type** (`preview_mode: 0`).

**Reason:** The `page` content type uses per-entity Layout Builder overrides. Drupal's node form Preview cannot properly render LB layouts in preview mode — it shows incomplete/wrong content and moderation state. This is a known limitation of the LB + Content Moderation interaction in Drupal core.

The `entry_page` content type (which also uses LB overrides) already had preview disabled for the same reason.

### Regarding "use a module instead of LB instant preview"

After investigation, **no additional module is needed**. The project already has a working preview mechanism for the latest unreleased version:

#### "Latest version" tab (already configured ✅)

- **Route:** `/node/{nid}/latest`
- **Permission:** `view latest version` — already granted to all authenticated users
- **How it works:** When a node has a forward revision (e.g. Draft or In Release on top of Published), the "Latest version" tab automatically appears on the node page. It renders the full page including the Layout Builder layout.
- **Blocks:** The `bsi_bund_opener` block is already configured to display on this route.

| Preview method | Where | Works with LB? | Status |
|---|---|---|---|
| Node form "Preview" button | Node edit form | ❌ Broken with LB | Disabled by this fix |
| LB "Show content preview" toggle | `/node/{nid}/layout` | ✅ Edit mode only | Not a full page preview |
| **"Latest version" tab** | `/node/{nid}/latest` | **✅ Full page render** | **Already works** |

### Recommendation

The **"Latest version" tab** is the intended Drupal core mechanism for previewing unreleased revisions. It provides a full page render with the correct LB layout and moderation state — no extra module required.

If in the future there's a need to **share preview links with external reviewers** (people without Drupal accounts), then the [`preview_link`](https://www.drupal.org/project/preview_link) module (stable, Drupal 11 compatible, security covered) could be added. But for internal editorial preview, the existing "Latest version" tab is sufficient.

### Branch

`feature/BSIR-855-preview-button` — 1 file changed: `config/sync/node.type.page.yml`
