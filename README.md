# Projekt- & Kundenmanagementsystem

Eine maßgeschneiderte, performante Webplattform zur Verwaltung von Projekten, Kunden, Arbeitszeiten und automatisierter Rechnungsstellung. Entwickelt mit nativem PHP und einer relationalen SQL-Datenbank über PDO, wurde diese Anwendung umfassenden Sicherheitsoptimierungen und architektonischen Refactorings unterzogen, um modernen Produktions- und Unternehmensstandards zu entsprechen.

---

## Architektonischer Aufbau & Ordnerstruktur

Die Anwendung erzwingt eine strikte Trennung zwischen dem öffentlich zugänglichen Web-Verzeichnis (Web-Root), der zentralen Systemkonfiguration, den Dokumentenvorlagen und den Backend-Abhängigkeiten:


```

.
├── htdocs/                         # Öffentlich zugängliches Web-Verzeichnis (Frontend & Backend)
│   ├── admin/                      # Administrative Dashboards, KPIs und Systemübersicht
│   ├── backup/                     # Backup-Verwaltung und Wiederherstellungs-Schnittstelle
│   ├── change_password/            # Passwort-Selbstverwaltung für Benutzer
│   ├── change_password_admin/      # Administrative Funktionen zum Zurücksetzen von Passworten
│   ├── clients/                    # Ansichten und Verarbeitungsdateien zur Kundenverwaltung
│   ├── css/                        # Stylesheets und UI-Komponenten der Anwendung
│   ├── docx/                       # Geschützte Dokumentenvorlagen für den Rechnungsexport
│   ├── download/                   # Download-Endpunkte für generierte Dateien
│   ├── edit_client/                # Formulare zur Bearbeitung von Kundendaten
│   ├── edit_project/               # Projektbearbeitung und Zuweisungsverwaltung
│   ├── edit_time/                  # Korrektur und Anpassung von Zeiterfassungseinträgen
│   ├── js/                         # JavaScript-Komponenten (AJAX, Validierung, UI-Helfer)
│   ├── log/                        # Audit-Logs und Überwachung der Systemaktivitäten
│   ├── login/                      # Authentifizierungs-Schnittstelle und Sitzungserstellung
│   ├── projects/                   # Projektübersicht, -planung und Datei-Uploads
│   │   └── uploads/                # Projektbezogene Dateianhänge
│   ├── register/                   # Benutzerregistrierung und Rechtevergabe
│   ├── settings/                   # Systemeinstellungen und Konfigurationsmasken
│   ├── users/                      # Benutzerverwaltung und Rollenzuweisung
│   ├── write_bill/                 # Rechnungsgenerierung und Abrechnungsvorbereitung
│   ├── add_client.php              # Erstellung neuer Kundendatensätze
│   ├── add_project.php             # Erstellung neuer Projekte
│   ├── add_time.php                # Erstellung neuer Zeiterfassungseinträge
│   ├── backup.php                  # Manuelle Ausführung von Datenbanksicherungen
│   ├── change_pw.php               # Verarbeitung von Passwortänderungen
│   ├── change_pw_admin.php         # Administrative Verarbeitung von Passwortänderungen
│   ├── delete_client.php           # Logik zum Löschen von Kunden
│   ├── delete_project.php          # Logik zum Löschen von Projekten
│   ├── delete_time.php             # Logik zum Löschen von Zeiterfassungseinträgen
│   ├── delete_user.php             # Logik zum Entfernen von Benutzern
│   ├── edit_amount.php             # Anpassung von Abrechnungsbeträgen
│   ├── edit_checklist.php          # Verwaltung von Projekt-Checklisten
│   ├── edit_client_process.php     # Verarbeitungslogik für Aktualisierungen von Kundendaten
│   ├── edit_order.php              # Aktualisierung von Auftrags- und Abrechnungsreihenfolgen
│   ├── edit_project_client.php     # Aktualisierung der Kundenzuweisung bei Projekten
│   ├── edit_project_process.php    # Verarbeitungslogik für Projektaktualisierungen
│   ├── edit_project_user.php       # Verwaltung der Benutzerzuweisungen bei Projekten
│   ├── edit_time.php               # Verarbeitungslogik für Zeiterfassungsaktualisierungen
│   ├── export.php                  # Funktionen zum Datenexport
│   ├── generate_word.php           # DOCX-Rechnungs- und Dokumentengenerierung
│   ├── get_amount.php              # Dynamischer Abruf von Abrechnungsbeträgen
│   ├── get_client.php              # AJAX-Endpunkt für Kundeninformationen
│   ├── mysql.php                   # Datenbankabstraktion und Helper-Funktionen
│   ├── session_loguot.php          # Automatisches Handling von Sitzungs-Timeouts
│   ├── settings.php                # Controller zur Speicherung von Systemeinstellungen
│   ├── setup.php                   # Webbasierte Installation und Initialisierung
│   ├── update_filter.php           # Aktualisierung der Dashboard-Filter
│   ├── update_permission.php       # Verwaltung von Benutzerberechtigungen
│   ├── update_project_status.php   # Statusaktualisierungen im Projekt-Workflow
│   ├── upload.php                  # Verarbeitung von Dateiuploads
│   ├── index.php                   # Haupt-Dashboard und primärer Einstiegspunkt der Anwendung
│   └── logout.php                  # Sichere Beendigung der Benutzersitzung
│
├── backups/                        # Speicherort für automatisierte Datenbanksicherungen
│   └── temp_restore/               # Temporärer Arbeitsbereich für Wiederherstellungen
│
├── libraries/                      # Eigene Helper-Bibliotheken und Hilfsprogramme
├── vendor/                         # Externe PHP-Abhängigkeiten (Composer)
├── README.md                       # Projektdokumentation und Installationsanleitung
├── backup_cron.php                 # Skript zur Automatisierung geplanter Sicherungen
├── config.php                      # Zentrale Umgebungs- und Datenbankkonfiguration
└── sql_backup.sql                  # Datenbank-Schema und Backup-Snapshot

```

