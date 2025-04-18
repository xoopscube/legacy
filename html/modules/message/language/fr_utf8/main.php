<?php

define( '_MD_MESSAGE_NEWMESSAGE' , 'Vous avez {0} messages.');
define( '_MD_MESSAGE_FORMERROR1' , 'Le Nom du destinataire est requis.');
define( '_MD_MESSAGE_FORMERROR2' , 'Ajouter un nom avec un maximum de 30 caractères. ');
define( '_MD_MESSAGE_FORMERROR3' , 'Le Sujet du message est requis.');
define( '_MD_MESSAGE_FORMERROR4' , 'Ajouter un sujet avec un maximum de 100 caractères.');
define( '_MD_MESSAGE_FORMERROR5' , 'Message est requis.');
define( '_MD_MESSAGE_FORMERROR6' , 'L\'utilisateur spécifié n\'existe pas.');

define( '_MD_MESSAGE_ACTIONMSG1' , 'Le message n\'existe pas.');
define( '_MD_MESSAGE_ACTIONMSG2' , 'Vous n\'avez pas les permissions requises.');
define( '_MD_MESSAGE_ACTIONMSG3' , 'Le Message a été supprimé.');
define( '_MD_MESSAGE_ACTIONMSG4' , 'Échec de la suppression!');
define( '_MD_MESSAGE_ACTIONMSG5' , 'Le message n\'a pas pu être envoyé.');
define( '_MD_MESSAGE_ACTIONMSG6' , 'Échec de l\'ajout aux messages envoyés.');
define( '_MD_MESSAGE_ACTIONMSG7' , 'Le Message a été envoyé.');
define( '_MD_MESSAGE_ACTIONMSG8' , 'Vous n\'avez pas les permissions requises. ');
define( '_MD_MESSAGE_ACTIONMSG9' , 'Le destinataire du message n\'existe pas.');

define( '_MD_MESSAGE_TEMPLATE0' , 'Annuler');
define( '_MD_MESSAGE_TEMPLATE1' , 'Envoyer le message');
define( '_MD_MESSAGE_TEMPLATE2' , 'Nom d\'Utilisateur');
define( '_MD_MESSAGE_TEMPLATE3' , 'Sujet');
define( '_MD_MESSAGE_TEMPLATE4' , 'Message');
define( '_MD_MESSAGE_TEMPLATE5' , 'Aperçu');
define( '_MD_MESSAGE_TEMPLATE6' , 'Envoyer');
define( '_MD_MESSAGE_TEMPLATE7' , 'Boite d\'envoi');
define( '_MD_MESSAGE_TEMPLATE8' , 'Nouveau message');
define( '_MD_MESSAGE_TEMPLATE9' , 'À');
define( '_MD_MESSAGE_TEMPLATE10' , 'Date');
define( '_MD_MESSAGE_TEMPLATE11' , 'Message');
define( '_MD_MESSAGE_TEMPLATE12' , 'De');
define( '_MD_MESSAGE_TEMPLATE13' , 'Répondre');
define( '_MD_MESSAGE_TEMPLATE14' , 'Supprimer');
define( '_MD_MESSAGE_TEMPLATE15' , 'Boite de réception');
define( '_MD_MESSAGE_TEMPLATE16' , 'Non lus');
define( '_MD_MESSAGE_TEMPLATE17' , 'Lu');
define( '_MD_MESSAGE_TEMPLATE18' , 'Ordre');
define( '_MD_MESSAGE_TEMPLATE19' , 'Verrouiller');
define( '_MD_MESSAGE_TEMPLATE20' , 'déverrouiller');
define( '_MD_MESSAGE_TEMPLATE21' , 'Envoyer un email');
define( '_MD_MESSAGE_TEMPLATE22' , 'Statut');
define( '_MD_MESSAGE_ADDFAVORITES' , 'Ajouter aux favoris');
define( '_MD_MESSAGE_FAVORITES0' , 'Sélectionner un utilisateur à ajouter');
define( '_MD_MESSAGE_FAVORITES1' , 'Échec de l\'ajout !');
define( '_MD_MESSAGE_FAVORITES2' , 'Ajouter');
define( '_MD_MESSAGE_FAVORITES3' , 'Échec de la mise à jour !');
define( '_MD_MESSAGE_FAVORITES4' , 'Mettre à jour');
define( '_MD_MESSAGE_FAVORITES5' , 'Supprimer');

