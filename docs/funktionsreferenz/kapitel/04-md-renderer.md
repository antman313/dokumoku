# Markdown – renderer (`md-renderer.php`)

> Minimaler Parser: Überschriften, Listen, Absätze, Inline‑Formatierungen, Codeblöcke, Links (mit Rewrite).

## `dokumoku_parse_inline(string $txt, string $set='', string $currentDoc='', bool $frontend=false): string`
- **Zweck:** Wandelt Inline‑Markdown um: `[]()` Links (mit `.md`‑Rewrite), `*…*`, `**…**`, `` `code` ``.  
- **Returns:** HTML (sicher, da `esc_html`/`esc_url` genutzt).  
- **Verwendet von:** Block‑Parser.

## `dokumoku_parse_block_markdown(string $text, string $set, string $currentDoc, bool $frontend=false): string`
- **Zweck:** Zerlegt Text in Blöcke: Überschriften `#..######`, UL/OL, Absätze.  
- **Returns:** HTML‑Fragmente.  
- **Verwendet von:** `dokumoku_render_doc()`.

## `dokumoku_render_doc(string $set, string $rel_file, bool $frontend=false): string`
- **Zweck:** Rendert eine `.md`‑Datei in `<article class="dm-article">…</article>`.  
- **Ablauf:**  
  1. `dokumoku_list_docs()` prüft Erlaubnis  
  2. `dokumoku_read()` liest Inhalt  
  3. Fenced Code via `preg_split` → `<pre><code class="language-…">`  
  4. Rest via Block/Inline‑Parser  
- **Returns:** fertiges HTML (oder Fehlermeldung).  
- **Verwendet von:** Admin‑Main, Shortcode.
