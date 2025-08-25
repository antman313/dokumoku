# admin-actions.php (Bearbeiten/Speichern)

## `dokumoku_render_editor(string $set, string $rel_file): string`
- Zweck: Zeigt Inline‑Editor (Textarea) für eine Datei inkl. Save‑Form.
- Sicherheit: Nur, wenn Datei im erlaubten Set vorkommt (`dokumoku_list_docs`).
- Returns: `<form>…</form>` HTML.
- Verwendet von: Admin‑Main im `mode=edit`.

## `admin_post_dokumoku_save` (Hook) → `dokumoku_handle_save()`
- Zweck: Speichert die bearbeitete Datei.  
- Guard: `current_user_can(dokumoku_capability())`  
- Sicherheit: `wp_verify_nonce('dokumoku_save_'.$set.'|'.$doc)`; Datei muss in `dokumoku_list_docs($set)` vorhanden sein.  
- I/O: `dokumoku_write($set,$doc,$content)` (legt `.bak` an)  
- Redirect: zurück zu `admin.php?page=dokumoku&set=…&doc=…&updated=1|0`  
- Verwendet von: Formular aus `dokumoku_render_editor()`.

**Geplant:**  
- `admin_post_dokumoku_new` (Datei anlegen)  
- `admin_post_dokumoku_delete` (löschen/verschieben nach `_trash/`).
