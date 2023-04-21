<?php

define( '_TPLSADMIN_INTRO', "Présentation d'un crochet pour compiler des modèles");

define( '_TPLSADMIN_DESC', "Les crochets de compilation offrent un moyen simple d'insérer des assistants d'édition visuels dans vos modèles et de collecter des variables Smarty.
Ces fonctions ne sont disponibles que dans les modèles frontaux et les modules dupliqués pour lesquels elles ont été écrites. ");

define( '_TPLSADMIN_NOTE', 'Important : Bien que les aides visuelles soient des moyens à mettre en évidence la structure de votre mise en page et de vos modèles, 
il existe des limites à la reconnaissance basée sur les caractéristiques, par exemple, des composants et des modèles personnalisés! ');

define( '_TPLSADMIN_TASK_Title', 'Why and when to perform this task!');
define( '_TPLSADMIN_TASK', "
Vous pouvez utiliser les modèles compilés pour effectuer les tâches suivantes:<br>
<ul>
<li>aperçu structurel facilitant la reconnaissance des défauts de conception fonctionnelle</li>
<li>insérer des éléments de superposition qui sont rendus pour chaque composant et modèle inclus
<li>insérer des commentaires de code pour faciliter l'édition du code source</li>
<li>détecter et résoudre les différences entre la conception d'un modèle et sa mise en œuvre</li>
<li>générer le code d'application utilisé dans les modèles et collecter les variables Smarty.</li>
</ul>");

define( '_TPLSADMIN_CACHE_TITLE', 'modèle compilé');
define( '_TPLSADMIN_CACHE_DESC' , 'le modèle source reste inchangé, dans la plupart des cas, vous pouvez supprimer tous les fichiers de modèle mis en cache et exécuter <b>Normaliser</b>. La compilation génère un nouvel ensemble de fichiers. ' );

define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYCOMMENT' , '%d modèles en cache sont entourés de commentaires tplsadmin.');
define( '_TPLSADMIN_DT_ENCLOSEBYCOMMENT' , 'Ajouter des commentaires au code source');
define( '_TPLSADMIN_DD_ENCLOSEBYCOMMENT' , "Ajoutez des commentaires HTML au début et à la fin de chaque modèle. Comme cela n'affecte pas la conception, il est recommandé pour l' édition du code source.");
define( '_TPLSADMIN_CNF_ENCLOSEBYCOMMENT' , 'Ajoutez un commentaire "tplsadmin" aux modèles compilés mis en cache. Confirmez pour continuer ou annulez !');

define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYBORDEREDDIV' , '%d modèles en cache sont entourés de balises div.l');
define( '_TPLSADMIN_DT_ENCLOSEBYBORDEREDDIV' , 'Ajoutez des balises div autour des modèles.');
define( '_TPLSADMIN_DD_ENCLOSEBYBORDEREDDIV' , "Chaque modèle est entouré d'une balise div et d'un lien vers le contrôleur d'édition. Bien que cela affecte la conception globale, vous pouvez facilement identifier le modèle que vous souhaitez modifier.");
define( '_TPLSADMIN_CNF_ENCLOSEBYBORDEREDDIV' , 'Enveloppez les modèles mis en cache avec des balises div. Confirmer pour continuer ou annuler !');

define( '_TPLSADMIN_FMT_MSG_HOOKSAVEVARS' , '%d logique implémentée dans le cache compilé pour collecter les variables des modèles.');
define( '_TPLSADMIN_DT_HOOKSAVEVARS' , 'Insérer la logique pour collecter des variables de modèle');
define( '_TPLSADMIN_DD_HOOKSAVEVARS' , "Etape préliminaire pour obtenir une liste d'informations sur les variables de modèle. En injectant de la logique dans le cache des modèles compilés et en rendant chaque page, les informations sur les variables de modèle sont sauvegardés. Obtenez des informations sur le bouton ci-dessous au bon moment. Videz le cache de compilation lors de la suppression de cette logique.");
define( '_TPLSADMIN_CNF_HOOKSAVEVARS' , 'Les modèles compilés dans le cache, implémenteront la logique pour collecter les variables de modèle. Voulez-vous continuer?');

define( '_TPLSADMIN_FMT_MSG_REMOVEHOOKS' , '%d templates mis en cache sont normalisés !');
define( '_TPLSADMIN_DT_REMOVEHOOKS' , 'Normaliser les modèles compilés.');
define( '_TPLSADMIN_DD_REMOVEHOOKS' , 'Cela supprime les commentaires, les balises div et la logique Smarty de tous les modèles compilés.');
define( '_TPLSADMIN_CNF_REMOVEHOOKS' , 'Normaliser les modèles compilés. Confirmez pour continuer ou annulez !');

define( '_TPLSADMIN_MSG_CLEARCACHE' , 'Les modèles en cache ont été supprimés !');
define( '_TPLSADMIN_MSG_CREATECOMPILECACHEFIRST' , "Il n'y a pas de modèles compilés. Parcourez votre front-end pour afficher vos pages et mettre en cache les modèles compilés.");

define( '_TPLSADMIN_CNF_DELETEOK' , 'Confirmez pour supprimer ou annulez !');

define( '_TPLSADMIN_DT_GETTPLSVARSINFO_DW' , "Générer l'extension DreamWeaver avec des variables de modèle.");
define( '_TPLSADMIN_DD_GETTPLSVARSINFO_DW' , "Ouvrir d'abord le gestionnaire d'extensions.<br>Extraire l'archive de téléchargement.<br>Exécutez les fichiers avec les extensions .mxi et suivez les instructions d'installation des boîtes de dialogue.<br>Les extraits de variables de modèle de votre site seront utilisables après le redémarrage de DreamWeaver.");

define( '_TPLSADMIN_DT_GETTEMPLATES' , 'Télécharger templates');
define( '_TPLSADMIN_DD_GETTEMPLATES' , "Sélectionnez un ensemble de modèles à télécharger et appuyez sur l'un des boutons.");

define( '_TPLSADMIN_FMT_MSG_PUTTEMPLATES' , '%d templates importés.');
define( '_TPLSADMIN_DT_PUTTEMPLATES' , 'Téléverser templates');
define( '_TPLSADMIN_DD_PUTTEMPLATES' , "Sélectionner un ensemble de modèles que vous souhaitez remplacer.<br> Sélectionner le fichier <b>tar</b> comprenant les modèles (.html)<br> Extraire automatiquement tous les modèles indépendamment de l'arborescence des répertoires.");

define( '_TPLSADMIN_ERR_NOTUPLOADED' , 'Aucun fichier téléversé.');
define( '_TPLSADMIN_ERR_EXTENSION' , "Cette extension n'est pas autorisée.");
define( '_TPLSADMIN_ERR_INVALIDARCHIVE' , "L'archive ne peut pas être extraite.");
define( '_TPLSADMIN_ERR_INVALIDTPLSET' , "Un nom d'ensemble de modèles non valide a été spécifié.");

define( '_TPLSADMIN_ERR_NOTPLSVARSINFO' , "Aucun fichier avec les variables des modèles.");

define( '_TPLSADMIN_NUMCAP_COMPILEDCACHES' , 'Templates compilés dans le répertoire cache');
define( '_TPLSADMIN_NUMCAP_TPLSVARS' , "Templates compilés avec options d'édition");
