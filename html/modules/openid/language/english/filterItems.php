<?php
/**
 * Sample of white list
 * @version $Rev$
 * @link $URL$
 */
$filterItems = array();

$filterItems[] = array(
    'description' => 'AOL/AIM',
    'pattern' => 'https://api.screenname.aol.com/auth/openidServer',
);

$filterItems[] = array(
    'description' => 'Blogger',
    'pattern' => 'http://www.blogger.com/openid-server.g',
);

$filterItems[] = array(
    'description' => 'Google',
    'pattern' => 'https://www.google.com/accounts/o8/ud',
);

$filterItems[] = array(
    'description' => 'LiveJournal',
    'pattern' => 'http://www.livejournal.com/openid/server.bml',
);

$filterItems[] = array(
    'description' => 'TypePad',
    'pattern' => 'https://www.typepad.com/secure/services/openid/profilesserver/',
);

$filterItems[] = array(
    'description' => 'Vox',
    'pattern' => 'http://www.vox.com/services/openid/server',
);

$filterItems[] = array(
    'description' => 'wordpress.com',
    'pattern' => 'http://wordpress.com/?openidserver=1',
);

$filterItems[] = array(
    'description' => 'Yahoo!',
    'pattern' => 'https://open.login.yahooapis.com/openid/op/auth',
);
?>