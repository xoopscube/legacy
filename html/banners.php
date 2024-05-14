<?php
/**
 * Client Banners
 * Function to let your client login to see the stats
 * @package    Legacy
 * @version    2.3.1
 * @author     Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000 Authors
 * @license    GPL 2.0
 */

include 'mainfile.php';
/**
 * Banner advertisement, otherwise known as an advert or ad, is generally considered a public communication
 * that promotes a product, service, brand or event. To some the definition can be even broader than that,
 * extending to any paid communication designed to inform or influence.
 * Control Panel »» Dashboard »» Renderer »» Banner Management
 * 1. Create client account
 * 2. Create client banner
 */
function clientlogin()
{
    global $xoopsDB, $xoopsLogger, $xoopsConfig;
    include('header.php');
    echo "<style>
                .redirect {width: 70%;margin: 110px;text-align: center;padding: 15px;text-align:center;text-align: center;}
                .redirect a:link {text-decoration: none;font-weight: bold;}
                .redirect a:visited {text-decoration: none;font-weight: bold;}
                .redirect a:hover {text-decoration: underline;font-weight: bold;}
        </style>

    <form action='banners.php' method='post'>
    <article>
    <header>
    <div class='headings'>
    <h3>Advertising Statistics</h3>
    <p>Please type your client information</p>
    </div>
    </header>
    <label>Login
    <input class='textbox' type='text' name='login' size='12' maxlength='10'>
    </label>
    <label>Password
    <input class='textbox' type='password' name='pass' size='12' maxlength='10'>
    </label>
    <input type='hidden' name='op' value='Ok'>";
    $token =& XoopsMultiTokenHandler::quickCreate('banner_Ok');
    echo $token->getHtml();
    echo "
    <footer><input type='submit' value='Login'></footer>
    </article></form>";
    include 'footer.php';
}

/*********************************************/
/* Function to display the banners stats for */
/* each client                               */
/*********************************************/
function bannerstats($login, $pass)
{
    global $xoopsDB, $xoopsConfig, $xoopsLogger;
    if ('' == $login || '' == $pass) {
        redirect_header('banners.php', 2);
        exit();
    }

    // Sanitize Textarea HTML
    $myts =& MyTextSanitizer::getInstance();

    $result = $xoopsDB->query(sprintf('SELECT cid, name, passwd FROM %s WHERE login=%s', $xoopsDB->prefix('bannerclient'), $xoopsDB->quoteString($login)));
    list($cid, $name, $passwd) = $xoopsDB->fetchRow($result);
    if ($pass==$passwd) {
        include 'header.php';
        echo "
            <h4>Current Active Banners for $name</h4>
            <table>
            <thead>
            <tr class='list_center'>
                <th class='list_id'>ID</th>
                <th class='list_center'>Imp. Made</th>
                <th class='b_td'>Imp. Total</th>
                <th class='b_td'><b>Imp. Left</th>
                <th class='b_td'><b>Clicks</th>
                <th class='b_td'><b>% Clicks</th>
                <th class='list-action'>Action</th>
            </tr></thead>";
        $result = $xoopsDB->query('select bid, imptotal, impmade, clicks, date from ' . $xoopsDB->prefix('banner') . " where cid=$cid");
        while (list($bid, $imptotal, $impmade, $clicks, $date) = $xoopsDB->fetchRow($result)) {
            if (0 == $impmade) {
                $percent = 0;
            } else {
                $percent = substr(100 * $clicks / $impmade, 0, 5);
            }
            if (0 == $imptotal) {
                $left = 'Unlimited';
            } else {
                $left = $imptotal-$impmade;
            }
            $token =& XoopsMultiTokenHandler::quickCreate('banner_EmailStats');
            echo "<tr class='list_center'>
                <td>$bid</td>
                <td>$impmade</td>
                <td>$imptotal</td>
                <td>$left</td>
                <td>$clicks</td>
                <td>$percent%</td>
                <td><a href='banners.php?op=EmailStats&amp;login=$login&amp;pass=$pass&amp;cid=$cid&amp;bid=$bid&amp;".$token->getUrl()."'>E-mail Stats</a></td>
                </tr>";
        }
        echo '</table>'
            .'<hr><div>Following are your running Banners in ' . htmlspecialchars($xoopsConfig['sitename']) . ' </div>';

        $result = $xoopsDB->query('select bid, imageurl, clickurl, htmlbanner, htmlcode from ' . $xoopsDB->prefix('banner') . " where cid=$cid");
        while (list($bid, $imageurl, $clickurl, $htmlbanner, $htmlcode) = $xoopsDB->fetchRow($result)) {
            $numrows = $xoopsDB->getRowsNum($result);
            if ($numrows>1) {
                echo '<hr>';
            }
            if (!empty($htmlbanner) && !empty($htmlcode)) {
                // Sanitize Textarea HTML
                $bannerHtml = $myts->displayTarea( $htmlcode );
                echo "<pre><code>".$bannerHtml."</code></pre>";

            } else {
                if ('.swf' == strtolower(substr($imageurl, strrpos($imageurl, '.')))) {
                    echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/ swflash.cab#version=6,0,40,0"; width="468" height="60">';
                    echo "<param name=movie value=\"$imageurl\" />";
                    echo "<param name=quality value='high' />";
                    echo "<embed src=\"$imageurl\" quality='high' pluginspage=\"https://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\"; type=\"application/x-shockwave-flash\" width=\"468\" height=\"60\">";
                    echo '</embed>';
                    echo '</object>';
                } else {
                    echo "<img src='$imageurl' border='1' alt=''>";
                }
            }
            $token =& XoopsMultiTokenHandler::quickCreate('banner_EmailStats');
            echo"Banner ID: $bid<br>
                Send <a href='banners.php?op=EmailStats&amp;login=$login&amp;cid=$cid&amp;bid=$bid&amp;pass=$pass&amp;".$token->getUrl()."'>E-Mail Stats</a> for this Banner<br>";
            if (!$htmlbanner) {
                $token =& XoopsMultiTokenHandler::quickCreate('banner_Change');
                $clickurl = htmlspecialchars($clickurl, ENT_QUOTES);
                echo "This Banner points to <a href='$clickurl'>this URL</a><br>
                    <form action='banners.php' method='post'>
                    Change URL: <input class='textbox' type='text' name='url' size='50' maxlength='200' value='$clickurl'>
                    <input class='textbox' type='hidden' name='login' value='$login'>
                    <input class='textbox' type='hidden' name='bid' value='$bid'>
                    <input class='textbox' type='hidden' name='pass' value='$pass'>
                    <input class='textbox' type='hidden' name='cid' value='$cid'>
                    <input type='submit' name='op' value='Change'>";
                echo $token->getHtml();
                echo '</form>';
            }
        }

            /* Finnished Banners */
            echo '<br>';
        if (!$result = $xoopsDB->query('select bid, impressions, clicks, datestart, dateend from ' . $xoopsDB->prefix('bannerfinish') . " where cid=$cid")) {
            echo "<h4 style='text-align:center;'>Banners Finished for $name</h4><br>
            <table><tr>
            <td>ID</td>
            <td>Impressions</td>
            <td>Clicks</td>
            <td>% Clicks</td>
            <td>Start Date</td>
            <td>End Date</td></tr>";
            while (list($bid, $impressions, $clicks, $datestart, $dateend) = $xoopsDB->fetchRow($result)) {
                $percent = substr(100 * $clicks / $impressions, 0, 5);
                echo "<tr>
                <td>$bid</td>
                <td>$impressions</td>
                <td>$clicks</td>
                <td>$percent%</td>
                <td>".formatTimestamp($datestart)."</td>
                <td>".formatTimestamp($dateend) . '</td></tr>';
            }
            echo '</table>';
        }
        include 'footer.php';
    } else {
        redirect_header('banners.php', 2);
        exit();
    }
}

