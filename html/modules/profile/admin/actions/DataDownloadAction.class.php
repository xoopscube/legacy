<?php
/**
 * @package    profile
 * @version    XCL 2.5.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Original Author Kilica
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/profile/class/AbstractListAction.class.php';

class Profile_Admin_DataDownloadAction extends Profile_AbstractListAction
{
    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('data');
        return $handler;
    }

    public function &_getBaseUrl()
    {
        return './index.php?action=DataDownload';
    }

    public function executeViewIndex(&$render)
    {
        $render->setTemplateName('data_download.html');
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
        if (0 == $count) {
            return PROFILE_FRAME_VIEW_INDEX;
        }
        $filename = sprintf('%s_Profile_data_List.csv', $GLOBALS['xoopsConfig']['sitename']);

        if (preg_match('/firefox/i', xoops_getenv('HTTP_USER_AGENT'))) {
            header('Content-Type: application/x-csv');
        } else {
            header('Content-Type: application/vnd.ms-excel');
        }
        header("Content-Disposition: attachment ; filename=\"{$filename}\"");

        $offset = 0;
        $limit = 20;
        $fp = fopen('php://output', 'w');

        $defHandler =& xoops_getmodulehandler('definitions');
        $defArr =& $defHandler->getDefinitions(false);
        $label = ['uid'];
        $columns = ['uid'];
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
                $data = [];
                foreach ($columns as $column) {
                    if (isset($defArr[$column]) && 'date' == $defArr[$column]->get('type')) {
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
        if (0 === strncasecmp($GLOBALS['xoopsConfig']['language'], 'ja', 2)) {
            mb_convert_variables('SJIS', _CHARSET, $text);
        }
        return $text;
    }
}
