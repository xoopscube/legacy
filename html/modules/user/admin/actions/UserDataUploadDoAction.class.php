<?php
/**
 * @package user
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once dirname(__FILE__)."/UserDataUploadAction.class.php";

class User_UserDataUploadDoAction extends User_UserDataUploadAction
{
    /// アップされたCSVファイルをデータに入れる
    public function execute(&$controller, &$xoopsUser)
    {
        /// back
        if (isset($_POST['back'])) {
            return $this->getDefaultView($controller, $xoopsUser);
        }
        /// csv file check
        if (isset($_SESSION['user_csv_upload_data']) &&
            count($_SESSION['user_csv_upload_data'])) {
            return USER_FRAME_VIEW_SUCCESS;
        }
        return $this->getDefaultView($controller, $xoopsUser);
    }
    
    
    /// 実行
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $csv_data = $_SESSION['user_csv_upload_data'];
        $user_handler =& $this->_getHandler();
        $user_tmp = $user_handler->create();
        $user_key = array_keys($user_tmp->gets());
        
        foreach ($csv_data as $data) {
            if ($data['is_new'] || $data['update']) {
                if ($data['update']) {
                    $user =& $user_handler->get($data['value'][0]['var']);
                } else {
                    $user =& $user_handler->create();
                }
                foreach ($user_key as $i=>$key) {
                    $value = $data['value'][$i]['var'];
                    switch ($user_key[$i]) {
                      case 'user_regdate':
                      case 'last_login':
                        $value = userTimeToServerTime(strtotime($value)) ;
                        break;
                      default:
                    }
                    $user->setVar($key, $value);
                }
                $user_handler->insert($user);
            }
        }
        
        unset($_SESSION['user_csv_upload_data']);
        
        $controller->executeRedirect("index.php", 1, _AD_USER_DATA_UPLOAD_DONE);
    }
}
