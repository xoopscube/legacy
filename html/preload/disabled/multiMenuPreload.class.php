<?php
/*
 * 2011/09/09 16:45
 * Multi-Menu preload for smarty insertion to theme
 * copyright(c) Yoshi Sakai at Bluemoon inc 2011
 * GPL ver3.0 All right reserved.
 */
if (!defined('XOOPS_ROOT_PATH')) exit();

include_once XOOPS_ROOT_PATH . '/modules/multiMenu/class/getMultiMenu.class.php';

class multiMenuPreload extends XCube_ActionFilter{
	function preBlockFilter(){
        $this->mRoot->mDelegateManager->add(
			'Legacy_RenderSystem.SetupXoopsTpl',
            array(&$this, 'menuSmartyAssign')
		);
	}
	function menuSmartyAssign(&$xoopsTpl) {
        $module_handler = & xoops_gethandler( 'module' );
		$module =& $module_handler->getByDirname('multiMenu');
		if ( !is_object( $module ) || !$module->getVar( 'isactive' ) ) {
			return ;
		}
		$gmm = new getMultiMenu();
		$options=array('40');
		$menu_num = $gmm->theme_menu();
		if ( !empty($menu_num) ) {
			$block = $gmm->getblock( $options, 'multimenu0' . $menu_num );
			$xoopsTpl->assign( 'multiMenuToTheme' , $block ) ;	// Insert smarty for entire site theme
		}
		$flow = new multiMenuFlow($gmm);
	    if ($flow->nextLink){
		    //            die($flow->nextLink);
            $_SESSION['multiMenuFlow'] = array();
            redirect_header($flow->nextLink,3,'Multimeu Flow next link. ');
//            $this->mController->executeForward($flow->nextLink);
		}
	}
}
/*
 *  hook Flow
*/
class multimenuFlow  {
    var $moduleId = null;
	var $nextLink = null;
	var $flowLink = null;
	var $pathMatch = false;
	function __construct($gmm){
		global $xoopsUser,$xoopsDB;
		if ($xoopsUser){
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')? 'https': 'http';
            $requestUrl = $protocol.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
            $refererUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
            if( isset($_SESSION['multiMenuFlow']) && isset($_SESSION['multiMenuFlow']['nextId'] )){
                if( $refererUrl != $_SESSION['multiMenuFlow']['requestUrl'] ){
                    $nextId =  $_SESSION['multiMenuFlow']['nextId'];
                    $this->nextLink = $_SESSION['multiMenuFlow']['nextLink'];
                    $this->moveFlowPosition($nextId);
                    return;
                }
            }
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')? 'https': 'http';
            $requestUrl = $protocol.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
            $options = array('40');
			$block = $gmm->getblock( $options, 'multimenu99');
			$sql = 'SELECT id FROM ' .$xoopsDB->prefix('multimenu_log') . ' WHERE uid=' . $xoopsUser->uid() . ';';
			$ret = $xoopsDB->query($sql);
            $id = NULL;
			if ($ret) list($id) = $xoopsDB->fetchRow($ret);
			if (!isset($block['contents'])) return NULL;
            $link_option = NULL;
			foreach($block['contents'] as $b){
				if(!is_null($this->flowLink)){
					$nextId = $b['id'];
					$nextLink = $b['link'];
					break;
				}
				if ($b['id'] == $id || $id==NULL){
					$this->flowLink = $nextLink = $b['link'];	// selected flow link
				}
			}
			/*
			 * Back to Flow Top When same as top link
			 */
			if( $this->linkComp($refererUrl,$block['contents'][0]['link']) == true ){
                $topId = $block['contents'][0]['id'];
                $this->moveFlowPosition($topId);
            }
			/*
			 * Check and Move Flow
			 */
            $ret = $this->linkComp($refererUrl,$this->flowLink,$link_option);
			// Redirect to new Flow menu
            if ( $ret && (isset($_POST['submit']) || isset($_POST['contents_submit'])) ){
                $_SESSION['multiMenuFlow']['nextId'] = $nextId;
                $_SESSION['multiMenuFlow']['nextLink'] = $nextLink;
                $_SESSION['multiMenuFlow']['requestUrl'] = $requestUrl;
            }
		}
	}
    private function getModuleId($refererUrl){
        $req = parse_url($refererUrl);
        $mydirname =  basename( dirname( $req['path'] ));
        $module_handler = xoops_gethandler( 'module' );
        $module = $module_handler->getByDirname($mydirname);
        if ($module) return $module->mid();
    }
	private function moveFlowPosition($id){
		global $xoopsUser,$xoopsDB;
		// Record to log
        if (is_null($id)) return;
		$sql = 'INSERT INTO ' .$xoopsDB->prefix('multimenu_log')
		.' (uid,id) VALUES('.$xoopsUser->uid().','.$id . ')';
		$ret = $xoopsDB->queryF($sql);
		if(!$ret){
			$sql = 'UPDATE ' .$xoopsDB->prefix('multimenu_log')
			.' SET id=' . $id . ' WHERE uid='.$xoopsUser->uid().';';
			$ret = $xoopsDB->queryF($sql);
		}
	}
	private function linkComp($refererUrl,$flowLink,$option=''){
		//echo '<hr>requestUrl: '.$refererUrl . '<br />'. 'flowLink: '. $flowLink .'<br />';
		if (! $refererUrl) {
			return false;
		}
		if (! $req = parse_url($refererUrl)) {
			return false;
		}
		if (! $flo = parse_url($flowLink)) {
			return false;
		}
		$parse = array('query'=>true);
		if ( substr($req['path'], -1)=='/') $req['path'] .= 'index.php';
		foreach($flo as $key => $val){
			if ($key=='path' && substr($val, -1)=='/'){
                $val .= 'index.php';
            }
            if (!isset($req[$key])){
				if ($key!='query'){
                    $parse[$key] = false;
                }
				break;
			}
            //echo '<br />' . $key . ':' . $val . '<->' . $req[$key];
			if (strcmp($val,$req[$key])==0){
				$parse[$key] = true;
			}else{
				$parse[$key] = false;
			}
			if ($key=='query'){
				$f_prm = explode('&',$val);
				$r_prm = explode('&',$req[$key]);
				$r_prm = array_merge($r_prm, explode('&',$option));
				//echo '<br />req: '; var_dump($r_prm);
				$parse[$key] = false;
				foreach($f_prm as $k=>$v){
					if(in_array($v,$r_prm)){
						//echo '<br />v: '.$v;
						$parse[$key] = true;
					}
				}
			}
		}
		if ( isset($parse['path']) ){
			$this->pathMatch = $parse['path'];
		}
		$ret = true;
		if ($parse && in_array(false,$parse)){
			$ret = false;
		}
		//if ($ret==true) echo 'Move Ok.<br />';else echo 'Move NO.<br />';
		return $ret;
	}
}