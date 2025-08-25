# Shortcode (`shortcode.php`)

## `[dokumoku set="…" file="index.md" only_logged_in="1"]`
- **Zweck:** Rendert Dokumentation im **Frontend** (gleicher Renderer).  
- **Parameter:**  
  - `set` (Pflicht) – Set‑Slug  
  - `file` – Startdatei (Default `index.md`)  
  - `only_logged_in` – nur für eingeloggte User (Default `1`)  
- **Query‑Override:** `?dokumoku_doc=…` überschreibt `file`.  
- **Sicherheit:** Bei `only_logged_in=1` → Hinweisbox, wenn ausgeloggt.  
- **Intern:** nutzt `dokumoku_render_doc($set, $file, true)` (Frontend‑Links).
