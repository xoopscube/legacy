<?php
// Syntax replace define with const v2.3.0 2021/05/15 @gigamaster XCL-PHP7

const _INSTALL_L0 = "<span>ẊOOPS Cube Web Application Platform</span><br>Démarrage de l'assistant d'installation";
const _INSTALL_L168 = 'XCL 2.3 requiert PHP7.x.x';
const _INSTALL_L70 = "Changer les permissions du fichier mainfile.php afin qu'il soit accessible en écriture par le serveur (ex. chmod 777 sur un serveur UNIX/LINUX, ou vérifier les propriétés du fichier et s'assurer que l'option 'Lecture seule' n'est pas cochée sur un serveur Windows). Recharger cette page une fois les permissions changées.";

//define("_INSTALL_L71","Cliquez sur le bouton ci-dessous pour commencer l'installation.");
const _INSTALL_L1 = "Ouvrir le fichier mainfile.php avec un éditeur de texte et chercher le code suivant à la ligne 31 :";
const _INSTALL_L2 = "Maintenant, changer cette ligne en :";
const _INSTALL_L3 = "Ensuite, à la ligne 35, changer %s en %s";
const _INSTALL_L4 = "OK, j'ai saisi les paramètres ci-dessus, laissez-moi essayer à nouveau !";
const _INSTALL_L5 = "ATTENTION !";
const _INSTALL_L6 = "Il y a une différence entre la configuration XOOPS_ROOT_PATH à la ligne 31 du fichier mainfile.php et les informations du chemin racine détectées.";
const _INSTALL_L7 = "Vos paramètres : ";
const _INSTALL_L8 = "Paramètres détectés : ";
const _INSTALL_L9 = "( Sur les plateformes Windows, ce message d'erreur peut s'afficher même si la configuration est correcte. Si c'est le cas, presser le bouton ci-dessous pour continuer)";
const _INSTALL_L10 = "Presser le bouton ci-dessous pour continuer si la configuration est correcte.";
const _INSTALL_L11 = "Le chemin du répertoire racine de XCL sur le serveur : ";
const _INSTALL_L12 = "L'URL du répertoire racine de XCL : " ;
const _INSTALL_L13 = "Si les paramètres ci-dessus sont corrects, pressez le bouton ci-dessous pour continuer." ;
const _INSTALL_L14 = "Suivant" ;
const _INSTALL_L15 = "Merci d'éditer le fichier mainfile.php et d'entrer les données requises pour votre base de données" ;
const _INSTALL_L16 = "%s est le nom d'hà´te de votre serveur de base de données." ;
const _INSTALL_L17 = "%s est le nom d'utilisateur de votre compte de base de données." ;
const _INSTALL_L18 = "%s est le mot de passe requis pour accéder à votre base de données." ;
const _INSTALL_L19 = "%s est le nom de votre base de données dans laquelle les tables de XCL seront créées." ;
const _INSTALL_L20 = "%s est le préfixe des tables qui seront créées durant l'installation." ;
const _INSTALL_L21 = "La base de données suivante n'a pas été trouvée sur le serveur :" ;
const _INSTALL_L22 = "Essayer de créer la base de données ?" ;
const _INSTALL_L23 = "Oui" ;
const _INSTALL_L24 = "Non" ;
const _INSTALL_L25 = "Nous avons détecté les infos de configuration suivantes pour votre base de données dans mainfile.php. Merci de rectifier maintenant si ce n'est pas correct." ;
const _INSTALL_L26 = "Configuration de la base de données" ;
const _INSTALL_L51 = "Base de données" ;
const _INSTALL_L66 = "Choisissez la base de données à utiliser" ;
const _INSTALL_L27 = "Nom de la base de données" ;
const _INSTALL_L67 = "Nom d'hôte du serveur de base de données. Si vous n'êtes pas sûr, 'localhost' fonctionne dans la majorité des cas." ;
const _INSTALL_L28 = "Nom d'utilisateur de la base de données" ;
const _INSTALL_L65 = "Nom d'utilisateur de votre compte de base de données sur le serveur." ;
const _INSTALL_L29 = "Nom de la base de données" ;
const _INSTALL_L64 = "Le nom de la base de données sur le serveur. L'assistant d'installation peut créer la base de données si elle n'existe pas." ;
const _INSTALL_L52 = "Mot de passe de la base de données" ;
const _INSTALL_L68 = "Mot de passe de votre compte utilisateur de base de données." ;
const _INSTALL_L30 = "Préfixe des tables" ;
const _INSTALL_L63 = "Le préfixe sera ajouté à toutes les tables créées pour éviter un conflit de noms dans la base de données. Si vous n'êtes pas sûr, utilisez juste par défaut 'xcl'." ;
const _INSTALL_L54 = "Utiliser les connexions persistentes?" ;
const _INSTALL_L69 = "Par défaut c'est 'NON'. Choisir 'NON' si vous n'êtes pas sûr." ;
const _INSTALL_L55 = "Chemin physique de XCL" ;
const _INSTALL_L59 = "Chemin physique de votre répertoire racine XCL sans le slash / de fin." ;
const _INSTALL_L75 = "Chemin physique de XOOPS_TRUST_PATH" ;
const _INSTALL_L76 = "Chemin physique de votre répertoire XOOPS_TRUST_PATH sans le slash / de fin.<br />XOOPS_TRUST_PATH doit se trouver hors du domaine public." ;

