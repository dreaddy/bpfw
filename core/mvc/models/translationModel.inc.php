<?php
/*
 *
 * Copyright (c) 2017-2023. Torsten Lüders
 *
 * Part of the BPFW project. For documentation and support visit https://bpfw.org .
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 */


/** @noinspection PhpUnused */
require_once(BPFW_MVC_PATH . "bpfwModelFormField.inc.php");


class TranslationModel extends BpfwModel
{
    //var bool $translateAllFields = true;

    static bool $translationChecked = false;
    var bool $translateDbModelLabels = true;
    function __construct($values = null, $autocompleteVariables = true)
    {

        parent::__construct($values, $autocompleteVariables);

        $this->showdata = true;

        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_ADMIN;

        if (!translationModel::$translationChecked) {

            $entries = 0;
            $tables = bpfw_getDb()->getAllTables();
            if (!in_array("translation", $tables )) {
                return;
            } else {
                $entries = $this->CountAllEntries();
            }

            if (empty($entries)) {
                $this->initTranslation(true);
            } else if (null != (getorpost("reinit"))) {
                $this->initTranslation();
            }

            translationModel::$translationChecked=true;

        }

    }

    /**
     * @throws Exception
     */
    function initTranslation($firstinit = false)
    {

        if (!$firstinit && !bpfw_isAdmin() && !bpfw_creatingTables()) {
            die("initTranslation -> admin only".bpfw_creatingTables());
        }

        $db = bpfw_getDb();

        // $db->makeQuery("delete from translation where 1");

        // add german default translations

        bpfw_addBatchTranslationsToDatabase($firstinit, "de",
            array(
                "Generated pdf attachment" => "Generierter .pdf Anhang",
                "Select all" => "Alle auswählen",
                "Attachments of category" => "Anhänge der Kategorie",
                "Mail sent" => "Mail gesendet",
                "Mail failed" => "Mail senden fehlgeschlagen",
                "New" => "Neu",
                "New entry" => "Neuer Eintrag",
                "Stop cropping" => "Nicht mehr zuschneiden",
                "Select image section" => "Bild zuschneiden",
                "Print" => "Drucken",
                "Languages" => "Sprachen",
                "Domain" => "Domain",
                "Original Word" => "Wort in Originalsprache",
                "Translation" => "Übersetzung",
                "ID" => "ID",
                "Language" => "Sprache",
                "Close" => "Schließen",
                "Save" => "Speichern",
                "Add" => "Hinzufügen",
                "Search" => "Suchen",
                "Discard" => "Verwerfen",
                "Add entry" => "Eintrag hinzufügen",
                "Create entry" => "Eintrag erstellen",
                "Edit entry" => "Eintrag editieren",
                "Delete entry" => "Eintrag löschen",
                "Duplicate entry" => "Eintrag duplizieren",
                "Input must not be empty" => "Bitte dieses Feld ausfüllen",
                "Nothing found to restore" => "Nichts zum wiederherstellen gefunden",
                "Import a JSON File (.zip or .json) to start import." => "Importieren Sie eine JSON Datei (.json oder .zip), um die Wiederherstellung zu starten",
                "Import / Restore data" => "Daten importieren / wiederherstellen",
                "Export - Hint: Attachments will not be exported. Use 'Backup' to do that" => "Datenexport - Hinweis: Anhänge werden nicht exportiert. Hierzu den Punkt Sicherungen verwenden oder manuell herunterladen.",
                "Export all" => "Alle exportieren",
                "Create new backup" => "Neues Backup erstellen",
                "New partial backup (Database only)" => "Neues Teilbackup erstellen (nur Datenbank)",
                "Sync database <-> models" => "Datenbank <-> Model Abgleich",
                "This page is used to sync your Model.inc.php files with the database" => "Dieser Punkt synchronisiert die verwendeten Objekt-Modelle mit der Datenbank",
                "CREATING A BACKUP BEFORE EXECUTING CHANGES IS HIGHLY RECOMMENDED!" => "EIN BACKUP DER DATENBANK VOR ÄNDERUNGEN UNTER DIESEM MENÜPUNKT WIRD DRINGEND EMPFOHLEN!",
                "Click here to download a backup"=>"hier klicken, um ein Backup herunterzuladen",
                "Execute all sync processes" => "Alle notwendigen Syncvorgänge ausführen",
                "Create searchindex - tables with index"=>"Suchindex erstellen - Tabellen mit Suchindex",
                "Settings" => "Einstellungen",
                "User" => "User",
                "Import" => "Import",
                "Export" => "Export",
                "Backup" => "Backup",
                "DbSync" => "DbSync",
                "Searchindex" => "Suchindizes",
                "E-mail attachments" => "E-Mail Anhänge",
                "Cronjobs" => "Automatisierung",
                "Translations" => "&Uuml;bersetzungen",
                "Crudaction log" => "CRUD log",
                "Administrator" => "Administrator",
                "Date" => "Datum",
                "Backup directory" => "Backupverzeichnis",
                "Filesize" => "Dateigröße",
                "Type"=>"Typ",
                "EXCEL"=>"EXCEL",
                "CSV(formatted)"=>"CSV(formatiert)",
                "CSV(plain)"=>"CSV(plain)",
                "JSON"=>"JSON",
                "Table name"=>"Tabellen name",
                "export"=>"export",
                "Files"=>"Dateien",
                "Backup folder" => "Backup Verzeichnis",

                "Sender EMail"=>"Absender EMail",
                "Sender Name"=>"Absender Name",

                "BCC of all mails to" => "BCC aller Mails an",
                "Internal address for system mails" => "Interne Mailadresse für System-EMails",

                "Send mails with" => "Mails senden mit",

                "SMTP Host/Server" => "SMTP Host/Server",
                "SMTP Port" => "SMTP Port",

                "SMTP Auth" => "SMTP Auth",
                "SMTP Encryption" => "SMTP Verschlüsselung",

                "SMTP Username" => "SMTP Username",
                "SMTP Password" => "SMTP Passwort",

                "IMAP Mail address" => "IMAP Mailadresse",
                "IMAP Username" => "IMAP Username",
                "IMAP Port" => "IMAP Port",
                "IMAP Password" => "IMAP Passwort",
                "IMAP Folder" => "IMAP Ordner",
                "IMAP Encryption" => "IMAP Verschlüsselung",
                "IMAP Host/Server" => "IMAP Host/Server",

                "Default language" => "Standardsprache",
                "General settings" => "Allgemein",
                "Mailsettings - send" => "Maileinstellungen - senden",
                "Mailsettings - receive" => "Maileinstellungen - empfangen",
                "Invoicedata default" => "Rechnungsdaten Standard",
                "Design" => "Design",
                "Frequency" => "H&auml;ufigkeit",
                "Execute with JS calls, too"=>"Auch über JS Aufrufe ausführen",
                "Created by"=>"Angelegt von",
                "Last execution" => "Letzte Ausführung",
                "Log last run" => "Log letzte Ausführung",
                "Created (local)"=>"Erstellt am (local)",
                "Last edited (local)"=>"Letzte Änderung (local)",
                "Executed by userID"=>"Ausgeführt von userId",
                "Metadata"=>"Metadata",
                "Logtype"=>"Logtyp",
                "Keyname"=>"Keyname",
                "Model"=>"Model",
                "Row key"=>"Keywert der Zeile",

                "Send emails" => "E-Mail verschicken",
                "Send as email" => "als E-Mail Verschicken",
                "Name of pdf attachment" => "Name pdf Anhang",
                "Mail title" => "Titel der EMail",
                "mail text" => "Text der EMail",
                "Recipient" => "Empfänger",
                "Documents" => "Unterlagen",
                "Mail from" => "Mail vom",
                "Attachment" => "Anhang",
                "Send mail and save in outbox" => "Mail versenden und in Postausgang speichern",
                "Save mass mail for sending in outbox"=>"Serienmail für ausgewählte Empfänger im Postausgang zum Versand speichern",
                "Outbox" => "Postausgang",
                "Sent" => "Gesendet",
                "Deleted" => "Gelöscht",
                "Subject" => "Betreff",
                "Status" => "Status",
                "To" => "An",
                "Cc" => "Cc",
                "Bcc" => "Bcc",
                "Created" => "Erstellt",
                "Attachments" => "Anhänge",
                "Text" => "Text",
                "Box" => "Box",
                "Creator" => "Ersteller",
                "Error" => "Fehler",
                "Waiting" => "Ausstehend",
                "First try failed" => "1 Versuch fehlgeschlagen",
                "Failed permanently" => "Dauerhaft fehlgeschlagen",
                "New mail" => "Neue EMail",
                "Massmail" => "Massen-Email",
                "Mails" => "E-Mails",
                "Mails - outbox" => "Mails - Postausgang",
                "Mails - sent" => "Mails - Gesendet",
                "Mails - inbox"	=> "Mails - Posteingang",
                "Mails - deleted" => "Mails - Papierkorb",
                "Mails - unknown box" => "Mails - unbekannte Box",
                "save mail in outbox" => "Testmail in Postausgang speichern",
                "Moved to deleted" => "Mails in den Papierkorb verschoben",
                "Deleted mails truncated" => "Papierkorb gelöscht",
                "Send mails" => "EMail verschicken",
                "Send massmails" => "Serienbrief schicken",
                "activate all"=> "Alle aktivieren",
                "deactivate all" => "Alle deaktivieren",
                "User deactivated newsletter" => "User hat Newsletter deaktiviert",
                "Reset models" => "Models zurücksetzen",
                "DELETE ALL SELECTED TABLES IN DATABASE"=>"ALLE GEWÄHLTEN TABELLEN/MODELLE LÖSCHEN",
                "Table"=>"Tabellen name",
                "Reset tables" => "Zurücksetzen von Tabellen",
                "Mr"=>"Herr",
                "Mrs"=>"Frau",
                "Dear Sir or Madam" => "Sehr geehrte Damen und Herren",
                "Customer" => "Kunde",
                "Developer" => "Entwickler",
                "No login" => "Kein login",
                "Backups" => "Sicherungen",
                "Send Massmail" => "Serienbrief zustellen",
                "Mail" => "Email",
                "Name" => "Name",
                "Write massmail text here" => "hier den Inhalt des Serienbriefs schreiben",
                "Send 1 mail" => "1 Mail senden",
                "Send 10 mails" => "10 Mails senden",
                "Send 50 mails" => "50 Mails senden",
                "Delete failed mails" => "Fehlgeschlagene Mails löschen",
                "Delete sent mails" => "Gesendete Mails löschen",
                "Truncate deleted" => "Papierkorb leeren",
                "Category" => "Kategorie",
                "Headline in Backend" => "Bezeichnung im Backend",
                "Attachment name" => "Attachment name",
                "Activated per default"=>"Standardmässig aktiviert?",
                "File" => "Datei",
                "Receive massmails?" => "Rundmails erhalten?",
                "City" => "Ort",
                "Zip" => "PLZ",
                "Street" => "Straße",
                "Mobile phone" => "Mobil",
                "Telephone" => "Telefon",
                "Email" => "E-mail",
                "First name" => "Vorname",
                "Last name" => "Nachname",
                "Salutation" => "Anrede",
                "Company" => "Firma",
                "Password" => "Passwort",
                "Login" => "Login",
                "User rank" => "Benutzer Rolle",
                "(None)" => "(Keine)",
                "Error: Page {{{pagename}}} not existing" => "Fehler: Seite {{{pagename}}} existiert nicht",
                "Alle 5 Minuten" => "Alle 5 Minuten",
                "Every 15 Minuten" => "Alle 15 Minuten",
                "Hourly" => "Stündlich",
                "Every 6 hours" => "Alle 6 Stunden",
                "Every 12 hours" => "Alle 12 Stunden",
                "Daily" => "Täglich",
                "Weekly" => "Wöchentlich",
                "Monthly" => "Monatlich",
                "Backup(Database only)" => "Backup(nur Datenbank)",
                "Backup(Complete)" => "Backup(Vollständig)",
                "Delete backups older than 1 month" => "Backups löschen, die älter als einen Monat sind",
                "Delete database" => "Tempfiles löschen",
                "Clean database" => "Datenbank bereinigen",
                "Send 10 mails from outbox" => "10 ausstehende E-Mails abschicken",
                "Send 50 mails from outbox" => "50 ausstehende E-Mails abschicken",
                "Send 100 mails from outbox" => "100 ausstehende E-Mails abschicken",
                "Db Entry Complete" => "Datenbankeintrag ok",
                "Backup Complete" => "Backup abgeschlossen",
                "To execute with an external Cronjob, call this url" => "Zum Ausführen über einen unabhängigen Cronjob (etwa über den Hoster), diese URL verwenden",
                "Usually cronjob execution is done, when a page is loaded and the frequency check says so." => "Standardmäßig wird der Cronjob beim laden einer Seite geprüft und ggf. ausgeführt.",
                "Username or Email" => "Benutzername oder E-Mail",
                "Please login to continue" => "Bitte einloggen, um fortzufahren",
                "Welcome back!" => "Willkommen zurück!",
                "Required input field not set" => "Pflichtfeld nicht gesetzt",
                "Invalid login" => "Ungültige Logindaten",
                "Open pdf file in browser" => "Als PDF im Browser öffnen",
                "From" => "Von",
                "External Cronjob Auth Key" => "Cronjob für externen Aufruf",
                "DB folder" => "DB Ordner",
                "Path files" => "Pfad zu Dateien",
                "Open pdf file in Browser" => "Pdf im Browser öffnen",
                "Logout" => "Ausloggen",
                "Profile" => "Profil",
                "Edit" => "Editieren",
                "Duplicate" => "Duplizieren",
                "Delete" => "Löschen",
                "Required input field not set2" => "Nicht gesetzt - 2",
                "Required field not set" => "Benötigtes Feld nicht gesetzt",
                "Every 5 minutes" => "Alle 5 Minuten",
                "Every 15 minutes" => "Alle 15 Minuten",
                "Send 50 mail" => "50 EMails senden",
                "Send 10 mail" => "10 EMails senden",
                "Sprache" => "Language",
                "datalog" => "datalog",
                "Invalid email" => "Email nicht gültig",
                "Logged in as" => "Eingeloggt als",
                " is still using it with the id " => " verwendet den Eintrag noch unter ID ",
                "Can't be deleted, " => "Kann nicht gelöscht werden, ",
                "Can't be deleted: error on FK_CONSTRAINT_NULL. Not all values could be deleted." => "Kann nicht gelöscht werden, Fehler bei FK_CONTRAINT_NULL: Es konnten nicht alle Werte gelöscht werden",
                "Confirm deletion?"=>"Wirklich löschen?",

                "Replace contents(delete all old data)"=>"Inhalte ersetzen(alte Daten löschen, dann backup einfügen)",
                "Merge contents(replace old data if it exists in backup)"=>"Zusammenführen(alte Daten überschreiben wenn neue Version vorhanden)",
                "Complete contents(only add new data row doesnt exist in old data)"=>"Vervollständigen(nur einfügen, wenn noch nicht vorhanden)",
                "Ignore backup for this table"=>"Backup für diese Tabelle ignorieren",
                "Amount"=>"Anzahl",
                "found"=>"gefunden",
                "Backup not found"=>"Backup nicht gefunden",


                "Full backup" => "Vollständiges Backup",
                "Database only" => "Nur Datenbank",

                "Restore DB" => "DB wiederherstellen",
                "Download uploads" => "Uploads downloaden",
                "Download DB" => "DB download",

                "Manage attachments" => "Anhänge verwalten",
                "type" => "Typ",

                "select this table for reset" => "Diese Tabelle zurücksetzen"
                
                )
        );


    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "translation";
    }

    public function GetTitle(): string
    {
        return __("Translations");
    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     */

    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("translationId");

            $this->addComboBox("languageId", "Language", new EnumHandlerCallback("bpfw_getLanguagesArray"), BpfwDbFieldType::TYPE_STRING, array(FORMSETTING::POSITION => POSITION_LEFT, LISTSETTING::HIDDENONLIST => false));

            $this->addTextField("word", "Original Word", "512", array(FORMSETTING::POSITION=>POSITION_RIGHT));
            $this->addTextField("translation", "Translation", "512", array(FORMSETTING::POSITION=>POSITION_RIGHT));

            $this->addTextField("domain", "Domain", "256", array());

        }

        return $this->dbModel;


    }


}
