# Free Field Configuration Design Baseline

Version: 1.1.0

Status: Approved Design Baseline (no implementation yet)

Changelog:
- v1.1.0: Added the six approved Import/Export configuration decisions and
  the six approved Admin Builder decisions.
- v1.0.0: Initial approval of the field schema, builder requirements, choice
  rules, Import/Export schema, and nine design decisions.

---

# Purpose

Freeze the final Free-plugin field configuration design before any further
implementation. This contract records the approved 10-property field schema,
the Field Builder requirements, the choice-field rules, the Import/Export
schema, the nine original locked design decisions, the six Import/Export
configuration decisions, and the six Admin Builder decisions.

It does not authorize implementation, debugging, or committing. A separate
task contract must be created before any code is written.

---

# Scope

- Free plugin field configuration model under `scfw_options.fields`.
- Designer-authorized additions: `placeholder`, `description`, `default`,
  `options`.
- Unchanged: `id`, `label`, `type`, `required`, `enabled`, ordering, and the
  opaque Pro `rules` property.
- Out of scope (Pro, not now): conditional logic T0061-T0065, licensing,
  subscriptions, multi-value checkbox groups, value/label option splits.

---

# 1. Final Free Field Schema

Canonical field entry under `scfw_options.fields`, stored as an ordered JSON
object keyed by field id. Object order is the checkout order (the data-model
"Priority").

```
fields.<id> = {
  "id":          "a-z0-9_-",            // sanitize_key; ^[a-z0-9_-]+$; unique
  "label":       "text",                // sanitize_text_field; non-empty (strict)
  "type":        "text|textarea|email|tel|number|date|checkbox|radio|select",
  "required":    true|false,            // boolean
  "enabled":     true|false,            // boolean
  "placeholder": "text",                // sanitize_text_field             }
  "description": "text",                // sanitize_text_field             } NEW in Free
  "default":     "text",                // validated against type/options  }
  "options":  [ "Option A", "Option B" ], // ONLY radio|select             } NEW in Free
  "rules":       <opaque>,              // Pro; preserved verbatim; never evaluated in Free
}
```

Grounding: the frozen data model (`docs/07-data-model.md`) lists
Placeholder, Description, Default Value, and Options as part of field
definitions; the T0020-T0023 domain contract defines the matching `Field`
properties; T0040-T0042 mandates "at minimum" ID/Label/Type/Required/Enabled
(not exclusive). Basic field functionality stays in Free.

Rule of thumb: `id`, `label`, `type`, `required`, `enabled`, ordering are
already Free and unchanged. `placeholder`, `description`, `default`, and
`options` are Free. Block Checkout support for placeholder/description/default
is limited by the native Blocks Additional Fields API and is documented as a
limitation, not silently invented (per T0040).

---

# 2. Field Builder UI Requirements

- Each field row keeps: ID, Label, Type, Required, Enabled, ordering, remove.
- Per-type extra inputs live in a collapsible sub-panel directly under each
  field row (no separate screen, no modal, no AJAX).
- Type = radio | select: the sub-panel contains an Options editor: one row
  per option, each a single text input (an option is one string = value and
  label), with:
  - Add option - appends a new blank option row.
  - Remove - removes that option row; blank pending option rows are ignored
    (never treated as options) on save.
  - Reorder - Up/Down buttons per option row.
- Type = text | textarea: sub-panel gains an optional Placeholder input.
- Type = text | textarea: sub-panel gains an optional Default input.
- Type = textarea: sub-panel additionally gains a Description input.
- Changing a field's Type between radio and select preserves the configured
  options and revalidates them on save.
- Removing a field is immediate, with no confirmation dialog.
- A required Select renders an automatic empty "select an option" entry at
  render time (not stored).
- Add-field panel stays Type + Label only; per-type extra fields appear in the
  row sub-panel, not in the add panel.
- All inputs sanitized through the Settings API. Invalid options, empty
  labels, or duplicates reject the whole save atomically (existing strict
  behavior).

---

# 3. Choice-Field Rules

## Select

- Options: ordered list of strings (domain `array<int,string>`); each string
  is both the submitted value and the displayed label.
- Validation: submitted value must match one option; required => a non-empty
  selection; optional => empty allowed.
- Default: must be one of the option strings, validated at admin time.
- Stored = selected option string. Display = the string.
- Save/HPOS/surfaces/REST operate on the string; no change needed.

## Radio

- Options: same ordered string list; exactly one selectable at checkout.
- Validation: value must match an option; required => must pick one; optional
  => empty allowed.
- Empty/default: with no default, nothing is pre-selected.
- Stored/displayed = selected option string.
- Block renders a radio field as a native select on the Block Checkout
  (final presentation limitation): the Blocks Additional Checkout Fields API
  supports text, select and checkbox only, so a true radio cannot render.
  The Core field type stays `radio`; the configured options are submitted
  through the native select and the selected option string is stored. No
  custom Block component or JS is used.

