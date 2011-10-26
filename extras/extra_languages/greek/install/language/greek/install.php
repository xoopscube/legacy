<?php
// $Id$
// Greek Translation by Angelos Plastropoulos (plusangel[at]xoopscube.gr) at 30/10/2006, revised 18/04/2007, revised 30/04/2007

define("_INSTALL_L0","Καλώς ήρθατε στον οδηγό εγκατάστασης του XOOPS Cube Legacy 2.1");
define("_INSTALL_L70"," Παρακαλείσθε να αλλάξετε τα δικαιώματα του αρχείου mainfile.php έτσι ώστε να μπορεί ο server να το τροποποιήσει (i.e. chmod 777 mainfile.php σε UNIX/LINUX server, ή ελέγξτε τις ιδιότητες του αρχείου και σιγουρευτείτε ότι η read-only δεν είναι σε Windows server ). Ξαναφορτώστε τη σελίδα αυτή όταν έχετε ρυθμίσει τα δικαιώματα που απαιτούνται.");
//define("_INSTALL_L71","Click on the button below to begin the installation.");
define("_INSTALL_L1","Άνοιξε το mainfile.php με έναν απλό επεξεργαστή κειμένου και βρες τα ακόλουθα στοιχεία στη γραμμή 31:");
define("_INSTALL_L2","Τώρα, τροποποίησε αυτή τη γραμμή σε:");
define("_INSTALL_L3","Μετά, στη γραμμή 35, άλλαξε %s με %s");
define("_INSTALL_L4","OK, έχω εφαρμόσει τις παραπάνω ρυθμίσεις, άφησε με να δοκιμάσω ξανά!");
define("_INSTALL_L5","ΠΡΟΣΟΧΗ!");
define("_INSTALL_L6","Υπάρχει ασυμφωνία μεταξύ της XOOPS_ROOT_PATH υπάρχουσας δομής που δηλώνεται στη γραμμή 31 του mainfile.php και της διαδρομής του βασικού φακέλου που εντοπίσαμε εμείς. ");define("_INSTALL_L7","Η ρύθμιση που πληκτρολογήσατε: ");
define("_INSTALL_L8","Αυτό που εντοπίσαμε: ");
define("_INSTALL_L7","Αυτό που μας δηλώσατε: ");
define("_INSTALL_L9","( Σε MS platforms, είναι πιθανό να λάβετε αυτό το μήνυμα λάθους ακόμα και αν οι ρυθμίσεις είναι σωστές. Αν είστε σε αυτή τη περίπτωση, παρακαλείσθε να πιέσετε το παρακάτω κουμπί για να συνεχίσετε)");define("_INSTALL_L10","Plesae press the button below to continue if this is really ok.");
define("_INSTALL_L10","Παρακαλώ πατήστε το κουμπί που ακολουθεί για να συνεχίσετε, αν συμφωνείτε ως εδώ.");
define("_INSTALL_L11","Η φυσική διαδρομή στο server του φακέλου που περιέχει το xoopsCube site (document root) είναι: ");
define("_INSTALL_L12","H URL του xoopsCube site είναι: ");
define("_INSTALL_L13","Αν τα παραπάνω δεδομένα είναι σωστά, πιέστε το κουμπί 'Επόμενο' που ακολουθεί για να συνεχίσετε τη διαδικασία.");
define("_INSTALL_L14","Επόμενο");
define("_INSTALL_L15","Please open mainfile.php and enter required DB settings data");
define("_INSTALL_L16","%s is the hostname of your database server.");
define("_INSTALL_L17","%s is the username of your database account.");
define("_INSTALL_L18","%s is the password required to access your database.");
define("_INSTALL_L19","%s is the name of your database in which XOOPS Cube tables will be created.");
define("_INSTALL_L20","%s is the prefix for tables that will be made during the installation.");
define("_INSTALL_L21","The following database was not found on the server:");
define("_INSTALL_L22","Να προσπαθήσω να τη δημιουργήσω?");
define("_INSTALL_L23","Ναι");
define("_INSTALL_L24","Όχι");
define("_INSTALL_L25","We have detected the following database information from your configuration in mainfile.php. Please fix it now if this is not correct.");
define("_INSTALL_L26","Στήσιμο βάσης δεδομένων");
define("_INSTALL_L51","Σύστημα διαχείρισης σχεσιακών βάσεων δεδομένων (RDBMS)");
define("_INSTALL_L66","Επιλέξτε το σύστημα διαχείρισης σχεσιακών βάσεων δεδομένων που θα χρησιμοποιηθεί για να φιλοξενήσει τη βάση δεδομένων του xoopsCube site. Η προεπιλογή είναι MySQL. ");
define("_INSTALL_L27","Όνομα του server που φιλοξενεί τις βάσεις δεδομένων (hostname)");
define("_INSTALL_L67","To όνομα του server που φιλοξενεί τη βάση δεδομένων (hostname). Αν δεν είστε σίγουροι, 'localhost' ισχύει για τις περισσότερες των περιπτώσεων.");
define("_INSTALL_L28","Όνομα του χρήστη στον databese server");
define("_INSTALL_L65","Είναι το όνομα χρήστη που έχει πρόσβαση στο server που φιλοξενεί τις βάσεις δεδομένων (MySQL server). ");
define("_INSTALL_L29","Όνομα της βάσης δεδομένων που έχει δημιουργηθεί για να υποστηρίξει το xoopsCube site");
define("_INSTALL_L64","Είναι το όνομα της βάσης δεδομένων που έχετε δημιουργήσει για το xoopsCube site (αν είναι ελληνικό utf, το collation πρέπει να είναι utf8_general_ci). Αν δεν έχετε δημιουργήσει βάση δεδομένων ο οδηγός εγκατάστασης θα προσπαθήσει να δημιουργήσει μια για εσάς. ");
define("_INSTALL_L52","Κωδικός πρόσβασης του χρήστη για τον databese server");
define("_INSTALL_L68","Πληκτρολογήστε το κωδικό πρόσβασης του λογαριασμού χρήστη στο server που φιλοξενεί τις βάσεις δεδομένων (MySQL server).");
define("_INSTALL_L30","Πρόθεμα ονόματος πινάκων");
define("_INSTALL_L63","Αυτό το πρόθεμα θα προστεθεί στην αρχή του ονόματος όλων των νέων πινάκων που θα δημιουργηθούν ώστε να αποφευχθεί σύγχυση με άλλους υπάρχοντες πίνακες στη βάση δεδομένων.  Αν δεν είστε σίγουροι, απλά χρησιμοποιήστε το προτεινόμενο. ");
define("_INSTALL_L54","Χρήση πάγιας σύνδεσης (persistent connection)?");
define("_INSTALL_L69","Η προεπιλογή είναι 'Όχι'. Επιλέξτε 'Όχι' εάν δεν είστε σίγουρος.");
define("_INSTALL_L55","XOOPS Cube φυσική διαδρομή (physical path)");
define("_INSTALL_L59","H φυσική διαδρομή στο βασικό φάκελο του XOOPS Cube ΧΩΡΙΣ κάθετο (backslash) στο τέλος");
define("_INSTALL_L56","XOOPS Cube εικονική διαδρομή (virtual path - URL)");
define("_INSTALL_L58","H εικονική διαδρομή στο βασικό φάκελο του XOOPS Cube ΧΩΡΙΣ κάθετο (backslash) στο τέλος");

