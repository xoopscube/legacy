<?php
/*
**使い方 (sonots バージョン) [#ee9649c9]
***書式 [#r1bd66ec]
 #iframe(URL[,オプション])
-URL~
とりこむURL。許可されているものしかとりこめない。許可するURLは iframe.inc.php 中で設定する。絶対アドレスのみ許可されるように設定してください。
-style~
スタイル設定。ここで style="width:500px;height:500px;" のように指定。CSSファイル中の記述よりも優先される。~
ところで IE以外(Mozilla, Opera) では object タグを使用しているのだが、その際 height:100% では全く 100% の表示にならない。height:500px あたりが無難？
-iestyle~
IE 用のスタイル指定。指定されなくても style の値が使われる。ここで iestyle="width:500px;height:500px;" のように指定。CSSファイル中の記述よりも優先される。~
ところで width:100% ではスクロールバーが切れたので width:99% あたりが無難？height:100% はこっちではありらしい。

***設定方法 [#i568a4ac]
iframe.inc.php を開き、許可する URL を編集します。
-$iframe_accept_regurl~
正規表現による指定。ホスト許可などに使用。例）
 $iframe_accept_regurl = '^http://www.google.co.jp$|^http://pukiwiki.org'; 
http://www.google.co.jp を許可。http://pukiwiki.org 以下のページをすべて許可。
正規表現なので本当は \. にすべきです。
-$iframe_accept_url~
ただの文字列マッチによる指定。常にこちらを使用した方が安全。
日本語を使用する場合はURLエンコードしておくこと。例）
 $iframe_accept_url = array(
 'http://pukiwiki.org/index.php?%E8%87%AA%E4%BD%9C%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3%2Fiframe.inc.php',
 );

デフォルトのサイズはスタイルシートを編集することで指定可能です。
-skin/css/iframe.css~
 .iframe_others
 {
 	height: 600px;
 	width:100%;
 	margin-left:auto;
 	margin-right:auto;
 }
 
 .iframe_ie
 {
 	height: 600px;
 	width:100%;
 	margin-left:auto;
 	margin-right:auto;
 }

*/

class xpwiki_plugin_iframe extends xpwiki_plugin {
	function plugin_iframe_init () {
		//////////////////////////////////////////
		//  iframe.inc.php by ino_mori and sonots
		//////////////////////////////////////////
		
		//正規表現による指定。ホスト許可などに使用。
		$this->iframe_accept_regurl = '^http://([a-z]+\.)*google\.co(\.jp|m)/'; 
		//ただの文字列マッチ。常にこちらを使用した方が安全。日本語を使用する場合はURLエンコードしておくこと。
		$this->iframe_accept_url = array(
			'',
		);
	}
	
	function plugin_iframe_inline()
	{
		if (!func_num_args())
		{
			return 'no argument(s).';
		}
		return $this->plugin_iframe_body(func_get_args());
	}
	
	function plugin_iframe_convert()
	{
		if (!func_num_args())
		{
			return 'no argument(s).';
		}
		return $this->plugin_iframe_body(func_get_args());
	}
	
	function plugin_iframe_body($args)
	{
		$url = array_shift( $args );
		
		if(! ereg( $this->iframe_accept_regurl , $url ) ) // 正規表現マッチが失敗
		{
			$match = FALSE;
			foreach( $this->iframe_accept_url as $value ) // ただの文字列マッチ
			{
				if( $value == $url )
				{
					$match = TRUE;
				}
			}
			if(! $match)
			{
				return "not accepted.";
			}
		}
		
		// ページキャッシュを無効に
		$this->root->pagecache_min = 0;
		
		// CSS 読み込み
		$this->func->add_tag_head('iframe.css');
		
		$url = htmlspecialchars($url); 
		$params = array(
			'style'    => FALSE,
			'iestyle'   => FALSE,
			'_args'   => array(),
		);
		
		$this->fetch_options($params, $args);
		
		$style = '';
		
		// USER_AGENT が IE の場合は iframe タグを使用
		// コンテンツがheight,widthの値よりも小さい場合でもダミーのscrollbarが表示されてしまうため
		// iframe を使用するには XHTML1.1 のままだと XHTML 構文エラー
		if (ereg("MSIE (3|4|5|6|7)", $this->root->ua ) )
		{
			$this->root->pkwk_dtd = $this->cont['PKWK_DTD_XHTML_1_0_TRANSITIONAL'];
			$this->root->html_transitional = 1;
			$class=" class=\"iframe_ie\"";
			if ( $params['iestyle'] != FALSE )
			{
				$style = ' style="' . htmlspecialchars(strip_tags(trim($params['iestyle'], '"'))) . '"'; 
			}
			else if ( $params['style'] != FALSE )
			{
				$style = ' style="' . htmlspecialchars(strip_tags(trim($params['style'], '"'))) . '"';
			}
			
			return <<<HTML
<iframe frameborder="0"${class}${style} src="$url">
Please see here by browsers dealing with iframe tag.<br />
Go to <a href="$url">$url</a>
</iframe>
HTML;
	
		}
		else
		// その他のブラウザは object タグを使用
		{
			$class = ' class="iframe_others"';
			if ( $params['style'] != FALSE )
			{
				$style = " style=".$params['style'];
			}
	
			return <<<HTML
<object${class}${style} data="$url" type="text/html">
Please see here by browsers dealing with object tag.<br />
Go to <a href="$url">$url</a>
</object>
HTML;
	
		}
	}
	
}
?>