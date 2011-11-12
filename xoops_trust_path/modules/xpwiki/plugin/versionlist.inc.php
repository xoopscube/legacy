<?php
// $Id: versionlist.inc.php,v 1.3 2008/02/11 01:02:41 nao-pon Exp $
/*
 * PukiWiki versionlist plugin
 *
 * CopyRight 2002 S.YOSHIMURA GPL2
 * http://masui.net/pukiwiki/ yosimura@excellence.ac.jp
*/

class xpwiki_plugin_versionlist extends xpwiki_plugin {
	function plugin_versionlist_init () {

	}
	
	function plugin_versionlist_action()
	{
	//	global $_title_versionlist;
	
		if ($this->cont['PKWK_SAFE_MODE']) $this->func->die_message('PKWK_SAFE_MODE prohibits this');
	
		return array(
			'msg' => $this->root->_title_versionlist,
		'body' => $this->plugin_versionlist_convert());
	}
	
	function plugin_versionlist_convert()
	{
		if ($this->cont['PKWK_SAFE_MODE']) return ''; // Show nothing
		
		$comments = $this->get_filelist($this->root->mytrustdirpath);
		
		if (count($comments) == 0)
		{
			return '';
		}
		ksort($comments);
		$retval = '';
		foreach ($comments as $comment)
		{
			$retval .= <<<EOD

  <tr>
   <td>{$comment['file']}</td>
   <td align="right">{$comment['rev']}</td>
   <td>{$comment['date']}</td>
  </tr>
EOD;
		}
		$retval = <<<EOD
<table border="1">
 <thead>
  <tr>
   <th>filename</th>
   <th>revision</th>
   <th>date</th>
  </tr>
 </thead>
 <tbody>
$retval
 </tbody>
</table>
EOD;
		return $retval;
	}
	
	function get_filelist ($sdir) {

		$comments = array();
		if (!$dir = @dir($sdir))
		{
			// die_message('directory '.$sdir.' is not found or not readable.');
			return $comments;
		}
		while($file = $dir->read())
		{
			if ($file[0] !== '.'&& is_dir($sdir.'/'.$file)) {
				$comments += $this->get_filelist ($sdir.'/'.$file);
			}
			if (!preg_match("/\.(php|lng|css|js)$/i",$file))
			{
				continue;
			}
			$data = file_get_contents($sdir.'/'.$file);
			$comment = array('file'=>htmlspecialchars(str_replace($this->root->mytrustdirpath,'TRUST',$sdir.'/'.$file)),'rev'=>'','date'=>'');
			if (preg_match('/\$'.'Id: (.+),v (\d+\.\d+) (\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2})/',$data,$matches))
			{
				$comment['rev'] = htmlspecialchars($matches[2]);
				$comment['date'] = htmlspecialchars($matches[3]);
			}
			$comments[str_replace($this->root->mytrustdirpath,'',$sdir.'/'.$file)] = $comment;
		}
		$dir->close();
		return $comments;
	}
}
?>