<?php
/**
 * Sitemap
 * Automated Sitemap and XML file for search engines
 * @package    Sitemap
 * @version    2.4.0
 * @author     Gigamaster, 2020 XCL PHP7
 * @author     chanoir
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL V2.0
 */

// Manifesto
$modversion['dirname']          = 'sitemap';
$modversion['name']             = _MI_SITEMAP_NAME;
$modversion['version']          = '2.40';
$modversion['detailed_version'] = '2.40.0';
$modversion['description']      = _MI_SITEMAP_DESC;
$modversion['author']           = 'chanoir';
$modversion['credits']          = 'The XOOPSCube Project';
$modversion['license']          = 'GPL LICENSE';
$modversion['image']            = 'images/module_sitemap.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['official']         = 0;
$modversion['cube_style']       = true;

// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Menu
$modversion['hasMain'] = 1;

// Templates
$modversion['templates'][1]['file'] = 'sitemap_inc_eachmodule.html';
$modversion['templates'][1]['description'] = 'Site map for each module';
$modversion['templates'][2]['file'] = 'sitemap_index.html';
$modversion['templates'][2]['description'] = 'Sitemap template visible to users';
$modversion['templates'][3]['file'] = 'xml_sitemap.html';
$modversion['templates'][3]['description'] = 'Site map for humans, search engines and crawlers';

// BLocks
$modversion['blocks'][1]['file'] = 'sitemap_blocks.php';
$modversion['blocks'][1]['name'] = _MI_BLOCK_BLOCKNAME ;
$modversion['blocks'][1]['description'] = _MI_BLOCK_BLOCKNAME_DESC ;
$modversion['blocks'][1]['show_func'] = 'b_sitemap_show';
$modversion['blocks'][1]['edit_func'] = 'b_sitemap_edit';
$modversion['blocks'][1]['template'] = 'sitemap_block_show.html';
$modversion['blocks'][1]['options'] = '1|0|0';

$modversion['blocks'][2]['file'] = 'sitemap_block_map.php';
$modversion['blocks'][2]['name'] = _MI_BLOCK_MAP;
$modversion['blocks'][2]['description'] = _MI_BLOCK_MAP_DESC ;
$modversion['blocks'][2]['show_func'] = 'b_sitemap_map_show';
$modversion['blocks'][2]['edit_func'] = 'b_sitemap_map_edit';
$modversion['blocks'][2]['template'] = 'sitemap_block_map.html';
$modversion['blocks'][2]['options'] = '1|1|1|1';
$modversion['blocks'][2]['show_all_module'] = true;

// Preference
$modversion['config'][1]['name'] = 'msgs';
$modversion['config'][1]['title'] = _MI_MESSAGE;
$modversion['config'][1]['description'] = '_MI_MESSAGEEDSC';
$modversion['config'][1]['formtype'] = 'textarea';
$modversion['config'][1]['valuetype'] = 'text';
$modversion['config'][1]['default'] = _MI_SITEMAP_MESSAGE;

$modversion['config'][2]['name'] = 'show_subcategoris';
$modversion['config'][2]['title'] = '_MI_SHOW_SUBCATEGORIES';
$modversion['config'][2]['description'] = '_MI_SHOW_SUBCATEGORIESDSC';
$modversion['config'][2]['formtype'] = 'yesno';
$modversion['config'][2]['valuetype'] = 'int';
$modversion['config'][2]['default'] = 1;

$modversion['config'][3]['name'] = 'alltime_guest';
$modversion['config'][3]['title'] = '_MI_ALLTIME_GUEST';
$modversion['config'][3]['description'] = '_MI_ALLTIME_GUESTDSC';
$modversion['config'][3]['formtype'] = 'yesno';
$modversion['config'][3]['valuetype'] = 'int';
$modversion['config'][3]['default'] = 0;

