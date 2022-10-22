<?php
$REFERER = $_SERVER['HTTP_REFERER'];
if(!preg_match("@^http:\/\/(www\.)?$domain\/@",$REFERER)){
    die("This page can't be call directly");
}
echo 'Theme Options</h1>
    <h4>Not available</h4>';

?>
