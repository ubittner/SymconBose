## Bose SoundTouch Discovery

![Logo](../imgs/bose_logo_white.png)

Dieses Modul listet die im Netzwerk bereits vorhandenen [Bose SoundTouch](https://www.bose.de/) Geräte in [IP-Symcon](https://www.symcon.de) auf und der Nutzer kann die Geräte automatisch anlegen lassen.  

Für dieses Modul besteht kein Anspruch auf Fehlerfreiheit, Weiterentwicklung, sonstige Unterstützung oder Support.
Bevor dieses Modul installiert wird, sollte unbedingt ein Backup von IP-Symcon durchgeführt werden.
Der Entwickler haftet nicht für eventuell auftretende Datenverluste oder sonstige Schäden.
Der Nutzer stimmt den o.a. Bedingungen, sowie den Lizenzbedingungen ausdrücklich zu.

### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Listet die im Netzwerk vorhandenen Geräte auf
* Automatisches Anlegen der ausgewählten Geräte  

### 2. Voraussetzungen

- IP-Symcon ab Version 5.1
- Bose SoundTouch Gerät

### 3. Software-Installation

- Bei kommerzieller Nutzung (z.B. als Einrichter oder Integrator) wenden Sie sich bitte zunächst an den Autor.
  
- Bei privater Nutzung wird das Modul über den Modul Store installiert.

### 4. Einrichten der Instanzen in IP-Symcon

- In IP-Symcon an beliebiger Stelle `Instanz hinzufügen` auswählen und `Bose SoundTouch Discovery` auswählen, welches unter dem Hersteller `Bose` aufgeführt ist. Es wird eine Bose SoundTouch Discovery Instanz unter der Kategorie `Discovery Instanzen` angelegt.  

__Konfigurationsseite__:

Name                    | Beschreibung
----------------------- | ---------------------------------
Bose SoundTouch Geräte  | Liste die vorhandenen Geräte auf

__Schaltflächen__:

Name            | Beschreibung
--------------- | ---------------------------------
Alle erstellen  | Erstellt für alle aufgelisteten Geräte jeweils eine Instanz
Erstellen       | Erstellt für das ausgewählte Gerät eine Instanz        

__Vorgehensweise__:

Über die Schaltfläche `AKTUALISIEREN` können Sie die Liste der verfügbaren Geräte jederzeit aktualisieren.  
Wählen Sie `ALLE ERSTELLEN` oder wählen Sie ein Gerät aus der Liste aus und drücken Sie dann die Schaltfläche `ERSTELLEN`, um das/die Gerät(e) automatisch anzulegen.  

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

##### Statusvariablen

Es werden keine Statusvariablen angelegt.

##### Profile:

Nachfolgende Profile werden zusätzlichen hinzugefügt:

Es werden keine Profile angelegt.

### 6. WebFront

Die Bose SoundTouch Discovery Instanz ist im WebFront nicht nutzbar.  

### 7. PHP-Befehlsreferenz

Eine PHP-Befehlsreferenz ist nich verfügbar.