/*********************************************/
/* Function to let the client E-mail his     */
/* banner Stats                              */
/*********************************************/
function EmailStats($login, $cid, $bid, $pass)
{
    global $xoopsDB, $xoopsConfig;
    if ('' != $login && '' != $pass) {
        $cid = (int)$cid;
        $bid = (int)$bid;
        if ($result2 = $xoopsDB->query(sprintf('select name, email, passwd from %s where cid=%u AND login=%s', $xoopsDB->prefix('bannerclient'), $cid, $xoopsDB->quoteString($login)))) {
            list($name, $email, $passwd) = $xoopsDB->fetchRow($result2);
            if ($pass == $passwd) {
                if ('' == $email) {
                    redirect_header('banners.php', 2, "There isn't an email associated with client " . $name . '.<br>Please contact the Administrator');
                    exit();
                } else {
                    if ($result = $xoopsDB->query('select bid, imptotal, impmade, clicks, imageurl, clickurl, date from ' . $xoopsDB->prefix('banner') . " where bid=$bid and cid=$cid")) {
                        list($bid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date) = $xoopsDB->fetchRow($result);
                        if (0 == $impmade) {
                            $percent = 0;
                        } else {
                            $percent = substr(100 * $clicks / $impmade, 0, 5);
                        }
                        if (0 == $imptotal) {
                            $left = 'Unlimited';
                            $imptotal = 'Unlimited';
                        } else {
                            $left = $imptotal-$impmade;
                        }
                        $fecha = date('F jS Y, h:iA.');
                        $subject = 'Your Banner Statistics at ' . $xoopsConfig['sitename'];
                        $message = 'Following are the complete stats for your advertising investment at '
                                   . $xoopsConfig['sitename'] . " :\n\n\nClient Name: $name\nBanner ID: $bid\nBanner Image: $imageurl\nBanner URL: $clickurl\n\nImpressions Purchased: $imptotal\nImpressions Made: $impmade\nImpressions Left: $left\nClicks Received: $clicks\nClicks Percent: $percent%\n\n\nReport Generated on: $fecha";
                        $xoopsMailer =& getMailer();
                        $xoopsMailer->useMail();
                        $xoopsMailer->setToEmails($email);
                        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                        $xoopsMailer->setFromName($xoopsConfig['sitename']);
                        $xoopsMailer->setSubject($subject);
                        $xoopsMailer->setBody($message);
                        $xoopsMailer->send();
                        $token =& XoopsMultiTokenHandler::quickCreate('banner_Ok');
                        redirect_header("banners.php?op=Ok&amp;login=$login&amp;pass=$pass&amp;".$token->getUrl(), 2, 'Statistics for your banner has been sent to your email address.');
                        exit();
                    }
                }
            }
        }
    }
    redirect_header('banners.php', 2);
    exit();
}

