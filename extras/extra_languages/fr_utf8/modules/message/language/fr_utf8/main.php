<?php
define('_MD_MESSAGE_NEWMESSAGE', 'Vous avez {0} messages.');

define('_MD_MESSAGE_FORMERROR1', 'Nom est requis.');
define('_MD_MESSAGE_FORMERROR2', 'Ajouter unnom avec un maximum de 30 characteres.');
define('_MD_MESSAGE_FORMERROR3', 'Sujet est requis.');
define('_MD_MESSAGE_FORMERROR4', 'Ajouter un sujet avec un maximum de 100 characteres.');
define('_MD_MESSAGE_FORMERROR5', 'Message est requiS.');
define('_MD_MESSAGE_FORMERROR6', 'L\'utilisateur spécifié n\'existe pas.');

define('_MD_MESSAGE_ACTIONMSG1', 'Le message n\'existe pas.');
define('_MD_MESSAGE_ACTIONMSG2', 'Vous n\'avez pas les permissions requises.');
define('_MD_MESSAGE_ACTIONMSG3', 'Le Message a été supprimé.');
define('_MD_MESSAGE_ACTIONMSG4', 'Echec de la suppression!');
define('_MD_MESSAGE_ACTIONMSG5', 'Le Message n\'a pu être envoyé.');
define('_MD_MESSAGE_ACTIONMSG6', 'Echec d\'ajout aux Messages Envoyés.');
define('_MD_MESSAGE_ACTIONMSG7', 'Le Message a été envoyé.');
define('_MD_MESSAGE_ACTIONMSG8', 'You don\'t have the required permissions.');
define('_MD_MESSAGE_ACTIONMSG9', 'The user the reply ahead doesn\'t exist.');

define('_MD_MESSAGE_TEMPLATE1', 'Send message');
define('_MD_MESSAGE_TEMPLATE2', 'User name');
define('_MD_MESSAGE_TEMPLATE3', 'Subject');
define('_MD_MESSAGE_TEMPLATE4', 'Message');
define('_MD_MESSAGE_TEMPLATE5', 'Preview');
define('_MD_MESSAGE_TEMPLATE6', 'Submit');
define('_MD_MESSAGE_TEMPLATE7', 'Sent Box');
define('_MD_MESSAGE_TEMPLATE8', 'New message');
define('_MD_MESSAGE_TEMPLATE9', 'To');
define('_MD_MESSAGE_TEMPLATE10', 'Date');
define('_MD_MESSAGE_TEMPLATE11', 'Message');
define('_MD_MESSAGE_TEMPLATE12', 'From');
define('_MD_MESSAGE_TEMPLATE13', 'Reply');
define('_MD_MESSAGE_TEMPLATE14', 'Delete');
define('_MD_MESSAGE_TEMPLATE15', 'Inbox');
define('_MD_MESSAGE_TEMPLATE16', 'Unread');
define('_MD_MESSAGE_TEMPLATE17', 'Read');
define('_MD_MESSAGE_TEMPLATE18', 'Order');
define('_MD_MESSAGE_TEMPLATE19', 'Lock');
define('_MD_MESSAGE_TEMPLATE20', 'Unlock');
define('_MD_MESSAGE_TEMPLATE21', 'Send email');
define('_MD_MESSAGE_TEMPLATE22', 'Status');

define('_MD_MESSAGE_ADDFAVORITES', 'Add to favorites');

define('_MD_MESSAGE_FAVORITES0', 'Select an user to add.');
define('_MD_MESSAGE_FAVORITES1', 'Fail adding!');
define('_MD_MESSAGE_FAVORITES2', 'Add');
define('_MD_MESSAGE_FAVORITES3', 'Fail Updating!');
define('_MD_MESSAGE_FAVORITES4', 'Update');
define('_MD_MESSAGE_FAVORITES5', 'Delete');

define('_MD_MESSAGE_SETTINGS', 'Private Message Settings');
define('_MD_MESSAGE_SETTINGS_MSG1', 'Use private message');
define('_MD_MESSAGE_SETTINGS_MSG2', 'Forword to email');
define('_MD_MESSAGE_SETTINGS_MSG3', 'change settings');
define('_MD_MESSAGE_SETTINGS_MSG4', 'Fail updating !');
define('_MD_MESSAGE_SETTINGS_MSG5', 'You cannot use private message. Please modify settings.');
define('_MD_MESSAGE_SETTINGS_MSG6', 'The selected user cannot receive the message.');
define('_MD_MESSAGE_SETTINGS_MSG7', 'The message is displayed in mail.');
define('_MD_MESSAGE_SETTINGS_MSG8', 'Number of messages displayed per page');
define('_MD_MESSAGE_SETTINGS_MSG9', 'Use default module settings if value is 0.');
define('_MD_MESSAGE_SETTINGS_MSG10', 'Blacklist');
define('_MD_MESSAGE_SETTINGS_MSG11', 'Separate User IDs with a comma.');
define('_MD_MESSAGE_SETTINGS_MSG12', '{0} was added to the blacklist.');
define('_MD_MESSAGE_SETTINGS_MSG13', 'Fail adding {0}\'s to blacklist.');
define('_MD_MESSAGE_SETTINGS_MSG14', '{0} already exists.');
define('_MD_MESSAGE_SETTINGS_MSG15', 'Blacklist management');
define('_MD_MESSAGE_SETTINGS_MSG16', 'The user was removed.');
define('_MD_MESSAGE_SETTINGS_MSG17', 'Fail removing user.');
define('_MD_MESSAGE_SETTINGS_MSG18', 'Details');
define('_MD_MESSAGE_SETTINGS_MSG19', 'The user does not exist.');

define('_MD_MESSAGE_MAILSUBJECT', 'You have a New Private Message');
define('_MD_MESSAGE_MAILBODY', '{0} login, please.');

define('_MD_MESSAGE_ADDBLACKLIST', 'This user was added to the blacklist.');

define('_MD_MESSAGE_DELETEMSG1', 'The parameter is illegal.');
define('_MD_MESSAGE_DELETEMSG2', 'It is not selected.');

define('_MD_MESSAGE_SEARCH', 'Search');

if ( !defined('LEGACY_MAIL_LANG') ) {
  define('LEGACY_MAIL_LANG','en');
  define('LEGACY_MAIL_CHAR','iso-8859-1');
  define('LEGACY_MAIL_ENCO','7bit');
}
?>
