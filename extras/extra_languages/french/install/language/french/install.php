<?php
// $Id$
define("_INSTALL_L0","Bienvenue dans l'assistant d'installation de XOOPS Cube 2.2");
define("_INSTALL_L168","XOOPS Cube Legacy requires PHP5 or later");
define("_INSTALL_L70","Merci de changer les permissions du fichier mainfile.php afin qu'il soit accessible en écriture par le serveur (ex. chmod 777 sur un serveur UNIX/LINUX, ou vérifier les propriétés du fichier et s'assurer que l'option 'Lecture seule' n'est pas cochée sur un serveur Windows). Rechargez cette page une fois les permissions changées.");
//define("_INSTALL_L71","Cliquez sur le bouton ci-dessous pour commencer l'installation.");
define("_INSTALL_L1","Ouvrez le fichier mainfile.php avec un éditeur de texte et cherchez le code suivant à la ligne 31 :");
define("_INSTALL_L2","Maintenant, changez cette ligne en :");
define("_INSTALL_L3","Ensuite, à la ligne 35, changez %s en %s");
define("_INSTALL_L4","OK, j'ai saisi les paramètres ci-dessus, laissez-moi essayer à nouveau !");
define("_INSTALL_L5","ATTENTION !");
define("_INSTALL_L6","Il y a une différence entre votre configuration XOOPS_ROOT_PATH à la ligne 31 du fichier mainfile.php et les infos du chemin racine que nous avons détectées.");
define("_INSTALL_L7","Vos paramètres :&nbsp;");
define("_INSTALL_L8","Nous avons détecté :&nbsp;");
define("_INSTALL_L9","( Sur les plateformes Windows, vous pouvez recevoir ce message d'erreur même si votre configuration est correcte. Si c'est le cas, merci de presser le bouton ci-dessous pour continuer)");
define("_INSTALL_L10","Merci de presser le bouton ci-dessous pour continuer si tout est OK.");
define("_INSTALL_L11","Le chemin du répertoire racine de XOOPS Cube sur le serveur :&nbsp;");
define("_INSTALL_L12","L'URL du répertoire racine de XOOPS Cube :&nbsp;");
define("_INSTALL_L13","Si les paramètres ci-dessus sont corrects, pressez le bouton ci-dessous pour continuer.");
define("_INSTALL_L14","Suivant");
define("_INSTALL_L15","Merci d'éditer le fichier mainfile.php et d'entrer les données requises pour votre base de données");
define("_INSTALL_L16","%s est le nom d'hôte de votre serveur de base de données.");
define("_INSTALL_L17","%s est le nom d'utilisateur de votre compte de base de données.");
define("_INSTALL_L18","%s est le mot de passe requis pour accéder à votre base de données.");
define("_INSTALL_L19","%s est le nom de votre base de données dans laquelle les tables de XOOPS Cube seront créées.");
define("_INSTALL_L20","%s est le préfixe des tables qui seront créées durant l'installation.");
define("_INSTALL_L21","La base de données suivante n'a pas été trouvée sur le serveur :");
define("_INSTALL_L22","Dois-je la créer ?");
define("_INSTALL_L23","Oui");
define("_INSTALL_L24","Non");
define("_INSTALL_L25","Nous avons détecté les infos de configuration suivantes pour votre base de données dans mainfile.php. Merci de rectifier maintenant si ce n'est pas correct.");
define("_INSTALL_L26","Configuration de la base de données");
define("_INSTALL_L51","Base de données");
define("_INSTALL_L66","Choisissez la base de données à utiliser");
define("_INSTALL_L27","Nom d'hôte de la base de données");
define("_INSTALL_L67","Nom d'hôte du serveur de base de données. Si vous n'êtes pas sûr, 'localhost' fonctionne dans la majorité des cas.");
define("_INSTALL_L28","Nom d'utilisateur de la base de données");
define("_INSTALL_L65","Nom d'utilisateur de votre compte de base de données sur le serveur.");
define("_INSTALL_L29","Nom de la base de données");
define("_INSTALL_L64","Le nom de la base de données sur le serveur. L'assistant d'installation peut créer la base de données si elle n'existe pas.");
define("_INSTALL_L52","Mot de passe de la base de données");
define("_INSTALL_L68","Mot de passe de votre compte utilisateur de base de données.");
define("_INSTALL_L30","Préfixe des tables");
define("_INSTALL_L63","Le préfixe sera ajouté à toutes les tables créées pour éviter un conflit de noms dans la base de données. Si vous n'êtes pas sûr, utilisez juste par défaut 'xoops'.");
define("_INSTALL_L54","Utiliser les connexions persistentes?");
define("_INSTALL_L69","Par défaut c'est 'NON'. Choisissez 'NON' si vous n'êtes pas sûr.");
define("_INSTALL_L55","Chemin physique de XOOPS Cube");
define("_INSTALL_L59","Chemin physique de votre répertoire racine XOOPS Cube sans le slash / de fin.");
define("_INSTALL_L75","Chemin physique de XOOPS_TRUST_PATH");
define("_INSTALL_L76","Chemin physique de votre répertoire XOOPS_TRUST_PATH sans le slash / de fin.<br />XOOPS_TRUST_PATH doit se trouver hors du domaine public.");

