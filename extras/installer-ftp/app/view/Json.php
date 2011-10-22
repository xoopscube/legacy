<?php
/**
 *  Json.php
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @version    $Id: app.view.default.php 532 2008-05-13 22:41:22Z mumumu-org $
 */

/**
 *  Json view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_View_Json extends Hdinstaller_ViewClass
{
    var $use_layout = false;
	
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		require_once 'HTML/AJAX/JSON.php';
		$json_object = $this->af->getAppArray();
		$json_object['error'] = '';
		if ($this->ae->count()>0){
			foreach ($this->ae->getMessageList() as $msg){
				$json_object['error'] .= $msg."\n";
			}
		}
		$json =& new HTML_AJAX_JSON();
		$json_value = $json->encode($json_object);
		header(sprintf('X-JSON: (%s)', $json_value));
    }
}
