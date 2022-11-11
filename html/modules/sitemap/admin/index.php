<?php
/**
 * Sitemap
 * Automated Sitemap and XML file for search engines
 * @package    Sitemap
 * @version    2.3.1
 * @author     Other authors, gigamaster 2020 XCL/PHP7
 * @author     chanoir
 * @copyright  Copyright 2005-2022 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/docs/GPL_V2.txt
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

redirect_header(XOOPS_MODULE_URL . '/legacy/admin/index.php?action=Help&amp;dirname=sitemap', 1, _MI_SITEMAP_NAME);

require_once XOOPS_ROOT_PATH . '/footer.php';