define("_INSTALL_L56","Chemin virtuel de XOOPS Cube (URL)");
define("_INSTALL_L58","Chemin virtuel de votre répertoire racine XOOPS Cube sans le slash / de fin.");

define("_INSTALL_L31","Impossible de créer la base de données. Contactez l'administrateur du serveur pour des détails.");
define("_INSTALL_L32","Installation terminée");
define("_INSTALL_L33","Cliquez <a href='../index.php'>ICI</a> pour voir la page d'acceuil de votre site.");
define("_INSTALL_L35","Si vous avez des erreurs, merci de contacter l'équipe de support sur <a href='http://www.xoopscube.eu/' target='_blank'>Xoops Cube Europe</a>");
define("_INSTALL_L36","Choisissez le nom, e-mail et mot de passe de votre compte administrateur du site.");
define("_INSTALL_L37","Nom de l'Administrateur");
define("_INSTALL_L38","E-mail de l'Administrateur");
define("_INSTALL_L39","Mot de passe de l'Administrateur");
define("_INSTALL_L74","Confirmation du mot de passe");
define("_INSTALL_L77","Set Default Timezone");
define("_INSTALL_L40","Créer les tables"); 
define("_INSTALL_L41","Merci de revenir en arrière et de saisir toutes les informations requises.");
define("_INSTALL_L42","Retour");
define("_INSTALL_L57","Merci d'entrer %s");

// %s is database name
define("_INSTALL_L43","Base de données %s créée !");

// %s is table name
define("_INSTALL_L44","Impossible de créer %s");
define("_INSTALL_L45","Table %s créée");

define("_INSTALL_L46","Pour que les modules inclus dans le package fonctionnent correctement, les fichiers suivants doivent être accessible en écriture par le serveur. Merci de changer les propriétés de ces fichiers. (chmod 777 sur un serveur UNIX/LINUX; sur un serveur Windows décocher l'option 'Lecture seule' des propriétés du fichier ou repértoire)");
define("_INSTALL_L47","Suivant");

define("_INSTALL_L53","Valider les paramètres de votre site");

define("_INSTALL_L60","Impossible d'ouvrir mainfile.php. Merci de vérifier les permissions du fichier et de recommencer.");
define("_INSTALL_L61","Impossible d'écrire dans mainfile.php. Contactez l'administrateur du serveur pour des détails.");
define("_INSTALL_L62","Les données de votre configuration ont été sauvegardées avec succès dans le fichier mainfile.php.");
define("_INSTALL_L72","Les répertoires suivants doivent être créés avec une permission d'écriture par le serveur. (ex. 'chmod 777 pour les répertoires' sur un serveur UNIX/LINUX)");
define("_INSTALL_L73","Adresse e-mail invalide");

