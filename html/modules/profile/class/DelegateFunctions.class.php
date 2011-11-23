<?php
/**
 * @package profile
 * @version $Id: DelegateFunctions.class.php,v 1.0 $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 * profile delegate
**/
class Profile_Delegate
{
    /**
     * save profile data.
     * Its key is field name and the value is value of profile.
     *
     * @param bool      &$ret
     * @param XCube_ActionForm  $actionForm User_EditUserForm
     *
     * @return  void
     */ 
    public static function saveProfile(/*** bool ***/ &$ret, XCube_ActionForm $actionForm)
    {
        $handler = Legacy_Utils::getModuleHandler('data', 'profile');
        if(! $obj = $handler->get($actionForm->get('uid'))){
            $obj = $handler->create();
            $obj->set('uid', $actionForm->get('uid'));
        }
        $defHandler = Legacy_Utils::getModuleHandler('definitions', 'profile');
        $defObjs = $defHandler->getFields4DataEdit();
        if (!empty($defObjs)) {
          foreach($defObjs as $def){
            $obj->setField($def->get('field_name'), $actionForm->get($def->get('field_name')));
          }
          $ret = $handler->insert($obj, true);
        } else {
          $ret = true;
        }
    }

    /**
     * getProfile
     *
     * @param XoopsSimpleObject &$profile
     * @param int       $uid    user id
     *
     * @return  void
     */ 
    public static function getProfile(/*** mixed ***/ &$profile, /*** int ***/ $uid)
    {
        $handler = Legacy_Utils::getModuleHandler('data', 'profile');
        $profile = $handler->get($uid);
        if(! $profile){
            $profile = $handler->create();
        }
    }

    /**
     * getDefinition
     *
     * @param mixed     &$defArr
     * @param string    $action
     *
     * @return  void
     */ 
    public static function getDefinition(/*** mixed ***/ &$defArr, /*** string ***/ $action)
    {
        $handler = Legacy_Utils::getModuleHandler('definitions', 'profile');
        switch($action){
        case 'edit':
            $defArr = $handler->getFields4DataEdit();
            break;
        case 'view':
        default:
            $defArr = $handler->getFields4DataShow();
            break;
        }
    }

    /**
     * setup Profile ActionForm. Add FormProperties and FieldProperties on User_EditUserForm
     *
     * @param XCube_ActionForm  &$actionForm    User_EditUserForm
     *
     * @return  void
     */ 
    public static function setupActionForm(XCube_ActionForm $actionForm)
    {
        $handler = Legacy_Utils::getModuleHandler('definitions', 'profile');
        $definitions = $handler->getFields4DataEdit();
        foreach($definitions as $def){
            $className = $def->mFieldType->getFormPropertyClass();
            $actionForm->mFormProperties[$def->get('field_name')] = new $className($def->get('field_name'));
        
            //
            //validation checks for custom fields
            //
            $validationArr = array();
            $actionForm->mFieldProperties[$def->get('field_name')] = new XCube_FieldProperty($actionForm);
            //required check
            if($def->get('required')==true){
                $validationArr[] = 'required';
                $actionForm->mFieldProperties[$def->get('field_name')]->addMessage('required', _MD_USER_ERROR_REQUIRED, $def->get('label'));
            }
            //validation check
            switch($def->get('validation')){
            case 'email' :
                $validationArr[] = 'email';
                $actionForm->mFieldProperties[$def->get('field_name')]->addMessage($def->get('field_name'), _MD_USER_ERROR_EMAIL);
                break;
            }
            $actionForm->mFieldProperties[$def->get('field_name')]->setDependsByArray($validationArr);
        }
    }

    /**
     * load Profile ActionForm
     *
     * @param XCube_ActionForm  &$actionForm    User_EditUserForm
     *
     * @return  void
     */ 
    public static function loadActionForm(XCube_ActionForm $actionForm)
    {
        $defHandler = Legacy_Utils::getModuleHandler('definitions', 'profile');
        $definitions = $defHandler->getFields4DataEdit();
        $dataHandler = Legacy_Utils::getModuleHandler('data', 'profile');
        $profile = $dataHandler->get($actionForm->get('uid'));
        if(! $profile){
            $profile = $dataHandler->create();
        }
        foreach($definitions as $def){
            $actionForm->set($def->get('field_name'), $profile->showField($def->get('field_name'), Profile_ActionType::EDIT));
        }
    }
}

/**
 * cool uri delegate
**/
class Profile_CoolUriDelegate
{
    /**
     * getNormalUri
     *
     * @param string    $uri
     * @param string    $dirname
     * @param string    $dataname
     * @param int       $data_id
     * @param string    $action
     * @param string    $query
     *
     * @return  void
     */ 
    public static function getNormalUri(/*** string ***/ &$uri, /*** string ***/ $dirname, /*** string ***/ $dataname=null, /*** int ***/ $data_id=0, /*** string ***/ $action=null, /*** string ***/ $query=null)
    {
        $sUri = '/%s/index.php?action=%s%s';
        $lUri = '/%s/index.php?action=%s%s&%s=%d';
        $key = 'uid';
    
        $table = isset($dataname) ? $dataname : 'data';
    
        if(isset($dataname)){
            if($data_id>0){
                if(isset($action)){
                    $uri = sprintf($lUri, $dirname, ucfirst($dataname), ucfirst($action), $key, $data_id);
                }
                else{
                    $uri = sprintf($lUri, $dirname, ucfirst($dataname), 'View', $key, $data_id);
                }
            }
            else{
                if(isset($action)){
                    $uri = sprintf($sUri, $dirname, ucfirst($dataname), ucfirst($action));
                }
                else{
                    $uri = sprintf($sUri, $dirname, ucfirst($dataname), 'List');
                }
            }
            $uri = isset($query) ? $uri.'&'.$query : $uri;
        }
        else{
            if($data_id>0){
                if(isset($action)){
                    die('invalid uri');
                }
                else{
                    $handler = Legacy_Utils::getModuleHandler($table, $dirname);
                    $key = $handler->mPrimary;
                    $uri = sprintf($lUri, $dirname, ucfirst($table), 'View', $key, $data_id);
                }
                $uri = isset($query) ? $uri.'&'.$query : $uri;
            }
            else{
                if(isset($action)){
                    die('invalid uri');
                }
                else{
                    $uri = sprintf('/%s/', $dirname);
                    $uri = isset($query) ? $uri.'index.php?'.$query : $uri;
                }
            }
        }
    }
}
?>
