<?php
// $Id$

// Initial greek Translation by Yannis yannis@xoopsgreece.gr 27/11/05
// modified by Angelos Plastropoulos 16/02/2006
// reviewed by Angelos Plastropoulos 15/03/2006, 02/03/2006
// reviewed by Angelos Plastropoulos (plusangel[at]xoopscube.gr) at 25/10/2006


define('_TOKEN_ERROR', 'Alert ! This prevent you from instantiating a malformed request or post. Please, submit again to confirm!');
define('_SYSTEM_MODULE_ERROR', 'Τα ακόλουθα modules δεν είναι εγκατεστημένα.');
define('_INSTALL','Εγκατάσταση');
define('_UNINSTALL','Απεγκατάσταση');
define('_SYS_MODULE_UNINSTALLED','Απαιτείται(Δεν είναι εγκατεστημένο)');
define('_SYS_MODULE_DISABLED','Απαιτείται(Απενεργοποιημένο)');
define('_SYS_RECOMMENDED_MODULES','Συνιστώμενο module');
define('_SYS_OPTION_MODULES','Προαιρετικό module');
define('_UNINSTALL_CONFIRM','Είστε σίγουροι ότι θέλετε να απεγκαταστήσετε το module?');

//%%%%%%	File Name mainfile.php 	%%%%%
define("_PLEASEWAIT","Παρακαλώ Περιμένετε");
define("_FETCHING","Μεταφορά δεδομένων...");
define("_TAKINGBACK","Επιστροφή στη σελίδα που ήσασταν....");
define("_LOGOUT","Αποσύνδεση");
define("_SUBJECT","Θέμα");
define("_MESSAGEICON","Εικονίδιο Μηνύματος");
define("_COMMENTS","Σχόλια");
define("_POSTANON","Ανώνυμη Αποστολή");
define("_DISABLESMILEY","Απενεργοποιήστε των smilies");
define("_DISABLEHTML","Απενεργοποίηση της HTML");
define("_PREVIEW","Προεπισκόπηση");

define("_GO","Go!");
define("_NESTED","Nested");
define("_NOCOMMENTS","Χωρίς σχόλια");
define("_FLAT","Flat");
define("_THREADED","Threaded");
define("_OLDESTFIRST","Παλαιότερα Πρώτα");
define("_NEWESTFIRST","Νεότερα Πρώτα");
define("_MORE","Περισσότερα...");
define("_MULTIPAGE","To have your article span multiple pages, insert the word <font color=red>[pagebreak]</font> (with brackets) in the article.");
define("_IFNOTRELOAD","Εάν η επόμενη σελίδα δεν εμφανιστεί αυτόματα, παρακαλώ κάντε click <a href='%s'>εδώ</a>");
define("_WARNINSTALL2","WARNING: Directory %s exists on your server. Please remove this directory for security reasons.");
define("_WARNINWRITEABLE","WARNING: File %s is writeable by the server. Please change the permission of this file for security reasons. in Unix (444), in Win32 (read-only)");
define('_WARNPHPENV','WARNING: php.ini parameter "%s" is set to "%s". %s');
define('_WARNSECURITY','(It may cause a security problem)');

//%%%%%%	File Name themeuserpost.php 	%%%%%
define("_PROFILE","Profile");
define("_POSTEDBY","Αποστολέας");
define("_VISITWEBSITE","Επισκευτείτε το δικτυακό τόπο");
define("_SENDPMTO","Στείλτε μήνυμα στον/στην %s");
define("_SENDEMAILTO","Στείλτε email στον/στην %s");
define("_ADD","Πρόσθεση");
define("_REPLY","Απάντηση");
define("_DATE","Ημερομηνία");   // Posted date

//%%%%%%	File Name admin_functions.php 	%%%%%
define("_MAIN","Main");
define("_MANUAL","Manual");
define("_INFO","Info");
define("_CPHOME","Κέντρο διαχείρισης Cube");
define("_YOURHOME","Αρχική σελίδα ");

//%%%%%%	File Name misc.php (who's-online popup)	%%%%%
define("_WHOSONLINE","Ποιός είναι Online");
define('_GUESTS', 'Επισκέπτες');
define('_MEMBERS', 'Μέλη');
define("_ONLINEPHRASE","Online μέλη: <b>%s</b>");
define("_ONLINEPHRASEX","<b>%s</b> μέλη στην ενότητα: <b>%s</b>");
define("_CLOSE","Κλείσιμο");  // Close window

//%%%%%%	File Name module.textsanitizer.php 	%%%%%
define("_QUOTEC","Παράθεση:");

//%%%%%%	File Name admin.php 	%%%%%
define("_NOPERM","Συγνώμη αλλά δεν έχετε δικαίωμα πρόσβασης σε αυτή την περιοχή.");