define("_INSTALL_L31","Δεν είναι δυνατή η δημιουργία της βάσης δεδομένων. Επικοινωνήστε με τον διαχειριστή του server για περισσότερες λεπτομέρειες.");
define("_INSTALL_L32","Το 1ο στάδιο της εγκατάστασης ολοκληρώθηκε με επιτυχία");
define("_INSTALL_L33","Κάντε click <a href='../index.php'>ΕΔΩ</a> για να δείτε την αρχική σελίδα του νέου σας site.");
define("_INSTALL_L35","Αν συναντήσατε τυχόν σφάλματα, παρακαλείσθε να επικοινωνήσετε με την ομάδα ανάπτυξης στο  <a href='http://xoopscube.org/' target='_blank'>XOOPS Cube.org</a>");
define("_INSTALL_L36","Παρακαλώ επιλέξτε το ψευδώνυμο του διαχειριστή του site που δημιουργήσατε και το κωδικό πρόσβασης αυτού.");
define("_INSTALL_L37","Ψευδώνυμο διαχειριστή");
define("_INSTALL_L38","Email διαχειριστή ");
define("_INSTALL_L39","Κωδικός πρόσβασης διαχειριστή");
define("_INSTALL_L74","Επιβεβαίωση κωδικού πρόσβασης");
define("_INSTALL_L40","Δημιουργία πινάκων");
define("_INSTALL_L41","Παρακαλώ επιστρέψτε στην προηγουμένη σελίδα και εισάγετε όλες τις απαιτούμενες πληροφορίες που σας ζητούνται.");
define("_INSTALL_L42","Πίσω");
define("_INSTALL_L57","Παρακαλώ εισάγετε  %s");