const _INSTALL_L56 = "Chemin virtuel de XCL (URL)" ;
const _INSTALL_L58 = "Chemin virtuel de votre répertoire racine XCL sans le slash / de fin." ;

const _INSTALL_L31 = "Impossible de créer la base de données. Contactez l'administrateur du serveur pour des détails." ;
const _INSTALL_L32 = "Installation terminée" ;
const _INSTALL_L33 = "Cliquez <a href='../index.php'>ICI</a> pour voir la page d'acceuil de votre site." ;
const _INSTALL_L35 = "Si vous avez des erreurs, merci de contacter l'équipe de support sur <a href='http://github.com/xoopscube/xcl' target='_blank'>XoopsCube-XCL</a>" ;
const _INSTALL_L36 = "Choisissez le nom, e-mail et mot de passe de votre compte administrateur du site." ;
const _INSTALL_L37 = "Nom de l'Administrateur" ;
const _INSTALL_L38 = "E-mail de l'Administrateur" ;
const _INSTALL_L39 = "Mot de passe de l'Administrateur" ;
const _INSTALL_L74 = "Confirmation du mot de passe" ;
const _INSTALL_L77 = "Set Default Timezone" ;

const _INSTALL_L40 = "Créer les tables" ;
const _INSTALL_L41 = "Veuillez revenir en arrière et vérifier toutes les informations requises et le champ de mot de passe.." ;
const _INSTALL_L42 = "Retour" ;
const _INSTALL_L57 = "Merci d'entrer %s" ;
// %s is database name
const _INSTALL_L43 = "Base de données %s créée !" ;
// %s is table name
const _INSTALL_L44 = "Impossible de créer %s" ;
const _INSTALL_L45 = "Table %s créée" ;

const _INSTALL_L46 = "Pour que les modules inclus dans le package fonctionnent correctement, les fichiers suivants doivent être accessible en écriture par le serveur. Merci de changer les propriétés de ces fichiers. (chmod 777 sur un serveur UNIX/LINUX; sur un serveur Windows décocher l'option 'Lecture seule' des propriétés du fichier ou repértoire)" ;
const _INSTALL_L47 = "Suivant" ;

const _INSTALL_L53 = "Valider les paramètres de votre site" ;

