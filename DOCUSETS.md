# 📂 DokuMoku – Leitfaden für DokuSets

Dieser Leitfaden beschreibt, wie du **DokuSets** für das Plugin DokuMoku anlegst und strukturierst.

---

## 1) Ordner-Layout (Beispiel)

```
docs/
└─ handbuch/                ← = Set-Slug (Frontend: set="handbuch")
   ├─ index.md              ← Startseite des Sets
   ├─ 01-einleitung.md
   ├─ 02-workflow.md
   ├─ kapitel/
   │  ├─ 01-editor-tipps.md
   │  └─ 02-medien.md
   └─ assets/
      ├─ screenshots/
      │  └─ upload-dialog.png
      └─ logos.svg
```

**Regel:** Ein Set = ein Unterordner unter `/docs/`.\
Die **Startseite** ist immer `index.md` im Set-Root.

---

## 2) Namens- und Sortier-Konventionen

- Numerische Präfixe für Reihenfolge: `01-`, `02-` …
- Kleine, sprechende Slugs: `einleitung`, `workflow`, `editor-tipps`
- Unterordner für Kapitelgruppen: `kapitel/...`
- Assets in `assets/` (Bilder, Diagramme)
- Dateiendung **immer** `.md`

---

## 3) Minimal-Template für `index.md`

Die Landing-Page des Sets sollte kurz einführen und zu Unterseiten verlinken.

```md
# Handbuch

Willkommen im internen Handbuch. Hier findest du Prozesse, Styleguides und How-Tos.

## Schnellstart
- [Einleitung](01-einleitung.md)
- [Redaktions-Workflow](02-workflow.md)
- [Editor-Tipps](kapitel/01-editor-tipps.md)
- [Medien & Upload](kapitel/02-medien.md)

## Häufig gesucht
- [Jobanzeigen anlegen](kapitel/02-medien.md#bilder-fuer-jobanzeigen)
- [Kontakt-Shortcode](../shortcodes/index.md#bt_contact) <!-- Cross-Link zu anderem Set -->
```

**Wichtig:**

- Relative Links verwenden (`kapitel/01-editor-tipps.md`)
- Cross-Set-Links funktionieren, sauberer ist aber innerhalb eines Sets zu bleiben

---

## 4) Template für Unterseiten

```md
# Redaktions-Workflow

> Kurzer Überblick, was die Seite behandelt.

1. Beiträge erstellen …
2. Vorschau prüfen …
3. Veröffentlichen …

---

**Weiterlesen:**  
← [Einleitung](01-einleitung.md) • [Zurück zur Übersicht](index.md) • [Editor-Tipps →](kapitel/01-editor-tipps.md)
```

---

## 5) Bilder & Assets einbinden

```md
![Upload-Dialog](assets/screenshots/upload-dialog.png)
```

Oder aus einem Unterordner heraus:

```md
![Upload-Dialog](../assets/screenshots/upload-dialog.png)
```

---

## 6) Überschriften, Anker, Links

- Nutze `#`, `##`, `###` sauber – kurze, eindeutige Titel
- Abschnitts-Anker:

Zielseite:

```md
### Bilder für Jobanzeigen
```

Link:

```md
kapitel/02-medien.md#bilder-fuer-jobanzeigen
```

---

## 7) Code & Hinweise

**Codeblöcke:**

```php
add_action('init', function(){ /* … */ });		
```

**Hinweise mit Blockquotes:**

> Tipp: Bilder max. 1600px Breite – spart Ladezeit.

**Tabellen:**

```md
| Feld       | Typ   | Pflicht |
|------------|-------|---------|
| Titel      | Text  | Ja      |
| Kategorie  | Liste | Nein    |
```

---

## 8) Inhaltsverzeichnisse (TOC)

Bis ein automatisches TOC eingebaut ist → manuell anlegen:

```md
## Auf dieser Seite
- [Workflow](#redaktions-workflow)
- [Abnahme-Checkliste](#abnahme-checkliste)
```

---

## 9) „Set-Readme“ (Meta)

Falls du Meta-Infos pro Set willst, lege zusätzlich eine `_about.md` ins Set und verlinke es im `index.md`.\
(Pro-Feature: später automatische Anzeige als Infobox.)

---

## 10) Checkliste zum Start

- Ordner unter `/docs/{set}/` anlegen (Set‑Slug = Ordnername)
- `index.md` im Set‑Root anlegen (Landing‑Page)
- Unterseiten erstellen und optional mit `01-`, `02-` … nummerieren
- Bilder/Assets in `assets/` (z. B. `assets/screenshots/...`)
- **Relative Links** setzen und testen (auch `.md`‑Links innerhalb des Admins)
- Abschnitts‑Anker prüfen (`#anker`) – Ziel‑Überschriften sauber setzen
- Dateinamen: **lowercase**, Bindestriche statt Leerzeichen, **keine Umlaute**
- Bilder optimieren: max. \~1600px Breite, WebP/JPEG, sinnvolle Dateinamen
- Optional: TOC‑Block in `index.md` („Auf dieser Seite“)
- Kurztest: Admin‑Navigation (Sidebar), Frontend mit `[dokumoku set="{set}" file="index.md"]`
- Finaler Review: Überschriften‑Hierarchie (`#` → `##` → `###`), Rechtschreibung, Links

