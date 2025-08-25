# Core – utils (`core-utils.php`)

## `dokumoku_normalize_path(string $path): string`
- **Zweck:** Normalisiert Pfade (Slash, `.`/`..` auflösen) → verhindert Traversal.  
- **Params:** `$path` – relativer Pfad.  
- **Returns:** Bereinigter Pfad ohne führende/trailing Slashes.  
- **Verwendet von:** Link‑Rewrite, FS‑Helpers.

## `dokumoku_admin_doc_url(string $set, string $rel): string`
- **Zweck:** Baut Admin‑URL zum Anzeigen einer `.md`‑Datei.  
- **Params:** `$set` Set‑Slug, `$rel` relativer Dateipfad.  
- **Returns:** `admin.php?page=dokumoku&set=…&doc=…`  
- **Verwendet von:** Sidebar, Link‑Rewrite, Header‑Aktionen.

## `dokumoku_current_url(): string`
- **Zweck:** Ermittelt aktuelle Seite als absolute URL (ohne Hash).  
- **Returns:** `https://host/pfad?...`  
- **Verwendet von:** Frontend‑Shortcode für Query‑basierte Navigation.

## `dokumoku_frontend_doc_url(string $set, string $rel): string`
- **Zweck:** Baut Frontend‑URL mit Query‑Parametern (`dokumoku_set`, `dokumoku_doc`).  
- **Verwendet von:** Link‑Rewrite im Frontend‑Modus.

## `dokumoku_rewrite_doc_href(string $set, string $currentDoc, string $href, bool $frontend=false): string`
- **Zweck:** Schreibt **relative `.md`‑Links** zu Admin/Frontend‑URLs um.  
- **Params:** `$set` (aktuelles Set), `$currentDoc` (aktuelle Datei, Basisordner), `$href` (Original‑Link), `$frontend` (Admin=false/Frontend=true).  
- **Returns:** umgeschriebene URL (inkl. Anker), nicht‑`.md` bleibt unverändert.  
- **Verwendet von:** Inline‑Parser.