$modversion['config'][4]['name'] = 'invisible_weights';
$modversion['config'][4]['title'] = '_MI_INVISIBLE_WEIGHTS';
$modversion['config'][4]['description'] = '_MI_INVISIBLE_WEIGHTSDSC';
$modversion['config'][4]['formtype'] = 'text';
$modversion['config'][4]['valuetype'] = 'text';
$modversion['config'][4]['default'] = '0';

$modversion['config'][5]['name'] = 'invisible_dirnames';
$modversion['config'][5]['title'] = '_MI_INVISIBLE_DIRNAMES';
$modversion['config'][5]['description'] = '_MI_INVISIBLE_DIRNAMESDSC';
$modversion['config'][5]['formtype'] = 'text';
$modversion['config'][5]['valuetype'] = 'text';
$modversion['config'][5]['default'] = '0';

// options
$modversion['config'][6]['name'] = 'show_sitename';
$modversion['config'][6]['title'] = '_MI_SHOW_SITENAME';
$modversion['config'][6]['description'] = '_MI_SHOW_SITENAME_DESC';
$modversion['config'][6]['formtype'] = 'yesno';
$modversion['config'][6]['valuetype'] = 'int';
$modversion['config'][6]['default'] = 1;

$modversion['config'][7]['name'] = 'show_siteslogan';
$modversion['config'][7]['title'] = '_MI_SHOW_SLOGAN';
$modversion['config'][7]['description'] = '_MI_SHOW_SLOGAN_DESC';
$modversion['config'][7]['formtype'] = 'yesno';
$modversion['config'][7]['valuetype'] = 'int';
$modversion['config'][7]['default'] = 1;

$modversion['config'][8]['name'] = 'show_site_map';
$modversion['config'][8]['title'] = '_MI_SHOW_MAP';
$modversion['config'][8]['description'] = '_MI_SHOW_MAP_DESC';
$modversion['config'][8]['formtype'] = 'yesno';
$modversion['config'][8]['valuetype'] = 'int';
$modversion['config'][8]['default'] = 1;

$modversion['config'][9]['name'] = 'show_map';
$modversion['config'][9]['title'] = '_MI_SHOW_MAP_CODE';
$modversion['config'][9]['description'] = '_MI_SHOW_MAP_CODE_DESC';
$modversion['config'][9]['formtype'] = 'textarea';
$modversion['config'][9]['valuetype'] = 'text';
$modversion['config'][9]['default'] = '<iframe width="100%" height="240" src="https://www.openstreetmap.org/export/embed.html?bbox=6.135316727231666%2C46.221972596685276%2C6.1464532590615%2C46.2277918578891&amp;layer=mapnik" style="border: 1px solid black"></iframe><br/><small><a href="https://www.openstreetmap.org/#map=17/46.22488/6.14088">Afficher une carte plus grande</a></small>';

$modversion['config'][10]['name'] = 'show_site_address';
$modversion['config'][10]['title'] = '_MI_SHOW_ADDRESS';
$modversion['config'][10]['description'] = '_MI_SHOW_ADDRESS_DESC';
$modversion['config'][10]['formtype'] = 'yesno';
$modversion['config'][10]['valuetype'] = 'int';
$modversion['config'][10]['default'] = 1;

$modversion['config'][11]['name'] = 'show_address';
$modversion['config'][11]['title'] = '_MI_SHOW_ADDRESS_CODE';
$modversion['config'][11]['description'] = '_MI_SHOW_ADDRESS_CODE_DESC';
$modversion['config'][11]['formtype'] = 'textarea';
$modversion['config'][11]['valuetype'] = 'text';
$modversion['config'][11]['default'] = '<address>Avenue de la Paix 8-14 · 1202 Genève, Suisse</address><p><small>Tel. <a href="tel:+41229171234">+41 22 917 12 34</a> ----- <a href="https://www.ungeneva.org/en/visit" target="_blank">Visiting UN Geneva</a></small></p>';