<?php
/**
 * Image
 * Disable image content invalid rewriting by mbstring
 * @package    Legacy
 * @version    2.3.1
 * @author     Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000 Authors
 * @license    GPL 2.0
 */

if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}

$image_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($image_id > 0) {
    include './mainfile.php';
    $imagehandler = & xoops_gethandler('image');
    $criteria = new CriteriaCompo(new Criteria('i.image_display', 1));
    $criteria->add(new Criteria('i.image_id', $image_id));
    $image = & $imagehandler->getObjects($criteria, false, true);
    if (count($image) > 0) {
        header('Content-type: '.$image[0]->getVar('image_mimetype'));
        header('Cache-control: max-age=31536000');
        header('Expires: '.gmdate('D, d M Y H:i:s', time() + 31536000) . 'GMT');
        header('Content-disposition: filename='.$image[0]->getVar('image_name'));
        header('Content-Length: '.strlen($image[0]->getVar('image_body')));
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $image[0]->getVar('image_created')) . 'GMT');
        echo $image[0]->getVar('image_body');
        exit();
    }
}
header('Content-type: image/gif');
readfile('./images/blank.gif');