const _INSTALL_L60 = "Impossible d'ouvrir mainfile.php. Merci de vérifier les permissions du fichier et de recommencer." ;
const _INSTALL_L61 = "Impossible d'écrire dans mainfile.php. Contactez l'administrateur du serveur pour des détails." ;
const _INSTALL_L62 = "Les données de votre configuration ont été sauvegardées avec succès dans le fichier mainfile.php." ;
const _INSTALL_L72 = "Les répertoires suivants doivent être créés avec une permission d'écriture par le serveur. (ex. 'chmod 777 pour les répertoires' sur un serveur UNIX/LINUX)" ;
const _INSTALL_L73 = "Adresse e-mail invalide" ;
// add by haruki
const _INSTALL_L80 = "Introduction" ;
const _INSTALL_L81 = "Vérifier les permissions des fichiers" ;
const _INSTALL_L82 = "Vérification des permissions des fichiers et des répertoires..." ;
const _INSTALL_L83 = "Le fichier N'EST PAS accessible en écriture  %s" ;
const _INSTALL_L84 = "Le fichier est accessible en écriture  %s" ;
const _INSTALL_L85 = "Le répertoire N'EST PAS accessible en écriture %s" ;
const _INSTALL_L86 = "Le répertoire est accessible en écriture %s" ;
const _INSTALL_L87 = "Aucune erreur détectée!" ;
const _INSTALL_L89 = "Paramètres généraux" ;
const _INSTALL_L90 = "Configuration générale" ;
const _INSTALL_L91 = "Valider" ;
const _INSTALL_L92 = "Sauvegarder les paramètres" ;
const _INSTALL_L93 = "Modifier les paramètres" ;
const _INSTALL_L88 = "Sauvegarde des données de configuration..." ;
const _INSTALL_L166 = 'Vérifier les permissions des fichiers de XOOPS_TRUST_PATH' ;
const _INSTALL_L167 = 'Vérifier les permissions de Trust Path' ;
const _INSTALL_L94 = "Vérifier le chemin & l'URL" ;
const _INSTALL_L127 = "Vérification du chemin des fichiers & de l'URL." ;
const _INSTALL_L95 = "Impossible de détecter le chemin physique de votre répertoire XCL." ;
const _INSTALL_L96 = "Il y a un conflit entre le chemin physique détecté (%s) et celui que vous avez saisi." ;
const _INSTALL_L97 = "Le <b>chemin physique</b> est correct." ;

const _INSTALL_L99 = "Le <b>chemin physique</b> doit être un répertoire." ;
const _INSTALL_L100 = "Le <b>chemin virtuel</b> que vous avez saisi est une URL valide." ;
const _INSTALL_L101 = "Le <b>chemin virtuel</b> que vous avez saisi n'est pas une URL valide." ;
const _INSTALL_L102 = "Valider ces paramètres" ;
const _INSTALL_L103 = "Recommencer depuis le début" ;
const _INSTALL_L104 = "Vérifier la base de données" ;
const _INSTALL_L105 = "Créer la base de donnéees" ;
const _INSTALL_L106 = "Impossible de se connecter au serveur de base de données." ;
const _INSTALL_L107 = "Vérifier le serveur de base de données et sa configuration." ;
const _INSTALL_L108 = "Connexion réussie au serveur de base données." ;
const _INSTALL_L109 = "La base de données %s n'existe pas." ;
const _INSTALL_L110 = "Connexion réussie à la base de données %s" ;
const _INSTALL_L111 = "Connexion réussie à la base de données.<br>Pressez le bouton ci-dessous pour créer les tables dans la base de données." ;
const _INSTALL_L112 = "Paramètres du compte de l'administrateur" ;
const _INSTALL_L113 = "Table %s supprimée." ;
const _INSTALL_L114 = "Échec de création des tables dans la base de données." ;
const _INSTALL_L115 = "Tables créées dans la base de données.<h3>Attention !</h3>Un message d'erreur peut s'afficher lorsque la table spécifiée existe. Vérifiez la présence de doublons qui pourraient causer des problèmes. Par exemple : les groupes." ;
const _INSTALL_L116 = "Ajouter des données" ;
const _INSTALL_L117 = "Terminer" ;

const _INSTALL_L118 = "Echec de création de la table %s." ;
const _INSTALL_L119 = "%d entrée(s) insérée(s) dans la table %s." ;
const _INSTALL_L120 = "Echec d'insertion de %d entrées dans la table %s." ;

const _INSTALL_L121 = "Constante %s écrite avec %s." ;
const _INSTALL_L122 = "Echec d'écriture de la constante %s." ;

const _INSTALL_L123 = "Fichier %s stocké dans le répertoire cache/." ;
const _INSTALL_L124 = "Echec de stockage du fichier %s dans le répertoire cache/." ;

const _INSTALL_L125 = "Fichier %s écrasé par %s." ;
const _INSTALL_L126 = "Impossible d'écrire dans le fichier %s." ;

