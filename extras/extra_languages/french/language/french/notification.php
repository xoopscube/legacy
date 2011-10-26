<?php
// $Id$

// RMV-NOTIFY

// Text for various templates...

define ('_NOT_NOTIFICATIONOPTIONS', 'Options de notification');
define ('_NOT_UPDATENOW', 'Mettre à jour');
define ('_NOT_UPDATEOPTIONS', 'Mettre à jour les options de notification');

define ('_NOT_CANCEL', 'Annuler');
define ('_NOT_CLEAR', 'Vider');
define ('_NOT_DELETE', 'Supprimer');
define ('_NOT_CHECKALL', 'Vérifier tous');
define ('_NOT_MODULE', 'Module');
define ('_NOT_CATEGORY', 'Catégorie');
define ('_NOT_ITEMID', 'ID');
define ('_NOT_ITEMNAME', 'Nom');
define ('_NOT_EVENT', 'Evènement');
define ('_NOT_EVENTS', 'Evènements');
define ('_NOT_ACTIVENOTIFICATIONS', 'Notifications actives');
define ('_NOT_NAMENOTAVAILABLE', 'Pas de nom disponible');
// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
define ('_NOT_ITEMNAMENOTAVAILABLE', "Nom de l'élément non disponible");
define ('_NOT_ITEMTYPENOTAVAILABLE', "Type de l'élément non disponible");
define ('_NOT_ITEMURLNOTAVAILABLE', "URL de l'élément non disponible");
define ('_NOT_DELETINGNOTIFICATIONS', 'Suppression des notifications');
define ('_NOT_DELETESUCCESS', 'Notification(s) supprimée(s) avec succès.');
define ('_NOT_UPDATEOK', 'Options de notification mise à jour');
define ('_NOT_NOTIFICATIONMETHODIS', 'La méthode de notification est');
define ('_NOT_EMAIL', 'e-mail');
define ('_NOT_PM', 'message privé');
define ('_NOT_DISABLE', 'désactivée');
define ('_NOT_CHANGE', 'Changer');
define ('_NOT_RUSUREDEL', 'Voulez-vous éliminer ces Notifications');
define ('_NOT_NOACCESS', "Vous n'avez pas la permission d'accéder à cette page.");


// Text for module config options

define ('_NOT_ENABLE', 'Activé');
define ('_NOT_NOTIFICATION', 'Notification');

define ('_NOT_CONFIG_ENABLED', 'Notification activée');
define ('_NOT_CONFIG_ENABLEDDSC', "Ce module permet aux utilisateurs de choisir d'être averti lorsque certains événements arrivent. Choisissez 'oui' pour permettre cette fonction.");

define ('_NOT_CONFIG_EVENTS', 'Activer les événements spécifiques');
define ('_NOT_CONFIG_EVENTSDSC', "Choisissez les notifications d'événements auquels vos utilisateurs peuvent souscrire.");

define ('_NOT_CONFIG_ENABLE', 'Activer la notification');
define ('_NOT_CONFIG_ENABLEDSC', "Ce module permet aux utilisateurs d'être averti lorsque certains événements arrivent. Choisissez si les utilisateurs doivent être prévenus avec les options de notifications dans un bloc (Style Bloc), dans le module (Style Intégré), ou les deux. Pour la notification de style bloc, le bloc d'options de notifications doit être activé pour ce module.");
define ('_NOT_CONFIG_DISABLE', 'Désactiver la notification');
define ('_NOT_CONFIG_ENABLEBLOCK', 'Activer uniquement le style bloc');
define ('_NOT_CONFIG_ENABLEINLINE', 'Activer uniquement le style intégré');
define ('_NOT_CONFIG_ENABLEBOTH', 'Activer la notification (les 2 Styles)');

// For notification about comment events

define ('_NOT_COMMENT_NOTIFY', 'Commentaire ajouté');
define ('_NOT_COMMENT_NOTIFYCAP', "Me prévenir lorsqu'un nouveau commentaire est posté pour cet article.");
define ('_NOT_COMMENT_NOTIFYDSC', "Recevoir une notification chaque fois qu'un nouveau commentaire est posté (ou approuvé) pour cet article.");
define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} notification automatique : Commentaire ajouté à {X_ITEM_TYPE}');

define ('_NOT_COMMENTSUBMIT_NOTIFY', 'Commentaire proposé');
define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', "Me prévenir lorsqu'un nouveau commentaire est proposé (en attente d'ê approuvé) pour cet article.");
define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', "Recevoir une notification chaque fois qu'un nouveau commentaire est proposé (en attente d'être approuvé) pour cet article.");
define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} notification automatique : Commentaire proposé à {X_ITEM_TYPE}');

// For notification bookmark feature
// (Not really notification, but easy to do with this module)

define ('_NOT_BOOKMARK_NOTIFY', 'Signet');
define ('_NOT_BOOKMARK_NOTIFYCAP', 'Pas de notification pour cet article.');
define ('_NOT_BOOKMARK_NOTIFYDSC', "Conserver la trace de cet article sans recevoir de notification d'événement.");

// For user profile
// FIXME: These should be reworded a little...

define ('_NOT_NOTIFYMETHOD', 'Méthode de notification : Lorsque vous contrôlerez par exemple un forum, comment voulez-vous recevoir les notifications de mises à jour ?');
define ('_NOT_METHOD_EMAIL', "E-mail (utiliser l'address de mon profil)");
define ('_NOT_METHOD_PM', 'Message privé');
define ('_NOT_METHOD_DISABLE', 'Temporairement désactivé');

define ('_NOT_NOTIFYMODE', 'Mode de notification par défaut');
define ('_NOT_MODE_SENDALWAYS', "M'avertir pour toutes les mises à jour sélectionnées");
define ('_NOT_MODE_SENDONCE', 'Me prévenir une seule fois');
define ('_NOT_MODE_SENDONCEPERLOGIN', "Me prévenir une fois et alors désactiver jusqu'à ce que je me connecte à nouveau");

?>
