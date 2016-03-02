<?php
/**
 *
 * @package Legacy
 * @version $Id: SearchResultsForm.class.php,v 1.3 2008/09/25 15:12:40 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class Legacy_SearchResultsForm extends XCube_ActionForm
{
    public $mQueries = array();
    public $_mKeywordMin = 0;
    
    public function Legacy_SearchResultsForm($keywordMin)
    {
        parent::XCube_ActionForm();
        $this->_mKeywordMin = intval($keywordMin);
    }
        
    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['mids'] =new XCube_IntArrayProperty('mids');
        $this->mFormProperties['andor'] =new XCube_StringProperty('andor');
        $this->mFormProperties['query'] =new XCube_StringProperty('query');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['andor'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['andor']->setDependsByArray(array('mask'));
        $this->mFieldProperties['andor']->addMessage('mask', _MD_LEGACY_ERROR_MASK, _MD_LEGACY_LANG_ANDOR);
        $this->mFieldProperties['andor']->addVar('mask', '/^(AND|OR|exact)$/i');
    }
    
    public function fetch()
    {
        parent::fetch();
        
        $t_queries = array();
        
        $myts =& MyTextSanitizer::sGetInstance();
        if ($this->get('andor') == 'exact' && strlen($this->get('query')) >= $this->_mKeywordMin) {
            $this->mQueries[] = $myts->addSlashes($this->get('query'));
        } else {
            $query = $this->get('query');
            if (defined('XOOPS_USE_MULTIBYTES')) {
                $query = xoops_trim($query);
            }

            $separator = '/[\s,]+/';
            if (defined('_MD_LEGACY_FORMAT_SEARCH_SEPARATOR')) {
                $separator = _MD_LEGACY_FORMAT_SEARCH_SEPARATOR;
            }
        
            $tmpArr = preg_split($separator, $query);
            foreach ($tmpArr as $tmp) {
                if (strlen($tmp) >= $this->_mKeywordMin) {
                    $this->mQueries[] = $myts->addSlashes($tmp);
                }
            }
        }
        
        $this->set('query', implode(" ", $this->mQueries));
    }
    
    public function fetchAndor()
    {
        if ($this->get('andor') == "") {
            $this->set('andor', 'AND');
        }
    }
    
    public function validate()
    {
        parent::validate();
        
        if (!count($this->mQueries)) {
            $this->addErrorMessage(_MD_LEGACY_ERROR_SEARCH_QUERY_REQUIRED);
        }
    }
    
    public function update(&$params)
    {
        $mids = $this->get('mids');
        if (count($mids) > 0) {
            $params['mids'] = $mids;
        }
        
        $params['queries'] = $this->mQueries;
        $params['andor'] = $this->get('andor');
        $params['maxhit'] = LEGACY_SEARCH_RESULT_MAXHIT;
    }
}