// %s is database name
define("_INSTALL_L43","Database %s created!");

// %s is table name
define("_INSTALL_L44","Unable to make %s");
define("_INSTALL_L45","O πίνακας %s δημιουργήθηκε.");

define("_INSTALL_L46","Για να λειτουργούν σωστά τα modules που περιέχονται στο πακέτο εγκατάστασης θα πρέπει τα προαναφερθέντα αρχεία και φάκελοι να είναι εγγράψιμα από το server. Παρακαλείσθε να αλλάξετε τις ρυθμίσεις των δικαιωμάτων για αυτά τα αρχεία και τους φακέλους, όπου αυτό είναι απαραίτητο. (π.χ. 'chmod 666 όνομα_αρχείου' και 'chmod 777 όνομα_φακέλου' σε UNIX/LINUX server, ή ελέγξτε τις ιδιότητες των αρχείων και σιγουρευτείτε ότι η ρύθμιση ανάγνωση-μόνο δεν είναι επιλεγμένη σε Windows server)");
define("_INSTALL_L47","Επόμενο");

define("_INSTALL_L53","Παρακαλώ επιβεβαιώστε τα ακόλουθα δεδομένα που υποβάλλατε:");

define("_INSTALL_L60","Δεν μπορεί να γραφεί πληροφορία στο αρχείο mainfile.php. Παρακαλώ ελέγξτε τα δικαιώματα του αρχείου και προσπαθήστε ξανά. ");
define("_INSTALL_L61","Δεν επιτρέπεται η εγγραφή στο mainfile.php. Επικοινωνήστε με το διαχειριστή του server για λεπτομέρειες.");
define("_INSTALL_L62","Τα δεδομένα παραμετροποίησης του site σας έχουν αποθηκευτεί με επιτυχία στο αρχείο mainfile.php.");
define("_INSTALL_L72","Οι ακόλουθοι φάκελοι πρέπει να δημιουργηθούν με ενεργοποιημένο το δικαίωμα εγγραφής για το server που τους φιλοξενεί. (π.χ. 'chmod 777 όνομα_φακλεου' σε UNIX/LINUX server)");
define("_INSTALL_L73","Μη έγκυρο email");