define( '_MD_MESSAGE_SETTINGS' , 'Réglages des messages privés');
define( '_MD_MESSAGE_SETTINGS_MSG1' , 'Utiliser la messagerie privée');
define( '_MD_MESSAGE_SETTINGS_MSG2' , 'Faire suivre par email');
define( '_MD_MESSAGE_SETTINGS_MSG3' , 'changer les réglages');
define( '_MD_MESSAGE_SETTINGS_MSG4' , 'Échec de la mise à jour !');
define( '_MD_MESSAGE_SETTINGS_MSG5' , 'Impossible d\'utiliser les messages privés. Modifiez les paramètres.');
define( '_MD_MESSAGE_SETTINGS_MSG6' , 'L\'utilisateur sélectionné ne peut pas recevoir le message.');
define( '_MD_MESSAGE_SETTINGS_MSG7' , 'Le message est affiché dans le mail.');
define( '_MD_MESSAGE_SETTINGS_MSG8' , 'Nombre de messages affichés par page.');
define( '_MD_MESSAGE_SETTINGS_MSG9' , 'Utiliser les réglages par défaut du module si la valeur est 0.');
define( '_MD_MESSAGE_SETTINGS_MSG10' , 'Bloqués');
define( '_MD_MESSAGE_SETTINGS_MSG11' , 'Séparez les IDs des utilisateurs par des virgules.');
define( '_MD_MESSAGE_SETTINGS_MSG12' , '{0} a été ajouté aux contacts bloqués.');
define( '_MD_MESSAGE_SETTINGS_MSG13' , '{0} n\'a pu être ajouté aux contacts bloqués.');
define( '_MD_MESSAGE_SETTINGS_MSG14' , '{0} existe déjà.');
define( '_MD_MESSAGE_SETTINGS_MSG15' , 'Gérer les contacts bloqués');
define( '_MD_MESSAGE_SETTINGS_MSG16' , 'L\'utilisateur a été retiré.');
define( '_MD_MESSAGE_SETTINGS_MSG17' , 'Échec du retrait de l\'utilisateur.');
define( '_MD_MESSAGE_SETTINGS_MSG18' , 'Détails');
define( '_MD_MESSAGE_SETTINGS_MSG19' , 'L\'utilisateur n\'existe pas.');

define( '_MD_MESSAGE_MAILSUBJECT' , 'Vous avez un nouveau message privé.');
define( '_MD_MESSAGE_MAILBODY' , '{0} connectez-vous.');

define( '_MD_MESSAGE_ADDBLACKLIST' , 'Cet utilisateur a été ajouté aux contacts bloqués.');

define( '_MD_MESSAGE_DELETEMSG1' , 'Le paramètre est invalide.');
define( '_MD_MESSAGE_DELETEMSG2' , 'Sélection manquante');
define( '_MD_MESSAGE_DELETE_CONFIRM' , 'Are you sure you want to delete?');

define( '_MD_MESSAGE_SEARCH' , 'Rechercher');
define( '_MD_MESSAGE_PREV' , 'Previous');
define( '_MD_MESSAGE_NEXT' , 'Next');
define( '_MD_MESSAGE_DELETEMSG_SUCCESS_NUM' , 'Deleted Messages: {0}');
define( '_MD_MESSAGE_DELETEMSG_FAIL_NUM' , 'Deleted Messages: {0}');

if (!defined('LEGACY_MAIL_LANG')) {
define('LEGACY_MAIL_LANG','fr');
define('LEGACY_MAIL_CHAR','UTF-8');
define('LEGACY_MAIL_ENCO','7bit');
}