## Checkbox

- Single boolean in Free; no options list.
- Required => must be checked; optional => unchecked allowed.
- Default: `'1'` pre-checked or `'0'`/nil unchecked (Classic only).
- Stored: checked `'1'`; unchecked optional checkbox skips writing empty meta.
- Surfaces render a friendly value ("Checked"/"Yes") instead of the literal
  `1`.

---

# 4. Import / Export Schema

Wrapper stays `checkout-fields-config-v1` (Free and unchanged):

```
{
  "format": "checkout-fields-config-v1",
  "enabled": 1|0,
  "fields": {
    "<id>": { "id","label","type","required","enabled",
              "placeholder","description","default","options",  // optional, NEW
              "rules": <opaque, optional> }
  }
}
```

- Backward compatible: old Phase 1-5 files without the new keys import
  unchanged (missing keys default to empty).
- New keys are optional and validated when present:
  - `options`: must be a non-empty string list for radio/select.
  - `default`: must validate against the field type and its options.
  - Violations reject the whole file atomically.
- Unknown keys are dropped except `rules` (preserved verbatim).
- Deterministic export, atomic import, `manage_options` + nonces (unchanged).

---

# 5. Per-Property Rules (summary)

| Property | Free/Pro | Config | Sanitize | Import/Export | Block |
|---|---|---|---|---|---|
| id | Free | key + value | `sanitize_key` | round-trips | namespace prefix |
| label | Free | value | `sanitize_text_field` | round-trips | label |
| type | Free | value | FieldType enum | round-trips | adapter mapping + documented limitations |
| required | Free | bool | boolean | round-trips | native |
| enabled | Free | bool | boolean | round-trips | native |
| ordering | Free | key order | - | preserved | preserved |
| placeholder | Free | value | `sanitize_text_field` | round-trips | limitation |
| description | Free | value | `sanitize_text_field` | round-trips | limitation |
| default | Free | value | type-aware | round-trips | limitation |
| options | Free | list | non-empty string list | round-trips | select native; radio presented as native select (true radio unsupported) |
| rules | Pro | value | opaque; never evaluated | preserved verbatim | via visibility filter only |

---

# 6. Locked Decisions

1. Options are single strings; the option string is both value and label
   (matches the frozen domain `array<int,string>` model).
2. Description is shown for text and textarea only.
3. Checkbox is a single boolean in Free; option-group checkboxes are out of
   Free scope.
4. Required Selects auto-render an empty selection entry; no stored empty
   option.
5. Required Radio enforces a choice; optional Radio with no default has
   nothing pre-selected.
6. Block Checkout limitations are accepted and documented only:
   `radio` maps to a native `select` (true radio is unsupported by the Blocks
   API, which allows text, select and checkbox only); `textarea`/`email`/
   `tel`/`number`/`date` map to `text`; no placeholder/description/default in
   Block.
7. Optional unchecked Checkbox does not write empty meta.
8. Checkbox surfaces render a friendly label ("Checked"/"Yes") instead of
   raw `1`.
9. Number/date validation stays lenient (current rules); stricter
   min/max/format rules are deferred to the separate validation design.

---

# 7. Approved Import/Export Configuration Decisions

1. A `checkbox` field with an `options` payload is rejected.
2. An invalid `default` value rejects the entire import/configuration
   atomically.
3. Empty or duplicate option strings are rejected.
4. Settings-save failure keeps the current behavior: the previously stored
   fields are preserved and a settings error is shown.
5. On import, the file's `rules` win (no merge with stored rules).
6. The exported `enabled` value stays `0|1`.

---

# 8. Approved Admin Builder Decisions

1. Options editor is a collapsible sub-panel directly under each field row.
2. Blank pending option rows are ignored (never treated as options) on save.
3. Option reordering uses Up/Down buttons.
4. Changing a field's Type between radio and select preserves options and
   revalidates them.
5. Removing a field is immediate with no confirmation dialog.
6. Placeholder, Description, and Default inputs live in the per-type
   sub-panel.

---

# Compatibility

- Existing two-property configuration and no-rule behavior unchanged.
- Existing `_scfw_` order metadata and HPOS storage unchanged.
- Existing `scfw_field_visibility` extension point and enable gates stay
  authoritative (global enabled -> field enabled -> future Pro visibility).
- Import/Export remains Free.

---

# Not Authorized

This contract is a DESIGN BASELINE ONLY.

No implementation, no PHP/JS/CSS changes, no DB/option changes, no docs
changes, no commit, and no push occur under this contract. A new task
contract must be prepared and approved before implementation begins.