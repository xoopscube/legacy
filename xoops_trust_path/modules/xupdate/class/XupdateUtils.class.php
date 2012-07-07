<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

/**
 * Xupdate_Utils
**/
class Xupdate_Utils
{
    /**
     * &getXoopsHandler
     * 
     * @param   string  $name
     * @param   bool  $optional
     * 
     * @return  XoopsObjectHandler
    **/
    public static function &getXoopsHandler(/*** string ***/ $name,/*** bool ***/ $optional = false)
    {
        // TODO will be emulated xoops_gethandler
        return xoops_gethandler($name,$optional);
    }

    /**
     * &getModuleHandler
     * 
     * @param   string  $name
     * @param   string  $dirname
     * 
     * @return  XoopsObjectHandleer
    **/
    public static function &getModuleHandler(/*** string ***/ $name,/*** string ***/ $dirname)
    {
        // TODO will be emulated xoops_getmodulehandler
        return xoops_getmodulehandler($name,$dirname);
    }

    /**
     * &getXupdateHandler
     * 
     * @param   string  $name
     * @param   string  $dirname
     * 
     * @return  XoopsObjectHandleer
    **/
    public static function &getXupdateHandler(/*** string ***/ $name,/*** string ***/ $dirname)
    {
        $asset = null;
        XCube_DelegateUtils::call(
            'Module.xupdate.Global.Event.GetAssetManager',
            new XCube_Ref($asset),
            $dirname
        );
        if(is_object($asset) && is_a($asset, 'Xupdate_AssetManager'))
        {
            return $asset->getObject('handler',$name);
        }
    }

    /**
     * getEnv
     * 
     * @param   string  $key
     * 
     * @return  string
    **/
    public static function getEnv(/*** string ***/ $key)
    {
        return getenv($key);
    }
    
    /**
     * [modules.ini] Options unserializer
     * @param object $mobj
     * @param string $dirname
     * @return array 
     */
    public static function unserialize_options($mobj, $dirname = NULL)
    {
    	if (is_null($dirname)) {
    		$dirname = $mobj->getVar('dirname');
    	}
    	
    	//unserialize xin option fileld and replace dirname
    	$options = array();
    	if ($option = $mobj->get('options')) {
    		if (! $options = @unserialize($mobj->get('options'))) {
    			$options = array();
    		}
    	}
    	if(isset($options['writable_dir'])) {
    		array_walk( $options['writable_dir'], 'self::_printf', array($dirname, XOOPS_ROOT_PATH, XOOPS_TRUST_PATH) );
    	} else {
    		$options['writable_dir'] = array();
    	}
    	if(isset($options['writable_file'])) {
    		array_walk( $options['writable_file'], 'self::_printf', array($dirname, XOOPS_ROOT_PATH, XOOPS_TRUST_PATH) );
    	} else {
    		$options['writable_file'] = array();
    	}
    	if(isset($options['install_only'])) {
    		array_walk( $options['install_only'], 'self::_printf', array($dirname, XOOPS_ROOT_PATH, XOOPS_TRUST_PATH) );
    	} else {
    		$options['install_only'] = array();
    	}
    	if(! isset($options['detailed_version'])) {
    		$options['detailed_version'] = '';
    	}
    
    	return $options;
    }
    
    /**
     * 
     * @param $format
     * @param $key
     * @param $args
     */
    private static function _printf(&$format, $key, $args ) {
    	$format = sprintf( $format, $args[0], $args[1], $args[2]);
    }
}

?>
