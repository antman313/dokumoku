# 📖 DokuMoku

![Version](https://img.shields.io/badge/version-0.1.0-blue)
![License](https://img.shields.io/badge/license-GPLv2-green)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-555)

**DokuMoku** ist ein WordPress-Plugin für **Markdown-basierte Dokumentationen im Admin-Bereich**.  
Erstelle mehrere Doku-Sets, verlinke Seiten mit Markdown und navigiere direkt im WP-Backend.  
Ideal für **Teams, Entwicklerdokus, Redaktions-Handbücher oder interne Wissensbasen**.

---

## ✨ Features

- 📂 **Mehrere Dokumentations-Sets** (`/docs/<set>/`)
- 📝 **Markdown-Syntax** (Überschriften, Listen, Links, Codeblöcke, …)
- 🔗 **Automatisches Link-Rewrite**: `.md` → Admin-URLs
- 🎨 **Helles, sauberes WP-UI** (Header, Sidebar, Content)
- 🔒 **Shortcode** für Frontend-Einbindung:  
  ```php
  [dokumoku set="handbuch" file="index.md"]
  ```
- 🚀 **Ready für Teams**: Handbücher, Shortcodes-Übersichten, How-Tos, Tech-Referenzen

---

## 📦 Installation

1. Lade das Plugin herunter (`dokumoku-0.1.0.zip`)  
2. Im WordPress-Admin unter *Plugins → Installieren → Plugin hochladen* aktivieren  
3. Menü **DokuMoku** öffnen → gewünschtes Set wählen → starten 🚀

**Dev-Setup (MAMP / macOS):**
```bash
git clone https://github.com/DEIN-USER/dokumoku.git ~/Projects/dokumoku
ln -s ~/Projects/dokumoku /Applications/MAMP/htdocs/<wp-site>/wp-content/plugins/dokumoku
```

---

## 🛠 Roadmap

- [ ] Set-Management im Admin (anlegen, umbenennen, löschen)  
- [ ] Markdown-Editor im Admin + Revisionen  
- [ ] ZIP-Import/Export kompletter Doku-Sets  
- [ ] Suche über alle Dateien  
- [ ] Syntax-Highlighting für Codeblöcke (Prism.js)  
- [ ] Dark-Mode Support 🌙  

---

## 📸 Screenshots (coming soon)

*(Platz für Screenshots aus deinem WP-Admin)*

---

## 🤝 Contributing

Pull Requests und Issues sind willkommen!  
Für größere Features bitte vorher ein Issue eröffnen.

---

## 📄 License

GPLv2 or later – siehe [LICENSE](LICENSE).  
© 2025 codekeks.de / Andreas Grzybowski