---

## Hauptmerkmale

* **Kunden- & Projekt-Lebenszyklus-Management:** Dedizierte Module für das Onboarding von Kunden, die Verfolgung laufender Projektstati und die Evaluierung administrativer KPIs.
* **Granulare Zeiterfassung:** Integrierte Erfassung von Arbeitsstunden mit administrativen Werkzeugen zur Korrektur, Verifizierung und Compliance-Prüfung von Datensätzen.
* **Automatisierte Abrechnungs-Pipeline:** Aggregierte Datenvorschauen, die gebuchte Stunden und Kundensätze dynamisch zusammenführen, um umfassende Abrechnungsübersichten und Dokumentenexporte zu generieren.
* **Mehrbenutzerverwaltung (Multi-Tenant):** Sichere Login-Schnittstellen, Routen zur Benutzerregistrierung sowie fein abgestufte Berechtigungs- und Rollenzuweisungen.

---

## Sicherheits-Hardening & Architektonisches Refactoring

Die Kernlogik der Anwendung wurde in einer umfassenden Refactoring-Phase signifikant aufgewertet, um gängige Schwachstellen von Webanwendungen vollständig zu eliminieren:

* **Eliminierung von SQL-Injections (SQLi):** Umstellung von über 40 unverschlüsselten, string-interpolierten Abfragepfaden in kritischen Entitäten (Projekte, Kunden, Zeiterfassung und Upload-Felder) auf sichere, parametrisierte **PDO Prepared Statements**. Dies trennt Benutzereingaben strikt von der Ausführungslogik der Datenbank.
* **Prävention von Cross-Site Scripting (XSS):** Implementierung einer strikten, kontextsensitiven Ausgabebereinigung in mehr als 17 View-Templates über den nativen Maskierungs-Helper `h()`. Die sichere Injektion von Variablen in JavaScript-Kontexte wird durch dynamische JSON-Skript-Serialisierung (`pm_json_script()`) gewährleistet.
* **Architektonische Bereinigung & Modularisierung (DRY-Prinzip):** Eliminierung struktureller Redundanzen durch das Auslagern wiederkehrender Prüfungen (wie Rollenberechtigungen und Konfigurationen) in effiziente, zentralisierte Helper-Funktionen innerhalb der `mysql.php`.
* **Berechtigungstrennung (Privilege Separation):** Refactoring des Logging-Systems (`log.php`), um operative Bibliotheken von den Ausführungspfaden der Controller zu isolieren. Dies verhindert zuverlässig, dass nicht-privilegierte Sitzungen sensible API-Aufrufe auslösen, während administrative Schnittstellen durch strikte Abfragetabellen-Whitelists geschützt bleiben.
* **Stabilitäts- und Fehlerbehebungen:** Behebung logischer Validierungsfehler im Workflow zur Projekterstellung (`add_project.php`) und Korrektur der Pluralisierungs-Parsing-Routinen in den Dokumenten-Exportmodulen (`generate_word.php`).

---

## Konfiguration

### Datenbank- und Admin-Konfiguration (config.php)

Die Datei `config.php` verwaltet essenzielle Umgebungsvariablen für die Datenbankverbindung und die Authentifizierung des Hauptadministrators. Stellen Sie vor der Ausführung der Anwendung sicher, dass diese Variablen mit Ihrer lokalen Umgebung oder Ihrer Produktionsumgebung übereinstimmen:

