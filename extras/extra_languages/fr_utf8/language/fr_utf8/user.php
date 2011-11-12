<?php

//%%%%%% File Name user.php %%%%%
define('_US_NOTREGISTERED','Pas encore membre ? Cliquez <a href=register.php>ici</a>.');
define('_US_LOSTPASSWORD','Perdu votre mot de passe ?');
define('_US_NOPROBLEM',"Pas de problème. Entrez simplement l'adresse e-mail que vous avez fournie pour votre compte.");
define('_US_YOUREMAIL','Votre E-mail : ');
define('_US_SENDPASSWORD','Envoyer le mot de passe');
define('_US_LOGGEDOUT','Vous êtes maintenant déconnecté(e)');
define('_US_THANKYOUFORVISIT',"Merci de votre visite sur notre site !");
define('_US_INCORRECTLOGIN','Indentifiant incorrect !');
define('_US_LOGGINGU','Merci pour votre connexion, %s.');

define('_US_NOACTTPADM',"L'utilisateur sélectionné a été désactivé ou n'a pas encore été activé.<br />Merci de contacter l'administrateur pour des détails.");
define('_US_ACTKEYNOT',"Clé d'activation incorrecte !");
define('_US_ACONTACT','Le compte sélectionné est déjà activé !');
define('_US_ACTLOGIN','Votre compte a été activé. Merci de vous connecter avec le mot de passe enregistré.');
define('_US_NOPERMISS',"Désolé, vous n'avez pas la permission de faire cette action !");
define('_US_SURETODEL','Etes-vous sûr de vouloir supprimer votre compte ?');
define('_US_REMOVEINFO','Ceci va supprimer toutes vos informations de notre base de données.');
define('_US_BEENDELED','Votre compte a été supprimé.');
//
//%%%%%% File Name register.php %%%%%
define('_US_USERREG','Enregistrement Membre');
define('_US_NICKNAME','Pseudo');
define('_US_EMAIL','E-mail');
define('_US_ALLOWVIEWEMAIL','Autoriser les autres utilisateurs à voir mon adresse e-mail');
define('_US_WEBSITE','Site Web');
define('_US_TIMEZONE','Fuseau horaire');
define('_US_AVATAR','Avatar');
define('_US_VERIFYPASS','Vérifier le mot de passe');
define('_US_SUBMIT','Valider');
define('_US_USERNAME','Pseudo');
define('_US_FINISH','Terminer');
define('_US_REGISTERNG',"Impossible d'enregistrer un nouveau membre.");
define('_US_MAILOK',"Autoriser les administrateurs du site et<br /> les modérateurs à m'envoyer occasionnellement des avis par e-mail ?");
define('_US_DISCLAIMER','Mise en garde');
define('_US_IAGREE',"J'accepte la mise en garde ci-dessus");
define('_US_UNEEDAGREE', "Désolé, vous n'avez pas accepté notre mise en garde pour l'inscription.");
define('_US_NOREGISTER','Désolé, nous avons actuellement fermé les nouvelles inscriptions');
// %s is username. This is a subject for email
define('_US_USERKEYFOR',"Clé d'activation membre pour %s"); // mail
define('_US_YOURREGISTERED',"Vous êtes maintenant enregistré. Un e-mail contenant une clé d'activation de votre compte a été envoyé à l'adresse e-mail que vous avez fournie. Merci de suivre les instructions de ce mail pour activer votre compte. ");
define('_US_YOURREGMAILNG',"Vous êtes maintenant enregistré. Cependant, nous sommes dans l'incapacité d'envoyer le mail d'activation à votre adresse e-mail en raison d'une erreur interne survenue sur notre serveur. Nous sommes désolés pour cet inconvénient, merci d'envoyer un e-mail de notification au(x) webmestre(s) du site.");
define('_US_YOURREGISTERED2',"Vous êtes maintenant enregistré. Merci de patienter afin que votre compte soit activé par un des administrateurs. Vous recevrez un e-mail lorsqu'il aura été activé. Ceci peut prendre quelques jours, merci d'être patient.");
// %s is your site name
define('_US_NEWUSERREGAT','Nouveau membre inscrit sur %s');
// %s is a username
define('_US_HASJUSTREG',"%s vient juste de s'inscrire !");
define('_US_INVALIDMAIL','ERREUR : E-mail Invalide');
define('_US_EMAILNOSPACES',"ERREUR : L'adresse e-mail ne doit pas contenir d'espaces.");
define('_US_INVALIDNICKNAME','ERREUR : Pseudo invalide');
define('_US_NICKNAMETOOLONG','Le Pseudo est trop long. Il doit faire moins de %s caractères.');
define('_US_NICKNAMETOOSHORT','Le Pseudo est trop court. Il doit faire plus de %s caractères.');
define('_US_NAMERESERVED','ERREUR : Ce Pseudo est réservé.');
define('_US_NICKNAMENOSPACES',"Il ne doit pas y avoir d'espaces dans le Pseudo.");
define('_US_NICKNAMETAKEN','ERREUR : Ce Pseudo est déjà utilisé.');
define('_US_EMAILTAKEN','ERREUR : Adresse e-mail déjà enregistrée.');
define('_US_ENTERPWD','ERREUR: Vous devez fournir un mot de passe.');
define('_US_SORRYNOTFOUND',"Désolé, aucune info membre correspondante n'a été trouvée.");
// %s is your site name
define('_US_NEWPWDREQ','Demande de nouveau mot de passe sur %s');
define('_US_YOURACCOUNT', 'Votre compte sur %s');
define('_US_MAILPWDNG',"mail_password : Impossible de mettre à jour l'entrée utilisateur. Contactez l'Administrateur");
// %s is a username
define('_US_PWDMAILED','Mot de passe pour %s envoyé.');
define('_US_CONFMAIL','Mail de confirmation pour %s envoyé.');
define('_US_ACTVMAILNG', "Echec d'envoi du mail de notification à %s");
define('_US_ACTVMAILOK', 'Mail de notification à %s envoyé.');
//%%%%%% File Name userinfo.php %%%%%
define('_US_SELECTNG',"Pas d'utilisateur sélectionné ! Merci de revenir en arrière et de recommencer.");
define('_US_PM','PM');
define('_US_ICQ','ICQ');
define('_US_AIM','AIM');
define('_US_YIM','YIM');
define('_US_MSNM','Windows Live ID');
define('_US_LOCATION','Résidence');
define('_US_OCCUPATION','Profession');
define('_US_INTEREST',"Centres d'intérêts");
define('_US_SIGNATURE','Signature');
define('_US_EXTRAINFO','Infos complémentaires');
define('_US_EDITPROFILE','Editer le profil');
define('_US_LOGOUT','Déconnexion');
define('_US_INBOX','En attente');
define('_US_MEMBERSINCE','Membre depuis');
define('_US_RANK','Classement');
define('_US_POSTS','Commentaires/Envois');
define('_US_LASTLOGIN','Dernière connexion');
define('_US_ALLABOUT','Tout à propos de %s');
define('_US_STATISTICS','Statistiques');
define('_US_MYINFO','Mes infos');
define('_US_BASICINFO','Informations de base');
define('_US_MOREABOUT','Plus à propos de moi');
define('_US_SHOWALL','Afficher tout');
//%%%%%% File Name edituser.php %%%%%
define('_US_PROFILE','Profil');
define('_US_REALNAME','Nom Réel');
define('_US_SHOWSIG','Toujours attacher ma signature');
define('_US_CDISPLAYMODE',"Mode d'affichage des commentaires");
define('_US_CSORTORDER','Ordre des commentaires');
define('_US_PASSWORD','Mot de passe');
define('_US_TYPEPASSTWICE','(Saisissez deux fois un nouveau mot de passe pour le changer)');
define('_US_SAVECHANGES','Sauver les changements');
define('_US_NOEDITRIGHT',"Désolé, vous n'avez pas le droit d'éditer les infos de ce membre.");
define('_US_PASSNOTSAME','Les 2 mots de passe sont différents. Ils doivent être identiques.');
define('_US_PWDTOOSHORT','Désolé, votre mot de passe doit avoir au moins <b>%s</b> caractères de long.');
define('_US_PROFUPDATED','Votre profil a été mis à jour !');
define('_US_USECOOKIE','Conserver mon pseudo dans un cookie pour 1 an');
define('_US_NO','Non');
define('_US_DELACCOUNT','Supprimer le compte');
define('_US_MYAVATAR', 'Mon avatar');
define('_US_UPLOADMYAVATAR', 'Envoyer mon avatar');
define('_US_MAXPIXEL','Nbre maxi de Pixels');
define('_US_MAXIMGSZ',"Taille maxi de l'image (Octets)");
define('_US_SELFILE','Sélectionnez un fichier');
define('_US_OLDDELETED','Votre ancien avatar va être effacé !');
define('_US_CHOOSEAVT', 'Choisir un avatar dans la liste disponible');
define('_US_PRESSLOGIN', 'Pressez le bouton ci-dessous pour vous connecter');
define('_US_ADMINNO', 'Les utilisateurs du groupe webmestres ne peuvent être enlevés');
define('_US_GROUPS', 'Groupes de l\'utilisateur');
?>