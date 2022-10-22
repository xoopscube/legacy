<?php

define( '_MD_A_PICO_LINK_EXTRACT' , 'Extrait');
define( '_MD_A_PICO_LABEL_SEARCHBYPHRASE' , 'Recherche par phrase');
define( '_MD_A_PICO_H2_TAGS' , 'Gestion de mots clés, étiquettes');
define( '_MD_A_PICO_TH_TAG' , 'Étiquette, mot clé');
define( '_MD_A_PICO_TH_USED' , 'Utilisé');
define( '_MD_A_PICO_LABEL_ORDER' , 'Ordre');

define( '_MD_A_PICO_H2_CLEARBODYCACHE' , 'Effacer le cache de contenu');
define( '_MD_A_PICO_BTN_DOCLEARBODYCACHE' , 'Effacer');
define( '_MD_A_PICO_MSG_CLEARBODYCACHEDONE' , 'Les cache de tout le contenu a été effacé.');
define( '_MD_A_PICO_HELP_CLEARBODYCACHE' , 'Exécuter simplement lorsque des problèmes de mise en cache se produisent, par exemple, après la migration du site.');

define( '_MD_A_PICO_H2_INDEPENDENTPERMISSION' , 'Autorisation indépendante');
define( '_MD_A_PICO_LABEL_INDEPENDENTPERMISSION' , 'Définir les autorisations pour cette catégorie indépendamment de la catégorie parent');
define( '_MD_A_PICO_LINK_CATPERMISSIONID' , 'Vérifiez les autorisations parentales');
define( '_MD_A_PICO_LINK_DETAIL' , 'Détail');
define( '_MD_A_PICO_H2_EXTRAS' , 'Extras');
define( '_MD_A_PICO_TH_ID' , 'ID');
define( '_MD_A_PICO_TH_TYPE' , 'Type');
define( '_MD_A_PICO_TH_SUMMARY' , 'Sommaire');
define( '_MD_A_PICO_LINK_POPUP' , 'popup');
define( '_MD_A_PICO_TH_EXTRASACTIONS' , 'Actions');
define( '_MD_A_PICO_LABEL_EXTRASRIGHTCHECKED' , 'les articles choisis seront:');
define( '_MD_A_PICO_BTN_CSVOUTPUT' , 'Sortie CSV');
define( '_MD_A_PICO_MSG_DELETED' , 'Supprimé avec succès');
define( '_MD_A_PICO_LABEL_MAINDISP' , 'Visualiser');
define( '_MD_A_PICO_MSG_FMT_DUPLICATEDVPATH' , 'Certaines publications n\'ont pas été mises à jour à cause d\'une duplication de vpath (ID: %s)');

define( '_MD_A_PICO_H2_CONTENTS' , 'Liste de Contenu');
define( '_MD_A_PICO_TH_CONTENTSID' , 'ID');
define( '_MD_A_PICO_TH_CONTENTSSUBJECT' , 'Sujet');
define( '_MD_A_PICO_TH_CONTENTSWEIGHT' , 'poids');
define( '_MD_A_PICO_TH_CONTENTSVISIBLE' , 'VIS');
define( '_MD_A_PICO_TH_CONTENTSSHOWINNAVI' , 'NAVI');
define( '_MD_A_PICO_TH_CONTENTSSHOWINMENU' , 'MENU');
define( '_MD_A_PICO_TH_CONTENTSALLOWCOMMENT' , 'Commentaire');
define( '_MD_A_PICO_TH_CONTENTSFILTERS' , 'Filtres');
define( '_MD_A_PICO_TH_CONTENTSACTIONS' , 'Actions');
define( '_MD_A_PICO_LEGEND_CONTENTSTHS' , 'VIS: visible &nbsp; NAVI:affiche dans la page de navigation &nbsp; MENU:affiche dans le menu &nbsp; COM:autorise les commentaires');
define( '_MD_A_PICO_BTN_MOVE' , 'DÉPLACÉ');
define( '_MD_A_PICO_LABEL_CONTENTSRIGHTCHECKED' , 'les articles choisis seront:');
define( '_MD_A_PICO_MSG_CONTENTSMOVED' , 'Le contenu a été déplacé');
define( '_MD_A_PICO_BTN_DELETE' , 'supprimer');
define( '_MD_A_PICO_CONFIRM_DELETE' , 'Confirmer la suppression?');
define( '_MD_A_PICO_MSG_CONTENTSDELETED' , 'Supprimé avec succès');
define( '_MD_A_PICO_BTN_EXPORT' , 'Exporter');
define( '_MD_A_PICO_CONFIRM_EXPORT' , 'La sélection sera exportée comme contenu du module. Les commentaires ne seront pas copiés. Confirmer pour continuer?');
define( '_MD_A_PICO_MSG_CONTENTSEXPORTED' , 'Exporté avec succès');

// category_access
define( '_MD_A_PICO_LABEL_SELECTCATEGORY' , 'Choisir une catégorie');
define( '_MD_A_PICO_H2_GROUPPERMS' , 'Permissions par groupe');
define( '_MD_A_PICO_H2_USERPERMS' , 'Permissions par utilisateur');
define( '_MD_A_PICO_TH_UID' , 'uid');
define( '_MD_A_PICO_TH_UNAME' , 'uname');
define( '_MD_A_PICO_TH_GROUPNAME' , 'groupname');
define( '_MD_A_PICO_NOTICE_ADDUSERS' , 'Ajouter l\'un ou l\'autre uid ou uname.');

// import
define( '_MD_A_PICO_H2_IMPORTFROM' , 'Importer');
define( '_MD_A_PICO_LABEL_SELECTMODULE' , 'Choisir un module');
define( '_MD_A_PICO_BTN_DOIMPORT' , 'Importer');
define( '_MD_A_PICO_CONFIRM_DOIMPORT' , 'Confirmer l\'importation?');
define( '_MD_A_PICO_MSG_IMPORTDONE' , 'Importé avec succès');
define( '_MD_A_PICO_ERR_INVALIDMID' , 'Le module indiqué pour importation n\'est pas valide');
define( '_MD_A_PICO_ERR_SQLONIMPORT' , 'Échec dde l\'importation. Vérifier les versions de chaque module');
define( '_MD_A_PICO_HELP_IMPORTFROM' , 'Importer de Pico et d\'autres versions de TinyD. ATTENTION car ce n\'est pas une copie parfaite. Vérifier en particulier les permissions. TOUTES LES DONNÉES dans ce module seront PERDUES avec une importation.');
define( '_MD_A_PICO_H2_SYNCALL' , 'Synchroniser les informations superflues');
define( '_MD_A_PICO_BTN_DOSYNCALL' , 'Synchroniser');
define( '_MD_A_PICO_MSG_SYNCALLDONE' , 'Synchronisé avec succès');
define( '_MD_A_PICO_HELP_SYNCALL' , 'Exécuter cette opération si les catégories ou les contenus affichent des données contradictoires.');