//%%%%%		Common Phrases		%%%%%
define("_NO","Όχι");
define("_YES","Ναι");
define("_EDIT","Επεξεργασία");
define("_DELETE","Διαγραφή");
define("_VIEW","Προβολή");
define("_SUBMIT","Υποβολή");
define("_MODULENOEXIST","Το επιλεγμένο module δεν υπάρχει!");
define("_ALIGN","Στοίχιση");
define("_LEFT","Αριστερά");
define("_CENTER","Κέντρο");
define("_RIGHT","Δεξιά"); 
define("_FORM_ENTER", "Παρακαλούμε καταχωρήστε %s");
// %s represents file name
define("_MUSTWABLE","Ο διακομιστής (server) πρέπει να έχει το δικαίωμα εγγραφής πάνω στο αρχείο %s!");
// Module info
define('_PREFERENCES', 'Ρυθμίσεις');
define("_VERSION", "Έκδοση");
define("_DESCRIPTION", "Περιγραφή");
define("_ERRORS", "Σφάλματα");
define("_NONE", "Κανένα");
define('_ON','on');
define('_READS','αναγνώσεις');
define('_WELCOMETO','Καλώς ήρθατε στο %s');
define('_SEARCH','Αναζήτηση');
define('_ALL', 'Όλα');
define('_TITLE', 'Τίτλος');
define('_OPTIONS', 'Επιλογές');
define('_QUOTE', 'Παράθεση');
define('_LIST', 'Λίστα');
define('_LOGIN','Είσοδος');
define('_USERNAME','Όνομα μέλους: ');
define('_PASSWORD','Κωδικός: ');
define("_SELECT","Επιλογή");
define("_IMAGE","Εικόνα");
define("_SEND","Αποστολή");
define("_CANCEL","Ακύρωση");
define("_ASCENDING","Αύξουσα σειρά");
define("_DESCENDING","Φθίνουσα σειρά");
define('_BACK', 'Πίσω');
define('_NOTITLE', 'Χωρίς Τίτλο');
define('_RETURN_TOP', 'επιστροφή στην αρχή');

/* Image manager */
define('_IMGMANAGER','Image Manager');
define('_NUMIMAGES', '%s εικόνες');
define('_ADDIMAGE','Πρόσθεση αρχείου εικόνας');
define('_IMAGENAME','Όνομα:');
define('_IMGMAXSIZE','Μέγιστο επιτρεπόμενο μέγεθος (bytes):');
define('_IMGMAXWIDTH','Μέγιστο επιτρεπόμενο πλάτος (pixels):');
define('_IMGMAXHEIGHT','Μέγιστο επιτρεπόμενο μήκος (pixels):');
define('_IMAGECAT','Κατηγορία:');
define('_IMAGEFILE','Αρχείο εικόνας:');
define('_IMGWEIGHT','Σειρά εμφάνισης εικόνων στον image manager:');
define('_IMGDISPLAY','Να εμφανιστεί αυτή η εικόνα?');
define('_IMAGEMIME','MIME type:');
define('_FAILFETCHIMG', 'Δεν είναι εφικτό να γίνει upload αυτό το αρχείο: %s');
define('_FAILSAVEIMG', 'Αποτυχία αποθήκευσης της εικόνας %s στη βάση δεδομένων');
define('_NOCACHE', 'No Cache');
define('_CLONE', 'Clone');

//%%%%%	File Name class/xoopsform/formmatchoption.php 	%%%%%
define("_STARTSWITH", "Αρχίζει με");
define("_ENDSWITH", "Τελειώνει με");
define("_MATCHES", "Ταιριάζει");
define("_CONTAINS", "Περιλαμβάνει");

//%%%%%%	File Name commentform.php 	%%%%%
define("_REGISTER","Εγγραφή");

//%%%%%%	File Name xoopscodes.php 	%%%%%
define("_SIZE","Μέγεθος");  // font size
define("_FONT","Γραμματοσειρά");  // font family
define("_COLOR","Χρώμα");  // font color
define("_EXAMPLE","Δείγμα");
define("_ENTERURL","Εισάγετε την URL διεύθυνση του συνδέσμου (link) που επιθυμείτε να προσθέσετε :");
define("_ENTERWEBTITLE","Εισάγετε το τίτλο του δικτυακού τόπου:");
define("_ENTERIMGURL","Εισάγετε την URL διεύθυνση της εικόνας που επιθυμείτε να προσθέσετε. ");
define("_ENTERIMGPOS","Τώρα, εισάγετε την θέση(στοίχιση) της εικόνας.");
define("_IMGPOSRORL","'R' ή 'r' για δεξιά, 'L' ή 'l' για αριστερά, ή αφήστε το κενό.");
define("_ERRORIMGPOS","ΣΦΑΛΜΑ! Εισάγετε τη θέση της εικόνας.");
define("_ENTEREMAIL","Εισάγετε την email διεύθυνση που επιθυμείτε να προσθέσετε.");
define("_ENTERCODE","Εισάγετε τα codes που επιθυμείτε να προσθέσετε. ");
define("_ENTERQUOTE","Εισάγετε το κείμενο που επιθυμείτε να είναι σε παράθεση(quoted).");
define("_ENTERTEXTBOX","Σας παρακαλούμε να εισάγετε το κείμενο μέσα στο πλαίσιο κειμένου(textbox).");
define("_ALLOWEDCHAR","Επιτρεπόμενο μέγιστο μήκος χαρακτήρων: ");
define("_CURRCHAR","Τρέχον μήκος χαρακτήρων: ");
define("_PLZCOMPLETE","Σας παρακαλούμε να συμπληρώσετε το θέμα και το κείμενο του μηνύματος.");
define("_MESSAGETOOLONG","Το μήνυμα σας είναι πολύ μεγάλο.");

//%%%%%		TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 δευτερόλεπτο');
define('_SECONDS', '%s δευτερόλεπτα');
define('_MINUTE', '1 λεπτό');
define('_MINUTES', '%s λεπτά');
define('_HOUR', '1 ώρα');
define('_HOURS', '%s ώρες');
define('_DAY', '1 μέρα');
define('_DAYS', '%s μέρες');
define('_WEEK', '1 εβδομάδα');
define('_MONTH', '1 μήνας');

define('_HELP', "Βοήθεια");

?>