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
			'Legacy_RenderSystem.SetupXoopsTpl', array(&$this, 'menuSmartyAssign')
		);
	}
	function menuSmartyAssign(&$xoopsTpl) {
		$module_handler = & xoops_gethandler( 'module' );
		$module =& $module_handler->getByDirname("multiMenu");
		if ( !is_object( $module ) || !$module->getVar( 'isactive' ) ) {
			return ;
		}
		$gmm = new getMultiMenu();
		$options=array("40");
		$menu_num = $gmm->theme_menu();
		if ( !empty($menu_num) ) {
			$block = $gmm->getblock( $options, "multimenu0" . $menu_num );
			$xoopsTpl->assign( 'multiMenuToTheme' , $block ) ;	// Insert smarty for entire site theme
		}
		$flow = new multiMenuFlow($gmm);
		if ($flow->nextLink){
			$this->mController->executeForward($flow->nextLink);
		}

	}
}
/*
 *  hook Flow
*/
class multimenuFlow {
	var $nextLink = null;
	var $flowLink = null;
	var $pathMatch = false;
	function multiMenuFlow($gmm){
		global $xoopsUser,$xoopsDB;
		if ($xoopsUser){
			$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')? 'https': 'http';
			$requestUrl = $protocol.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$options=array("40");
			$block = $gmm->getblock( $options, "multimenu99");
			$sql = "SELECT id FROM " .$xoopsDB->prefix("multimenu_log") ." WHERE uid=" . $xoopsUser->uid() . ";";
			$ret = $xoopsDB->query($sql);
			if ($ret) list($id) = $xoopsDB->fetchRow($ret);
			if (!isset($block['contents'])) return NULL;
			foreach($block['contents'] as $b){
				if(!is_null($this->flowLink)){
					$nextId = $b['id'];
					$nextLink = $b['link'];
					break;
				}
				if ($b['id'] == $id){
					$this->flowLink = $nextLink = $b['link'];	// selected flow link
					$mid = $b['mid'];				// module id
				}
				/*
				 * Back to Flow Top When same as top link
				 */
				if( $this->linkComp($requestUrl,$b['link'])==true && $_POST){
					$this->moveFlowPosition($b['id']);
				}
			}
			/*
			 * Check and Move Flow
			 */
			//echo "requestUrl: ".$requestUrl . "<br />"
			// . "flowLink: ". $this->flowLink ."<br />"
			// . "nextLink: ". $nextLink ."<br />";
			$this->linkComp($requestUrl,$this->flowLink);
			//echo $this->pathMatch;
			//var_dump($_GET);
			//var_dump($_POST);die;
			// Redirect to new Flow menu
			if ( $this->pathMatch && (isset($_POST['submit']) || isset($_POST['insert'])) ){
				// store to session
				$_SESSION['multiMenuFlow'][$mid] = $_POST;
				//var_dump($_POST);die;
				$this->nextLink = $nextLink;
				$this->moveFlowPosition($nextId);
			}
			//echo $requestUrl."<br />".$nextLink; die;
		}
	}
	private function moveFlowPosition($id){
		global $xoopsUser,$xoopsDB;
		// Record to log
		$sql = "INSERT INTO " .$xoopsDB->prefix("multimenu_log")
		." (uid,id) VALUES(".$xoopsUser->uid().",".$id . ")";
		$ret = $xoopsDB->queryF($sql);
		if(!$ret){
			$sql = "UPDATE " .$xoopsDB->prefix("multimenu_log")
			." SET id=" . $id . " WHERE uid=".$xoopsUser->uid().";";
			$ret = $xoopsDB->queryF($sql);
		}
		//echo $sql;
	}
	private function linkComp($requestUrl,$flowLink){
		$req = parse_url($requestUrl);
		$flo = parse_url($flowLink);
		$r = $parse = array();
		foreach($flo as $key => $val){
			if (!isset($req[$key])){ $parse[$key] = false; break; }
			if (strcmp($val,$req[$key])==0){
				$parse[$key] = true;
			}
			if ($key=="query"){
				$f_prm = explode("&",$val);
				$r_prm = explode("&",$req[$key]);
				foreach($f_prm as $k=>$v){
					if(in_array($v,$r_prm)) $r[] = true;
				}
				if (!$r || in_array(false,$r)){
					$parse[$key] = false;
					break;
				}else{
					$parse[$key] = true;
				}
			}
		}
		//var_dump($flo); var_dump($req); var_dump($parse);
		//echo $requestUrl."<br />".$flowLink."<br />";
		if ( isset($parse['path']) ){
			$this->pathMatch = $parse['path'];
		}
		$ret = true;
		if (!$parse || in_array(false,$parse)){
			$ret = false;
		}
		return $ret;
	}
}
?>