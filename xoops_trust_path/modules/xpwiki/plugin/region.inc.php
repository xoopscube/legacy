<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: region.inc.php,v 1.14 2011/06/01 06:27:51 nao-pon Exp $
//

class xpwiki_plugin_region extends xpwiki_plugin {
	function plugin_region_init () {

	}

	function plugin_region_convert()
	{
		static $builder = array();

		if(! isset($builder[$this->xpwiki->pid])) {
			$builder[$this->xpwiki->pid] = new XpWikiRegionPluginHTMLBuilder($this->xpwiki);
		}

		// static で宣言してしまったので２回目呼ばれたとき、前の情報が残っていて変な動作になるので初期化。
		$builder[$this->xpwiki->pid]->setDefaultSettings();

		// 引数が指定されているようなので解析
		if (func_num_args() > 0){
			$args = func_get_args();

			// multi line body?
			$body = array_pop($args);
			if (strpos($body, "\r") !== FALSE) {
				$builder[$this->xpwiki->pid]->body = $body;
			} else {
				array_push($args, $body);
			}

			// end 指定?
			if (@ $args[0] === 'end') {
				// Close area div
				return $this->func->get_areadiv_closer() . '</td></tr></table>' . "\n";
			} else {
				$builder[$this->xpwiki->pid]->setDescription( array_shift($args) );
				foreach( $args as $value ){
					// opened が指定されたら初期表示は開いた状態に設定
					if( preg_match("/^open/i", $value) ){
						$builder[$this->xpwiki->pid]->setOpened();
					// closed が指定されたら初期表示は閉じた状態に設定。
					}elseif( preg_match("/^close/i", $value) ){
						$builder[$this->xpwiki->pid]->setClosed();
					}
				}
			}
		}
		// ＨＴＭＬ返却
		return $builder[$this->xpwiki->pid]->build();
	}
}


	// クラスの作り方⇒http://php.s3.to/man/language.oop.object-comparison-php4.html
class XpWikiRegionPluginHTMLBuilder
{
	var $description;
	var $isopened;
	var $scriptVarName;
	//↓ buildメソッドを呼んだ回数をカウントする。
	//↓ これは、このプラグインが生成するJavaScript内でユニークな変数名（被らない変数名）を生成するために使います
	var $callcount;
	var $body;

	function XpWikiRegionPluginHTMLBuilder(& $xpwiki) {
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;

		$this->setDefaultSettings();
	}
	function setDefaultSettings(){
		$this->description = "...";
		$this->isopened = false;
	}
	function setClosed(){ $this->isopened = false; }
	function setOpened(){ $this->isopened = true; }
	// convert_html()を使って、概要の部分にブラケットネームを使えるように改良。
	function setDescription($description){
		//$this->description = convert_html($description);
		if ($description) $this->description = $this->func->make_link($description);
		// convert_htmlを使うと <p>タグで囲まれてしまう。Mozzilaだと表示がずれるので<p>タグを消す。
		//$this->description = preg_replace( "/^<p>/i", "", $this->description);
		//$this->description = preg_replace( "/<\/p>$/i", "", $this->description);
	}
	function build(){
		$this->elemid = $this->func->get_domid('region', '', true);
		$html = array();
		// 以降、ＨＴＭＬ作成処理
		array_push( $html, $this->buildButtonHtml() );
		array_push( $html, $this->buildBracketHtml() );
		array_push( $html, $this->buildSummaryHtml() );
		array_push( $html, $this->buildContentHtml() );
		if ($this->body) {
			array_push( $html, $this->buildBody() );
			array_push( $html, $this->buildClose() );
		}
		return join($html);
	}

	// ■ ボタンの部分。
	function buildButtonHtml(){

		// Close area div
		$areadiv_closer = $this->body? '' : $this->func->get_areadiv_closer();

		$button = ($this->isopened) ? "-" : "+";
		return <<<EOD
{$areadiv_closer}<table cellpadding="1" cellspacing="2" style="width:auto;"><tr>
<td valign="top">
	<span id="rgn_button{$this->elemid}" style="cursor:pointer;font-weight:normal;font-size:10px;font-family:monospace;border:gray 1px solid;"
	onclick="
	if(\$('rgn_summary$this->elemid').style.display!='none'){
		\$('rgn_summary$this->elemid').style.display='none';
		\$('rgn_content$this->elemid').style.display='';
		\$('rgn_bracket$this->elemid').style.borderStyle='solid none solid solid';
		\$('rgn_button$this->elemid').innerHTML='-';
	}else{
		\$('rgn_summary$this->elemid').style.display='';
		\$('rgn_content$this->elemid').style.display='none';
		\$('rgn_bracket$this->elemid').style.borderStyle='none';
		\$('rgn_button$this->elemid').innerHTML='+';
	}
	">$button</span>
</td>
EOD;
	}

	// ■ 展開したときの左側の囲いの部分。こんなやつ ⇒ [ 。 ボーダーで上下左をsolid。右側だけnoneにして [ に見せかける。
	function buildBracketHtml(){
		$bracketstyle = ($this->isopened) ? "border-style: solid none solid solid;" : "border-style:none;";
		return <<<EOD
<td id="rgn_bracket{$this->elemid}" style="font-size:1pt;border:gray 1px;{$bracketstyle}">&nbsp;</td>
EOD;
	}

	// ■ 縮小表示しているときの表示内容。
	function buildSummaryHtml(){
		$summarystyle = ($this->isopened) ? "display:none;" : "display:block;";
		return <<<EOD
<td id="rgn_summary{$this->elemid}" style="color:gray;border:gray 1px solid;{$summarystyle}">$this->description</td>
EOD;
	}

	// ■ 展開表示しているときの表示内容ヘッダ部分。ここの<td>の閉じタグは endregion 側にある。
	function buildContentHtml(){
		$contentstyle = ($this->isopened || $this->body) ? "display:block;" : "display:none;";
		return <<<EOD
<td valign="top" id="rgn_content{$this->elemid}" style="{$contentstyle}">
EOD;
	}

	function buildBody() {
		return $this->func->convert_html_multiline($this->body);
	}

	function buildClose() {
		$js = '';
		if (!$this->isopened && $this->body) {
			$js = <<<EOD
<script type="text/javascript">
//<![CDATA[
if (! XpWiki.printing)
\$('rgn_content{$this->elemid}').style.display='none';
//]]>
</script>
EOD;
		}
		return '</td></tr></table>' . "\n" . $js;
	}

}// end class XpWikiRegionPluginHTMLBuilder
?>