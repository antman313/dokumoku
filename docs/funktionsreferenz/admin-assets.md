# admin-assets.php (CSS/JS)

## `admin_enqueue_scripts` (Hook)
- Zweck: Lädt Admin‑Styles und -Skripte **nur** auf der DokuMoku‑Seite (`$hook === 'toplevel_page_dokumoku'`).  
- CSS: helles UI (Header/Badge, Sidebar, Content, Code‑Blöcke).  
- JS:  
  - `.md`‑Link‑Rewrite im Admin (klickbare interne Links)  
  - UX: beim Set‑Wechsel `index.md` vorwählen