// add by haruki
define("_INSTALL_L80","Introduction");
define("_INSTALL_L81","Vérifier les permissions des fichiers"); 
define("_INSTALL_L82","Vérification des permissions des fichiers et des répertoires...");
define("_INSTALL_L83","Le fichier %s N'EST PAS accessible en écriture.");
define("_INSTALL_L84","Le fichier %s est accessible en écriture.");
define("_INSTALL_L85","Le répertoire %s N'EST PAS accessible en écriture.");
define("_INSTALL_L86","Le répertoire %s est accessible en écriture.");
define("_INSTALL_L87","Aucune erreur détectée!");
define("_INSTALL_L89","Paramètres généraux"); 
define("_INSTALL_L90","Configuration générale");
define("_INSTALL_L91","Valider");
define("_INSTALL_L92","Sauvegarder les paramètres"); 
define("_INSTALL_L93","Modifier les paramètres"); 
define("_INSTALL_L88","Sauvegarde des données de configuration...");
define("_INSTALL_L166","Vérifier les permissions des fichiers XOOPS_TRUST_PATH");
define("_INSTALL_L167","Vérification des permissions des fichiers et des répertoires...");
define("_INSTALL_L94","Vérifier le chemin & l'URL"); 
define("_INSTALL_L127","Vérification du chemin des fichiers & de l'URL.");
define("_INSTALL_L95","Impossible de détecter le chemin physique de votre répertoire XOOPS Cube.");
define("_INSTALL_L96","Il y a un conflit entre le chemin physique détecté (%s) et celui que vous avez saisi.");
define("_INSTALL_L97","Le <b>chemin physique</b> est correct.");

define("_INSTALL_L99","Le <b>chemin physique</b> doit être un répertoire.");
define("_INSTALL_L100","Le <b>chemin virtuel</b> que vous avez saisi est une URL valide.");
define("_INSTALL_L101","Le <b>chemin virtuel</b> que vous avez saisi n'est pas une URL valide.");
define("_INSTALL_L102","Valider ces paramètres");
define("_INSTALL_L103","Recommencer depuis le début"); 
define("_INSTALL_L104","Vérifier la base de données");
define("_INSTALL_L105","Créer la base de donnéees"); 
define("_INSTALL_L106","Impossible de se connecter au serveur de base de données.");
define("_INSTALL_L107","Merci de vérifier le serveur de base de données et sa configuration.");
define("_INSTALL_L108","La connexion au serveur de base données est OK.");
define("_INSTALL_L109","La base de données %s n'existe pas.");
define("_INSTALL_L110","La base de données %s existe et est connectable.");
define("_INSTALL_L111","La connexion à la base de données est OK.<br />Pressez le bouton ci-dessous pour créer les tables dans la base de données.");
define("_INSTALL_L112","Paramètres du compte de l'administrateur");
define("_INSTALL_L113","Table %s supprimée.");
define("_INSTALL_L114","Echec de création des tables dans la base de données.");
define("_INSTALL_L115","Tables créées dans la base de données.");
define("_INSTALL_L116","Ajouter des données");
define("_INSTALL_L117","Terminer");

define("_INSTALL_L118","Echec de création de la table %s.");
define("_INSTALL_L119","%d entrée(s) insérée(s) dans la table %s.");
define("_INSTALL_L120","Echec d'insertion de %d entrées dans la table %s.");

define("_INSTALL_L121","Constante %s écrite avec %s.");
define("_INSTALL_L122","Echec d'écriture de la constante %s.");

define("_INSTALL_L123","Fichier %s stocké dans le répertoire cache/.");
define("_INSTALL_L124","Echec de stockage du fichier %s dans le répertoire cache/.");

define("_INSTALL_L125","Fichier %s écrasé par %s.");
define("_INSTALL_L126","Impossible d'écrire dans le fichier %s.");

