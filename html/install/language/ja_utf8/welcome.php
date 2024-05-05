<?php
// v2.3.0 2021/05/15 @gigamaster XCL-PHP7

$content =
    '<div class="ui-tab-wrap">
	<input type="radio" id="ui-tab1" name="ui-tabGroup1" class="ui-tab" checked="">
	<label for="ui-tab1">XOOPSCube</label>

	<input type="radio" id="ui-tab2" name="ui-tabGroup1" class="ui-tab">
	<label for="ui-tab2">ライセンス</label>

	<input type="radio" id="ui-tab3" name="ui-tabGroup1" class="ui-tab">
    <label for="ui-tab3">要件</label>

    <div class="ui-tab-content">
    <p><b>XCL</b>はモジュラーアーキテクチャを採用したWebアプリケーションプラットフォームです。
    規模の大小を問わず、ダイナミックなコミュニティーサイトや、イントラネット用のポータルサイト、企業のポータルサイト、ウェブログ、そういったものの作成に最適です。
    </p>
    </div>

    <div class="ui-tab-content">
    <p>
    XOOPSCube は、<a href="https://github.com/xoopscube/legacy/blob/2.3/BSD_license.txt" target="_blank">修正BSDライセンス</a>。というライセンスでリリースされており、自由に使用し、また改変できます。再配布も修正BSDライセンスの条項にしたがっていれば、自由に行うことができます。
    </p>
    <p>
    XCLモジュールはGPLライセンスの条件の下でリリースされます <a href="https://github.com/xoopscube/legacy/blob/2.3/gpl-2.0_license.md" target="_blank">General Public License</a>
    </p>
    </div>

    <div class="ui-tab-content">
    <p>
    </p><ul>
    <li><a href="https://www.apache.org/" target="_blank" rel="external">Apache</a>, <a href="https://www.nginx.com/" target="_blank" rel="external">Nginx</a>または他のWebサーバー。.</li>
    <li><a href="https://www.php.net/" target="_blank" rel="external">PHP7.4</a> 以降。</li>
    <li><a href="https://www.mysql.com/" target="_blank" rel="external">MySQL</a> or <a href="https://mariadb.org/" target="_blank" rel="external">MariaDB</a> Database v5.6.x 以降。</li>
    </ul>
    <p></p>
    </div>
</div>
    <h3>取り付けチェックリスト</h3>
    <p><input type="checkbox" required> Webサーバー、PHP7.4、およびSQLデータベースをセットアップします.
    </p><p><input type="checkbox" required> 文字セットを使用したデータベース <em>utf8mb4_general_ci</em>, ユーザーとパスワード。
    </p><p>ディレクトリとファイルを書き込み可能にする :
    </p><p><input type="checkbox" required> <code>html/uploads/</code>
    </p><p><input type="checkbox" required> <code>xoops_trust_path/cache/</code>
    </p><p><input type="checkbox" required> <code>xoops_trust_path/templates_c/</code>
    </p><p><input type="checkbox" required> <code>html/mainfile.php</code>
    </p><p>Webブラウザの設定
    </p><p><input type="checkbox" required> ブラウザのクッキーとJavaScriptをオンにする。
    </p><h3>すぐに取り付けられる</h3>
    <p><input type="checkbox" class="all-check" name="all-check" id="all-check"> すべて確認済み</input></p>
    <div class="confirmInfo">このウィザードに従ってください</div>
';
