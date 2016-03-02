<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_TextFilter.class.php,v 1.3 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

/**
 *
 * @final
 */
class XCube_TextFilter
{
    public $mDummy=null;  //Dummy member for preventing object be treated as empty.

    public function getInstance(&$instance)
    {
        if (empty($instance)) {
            $instance = new XCube_TextFilter();
        }
    }
    
    public function toShow($str)
    {
        return htmlspecialchars($str, ENT_QUOTES);
    }

    public function toEdit($str)
    {
        return htmlspecialchars($str, ENT_QUOTES);
    }
}
