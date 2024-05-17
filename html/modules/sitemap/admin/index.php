<?php
/**
 * Sitemap
 * Automated Sitemap and XML file for search engines
 * @package    Sitemap
 * @version    2.4.0
 * @author     gigamaster 2020 XCL/PHP7
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

$root =& XCube_Root::getSingleton();
$root->mController->execute();

// get config
$module_handler  =& xoops_gethandler('module');
$module          =& $module_handler->getByDirname('sitemap');
$config_handler  =& xoops_gethandler('config');
$sitemap_configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

// module id
$name = $xoopsModule->getVar( 'name' );
$mid  = $xoopsModule->getVar('mid');

// nav
$dash = XOOPS_URL . '/admin.php';
$pref = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&amp;confmod_id='.$mid;
$help = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=Help&amp;dirname=sitemap';

$myts =& MyTextSanitizer::getInstance();

// options map & address
if($sitemap_configs['show_sitename'] == 1) {
	$show_sitename = $myts->makeTboxData4Show($xoopsConfig['sitename']);
}
if($sitemap_configs['show_siteslogan'] == 1) {
	$show_slogan   = $myts->makeTboxData4Show($xoopsConfig['slogan']);
} 
if($sitemap_configs['show_site_map'] == 1) {
	$show_map   = $myts->displayTarea( $sitemap_configs['show_map'] , 1 ) ;
} 
if($sitemap_configs['show_site_address'] == 1) {
	$show_address   = $myts->displayTarea( $sitemap_configs['show_address'] , 1 ) ;
} 

// RENDER XML
$url = XOOPS_URL.'/xml_sitemap.php';

// ssl
$opts=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);
// stream local    
$local = stream_context_create($opts);
// contents
$file = file_get_contents($url, false, $local);

// HTML decode
$decode = html_entity_decode($file);

// DOM
$dom = new DOMDocument;
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($decode); // <-- raw xml to format

// Human readable format
$human = '<pre style="max-height:40vh"><code class="lang-html">'. $dom->saveXML().'</code></pre>';
// Machine format
$machine = '<pre style="max-height:40vh"><code class="lang-html">'. htmlentities($dom->saveXML()).'</code></pre>';

?>
<style>
.resize { 
resize: both;
    background: var(--layer-3);
    border: 1px solid var(--layer-3);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-2);
    border: 7px solid rgb(54 61 73 / 70%);
    display: block;
    overflow: auto;
    margin: 1em auto;
    width: 70vw;
    min-width: 520px;
    max-width: 70vw;
    min-height: 420px;
    max-height: 640px;
}
.contain-sitemap {
    padding: 1rem 2rem;
    grid-column: 1 / 7;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    align-items: center;
}
.contain-sitemap h2 {font-size:2rem}
.contain-sitemap .map iframe {border-radius:8px}
</style>

<nav class="ui-breadcrumbs" aria-label="breadcrumb">
    <a href="<?php echo $dash ?>"><?php echo _CPHOME ?></a>
    »» <span class="page-title" aria-current="page"><?php echo ucfirst($name); ?></span>
</nav>

<nav class="adminavi">
    <a href="admin/index.php" class="adminavi-item"><?php echo _MI_SITEMAP_ADMENU_OVERVIEW ?></a>
    <a href="<?php echo $pref ?>" class="adminavi-item"><?php echo _PREFERENCES ?></a>
    <a href="<?php echo $help ?>" class="adminavi-item"><?php echo _HELP ?></a>
</nav>

<h2><?php echo ucfirst($name); ?></h2>
<p><?php echo _AD_SITEMAP_PREVIEW_MAP ?></p>
<hr>
<div class="resize">
    <div class="contain-sitemap">
        <div>
        <h2><?php echo $show_sitename ?? ''; ?></h2>
        <h4><?php echo $show_slogan ?? ''; ?></h4>
        </div>
        <div class="map">
        <?php echo $show_map ?? ''; ?>
        <br>
        <?php echo $show_address ?? ''; ?>
        </div>
    </div>
</div>
<hr>
<h3><?php echo _AD_SITEMAP_PREVIEW_GEN ?></h3>
<p><?php echo _AD_SITEMAP_PREVIEW_XML ?></p>

<div data-layout="row sm-column">
<div data-self="column size-1of2 sm-full">
        <h3><?php echo _AD_SITEMAP_PREVIEW_MAC ?></h3>
        <?php echo $machine ?? ''; ?>
    </div>
    <div data-self="column size-1of2 sm-full">
        <h3><?php echo _AD_SITEMAP_PREVIEW_HUM ?></h3>
        <?php echo $human ?? ''; ?>
    </div>
</div>

<?php

require_once XOOPS_ROOT_PATH . '/footer.php';
