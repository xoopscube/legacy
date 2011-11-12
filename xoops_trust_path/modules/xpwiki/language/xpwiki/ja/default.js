if (!wikihelper_loaded) {
var wikihelper_loaded = true;

// Set masseges.
var wikihelper_msg_copyed = "クリップボードにコピーしました。";
var wikihelper_msg_select = "対象範囲を選択してください。";
var wikihelper_msg_fontsize = "文字の大きさ ( % または pt[省略可] で指定): ";
var wikihelper_msg_to_ncr = "数値文字参照へ変換";
var wikihelper_msg_hint = "ヒント";
var wikihelper_msg_winie_hint_text = "\n\n色指定は、最初に選択した色が文字色、次に選択した色が背景色になります。\n\n選択範囲を処理後は、その範囲が選択したままになっています。\n続けて文字を入力する場合は、[ → ]キーでカーソルを移動してから入力してください。\n\n\n-- +α(アドバンスモード) --\n\n[ &# ] ボタンは、選択文字列を数値文字参照に変換します。";
var wikihelper_msg_gecko_hint_text = wikihelper_msg_winie_hint_text + "\n\n" + "表示範囲が先頭に戻ってしまい、処理した範囲が見えなくなった時は、[ ESC ]キーを押してみてください。";
var wikihelper_msg_to_easy_t = "イージーモードへ変更";
var wikihelper_msg_to_adv_t = "アドバンスドモードへ変更";
var wikihelper_msg_to_easy = "イージーモードに変更しました。\nリロード後に有効になります。\n\n今すぐリロードしますか？";
var wikihelper_msg_to_adv = "アドバンスモードに変更しました。\nリロード後に有効になります。\n\n今すぐリロードしますか？";
var wikihelper_msg_inline1 = "プラグイン名を入力してください。[ ＆ は省く ]";
var wikihelper_msg_inline2 = "パラメーターを入力してください。[ ( )内 ]";
var wikihelper_msg_inline3 = "本文を入力してください。[ { }内 ]";
var wikihelper_msg_link = "リンクを設定する文字を入力してください。";
var wikihelper_msg_url = "リンク先のURLを入力してください。";
var wikihelper_msg_elem = "処理をする対象を選択してください。";
var wikihelper_msg_submit = "このまま送信しますか？";
var wikihelper_msg_attach = "ファイル添付・参照";
var wikihelper_msg_thumbsize = "サムネイルを作成する場合は、[最大幅(px)]x[最大高(px)] を入力してください。\n(例: \"240x120\" or \"240 120\" or \"240\" etc...)";
var wikihelper_msg_notsave = "編集内容を保存していません。";
var wikihelper_msg_wrap = "テキストを折り返す";
var wikihelper_msg_nowrap = "テキストを折り返さない";
var wikihelper_msg_rich_editor = "リッチエディタ";
var wikihelper_msg_normal_editor = "通常エディタ";

// Set wikihelper_root_url
var wikihelper_root_url = "$wikihelper_root_url";

// JavaScripts loader
document.write ('<script type="text/javascript" src="' + wikihelper_root_url + '/skin/loader.php?src=loader.js"></script>');

}
