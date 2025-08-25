# fs-storage.php (Dateisystem)

## `dokumoku_docsets(): array<string>`
- Zweck: Listet alle Set‑Ordner unter `/docs` (alphabetisch/natürlich sortiert).
- Returns: Array der Set‑Slugs.
- Verwendet von: Admin‑Topbar, Admin‑Page‑Init.

## `dokumoku_scan_tree(string $base_dir): array<string>`
- Zweck: Rekursiv alle `.md`‑Dateien unterhalb eines Ordners sammeln.
- Returns: relative Pfade (mit Unterordnern).
- Verwendet von: `dokumoku_list_docs()`.

## `dokumoku_list_docs(string $set): array<string>`
- Zweck: Listet alle erlaubten `.md`‑Dateien eines Sets.
- Returns: Pfadliste; leer, wenn Set nicht existiert.
- Verwendet von: Renderer, Editor, Save‑Handler, Sidebar.

## `dokumoku_read(string $set, string $rel): string|false`
- Zweck: Inhalt einer `.md`‑Datei lesen.
- Returns: Dateiinhalt oder `false` bei Fehler.
- Verwendet von: Renderer, Editor.

## `dokumoku_write(string $set, string $rel, string $content): bool`
- Zweck: `.md` speichern + Backup der alten Version (`.YYYYmmdd-HHMMSS.bak`).
- Side effects: normalisiert Zeilenenden zu `\n`, legt `.bak` an.
- Returns: `true` bei Erfolg, sonst `false`.
- Verwendet von: Save‑Handler.
