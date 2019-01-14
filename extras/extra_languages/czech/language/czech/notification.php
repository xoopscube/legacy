<?php
// $Id: notification.php,v 1.1 2008/07/05 08:25:21 minahito Exp $

// RMV-NOTIFY

// Text for various templates...

define('_NOT_NOTIFICATIONOPTIONS', 'Možnosti upozoròování');
define('_NOT_UPDATENOW', 'Aktualizovat');
define('_NOT_UPDATEOPTIONS', 'Aktualizovat možnosti upozoròování');

define('_NOT_CANCEL', 'Storno');
define('_NOT_CLEAR', 'Vyèistit');
define('_NOT_DELETE', 'Smazat');
define('_NOT_CHECKALL', 'Zkontrolovat vše');
define('_NOT_MODULE', 'Modul');
define('_NOT_CATEGORY', 'Kategorie');
define('_NOT_ITEMID', 'ID');
define('_NOT_ITEMNAME', 'Jméno');
define('_NOT_EVENT', 'Událost');
define('_NOT_EVENTS', 'Události');
define('_NOT_ACTIVENOTIFICATIONS', 'Aktivní upozoròování');
define('_NOT_NAMENOTAVAILABLE', 'Jméno není zadáno');
// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
define('_NOT_ITEMNAMENOTAVAILABLE', 'Není název položky');
define('_NOT_ITEMTYPENOTAVAILABLE', 'Není typ položky');
define('_NOT_ITEMURLNOTAVAILABLE', 'Není URL položky');
define('_NOT_DELETINGNOTIFICATIONS', 'Mažu upozornìní');
define('_NOT_DELETESUCCESS', 'Upozornìní smazáno.');
define('_NOT_UPDATEOK', 'Možnosti upozornìòování aktualizovány');
define('_NOT_NOTIFICATIONMETHODIS', 'Zpùsob upozornìní je');
define('_NOT_EMAIL', 'email');
define('_NOT_PM', 'soukromá zpráva');
define('_NOT_DISABLE', 'zakázáno');
define('_NOT_CHANGE', 'Zmìnit');
define('_NOT_RUSUREDEL', 'Opravdu chcete smazat tato upozornìní');
define('_NOT_NOACCESS', 'Nemáte oprávnìní pro vstup na tuto stránku.');

// Text for module config options

define('_NOT_ENABLE', 'Povolit');
define('_NOT_NOTIFICATION', 'Upozornìní');

define('_NOT_CONFIG_ENABLED', 'Povolit upozornìní');
define('_NOT_CONFIG_ENABLEDDSC', 'Tento modul umoòuje uživatelùm výbìr upozornìní na urèité události. Zvolte \"Ano\" pro povolení tété vlastnosti.');

define('_NOT_CONFIG_EVENTS', 'Povolit speciální upozoròování');
define('_NOT_CONFIG_EVENTSDSC', 'Vyberte událost, na kterou chcete být upozornìni.');

define('_NOT_CONFIG_ENABLE', 'Povolit upozornìní');
define('_NOT_CONFIG_ENABLEDSC', 'Tento modul umoòuje uživatelùm výbìr upozornìní na urèité události. Vyberte, jak bude uživatel upozornìn (Block-style), (Inline-style), nebo obojí. Pro block-style upozornìní, je tøeba, aby byl povolen blok pro tento modul.');
define('_NOT_CONFIG_DISABLE', 'Zakázat upozornìní');
define('_NOT_CONFIG_ENABLEBLOCK', 'Povolit pouze styl Bloku');
define('_NOT_CONFIG_ENABLEINLINE', 'Povolit pouze Inline-style');
define('_NOT_CONFIG_ENABLEBOTH', 'Povolit upozoròování (oba styly)');

// For notification about comment events

define('_NOT_COMMENT_NOTIFY', 'Pøidání komentáøe');
define('_NOT_COMMENT_NOTIFYCAP', 'Upozornit mì, je-li pøidán nový komentáø pro tuto položku.');
define('_NOT_COMMENT_NOTIFYDSC', 'Zaslat upozornìní, když bude pøidán èi schválen nový komentáø k této položce.');
define('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} automatické upozornìní: Pøidání komentáøe k {X_ITEM_TYPE}');

define('_NOT_COMMENTSUBMIT_NOTIFY', 'Vložení komentáøe');
define('_NOT_COMMENTSUBMIT_NOTIFYCAP', 'Upozornit mì, je-li vložen (èeká na schválení) nový komentáø pro tuto položku.');
define('_NOT_COMMENTSUBMIT_NOTIFYDSC', 'Zaslat upozornìní, když bude vložen (èeká na schválení) nový komentáø k této položce.');
define('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} automatické upozornìní: Vložení komentáøe (èeká na schválení) k {X_ITEM_TYPE}');

// For notification bookmark feature
// (Not really notification, but easy to do with this module)

define('_NOT_BOOKMARK_NOTIFY', 'Záložka');
define('_NOT_BOOKMARK_NOTIFYCAP', 'Oznaèit tuto položku (bez upozornìní).');
define('_NOT_BOOKMARK_NOTIFYDSC', 'Sledovat tuto položku bez zasílání oznámení pøi zmìnì.');

// For user profile
// FIXME: These should be reworded a little...

define('_NOT_NOTIFYMETHOD', 'Zpùsob upozornìní: Když napøíklad sledujete fórum, jak chcete být informováni o zmìnách a nových pøíspìvcích?');
define('_NOT_METHOD_EMAIL', 'Email (použije se adresa z profilu)');
define('_NOT_METHOD_PM', 'Soukromá zpráva');
define('_NOT_METHOD_DISABLE', 'Doèasnì zakázat');

define('_NOT_NOTIFYMODE', 'Výchozí zpùsob upozornìní');
define('_NOT_MODE_SENDALWAYS', 'Upozornit mì na každou zmìnu zvláš');
define('_NOT_MODE_SENDONCE', 'Upozornit mì pouze jednou');
define('_NOT_MODE_SENDONCEPERLOGIN', 'Zakázat mì upozoròovat pouze do pøíštího pøihlášení');
