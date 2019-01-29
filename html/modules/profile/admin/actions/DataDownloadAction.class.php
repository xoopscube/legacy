<?php
/**
 * @package user
 * @version $Id: DataDownloadAction.class.php,v 1.1 2007/08/01 02:34:42 kilica Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractListAction.class.php";

class Profile_Admin_DataDownloadAction extends Profile_AbstractListAction
{
    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('data');
        return $handler;
    }
    
    // !Fix compatibility with Profile_AbstractListAction::_getBaseUrl() in file /modules/profile/class/AbstractListAction.class.php line 36
    public function &_getBaseUrl()
    // public function _getBaseUrl()
    {
        return "./index.php?action=DataDownload";
    }
    
    public function executeViewIndex(&$render)
    {
        $render->setTemplateName("data_download.html");
        $handler =& $this->_getHandler();
        $count = $handler->getCount();
        $render->setAttribute('profileCount', $count);
    }
    
    public function getDefaultView()
    {
        return PROFILE_FRAME_VIEW_INDEX;
    }
    
    
    /// CSVファイルを出力する
    public function execute()
    {
        $handler =& $this->_getHandler();
        $count = $handler->getCount();
        if ($count == 0) {
            return PROFILE_FRAME_VIEW_INDEX;
        }
        $filename = sprintf('%s_Profile_data_List.csv', $GLOBALS['xoopsConfig']['sitename']);
        
        if (preg_match('/firefox/i', xoops_getenv('HTTP_USER_AGENT'))) {
            header("Content-Type: application/x-csv");
        } else {
            header("Content-Type: application/vnd.ms-excel");
        }
        header("Content-Disposition: attachment ; filename=\"{$filename}\"");

        $offset = 0;
        $limit = 20;
        $fp = fopen("php://output", "w");

        $defHandler =& xoops_getmodulehandler('definitions');
        $defArr =& $defHandler->getDefinitions(false);
        $label = array('uid');
        $columns = array('uid');
        foreach ($defArr as $column => $obj) {
            $label[] = $this->_encoding($obj->get('label'));
            $columns[] = $obj->get('field_name');
        }
        fputcsv($fp, $label, ',', '"');

        $criteria = new CriteriaElement();
        $criteria->setSort('uid');
        $criteria->setLimit($limit);
        for ($i = 1; $offset < $count; $i++) {
            $criteria->setStart($offset);
            $dataArr = $handler->getObjects($criteria);
            foreach ($dataArr as $profile) {
                $data = array();
                foreach ($columns as $column) {
                    if (isset($defArr[$column]) && $defArr[$column]->get('type') == 'date') {
                        $value = $value ? formatTimestamp($profile->get($column), 'Y/n/j H:i') : '';
                    } else {
                        $value = $this->_encoding($profile->get($column));
                    }
                    $data[] = $value;
                }
                fputcsv($fp, $data, ',', '"');
            }
            $offset = $i * $limit;
        }
        fclose($fp);
        exit();
    }

    protected function _encoding($text)
    {
        // japanese 
        if (strncasecmp($GLOBALS['xoopsConfig']['language'], 'ja', 2)===0) {
            mb_convert_variables('SJIS', _CHARSET, $text);
        }
        return $text;
    }
}