/*********************************************/
/* Function to let the client change the     */
/* url for his banner                        */
/*********************************************/
function change_banner_url_by_client($login, $pass, $cid, $bid, $url)
{
    global $xoopsDB;
    if ('' != $login && '' != $pass && '' != $url) {
        $cid = (int)$cid;
        $bid = (int)$bid;
        $sql = sprintf('select passwd from %s where cid=%u and login=%s', $xoopsDB->prefix('bannerclient'), $cid, $xoopsDB->quoteString($login));
        if ($result = $xoopsDB->query($sql)) {
            list($passwd) = $xoopsDB->fetchRow($result);
            if ($pass == $passwd) {
                $sql = sprintf('update %s set clickurl=%s where bid=%u AND cid=%u', $xoopsDB->prefix('banner'), $xoopsDB->quoteString($url), $bid, $cid);
                if ($xoopsDB->query($sql)) {
                    $token =& XoopsMultiTokenHandler::quickCreate('banner_Ok');
                    redirect_header("banners.php?op=Ok&amp;login=$login&amp;pass=$pass&amp;".$token->getUrl(), 2, 'URL has been changed.');
                    exit();
                }
            }
        }
    }
    redirect_header('banners.php', 2);
    exit();
}

function clickbanner($bid)
{
    global $xoopsDB;
    if (is_int($bid) && $bid > 0) {
        if (xoops_refcheck()) {
            if ($bresult = $xoopsDB->query('select clickurl from ' . $xoopsDB->prefix('banner') . " where bid=$bid")) {
                list($clickurl) = $xoopsDB->fetchRow($bresult);
                $xoopsDB->queryF('update ' . $xoopsDB->prefix('banner') . " set clicks=clicks+1 where bid=$bid");
                header('Location: '.$clickurl);
            }
        }
    }
    exit();
}

$op = '';
if (!empty($_POST['op'])) {
    $op = $_POST['op'];
} elseif (!empty($_GET['op'])) {
    $op = $_GET['op'];
}

$myts =& MyTextSanitizer::sGetInstance();

switch ($op) {
    case 'click':
        $bid = 0;
        if (!empty($_GET['bid'])) {
            $bid = (int)$_GET['bid'];
        }
        clickbanner($bid);
        break;
    case 'login':
        clientlogin();
        break;
    case 'Ok':
        if (!XoopsMultiTokenHandler::quickValidate('banner_Ok')) {
            redirect_header('banners.php');
            exit();
        }
        $login = $pass = '';
        if (!empty($_GET['login'])) {
            $login = $myts->stripslashesGPC(trim($_GET['login']));
        }
        if (!empty($_GET['pass'])) {
            $pass = $myts->stripslashesGPC(trim($_GET['pass']));
        }
        if (!empty($_POST['login'])) {
            $login = $myts->stripslashesGPC(trim($_POST['login']));
        }
        if (!empty($_POST['pass'])) {
            $pass = $myts->stripslashesGPC(trim($_POST['pass']));
        }
        bannerstats($login, $pass);
        break;
    case 'Change':
        if (!XoopsMultiTokenHandler::quickValidate('banner_Change')) {
            redirect_header('banners.php');
            exit();
        }
        $login = $pass = $url = '';
        $bid = $cid = 0;
        if (!empty($_POST['login'])) {
            $login = $myts->stripslashesGPC(trim($_POST['login']));
        }
        if (!empty($_POST['pass'])) {
            $pass = $myts->stripslashesGPC(trim($_POST['pass']));
        }
        if (!empty($_POST['url'])) {
            $url = $myts->stripslashesGPC(trim($_POST['url']));
        }
        if (!empty($_POST['bid'])) {
            $bid = (int)$_POST['bid'];
        }
        if (!empty($_POST['cid'])) {
            $cid = (int)$_POST['cid'];
        }
        change_banner_url_by_client($login, $pass, $cid, $bid, $url);
        break;
    case 'EmailStats':
        if (!XoopsMultiTokenHandler::quickValidate('banner_EmailStats')) {
            redirect_header('banners.php');
            exit();
        }
        $login = $pass = '';
        $bid = $cid = 0;
        if (!empty($_GET['login'])) {
            $login = $myts->stripslashesGPC(trim($_GET['login']));
        }
        if (!empty($_GET['pass'])) {
            $pass = $myts->stripslashesGPC(trim($_GET['pass']));
        }
        if (!empty($_GET['bid'])) {
            $bid = (int)$_GET['bid'];
        }
        if (!empty($_GET['cid'])) {
            $cid = (int)$_GET['cid'];
        }
        EmailStats($login, $cid, $bid, $pass);
        break;
    default:
        clientlogin();
        break;
}