const _INSTALL_L130 = "L'installateur a détecté des tables dans la base de données d'une version antérieure.<br>L'installateur va maintenant essayer de mettre à jour votre base de données." ;
const _INSTALL_L131 = "Les Tables pour XCL existe déjà dans votre base de données." ;
const _INSTALL_L132 = "Mise à jour des tables" ;
const _INSTALL_L133 = "Table %s mise à jour." ;
const _INSTALL_L134 = "Echec de mise à jour de la table %s." ;
const _INSTALL_L135 = "Echec de mise à jour des tables de la base de données." ;
const _INSTALL_L136 = "Tables de la base de données mises à jour." ;
const _INSTALL_L137 = "Mettre à jour les modules" ;
const _INSTALL_L138 = "Mettre à jour les commentaires" ;
const _INSTALL_L139 = "Mettre à jour les avatars" ;
const _INSTALL_L140 = "Mettre à jour les emoticones" ;
const _INSTALL_L141 = "L'installateur va maintenant mettre à jour chaque module pour qu'ils fonctionnent avec XCL<br />Assurez-vous d'avoir téléchargé tous les fichiers de XCL sur votre serveur.<br />Cela peut prendre un certain temps pour finir." ;
const _INSTALL_L142 = "Mise à jour des modules..." ;
const _INSTALL_L143 = "L'installateur va maintenant mettre à jour les données de configuration de XOOPS pour être utilisées avec XCL" ;
const _INSTALL_L144 = "Mettre à jour la configuration" ;
const _INSTALL_L145 = "Commentaire (ID : %s) inséré dans la base de données." ;
const _INSTALL_L146 = "Impossible d'insérer le commentaire (ID : %s) dans la base de données." ;
const _INSTALL_L147 = "Mise à jour des commentaires..." ;
const _INSTALL_L148 = "Mise à jour terminée." ;
const _INSTALL_L149 = "L'installateur va maintenant mettre à jour les envois de commentaires de XCL pour être utilisés dansXCL.<br />Cela peut prendre un certain temps pour finir." ;
const _INSTALL_L150 = "L'installateur va maintenant mettre à jour les émoticones et les images de classement utilisateur pour être utilisés dans XCL.<br />Cela peut prendre un certain temps pour finir." ;
const _INSTALL_L151 = "L'installateur va maintenant mettre à jour les avatars utilisateurs pour être utilisés dans XCL<br />Cela peut prendre un certain temps." ;
const _INSTALL_L155 = "Mise à jour des émoticà´nes/images de classement..." ;
const _INSTALL_L156 = "Mise à jour des avatars utilisateurs..." ;
const _INSTALL_L157 = "Sélectionnez le groupe utilisateurs par défaut pour chaque type de groupe" ;
const _INSTALL_L158 = "Groupes de la v1.3.x" ;
const _INSTALL_L159 = "Webmestres" ;
const _INSTALL_L160 = "Membres" ;
const _INSTALL_L161 = "Anonymes" ;
const _INSTALL_L162 = "Vous devez sélectionner un groupe par défaut pour chaque type de groupe." ;
const _INSTALL_L163 = "Table %s supprimée." ;
const _INSTALL_L164 = "Echec de suppression de la table %s." ;
const _INSTALL_L165 = "Le site est actuellement fermé pour maintenance. Merci de revenir plus tard." ;
// %s is filename
const _INSTALL_L152 = "Impossible d'ouvrir %s." ;
const _INSTALL_L153 = "Impossible de mettre à jour %s." ;
const _INSTALL_L154 = "%s mis à jour." ;

const _INSTALL_L128 = "Choisissez le langage à utiliser pour la procédure d'installation" ;
const _INSTALL_L200 = "Recharger" ;
const _INSTALL_L210 = "La 2ème étape de l'Installation" ;


const _INSTALL_CHARSET = "UTF-8" ;

const _INSTALL_LANG_XOOPS_SALT = "SALT" ;
const _INSTALL_LANG_XOOPS_SALT_DESC = "Ceci joue un rôle supplémentaire pour produire un code secret et de suivi (token). Vous n'avez pas besoin de changer la valeur par défaut." ;

const _INSTALL_HEADER_MESSAGE = "Suivez les instructions d'installation à l'écran" ;
