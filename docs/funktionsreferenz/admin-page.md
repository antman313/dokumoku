# admin-page.php (UI)

## `dokumoku_admin_page(): void`
- Zweck: Baut die komplette Admin‑Seite (Header, Topbar, Sidebar, Main).
- Ablauf:  
  1. Access‑Check via `dokumoku_capability()`  
  2. Sets/Dokumente ermitteln (`dokumoku_docsets`, `dokumoku_list_docs`)  
  3. Auswahl `set`, `doc`, `mode` (`view|edit`) aus `$_GET`  
  4. Header + Version aus Plugin‑Header (`get_file_data`)  
  5. Notices (`updated` Query)  
  6. Sidebar‑Tree (`dokumoku_sidebar_tree`)  
  7. Main: Aktionsleiste (Bearbeiten/Neu/Löschen) + Render/Editor  
- Side effects: Echoes HTML.

## `dokumoku_sidebar_tree(string $set, string $current): string`
- Zweck: Erzeugt UL/LI‑Baum aller `.md` im Set (Ordner werden gruppiert).
- Returns: `<ul class="dm-tree">…</ul>`.

## `dokumoku_render_tree_nodes(string $set, array $node, string $current, string $prefix=''): string`
- Zweck: Rekursive Hilfsfunktion zum Rendern der Baumknoten.
- Returns: HTML‑Fragmente.
