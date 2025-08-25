# Flows (kurz)

## Anzeigen (Admin)
Topbar wählt Set/Doc → Sidebar verlinkt → `dokumoku_render_doc()` rendert.

## Bearbeiten
Klick „Bearbeiten“ → `mode=edit` → `dokumoku_render_editor()` zeigt Textarea.  
Submit → `admin_post_dokumoku_save` → `dokumoku_write()` (+ `.bak`) → Redirect mit `updated=1`.

## Frontend
`[dokumoku set="x"]` → Render mit Frontend‑Link‑Rewrite; Navigation via Query.
