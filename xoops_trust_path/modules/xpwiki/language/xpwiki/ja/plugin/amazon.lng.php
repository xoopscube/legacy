<?php
/*
 * Created on 2009/10/20 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: amazon.lng.php,v 1.1 2009/10/22 08:50:05 nao-pon Exp $
 */

$msg = array(
	'edit_title'   => 'ブックレビュー編集',
	'edit_btn'     => 'レビュー編集',
	'edit_caption' => '(ISBN 10 or 13 桁 or ASIN 12 桁)',
	'edit_body'    => '
- 作者: $author
- 評者: $uname
- 日付: &date;

** お薦め対象

[ここ編集のこと]

#amazon(,clear)

** 感想

[ここ編集のこと]

// まず、このレビューを止める場合、ページの [キャンセル] を押してください。
// 続けるなら、上の、[ここ編集のこと] 部分を括弧を含めて削除し、書き直してください。
// **お薦め対象、より上は、新しい行を追加しないでください。目次作成に使用するので。
// //で始まるコメント行は、最終的に全部カットしてください。目次が正常に作成できない可能性があります。
#comment
',
);
?>