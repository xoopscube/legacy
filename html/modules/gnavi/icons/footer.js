var u = location.href;
var b = "<a href='http://b.hatena.ne.jp/entry/" + u + "' target='_blank'><img src='http://b.hatena.ne.jp/entry/image/" + u + "' /></a> <a href='http://clip.livedoor.com/page/" + u + "' target='_blank'><img src='http://image.clip.livedoor.com/counter/" + u + "' /></a> <a href='http://buzzurl.jp/entry/" + u + "' target='_blank'><img src='http://api.buzzurl.jp/api/counter/v1/image?url=" + u + "' /></a> <span id='delicious'></span><script type='text/javascript' src='http://k52.if.tv/tool/js/delicious.js'></script>";
document.write (b);

var l = '<div style="font-size:85%;">' +
'<a style="color:#444;" href="http://k52.if.tv/tool/y_hateb/">Yahoo!検索 はてブ順</a>｜' + 
'<a style="color:#444;" href="http://k52.if.tv/tool/tikab/">好みの近いはてなブックマーカーを探そう</a>｜' + 
'<a style="color:#444;" href="http://k52.if.tv/tool/konop/">このページについて</a>｜' + 
'<a style="color:#444;" href="http://ichibou.net/">一望amazon</a>｜' + 
'<a style="color:#444;" href="http://k52.org/jwjw/">じわじわ来てるエントリー</a>' + 
'</div>';
document.write (l);
