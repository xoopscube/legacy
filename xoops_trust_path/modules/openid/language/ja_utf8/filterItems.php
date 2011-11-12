<?php
/**
 * Sample of white list
 * @version $Rev$
 * @link $URL$
 */
$filterItems = array();

$filterItems[] = array(
    'description' => 'mixi',
    'pattern' => 'https://mixi.jp/openid_server.pl',
    'image' => 'icons/mixi.gif',
    'op_identifier' => 'https://mixi.jp'
);

$filterItems[] = array(
    'description' => 'Yahoo! JAPAN',
    'pattern' => 'https://open.login.yahooapis.jp/openid/op/auth',
    'image' => 'icons/yahoo.gif',
    'op_identifier' => 'yahoo.co.jp'
);

$filterItems[] = array(
    'description' => 'livedoor',
    'pattern' => 'http://auth.livedoor.com/openid/server',
    'image' => 'icons/livedoor.gif',
    'op_identifier' => 'http://livedoor.com/'
);

$filterItems[] = array(
    'description' => 'BIGLOBE',
    'pattern' => 'https://openid.biglobe.ne.jp/cgi-bin/endpoint',
    'image' => 'icons/biglobe.gif',
    'op_identifier' => 'biglobe.ne.jp'
);

$filterItems[] = array(
    'description' => 'エキサイト',
    'pattern' => 'https://openid.excite.co.jp/in/chk',
    'image' => 'icons/excite.gif',
    'op_identifier' => 'https://excite.co.jp'
);

$filterItems[] = array(
    'description' => 'はてな',
    'pattern' => 'http://www.hatena.ne.jp/openid/server',
    'image' => 'icons/hatena.gif',
    'user_identifier' => 'http://www.hatena.ne.jp/hatena_id/',
    'range' => '24,9'
);

$filterItems[] = array(
    'description' => 'JugemKey',
    'pattern' => 'https://secure.jugemkey.jp/openid/server.php',
    'image' => 'icons/jugemkey.gif',
    'user_identifier' => 'http://profile.jugemkey.jp/your_jugemkey',
    'range' => '27,13'
);

$filterItems[] = array(
    'description' => 'Blogger',
    'pattern' => 'http://www.blogger.com/openid-server.g',
    'image' => 'icons/blogger.gif',
    'user_identifier' => 'http://blogname.blogspot.com/',
    'range' => '7,8'
);

$filterItems[] = array(
    'description' => 'Google',
    'pattern' => 'https://www.google.com/accounts/o8/ud',
    'image' => 'icons/google.gif',
    'op_identifier' => 'https://www.google.com/accounts/o8/id'
);

$filterItems[] = array(
    'description' => 'TypePad',
    'pattern' => 'https://www.typepad.com/secure/services/openid/profilesserver/',
    'image' => 'icons/typepad.gif',
    'user_identifier' => 'http://profile.typekey.com/username',
    'range' => '27,8'
);

$filterItems[] = array(
    'description' => 'docomo ID',
    'pattern' => 'https://i.mydocomo.com/oid/auth',
    'image' => 'icons/docomo.gif',
    'op_identifier' => 'https://i.mydocomo.com',
);
?>