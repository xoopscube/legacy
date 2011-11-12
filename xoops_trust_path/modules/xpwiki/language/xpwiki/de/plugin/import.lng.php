<?php
/*
 * Created on 2007/05/28 by nao-pon http://hypweb.net/
 * $Id: import.lng.php,v 1.1 2008/04/20 01:46:10 nao-pon Exp $
 * Thanks bokanta :-D
 */
//
// German Translation Version 1.0 (11.03.2008)
// Translation English --> German: Octopus (hunter0815@googlemail.com)
// Da die Funktio in Xoops irgendwie nicht zu Laufen scheint habe ich die Übersetzung unten weggelassen.
// sicherlich steckt hier noch reichlich Qualitätspotential in den Übersetzungen ;-)

$msg = array(
    'title_import_dir' => 'Selektion die importiert werden soll.',
    'import_dir' => 'Wähle das Modul (z. B. Ordner Name) das importiert werden soll.',
    'target_page' => 'Ausgewählte Seiten',
    'target_page_sel' => 'von $from',
    'target_page_all' => 'Alle Seiten von $from',
    'target_page_note' => 'trennen mit \'&\' für Mehrfachauswahl (Hinweis: Alle Seiten in Unterverzeichnissen sind inbegriffen).<br />Alle Seiten außer der vorgegebenen werden importiert.',
    'title_select_option' => 'Selektion der Import-Optionen',
    'target_module' => 'Das Modul das importiert werden soll: ',
    'keep_pgid' => 'Seiten ID',
    'keep_pgid_1' => 'Behalte die Seiten ID von $from',
    'keep_pgid_1_note' => '(Alle existierenden Seiten werden gelöscht.)',
    'keep_pgid_2' => 'Behalte die Seiten ID von $to',
    'keep_pgid_2_note' => '(Spezifiziere die Optionen unten.)',
    'keep_page' => 'Behalte den original Seiten Namen',
    'keep_page_1' => 'Überschreibe $to mit $from',
    'keep_page_2' => 'Behalte die Seiten von $to (kein Import)',
    'invalid_option' => 'ungültige Option(en)',
    'title_do_import' => 'Letzte Bestätigung bevor der Import ausgeführt wird.',
    'title_no_files' => 'Die zu importierenden Seiten existieren nicht.',
    'title_do_check' => 'Überprüfe die zu importierenden Inhalte',
    'do_check_note' => 'Die zu importierenden Dateien werden im nächsten Schritt überprüft. Dies wird etwas Zeit brauchen.<br />Bitte warte nach dem Klicken [Überprüfung der zu importierenden Dateien].',
    'btn_do_next' => 'Nächster Schritt',
    'btn_go_first' => 'Zurück zum Start',
    'btn_do_check' => 'Überprüfe die zu importierenden Dateien',
    'btn_do_copy' => 'Fürhe den Import aus (Kopiere die ausgewählten Dateien)',
    'do_copy_note' => 'Die oben aufgeführten Dateien werden kopiert von $from nach $to. Im Falle eines PHP-Ausführungs-Timeouts aufgrund der Anzahl oder Größe der Dateien, erscheint ein Dialog zum Fortführen. Bitte bestätige diesen.',
    'do_copy_nothing' => 'Die zu importierenden Dateien existieren nicht.',
    'title_convert' => 'Wiki Format Konvertierung',
    'do_convert' => 'Konverierung ins Wiki Format',
    'do_convert_note' => 'Das PukiWiki 1.3 Format wird in das PukiWiki 1.4 Format konvertiert.',
    'do_convert_wiki'     => "
** Inhalte ausführen
:Änderung der Formatierung der Definitions-Liste|
'': :'' wurde geändert in '': |''.
:Trenne verschachtelte Block Elemente|
Füge eine leere Linie nach den verchachtelten Block Elementen ein um zu verhindern, dass die folgenden Elemente die Kinder-Elemente (child) werden
:Tilde \"~\" am Ende der Elemente Liste|
Wenn eine Tilde \"~\" am Ende von \"-/+\" erscheint, wird zu Beginn einer Zeile
eine Leerstelle eingefügt um zu verhindern, dass die Tilde als Formatierung für eine neue Zeile genutzt wird.
:Konvertiere Plugin s.|
--&#35;category() -> &#38;tag();
--&#35;attacheref( -> &#35;ref(
--&#38;attacheref( -> &#38;ref(
:Konvertiere die Seiteninhalte|
Die Seiteninhalte von PukiWikiMod werden in xpWiki-formatierte Inhalte formatiert.
 
** Hinweis
Die Konvertierung kann einige Minuten in Anspruch nehmen.  Bitte habe ein wenig Geduld nach dem Ausführen des Buttons.
 
Falls ein PHP-Ausführungs time-out auftritt, bestätige den Dialog zum Fortfahren.
 
** Ausführen der Konvertierung
Bitte drücke den [Konvertiere das Wiki Format] Button.
",
    'msg_all_done' => 'Import wurde erfolgreich beendet. Bitte führe die Datenbank-Synchronisation aus.',
    'msg_exec'    => "* Datei Namen wurden übergrüft.\n Es wurden keine Fehler gefunden.\n\n Bitte klicke [[Execute>%s]] zur Weiterführung der Konvertierung\n",
    'msg_error'   => "* Datei Namen wurden überprüft.\n Es wurden Fehler gefunden und die Konvertierung wurde abgebrochen. \n Bitte führe die Konvertierung nach Fehlerbehebung erneut aus.\n",
    'msg_done'    => '* Die Datei-Konvertierung it abgeschlossen.',
    'err_writable' => '** Weder Dateien noch Verzeichnisse wurden gefunden und/oder sind nicht beschreibbar.',
    'err_already' => '** Es existiert bereits eine Datei mit demselben Namen.',
    'err_invalid' => '** Die Seite ist nicht für PukiWiki 1.4. zugelassen',
    'err_no_from_dir' => 'Das zu importierende Verzeichnis wurde nicht gefunden.',
    'err_no_to_dir' => 'Das zu exportierende Verzeichnis wurde nicht gefunden.',
    'err_writable_to' => 'Das zu exportierende Verzeichnis ist nicht beschreibbar.',
    'more_copy_note' => 'Der Prozess wurde gestoppt aufgrund eines time-out Fehlers während des Datei-Kopier Vorgangs.<br />Bitte drücke [Fortfahren], und ',
    'more_convert_note' => 'Der Prozess wurde gestoppt aufgrund eines time-out Fehlers während der Format-Konvertierung.<br />[Bitte drücke [Fortfahren], und ',
    'title_do_more' => 'der Rest des Prozesses wird fortgesetzt.',
    'do_more' => 'Verbleibende $count werden konvertiert',
    'btn_do_more' => 'Fortfahren',
); 
?>
