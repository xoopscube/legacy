<?php
/*
 * Created on 2008/01/07 by nao-pon http://hypweb.net/
 * $Id: fusen.lng.php,v 1.1 2008/04/20 01:46:10 nao-pon Exp $
 */
//
// German Translation Version 1.0 (11.03.2008)
// Translation English --> German: Octopus (hunter0815@googlemail.com)
// sicherlich steckt hier noch reichlich Qualitätspotential in den Übersetzungen ;-)

$msg = array(
	'cap_refresh' => 'Aktualisieren',
	'cap_none' => 'kein',
	'cap_second' => ' Sek',
	'cap_dustbox_empty' => 'Leere den Mülleimer (dust box)',
	'cap_empty' => 'leeren',
	'cap_fusen_menu' => 'Fusen(Tag) Menu',
	'cap_fusen_func' => 'Fusen(Tag)',
	'cap_menu_new' => 'Erstelle einen neuen tag',
	'btn_menu_new' => 'Neu',
	'cap_menu_dust' => 'zum Mülleimer umstellen',
	'btn_menu_dust' => 'Müll',
	'cap_menu_transparent' => 'transparent einstellen',
	'btn_menu_transparent' => 'Trns.',
	'cap_menu_refresh' => 'Aktualisieren',
	'btn_menu_refresh' => 'Ref.',
	'cap_menu_list' => 'Tag Liste auf dieser Seite',
	'btn_menu_list' => 'Liste',
	'cap_menu_help' => 'Wie wird das genutzt',
	'btn_menu_help' => 'Hilfe',
	'cap_menu_search' => 'Suche',
	'msg_not_work' => '<strong>JavaScript unoperation</strong>: Tag kann nicht bearbeitet werden. Und die Position an der das tag angezeigt wird könnte verschoben werden.',
	'msg_show_fusen' => 'Zeige Tag von "$1".',
	'cap_fusen_edit' => 'Tag Editor',
	'cap_fore_color' => 'Farbe',
	'cap_black' => 'Schwarz',
	'cap_gray' => 'Grau',
	'cap_red' => 'Rot',
	'cap_green' => 'Grün',
	'cap_blue' => 'Blau',
	'cap_white' => 'Weiss',
	'cap_back_color' => 'BG (schwarz)',
	'cap_lightred' => 'hellrot',
	'cap_lightgreen' => 'hellgrün',
	'cap_lightblue' => 'hellblau',
	'cap_lightyellow' => 'hellgelb',
	'cap_transparent' => 'transparent',
	'cap_name' => 'Name',
	'cap_lineid' => 'Verbinde Linien ID',
	'btn_write' => 'absenden',
	'btn_close' => 'schließen',

	'js_messages' => array(
		'now_communicating' => 'Es wird gerade mit dem Server kommuniziert.',
		'fusen_func' => 'Fusen(Tag)',
		'com_comp' => 'Kommunikation abgeschlossen',
		'refreshing' => 'Automatische Aktualisierung',
		'waiting' => 'Stand by',
		'stopping' => 'gestoppt',
		'connecting' => 'Verbindung zum Server wird hergestellt...',
		'err_posting' => 'Fehler bei der Datenübertragung. Bitte bestätigen durch Nutzung von "Aktualisieren" in der tag Funktion.',
		'communicating' => 'Kommuniziere mit Server...',
		'err_notconnect' => 'Es ist keine Verbindung zum Server möglich. Erneut versuchen?',
		'err_baddata' => 'Ungültige Daten.',
		'err_notcommunicating' => 'Tag konnte nicht kommunizieren.',
		'msg_retryto' => 'Erneut versuchen? Verbindung:',
		'err_nottext' => 'Bitte gib den Inhalt ein.',
		'msg_burn' => 'Komplett löschen?',
		'msg_dustbox' => 'In den Mülleimer packen?',
		'msg_dustall' => 'Ausgewählte tags in den Mülleimer packen?',
		'msg_emptydustbox' => 'Mülleimer leeren?',
		'emptydustbox' => 'Mülleimer geleert.',
		'recover' => 'Aus dem Mülleimer zurückholen?',
		'dustbox' => 'Mülleimer',
		'burn' => 'Löschen abgeschlossen',
		'unlock' => 'Entsperren',
		'new_with_line' => 'Einen neuen tag mit Verbindungslinie anlegen.',
		'edit' => 'Bearbeiten',
		'lock' => 'Sperren',
		'to_dustbox' => 'In den Mülleimer',
		'auto_resize' => 'Automatische Größenänderung',
		'owner' => 'Tag Inhaber',
		'lastedit_time' => 'Letzte Bearbeitungszeit',
		'dbc2edit' => 'Doppel-Klick -> Ändern',
		'dbc2showall' => 'Doppel-Klick -> Alle anzeigen',
		'fusen' => 'Tag',
		'resizing' => 'Größenänderung...',
		'moving' => 'Verschiebe...',
		'help_html' => '<ul>
<li>Neuer Tag kann durch Doppel-Klick angelegt werden. </li>
<li>Tag wird beim Drücken angezeigt.</li>
<li>Die Position kann durch Ziehen verändert werden.</li>
<li>Wenn "Bearbeiten" gedrückt oder tag doppelgeklickt wird, kann das tag editiert werden. <br />
- Nur das eigene tag kann bearbeitet werden.</li>
<li>Falls "Sperren" gedrückt ist, ist die Bearbeitung und das Verschieben nicht möglich.<br />
Tag das gesperrt ist kann durch "Entsperren" entsperrt werden.<br />
- Nur das eigene tag sollte gesperrt werden. </li>
<li>Wenn "In den Mülleimer" gedrückt wird, wird das tag in den Mülleimer verachoben.<br />
Das tag kann mit "Aus dem Mülleimer zurückholen?" zurückgeholt werden.<br />
Falls "In den Mülleimer" mit dem tag im Mülleimer gedrückt wird, wird es endgültig gelöscht. <br />
- Nur eigene Tags sollten gelöscht werden.</li>
</ul>
<dl>
<dt>[Neu]</dt>
<dd>Die Bearbeiten-Anzeige des neuen tags wird angezeigt.</dd>
<dt>[Müll]</dt>
<dd>Das Tag wird im Mülleimer angezeigt.</dd>
<dt>[Trns.]</dt>
<dd>Alle tags werden durchscheinend angezeigt.</dd>
<dt>[Aktualisieren]</dt>
<dd>Tag ist aktualisiert.</dd>
<dt>[Liste]</dt>
<dd>Die angezeigten Tags dieser Seite sind gesperrt.</dd>
<dt>[Hilfe]</dt>
<dd>Das angezeigte Tag dieser Seite ist gesperrt.</dd>
<dt>Suche</dt>
<dd>Nur Tags mit dem eingegebenen Schlüsselwort werden angezeigt.</dd>
</dl>',
		'burn_checked' => 'Selektierte tags werden aus dem Mülleimer entfernt.',
		'dust_checked' => 'Müll-Selektion',
		'empty' => 'Mülleimer leer',
		'close' => 'Schließen',
		'newtag' => 'Neuen tag erstellen',
		'new' => 'Neu',
		'howto' => 'Wie wird genutzt',
		'help' => 'Hilfe',
		'notag' => 'Es gibt keien tag auf dieser Seite.',
	),
);
?>