// add by haruki
define("_INSTALL_L80","εισαγωγή");
define("_INSTALL_L81","έλεγχος δικαιωμάτων αρχείων και φακέλων");
define("_INSTALL_L82","Γίνεται έλεγχος στα δικαιώματα των αρχείων και των φακέλων…");
define("_INSTALL_L83","Το αρχείο %s ΔΕΝ είναι εγγράψιμο.");
define("_INSTALL_L84","Το αρχείο %s είναι εγγράψιμο.");
define("_INSTALL_L85","Ο φάκελος %s ΔΕΝ είναι εγγράψιμος.");
define("_INSTALL_L86","Ο φάκελος %s είναι εγγράψιμος.");
define("_INSTALL_L87","Δεν παρουσιάστηκε κανένα σφάλμα. Η διαδικασία συνεχίζεται κανονικά.");
define("_INSTALL_L89","γενικές ρυθμίσεις");
define("_INSTALL_L90","Γενικές ρυθμίσεις παραμέτρων εγκατάστασης");
define("_INSTALL_L91","επιβεβαίωση");
define("_INSTALL_L92","αποθήκευση ρυθμίσεων");
define("_INSTALL_L93","τροποποίηση ρυθμίσεων");
define("_INSTALL_L88","Αποθήκευση δεδομένων σύνθεσης...");
define("_INSTALL_L94","έλεγχος διαδρομών (paths) & URL");
define("_INSTALL_L127","Γίνεται έλεγχος των διαδρομών αρχείων (file path) & URL ρυθμίσεων...");
define("_INSTALL_L95","Αδυναμία εντοπισμού της φυσικής διαδρομής (physical path) του xoops Cube φακελου.");
define("_INSTALL_L96","Υπάρχει ασυμφωνία μεταξύ της φυσικής διαδρομής που ανιχνεύτηκε (physical path : %s) και αυτής που δηλώσατε εσείς. ");
define("_INSTALL_L97","Η <b>φυσική διαδρομή (physical path)</b> είναι σωστή.");

define("_INSTALL_L99","<b>Physical path</b> must be a directory.");
define("_INSTALL_L100","Η <b>εικονική διαδρομή (virtual path)</b> είναι μια έγκυρη URL.");
define("_INSTALL_L101","<b>Virtual path</b> is not a valid URL.");
define("_INSTALL_L102","επιβεβαίωση ρυθμίσεων βάσης δεδομένων");
define("_INSTALL_L103","επανεκκίνηση από την αρχή");
define("_INSTALL_L104","έλεγχος των ρυθμίσεων της βάσης δεδομένων ");
define("_INSTALL_L105","προσπάθεια για δημιουργία βάσης δεδομένων");
define("_INSTALL_L106","Δεν είναι εφικτή η σύνδεση με το διακομιστή των βάσεων δεδομένων (MySQL server).");
define("_INSTALL_L107","Σας παρακαλώ να ελέγξετε των διακομιστή των βάσεων δεδομένων (MySQL server) και τις γενικές ρυθμίσεις των παραμέτρων του.");
define("_INSTALL_L108","Η σύνδεση με τον database server είναι OK.");
define("_INSTALL_L109","Η βάση δεδομένων %s δεν υπάρχει.");
define("_INSTALL_L110","Η βάση δεδομένων %s υπάρχει και είναι προσπελάσιμη.");
define("_INSTALL_L111","Η πρόσβαση στη βάση δεδομένων είναι OK.<br />Πιέστε το κουμπί ‘Επόμενο’ για τη δημιουργία των πινάκων που θα αποτελούν τη βάση δεδομένων.");
define("_INSTALL_L112","ρύθμιση παραμέτρων διαχειριστή");
define("_INSTALL_L113","Table %s deleted.");
define("_INSTALL_L114","Failed creating database tables.");
define("_INSTALL_L115","Οι πίνακες της βάσης δεδομένων δημιουργηθήκαν.");
define("_INSTALL_L116","εισαγωγή δεδομένων");
define("_INSTALL_L117","ολοκλήρωση διαδικασίας");

define("_INSTALL_L118","Αποτυχία δημιουργίας του πίνακα %s.");
define("_INSTALL_L119","%d εγγραφές εισάχθηκαν στο πίνακα %s.");
define("_INSTALL_L120","Αποτυχία εισαγωγής %d εγγραφών στο πίνακα %s.");

define("_INSTALL_L121","Η σταθερά %s γράφτηκε στο %s.");
define("_INSTALL_L122","Αποτυχία εγγραφής σταθεράς %s.");

define("_INSTALL_L123","Το αρχείο %s αποθηκεύτηκε στο φάκελο cache/");
define("_INSTALL_L124","Αποτυχία αποθήκευσης του αρχείου %s στο φάκελο cache/");

