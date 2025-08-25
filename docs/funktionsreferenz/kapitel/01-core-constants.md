# Core – constants (`core-constants.php`)

## `DOKUMOKU_PATH` / `DOKUMOKU_URL` / `DOKUMOKU_DOCS_DIR`
- **Typ:** const  
- **Beschreibung:** Basispfade/URL des Plugins und Stammordner für Doku‑Sets (`/docs`).  
- **Verwendet von:** allen Subsystemen (FS, Renderer, Admin UI).

## `dokumoku_capability(): string`
- **Zweck:** Liefert die benötigte Capability für den Admin‑Zugriff.  
- **Returns:** Standard `"manage_options"` – per Filter `dokumoku_capability` änderbar.  
- **Side effects:** keiner.  
- **Verwendet von:** Menüregistrierung, Save‑Handler, Admin‑Page‑Guard.

## `dokumoku_menu_icon(): string`
- **Zweck:** Liefert ein Base64‑SVG als Menü‑Icon (Fallback: `dashicons-media-document`).  
- **Returns:** Data‑URL mit SVG oder Dashicons‑Slug.  
- **Verwendet von:** `add_menu_page(...)` im Hauptplugin.