```php
<?php
$host = "localhost";       // Hostname des Datenbankservers (z. B. localhost oder eine IP-Adresse)
$user = "root";            // Datenbank-Benutzername
$password = "";            // Datenbank-Passwort
$database = "PM_System";   // Name der relationalen Datenbank
$high_admin_pw = "admin";  // Passwort des Hauptadministrators für hoch-privilegierte Operationen
$high_admin_name = "admin";// Benutzername des Hauptadministrators
?>

```

### Dokumentenvorlagen (docx-Ordner)

Das System enthält ein `/docx`-Verzeichnis mit vorkonfigurierten Dateivorlagen für Exporte und Berichte.

* Diese Vorlagen müssen vor dem Deployment an Ihr spezifisches Corporate Design oder Ihre Berichtstandards angepasst werden.
* **Hinweis:** Während Sie diese Dateien manuell im Dateisystem bearbeiten können, bietet die Plattform eine integrierte Verwaltungsoption, mit der Administratoren diese Vorlagen direkt über die Weboberfläche modifizieren und aktualisieren können.

---

## Bereitstellung & Installationsanleitung

### 1. Allgemeine Systemvoraussetzungen

* **PHP-Laufzeitumgebung:** Version 7.0 oder höher
* **Webserver:** Apache HTTP Server (mit aktiviertem `mod_rewrite`)
* **Datenbank-Engine:** MySQL- oder MariaDB-Instanz

### 2. Lokale Einrichtung unter Windows (über XAMPP)

1. Öffnen Sie das **XAMPP Control Panel** und starten Sie sowohl das **Apache**- als auch das **MySQL**-Modul.
2. Kopieren Sie den Inhalt des Ordners `/htdocs` in Ihr lokales Stammverzeichnis: `C:\xampp\htdocs`.
3. Verschieben Sie die Verzeichnisse `/vendor` und `/docx` zusammen mit `backup_cron.php`, `config.php` und `setup.php` in denselben Stammordner (`C:\xampp\htdocs`).
4. Fahren Sie mit den Anweisungen für die automatische Ersteinrichtung fort.

### 3. Server-Bereitstellung unter Linux (Debian / Ubuntu)

1. Installieren Sie die grundlegenden Umgebungspakete über den Paketmanager Ihres Systems:

```bash
sudo apt update
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql

```

2. Kopieren Sie die Kern-Webverzeichnisse in das Dokumenten-Stammverzeichnis Ihres Servers:

* Verknüpfen Sie den Inhalt von `/htdocs` mit `/var/www/html`
* Verknüpfen Sie den Abhängigkeitsordner `/vendor` mit `/var/www/vendor`
* Stellen Sie sicher, dass `backup_cron.php`, `config.php` und `setup.php` innerhalb von `/var/www/html` liegen

3. Richten Sie die korrekten Systembesitzrechte und Dateiberechtigungen ein, damit der Benutzer-Thread des Webservers die Anwendung sauber ausführen kann:

```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

```

### 4. NAS-Bereitstellung auf einer Synology NAS

1. Öffnen Sie das Synology **Paket-Zentrum** und installieren Sie die **Web Station**, **PHP (v7.x oder höher)** sowie **MariaDB**.
2. Laden Sie die Dateien über die File Station oder per SFTP hoch:

* Legen Sie alle Dateien aus dem Verzeichnis `/htdocs` direkt im gemeinsamen Stammverzeichnis `/web` ab.
* Laden Sie den Ordner `/vendor` direkt nach `/web/vendor` hoch.
* Platzieren Sie `backup_cron.php`, `config.php` und `setup.php` im Stammverzeichnis `/web`.

---

## Automatische Ersteinrichtung

Die Umgebung initialisiert ihre relationalen Schematabellen und Konfigurationen nahtlos über einen integrierten grafischen Installationsassistenten:

1. Öffnen Sie Ihren Webbrowser und rufen Sie den Pfad des Setup-Skripts Ihrer Installation auf:

* **Lokale Umgebung:** `http://localhost/setup.php`
* **Server- / NAS-Instanz:** `http://<IHRE-SERVER-IP>/setup.php`

2. Folgen Sie den Anweisungen in der grafischen Benutzeroberfläche des Installers. Das System erstellt automatisch die relationalen Beschränkungen (Constraints), generiert die Tabellen und serialisiert Ihre aktiven Datenbank-Umgebungsvariablen in die `config.php`.
3. **KRITISCHER SICHERHEITSHINWEIS:** Sobald der Installer eine erfolgreiche Konfiguration der Umgebung meldet, **entfernen Sie das Installationsskript unverzüglich von Ihrem Server**, um unbefugte Reinitialisierungsversuche der Datenbank zu verhindern:

* **Windows-Umgebung:** Löschen Sie `setup.php` über den Datei-Explorer oder die Befehlszeile.
* **Linux-Umgebung:** Führen Sie `sudo rm /var/www/html/setup.php` aus.