define("_INSTALL_L125","Το αρχείο %s διαμορφώθηκε σύμφωνα με τα περιεχόμενα του %s");
define("_INSTALL_L126","Δεν είναι δυνατό να γίνει εγγραφή στο αρχείο %s.");

define("_INSTALL_L130","The installer has detected tables for XOOPS 1.3.x in your database.<br />The installer will now attempt to upgrade your database to XOOPS2.");
define("_INSTALL_L131","Tables for XOOPS2 already exist in your database.");
define("_INSTALL_L132","update tables");
define("_INSTALL_L133","Table %s updated.");
define("_INSTALL_L134","Failed updating table %s.");
define("_INSTALL_L135","Failed updating database tables.");
define("_INSTALL_L136","Database tables updated.");
define("_INSTALL_L137","update modules");
define("_INSTALL_L138","update comments");
define("_INSTALL_L139","update avatars");
define("_INSTALL_L140","update smilies");
define("_INSTALL_L141","The installer will now update each module to work with XOOPS Cube.<br />Make sure that you have uploaded all files in XOOPS Cube package to your server.<br />This may take a while to complete.");
define("_INSTALL_L142","Updating modules..");
define("_INSTALL_L143","The installer will now update configuration data of XOOPS 1.3.x to be used with XOOPS Cube.");
define("_INSTALL_L144","update config");
define("_INSTALL_L145","Comment (ID: %s) inserted to the database.");
define("_INSTALL_L146","Could not insert comment (ID: %s) to the database.");
define("_INSTALL_L147","Updating comments..");
define("_INSTALL_L148","Update complete.");
define("_INSTALL_L149","The installer will now update comment posts in XOOPS 1.3.x to be used in XOOPS Cube.<br />This may take a while to complete.");
define("_INSTALL_L150","The installer will now update the smiley and user rank images to be used with XOOPS Cube.<br />This may take a while to complete.");
define("_INSTALL_L151","The installer will now update the user avatar images to be used in XOOPS Cube.<br />This may take a while to complete.");
define("_INSTALL_L155","Updating smiley/rank images..");
define("_INSTALL_L156","Updating user avatar images..");
define("_INSTALL_L157","Select the default user group for each group type");
define("_INSTALL_L158","Groups in 1.3.x");
define("_INSTALL_L159","Webmasters");
define("_INSTALL_L160","Register Users");
define("_INSTALL_L161","Anonymous Users");
define("_INSTALL_L162","You must select a default group for each group type.");
define("_INSTALL_L163","Table %s dropped.");
define("_INSTALL_L164","Failed deleting table %s.");
define("_INSTALL_L165","Ο δικτυακός τόπος είναι προσωρινά μη διαθέσιμος λόγω εκτέλεσης εργασιών αναβάθμισης/συντήρησης. Παρακαλώ επισκεφτείτε μας σε λίγο.");

// %s is filename
define("_INSTALL_L152","Αδυναμία ανοίγματος %s.");
define("_INSTALL_L153","Αδυναμία ενημέρωσης (update) %s.");
define("_INSTALL_L154","%s ενημερώθηκε (updated).");

define('_INSTALL_L128', 'Επιλέξτε τη γλώσσα που επιθυμείτε να χρησιμοποιηθεί στη διαδικασία εγκατάστασης. ');
define('_INSTALL_L200', 'Ανανέωση');
define("_INSTALL_L210","To 2o στάδιο εγκατάστασης ");


define('_INSTALL_CHARSET','UTF-8');

define('_INSTALL_LANG_XOOPS_SALT', "SALT");
define('_INSTALL_LANG_XOOPS_SALT_DESC', "Αυτό παίζει συμπληρωματικό ρόλο στη δημιουργία ενός μυστικού κωδικού και ενός κουπονιού. Δεν χρειάζεται να αλλάξετε την προτεινομένη τιμή. ");
define('_INSTALL_HEADER_MESSAGE','Παρακαλείσθε να ακολουθήσετε με προσοχή τις οδηγίες που αναγράφονται στις σελίδες που ακολουθούν κατά τη διάρκεια της εγκατάστασης.');
?>
