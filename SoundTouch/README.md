## SoundTouch

[![Version](https://img.shields.io/badge/Symcon_Version-5.1>-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
![Version](https://img.shields.io/badge/Modul_Version-2.01-blue.svg)
![Version](https://img.shields.io/badge/Modul_Build-2001-blue.svg)
![Version](https://img.shields.io/badge/Code-PHP-blue.svg)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![StyleCI](https://github.styleci.io/repos/183038756/shield?branch=master&style=flat)](https://github.styleci.io/repos/183038756)

![Logo](../imgs/bose_logo_white.png)

Dieses Modul integriert [Bose SoundTouch](https://www.bose.de/) Lautsprecher in [IP-Symcon](https://www.symcon.de). 

Für dieses Modul besteht kein Anspruch auf Fehlerfreiheit, Weiterentwicklung, sonstige Unterstützung oder Support.

Bevor das Modul installiert wird, sollte unbedingt ein Backup von IP-Symcon durchgeführt werden.

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
8. [GUIDs](#8-guids)
9. [Changelog](#9-changelog)

### 1. Funktionsumfang

* Ein- / Ausschalten des Lautsprechers
* Lautstärke verändern
* Basseinstellung verändern 
* Titel zurück
* Pause
* Wiedergabe
* Audioquelle ausblenden
* Titel vor
* Zonensteuerung
* Auswahl von Audioquellen
* Anzeige von Medieninformationen (Quelle, Künstler, Titel, Album)
* Schaltuhr (Timer)
* Einschaltautomatik
* Ausschaltautomatik
* Audio Benachrichtigungen (Audio notifications)
   
### 2. Voraussetzungen

- IP-Symcon ab Version 5.1
- Bose SoundTouch System

### 3. Software-Installation

- Bei kommerzieller Nutzung (z.B. als Einrichter oder Integrator) wenden Sie sich bitte zunächst an den Autor.
  
- Bei privater Nutzung wird das Modul über den Modul Store installiert.

### 4. Einrichten der Instanzen in IP-Symcon

- In IP-Symcon an beliebiger Stelle `Instanz hinzufügen` auswählen und `Bose SoundTouch` auswählen, welches unter dem Hersteller `Bose` aufgeführt ist. Es wird eine Bose SoundTouch Instanz angelegt, in der die Eigenschaften zur Steuerung des Systems gesetzt werden können.

__Konfigurationsseite__:

Name                            | Beschreibung
------------------------------- | -----------------------------------------------------------------------------
(1) Gerätedaten                 | 
Gerätebezeichnung               | Optionale Gerätebeschreibung, z.B.  SoundTouch 10 Wohnzimmer.
IP-Adresse                      | IP-Adresse des Gerätes.
Timeout                         | Netzwerktimeout, sollte nur bei Problemen verändert werden.
Aktualisierungsintervall        | Geräteinformationen werden vom Gerät ausgelesen und im Modul aktualisiert.
(2) Weitere Audioquellen        |  
Position                        | Positionsnummer, darf nur einmal vorhanden sein und sollte mit 1 beginnen.
Musikdienst                     | Musikdienst, der für die Wiedergabe verwendet werden soll.
Bezeichnung                     | Bezeichnung der Quelle, z.B. WDR2.
Quellenangaben                  | Angaben zur Quelle.
Benutzerkonto                   | Bei bestimmten Musikdiensten ist die Angabe eines Benutzerkontos erforderlich.
Lautstärke                      | Lautstärke mit der die Quelle initial wiedergegeben werden soll.
(3) Visualisierung              |
Zonensteuerung                  | Schaltet die Sichtbarkeit der Zonensteuerung im WebFront ein/aus.
Audioquellen                    | Schaltet die Sichtbarkeit der Audioquellen im WebFront ein/aus.
Medieninformationen             | Schaltet die Sichtbarkeit der Medieninformationen im WebFront ein/aus.
Schaltuhr                       | Schaltet die Sichtbarkeit der Schaltuhr im WebFront ein/aus.
Einschaltautomatik              | Schaltet die Sichtbarkeit der Einschaltautomatik im WebFront ein/aus.
Ausschaltautomatik              | Schaltet die Sichtbarkeit der Ausschaltautomatik im WebFront ein/aus.
(4) Mediendatei veröffentlichen |
Mediendatei                     | Wählen Sie die Mediendatei die sich unter Medien Dateien befindet aus.
(5) Audiobenachrichtigungen     |
App-Schlüssel                   | Hier können Sie Ihren eigenen App-Schlüssel (Bose Entwickler Konto erforderlich) angeben.
Servicebezeichnung              | Servicebezeichnung, bzw. Künstlerangabe.
Details zur Benachrichtigung    | Details zur Benachrichtigung, bzw. Albuminformation.
Position                        | Positionsnummer, darf nur einmal vorhanden sein und sollte mit 1 beginnen.
Bezeichnung                     | Bezeichnung der Audiobenachrichtigung, z.B. Glocke
URL                             | URL Pfad zur Audiodatei.
Lautstärke                      | Lautstärke mit der die Audiobenachrichtigung initial wiedergegeben werden soll.

__Schaltfläche__:

Bezeichnung                     | Beschreibung
------------------------------- | -------------------------------------------
Anleitung auf GitHub            | Ruft die Dokumentation auf GitHub auf.
Mediendatei veröffentlichen     | Veröffentlich die unter Punkt (4) ausgewählte Datei auf diesem Server.
Geräteinformationen anzeigen    | Zeigt Informationen über den Lautsprecher an.
Wiedergabeinformationen azeigen | Zeigt Informationen zur aktuellen Wiedergabe an.

__Vorgehensweise__:

Sofern Sie mehr als einen Bose SoundTouch Lautsprecher nutzen wollen, so wiederholen Sie den Installationsvorgang. 
Jeder Bose SoundTouch Lautsprecher ist eine eigene Instanz.

__Weitere Audioquellen__:

In der Instanzkonfiguration können Sie unter Punkt `2 Weitere Audioquellen` angeben.  
Um Audioquellen hinzuzufügen, spielen Sie zunächst die gewünschte Audioquelle mit der Bose SoundTouch App ab.  
Im Instanzkonfigurator wählen Sie die Schaltfläche `WIEDERGABEINFORMATIONEN ANZEIGEN` und entnehmen die entsprechenden Werte der Anzeige.  
Die Informationen werden auch unter Meldungen (Log) gespeichert / angezeigt.

__Audiobenachrichtigungen__:

Mit Version 2.00 besteht die Möglichkeit Audiobenachrichtigungen auf dem Bose SoundTouch Gerät auszugeben.  
Nachfolgende Konfiguration ist vorzunehemen, wenn die Audioquelle sich auf dem IP-Symcom Server befinden soll.  
Bitte beachten Sie, dass nicht alle Bose SoundTouch Systeme (ältere Modelle) Audiobenachrichtigungen wiedergeben können.  
Fügen Sie zunächst die Audiodatei zu den Medien Dateien hinzu.  
Nachdem Sie die Audio Datei hinzugefügt haben erscheint diese unter Medien Dateien.  
Wählen Sie nun in der Instanzkonfiguration den Punkt `4 Mediendatei veröffentlichen` aus.  
Wählen Sie die Mediendatei aus, die sich in Ihren Mediendateien befindet.  
Bestätigen Sie die Änderungen über die Schaltfläche `ÄNDERUNGEN ÜBERNEHMEN`.  
Wählen Sie unterhalb der Instanzkonfiguration die Schaltfläche `MEDIENDATEI VERÖFFENTLICHEN`.  
Es wird ein Hardlink auf dem IP-Symcon Webserver erstellt und ist unter http://IP-Adresse:3777/user/media/ erreichbar.

Beispiel: `http://192.168.1.2:3777/user/media/Glocken.mp3`

Nun können Sie in der Instanzkofiguration unter Punkt `5 Audiobenachrichtigungen` die Audiodatei nebst URL eintragen.  
Wenn Sie eine externe Audiodatei wiedergeben wollen, so geben Sie die externe URL an.  
Vergeben Sie eine eindeutige Positionsnummer und starten Sie Ihren ersten Eintrag mit der Positionsnummer 1.  
Unter Lautstärke können Sie die Wiedergabelautstärke vorgeben.  
Im Webfront finden Sie dann einen Eintrag unter den Audioquellen.  
Wenn Sie die Audiobenachrichtigung in einem Script wieder geben wollen, so nutzen Sie nachfolgenden Befehl mit der Positionsnummer, die Sie wiedergeben möchten.

`BST_PlayAudioNotificationFromList(integer InstanzID, integer Positionsnummer);`

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

##### Statusvariablen

Variablenname           | Beschreibung
------------------------|-------------------------------
Ein / Aus               | Schaltet den Lautsprecher ein / aus.
Lautstärke              | Verändert die Lautstärke.
Bass                    | Verändert den Bass.
Wiedergabe / Pause      | Wiedergabemöglichkeiten.
Zonensteuerung          | Fügt einen Lautsprecher zur Zone hinzu.
Audioquellen            | Verfügbare Audioquellen zur Wiedergabe.
Medieninformationen     | Informationen zur aktuellen Wiedergabe, Künstler, Titel, Album.
Schaltuhr (Stunde)      | Stundenangabe der Schaltuhr.
Schaltuhr (Minute)      | Minutenangaben der Schaltuhr.
Auswahl                 | Auswahl der Audioquelle für die Schaltuhrfunktionen.
Lautstärke              | Laustärke mit der die Audioquelle bei Schaltuhrfunktionen initital wiedergegeben wird.
Einschaltautomatik      | Schaltet die Einschaltautomatik ein / aus.
Lautstärke einnlenden   | Blendet die Lautstärke bei der Einschaltautomatik ein.
Nächste Einschaltzeit   | Zeigt die nächste Einschaltzeit an.
Nächste Audioquelle     | Zeigt die Audioquelle an die bei der nächsten Einschaltung wiedergegeben wird.
Ausschaltautomatik      | Schaltet die Ausschaltautomatik ein / aus.
Lautstärke ausblenden   | Blendet die Lautstärke bei der Ausschaltautomatik aus.
Nachtmodus              | Schaltet den Lautsprecher in 30 Minuten aus und blendet die Lautstärke aus.
Nächste Ausschaltzeit   | Zeigt die nächste Ausschaltzeit an.

##### Profile:

Nachfolgende Profile werden zusätzlichen hinzugefügt:

* BST.InstanzID.VolumeSlider

* BST.InstanzID.BassSlider

* BST.InstanzID.PlayMode

* BST.InstanzID.ZoneDevices

* BST.InstanzID.AudioSources

* BST.InstanzID.AudioLocations

* BST.InstanzID.TimerHour

* BST.InstanzID.TimerMinute

* BST.InstanzID.TimerSources
 
Wird eine Bose SoundTouch Instanz gelöscht, so werden automatisch die zugehörigen Profile gelöscht.
 
### 6. WebFront

Die Instanz wird im WebFront angezeigt. Sie können die vorhandenen Funktionen nutzen.

### 7. PHP-Befehlsreferenz

Präfix des Moduls `BST` (Bose SoundTouch)

`BST_GetDataFromDevice(integer $InstanceID, string $Endpoint)`

Holt Daten / Informationen von dem angegeben Endpunkt vom Gerät ab.

`BST_GetDeviceInfo(integer $InstanceID)`

Ruft grundlegende Informationen über das Gerät ab.

`BST_GetDeviceNowPlaying(integer $InstanceID)`

Ruft Informationen über die aktuelle Wiedergabe des Gerät ab.

`BST_GetDeviceVolume(integer $InstanceID)`

Ruft die aktuelle Lautstärke des Gerät ab.

`BST_GetDeviceSources(integer $InstanceID)`

Ruft die verfügbaren Quellen des Gerät ab.

`BST_GetDeviceZone(integer $InstanceID)`

Ruft den aktuellen Multiroom-Zonenstatus des Gerätes ab.

`BST_GetDeviceBassCapabilities(integer $InstanceID)`

Prüft ob Bass Einstellungen für das Gerät möglich sind.

`BST_GetDeviceBass(integer $InstanceID)`

Ruft den aktuellen Bass Level des Gerätes ab.

`BST_GetDevicePresets(integer $InstanceID)`

Ruft die aktuellen Presets 1 - 6 des Gerätes ab.

`BST_GetDeviceGroup(integer $InstanceID)`

Ruft die aktuelle linke/rechte Stereo-Paar-Konfiguration eines Gerätes ab.

`BST_ SendDataToDevice(integer $InstanceID, string $Endpoint, string $Postfields)`

Sendet spezifische Daten an einen Endpunkt des Gerätes. 

`BST_PowerDevice(integer $InstanceID)`

Schaltet das Gerät an, wenn das Gerät aus ist und umgekehrt.

`BST_SelectDevicePreset(integer $InstanceID, integer $Preset)`

Spielt das angegebene Preset (1 - 6) des Gerätes ab.

`BST_ToggleDevicePlayPause(integer $InstanceID)`

Startet, bzw. pausiert die Wiedergabe des Gerätes.

`BST_ToggleDeviceMute(integer $InstanceID)`

Schaltet das Gerät stumm.

`BST_SelectDevicePreviousTrack(integer $InstanceID)`

Wird eine Wiedergabeliste verwendet, so kann der vorherige Titel abgespielt werden.

`BST_SelectDeviceNextTrack(integer $InstanceID)`

Wird eine Wiedergabeliste verwendet, so kann der nächste Titel abgespielt werden.

`BST_SetDeviceVolume(integer $InstanceID, integer $Volume)`

Setzt die angegeben Lautstärke.

`BST_SelectDeviceSource(integer InstanceID, string $Source, string $SourceAccount)`

Wählt die verfügbaren Audioquellen aus.

`BST_SelectDeviceAUXMode(integer InstanceID)`

Schaltet das Gerät in den Aux Modus.

`BST_SelectDeviceBluetoothMode(integer InstanceID)`

Schaltet das Gerät in den Bluetooth Modus.

`BST_SelectDeviceHDMIMode(integer InstanceID, integer $SourceNumber)`

Schaltet das Gerät in den angegebenen HDMI Modus, sofern verfügbar.

`BST_SelectDeviceTVMode(integer InstanceID)`

Schaltet das Gerät in den TV Modus, sofern verfügbar.

`BST_SelectDeviceLocation(integer InstanceID, string $Source, string $Type, string $Location, string $SourceAccount)`

Wählt einen Radio-Modus aus.

`BST_SetDeviceZone(integer $InstanceID, string $MasterID, string $MemberIP, string $MemberID)`

Erstellt eine Multiroom-Zone.

`BST_AddDeviceZoneSlave(integer $InstanceID, string $MasterID, string $MemberIP, string $MemberID)`

Fügt ein weiteres Gerät zu einer bereits vorhandenen Multiroom-Zone hinzu.

`BST_RemoveDeviceZoneSlave(integer $InstanceID, string $MasterID, string $MemberIP, string $MemberID)`

Entfernt ein Gerät aus einer Multiroom-Zone.

`BST_SetDeviceBass(integer $InstanceID, integer $Level)`

Setzt den Bass Level eines Gerätes, sofern verfügbar.

`BST_SetDeviceName(integer $InstanceID, string $Name)`

Setzt den Namen des Gerätes.

`BST_PlayAudioNotification(integer $InstanceID, string $Reason, string $URL, integer $Volume)`

Spielt eine Audiobenachrichtigung ab.

`BST_PlayAudioNotificationFromList(integer $InstanceID, integer $Position)`

Spielt die angegebene Audiobenachrichtigung aus der Liste des Instanzeditors ab.

`BST_UpdateInformation(integer $InstanceID)`

Aktuaisiert die Informationen im Modul.

`BST_FadeOutPlayback(integer $InstanceID)`

Blendet die Lautstärke aus, jede Sekunde um den Wert -1.

`BST_ExecuteAutomaticPowerOn(integer $InstanceID)`

Wird von Einschalt-Timer verwendet und schaltet das Gerät zum vorgegebenen Zeitpunkt ein.

`BST_ExecuteAutomaticPowerOff(integer $InstanceID)`

Wird von Ausschalt-Timer verwendet und schaltet das Gerät zum vorgegebenen Zeitpunkt aus.

`BST_PublishMediaFile(integer $InstanceID)`

Veröffentlicht die ausgewählte Mediendatei.


### 8. GUIDs

__Modul GUIDs__:

 Name       | GUID                                   | Bezeichnung  |
------------| -------------------------------------- | -------------|
Bibliothek  | {F5AAB293-F714-4FD1-ADF9-8F30B22201B7} | Library GUID |
Modul       | {4836EF46-FF79-4D6A-91C9-FE54F1BDF2DB} | Module GUID  |

### 9. Changelog

Version     | Datum      | Beschreibung
----------- | -----------| -------------------
2.01-2001   | 23.04.2019 | Version für Module-Store
2.00        | 19.09.2018 | Version 2.00 für IP-Symcon 5.0 