define("_INSTALL_L130","L'installateur a détecté des tables pour XOOPS 1.3.x dans votre base de données.<br />L'installateur va maintenant essayer de mettre à jour votre base de données pour XOOPS Cube 2.1");
define("_INSTALL_L131","Les Tables pour XOOPS Cube 2.1 existe déjà dans votre base de données.");
define("_INSTALL_L132","Mise à jour des tables");
define("_INSTALL_L133","Table %s mise à jour.");
define("_INSTALL_L134","Echec de mise à jour de la table %s.");
define("_INSTALL_L135","Echec de mise à jour des tables de la base de données.");
define("_INSTALL_L136","Tables de la base de données mises à jour.");
define("_INSTALL_L137","Mettre à jour les modules");
define("_INSTALL_L138","Mettre à jour les commentaires");
define("_INSTALL_L139","Mettre à jour les avatars");
define("_INSTALL_L140","Mettre à jour les emoticones");
define("_INSTALL_L141","L'installateur va maintenant mettre à jour chaque module pour qu'ils fonctionnent avec XOOPS Cube 2.1<br />Assurez-vous d'avoir uploadé tous les fichiers du package XOOPS Cube 2.1 sur votre serveur.<br />Cela peut prendre un certain temps pour finir.");
define("_INSTALL_L142","Mise à jour des modules...");
define("_INSTALL_L143","L'installateur va maintenant mettre à jour les données de configuration de XOOPS 1.3.x pour être utilisées avec XOOPS Cube 2.1");
define("_INSTALL_L144","Mettre à jour la configuration");
define("_INSTALL_L145","Commentaire (ID : %s) inséré dans la base de données.");
define("_INSTALL_L146","Impossible d'insérer le commentaire (ID : %s) dans la base de données.");
define("_INSTALL_L147","Mise à jour des commentaires...");
define("_INSTALL_L148","Mise à jour terminée.");
define("_INSTALL_L149","L'installateur va maintenant mettre à jour les envois de commentaires de XOOPS 1.3.x pour être utilisés dans XOOPS Cube 2.<br />Cela peut prendre un certain temps pour finir.");
define("_INSTALL_L150","L'installateur va maintenant mettre à jour les émoticônes et les images de classement utilisateur pour être utilisés dans XOOPS Cube 2.<br />Cela peut prendre un certain temps pour finir.");
define("_INSTALL_L151","L'installateur va maintenant mettre à jour les avatars utilisateurs pour être utilisés dans XOOPS Cube 2.<br />Cela peut prendre un certain temps pour finir.");
define("_INSTALL_L155","Mise à jour des émoticônes/images de classement...");
define("_INSTALL_L156","Mise à jour des avatars utilisateurs...");
define("_INSTALL_L157","Sélectionnez le groupe utilisateurs par défaut pour chaque type de groupe");
define("_INSTALL_L158","Groupes de la v1.3.x");
define("_INSTALL_L159","Webmestres");
define("_INSTALL_L160","Membres");
define("_INSTALL_L161","Anonymes");
define("_INSTALL_L162","Vous devez sélectionner un groupe par défaut pour chaque type de groupe.");
define("_INSTALL_L163","Table %s supprimée.");
define("_INSTALL_L164","Echec de suppression de la table %s.");
define("_INSTALL_L165","Le site est actuellement fermé pour maintenance. Merci de revenir plus tard.");

// %s is filename
define("_INSTALL_L152","Impossible d'ouvrir %s.");
define("_INSTALL_L153","Impossible de mettre à jour %s.");
define("_INSTALL_L154","%s mis à jour.");

define('_INSTALL_L128', "Choisissez le langage à utiliser pour la procédure d'installation");
define('_INSTALL_L200', 'Recharger');
define("_INSTALL_L210","La 2ème étape de l'Installation");

define('_INSTALL_CHARSET','ISO-8859-1');

define('_INSTALL_LANG_XOOPS_SALT', "SALT");
define('_INSTALL_LANG_XOOPS_SALT_DESC', "Ceci joue un rôle supplémentaire pour produire un code secret et de suivi (token). Vous n'avez pas besoin de changer la valeur par défaut.");

define('_INSTALL_HEADER_MESSAGE','Suivez les instructions d\'installation à l\'écran');
?>
