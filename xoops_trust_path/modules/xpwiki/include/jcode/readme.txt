/*************************************************************************
                      ________________________________

                             jcode.php by TOMO
                      ________________________________


 [Version] : 1.35a (2004/06/23)
 [URL]     : http://www.spencernetwork.org/
 [E-MAIL]  : groove@spencernetwork.org
 [Changes] :
     v1.30 Changed XXXtoUTF8 and UTF8toXXX with conversion tables.
     v1.31 Deleted a useless and harmful line in JIStoUTF8() (^^;
     v1.32 Fixed miss type of jsubstr().
           Fixed HANtoZEN_EUC(), HANtoZEN_SJIS() and HANtoZEN_JIS().
     v1.33 Fixed JIStoXXX(), HANtoZEN_JIS() and ZENtoHAN_JIS().
           Added jstr_split() as O-MA-KE No.4.
           Added jstrcut() as O-MA-KE No.5.
           Changed the logic of AutoDetect()
     v1.34 Fixed ZENtoHAN_SJIS()
     v1.35 Fixed ZENtoHAN_SJIS()
           Fixed jstr_replace()
           Changed file extension from ".phps" to ".php".
     v1.35a Fixed jcode_wrapper.php

 * jcode.phps is free but without any warranty.
 * use this script at your own risk.

***************************************************************************/

■ 最近の主な変更点
- v1.35
  ファイルの拡張子を.phpsから.phpに変更しました。最近.phpsは使いませんよね。
  これまで報告のあったバグを修正しました。
  Shift_JISで全角->半角の変換してる人、jstr_replace()を使用している人は
  バージョンアップして下さい。
  jcode_wrapper.phpの修正しました。

- v1.33から文字コード自動判別関数AutoDetect()のロジックを一新して半角カタカナ
  に対応しました。

- v1.32から半角->全角変換時の濁点、半濁点が付く文字は一文字にまとまるように
  なりました。(UTF8を除く。要望があれば対応します。)
  「カ゛」 -> 「ガ」 という感じです。

- v1.30からUTF-8関連の変換テ−ブルを新しくしました。それにともなってUTF-8の
  変換関数を更新。処理速度はかなり向上したと思われます。

- v1.20から重要な変更点があります(注意点参照)

■ PHPスクリプトで漢字コード変換をします

    ・いまのところ、
      - EUC-JP
      - Shift_JIS
      - ISO-2022-JP(JIS)
      - UTF-8
      を相互に変換
    ・半角カタカナを全角カタカナへ変換。
    ・全角文字(カタカナ、英数字、一部の記号)を対応半角文字へ変換。
      (いまのところEUC-JP、Shift_JISとJISのみ変換可能です)

■ ファイルの説明

    ・jcode.zip ：下記ファイルのアーカイブ
    ・jcode.phps：コード変換用の関数群
    ・jcode_wrapper.php：mb_convert_encoding()っぽく使うためのラッパー関数
    ・code_table.ucs2jis：Unicode→JISのコード変換テーブル
    ・code_table.jis2ucs：JIS→Unicodeのコード変換テーブル

■ 使い方

    ・この関数を使いたいスクリプトの先頭でjcode.phpsをrequire()かinclude()
      してください。これでjcode.phpsの中の関数が使えます。
      PHP4ではinclude_once()とrequire_once()が使えますので、こちらの方が
      良いと思われます。

    ・UTF-8用の変換テーブルは必要に応じてinclude()して下さい。少しメモリを
      消費するので必要ないときはinclude()しない方がいいと思います。

    ・各関数への引数(文字列のみ)の渡し方は参照渡しです。(後述の注意点参照)

    ・文字コードの変換

        JcodeConvert(文字列, 変換前の漢字コード, 変換後の漢字コード)
      で漢字コード変換されたあとの文字列を返します。
      - 変換前の漢字コードは次の0-4の数字で指定して下さい。
        0:AUTO DETECT（自動認識）
        1:EUC-JP
        2:Shift_JIS
        3:ISO-2022-JP(JIS)
        4:UTF-8
      - 変換後の漢字コードは次の1-4の数字で指定してください。
        1:EUC-JP
        2:Shift_JIS
        3:ISO-2022-JP(JIS)
        4:UTF-8
      - たとえば、
        <?php
            include("./jcode.phps");
            $string = 'てすと';
            echo JcodeConvert($string, 1, 2);
        ?>
        で（ファイルがEUC-JPで書かれていれば）EUC-JPの文字列「てすと」が
        Shift_JISに変換されてブラウザに出力されます。

    ・半角→全角の変換
        HANtoZEN(文字列, 漢字コード)
      という関数を使います。
      漢字コードには以下の数字を指定して下さい。
        0:PASS（無変換）
        1:EUC-JP
        2:Shift_JIS
        3:ISO-2022-JP(JIS)
        4:UTF-8

    ・全角→半角の変換
        ZENtoHAN(文字列, 漢字コード, flag1, flag2)
      という関数を使います。指定できる漢字コードはEUC-JP、Shift_JISとJIS
      のみです。
      以下の数字で指定して下さい。
        0:PASS(無変換)
        1:EUC-JP
        2:Shift_JIS
        3:ISO-2022-JP(JIS)
      カタカナ、英数字を変換するかどうかは、
      flag1, flag2 にそれぞれ 0 か 1 の数字を指定して下さい。
      <?php
          include("./jcode.phps");
          $string='アイウエオ１２３４５ＡＢＣＤＥ';
          echo ZENtoHAN($string, 1, 1, 0);
      ?>
      この例では、カタカナだけが半角に変換されます。
      デフォルト値はflag1=1, flag2=1 で、省略時にこの値が使用されます。
      つまり、
          echo ZENtoHAN($string, 1);
      でカタカナ、英数字は両方とも半角に変換されて出力されます。
      (注)カタカナの変換で「」。、・゛゜を半角カナに変換します。
          また、"　"(全角スペース)も半角スペース" "(ASCII:0x20)に変換します。

    ・文字コードの自動判別
        AutoDetect(文字列)
      で自動判別しようとします。
      判別結果に応じて以下の数値を返します。
        0:ASCII
        1:EUC-JP
        2:Shift_JIS
        3:ISO-2022-JP(JIS)
        4:UTF-8
        5:判別不可
      予め確実に判別できる文字列をフォーム内にHidden属性で書いておいて、
      その文字を判別させることでブラウザが送信した文字のコードを知ることが
      できます。
      主にそういう使い方を想定しています。

■ 注意点

    ・バージョン1.20から各関数への引数(文字列)の渡し方を値渡しから参照渡しに
      変更しました。
      これにより、JcodeConvert("テスト", 1, 3)という使い方ができなくなりま
      した。文字列を一度変数に格納してから引数として渡してください。
      <?php
          $string = "テスト";
          echo JcodeConvert($string, 1, 3);
      ?>
      また、元の文字列(上の例の$string)には一切変更は加えられません。
      これが嫌な人はソースの
      function JcodeConvert(&$str, $from, $to)
      を
      function JcodeConvert($str, $from, $to)
      に変更してください。

    ・内部処理にはEUC-JPかUTF8を使用した方が余計なトラブルを招かずに済みます。
      - Shift_JISにはアスキーの"\"記号含む文字が存在する。
      - JISのエスケープシーケンスに"$"記号が含まれている。
      - JISの文字に"'"シングルクォートとかを含むものがある。
      - ソースをJISで書く人はいないと思いますが、Shift_JISでは注意しないと、
        予期せぬエラーが出ます。

    ・フォームから送られてくる文字の一部は"\"円記号でエスケープされています。
      (php.iniで magic_quotes_gpc = on の場合)
      - 変換する前や後にstripslashes()関数をうまく組合わせると正しく変換され
        るはずです。
          if(get_magic_quotes_gpc()) $string = stripslashes($string);
        こんな感じの一行を入れると良いかと思われます。

    ・処理速度は遅いです。
      - スクリプトで処理しているので組込み関数と比べれば当然遅いです。
      - 文字数が多いとタイムアウトします(設定によるけど)。
       （と言ってもちょっとしたフォームからの入力やメール送信ぐらいなら
         特に気にならないと思います）
      - 処理時間はマシンの性能や状態に依存します。
      - PHP3とPHP4では相当違います。PHP3の方が圧倒的に遅いです。

    ・ブラウザで表示したときに文字化けを起こす場合はheader()関数で文字コード
      を明示的に指定してあげると良いです。(METAタグより効果的)
      例えば、HTMLを出力する前に、
        <?php header("Content-type: text/html; charset=UTF-8") ?>
      とすることで、ほとんどの場合ブラウザは正しく表示します。

■ その他

    ・普通のPHPスクリプトなので、国際化されてなくても使用できます。
    ・フォームから入力を気軽にコード変換したり出来て便利です。
    ・個々の関数は単体でも使用できます。必要な関数だけコピーしてお使い下さい。
    ・このスクリプトをアップデートする時は基本的に上書きするだけで良いです。
      特別に仕様の変更がある場合はその旨をお知らせします。
    ・関数名は大文字小文字の区別がない(はず)です。

■ おまけの関数について(不具合あるかもしれません)

    ・漢字コードの変換ではありませんが、日本語を扱う上であったら便利かなと思う
      関数を収録しています。jcode.phpsをincludeしてあれば使用可能です。
      単体でも使用可能なので使用するものだけコピー&ペーストでご自分のスクリプト
      に組み込むことができます。

    ・その1 - jsubstr()
      substr()では2バイト文字の真中で切られて文字化けを起こすことがあるため。
      2バイト文字を1文字で数える関数で、引数の指定はsubstr()と同様です。
      echo jsubstr('あいうえお', 1, 3);  //これは"いうえ"を返します

    ・その2 - jstrlen()
      strlen()は2バイト文字1文字を2文字と数えるため。
      この関数は2バイト文字を1文字と数えます。
      echo jstrlen('あいうえお');  //これは"5"を返します

    ・その3 - jstr_replace()
      str_replace()では第一引数が2バイト文字の時に文字化けを起こすことがある。
      引数の指定方法はstr_replace()と同じです。
      ちなみに今までうまくいっていたとしてもそれはたまたまです。

    ・その4 - jchunk_split()
      基本的にchunk_split()と同じですが、2バイト文字が分断されないようにします。
      分断されそうな場合は引数で指定したバイト数を越えない長さになります。

    ・その5 - jstrcut()
      mb_strcut()と同じように使えます。

    ・上記関数はデフォルトでEUC-JP用です。Shift_JISで使用する場合は、ソース
      をエディタで開いてコメントアウトしてある部分をアンコメントして下さい。
      (Shift_JISでは半角カナが1バイトのためその分の処理を加えることになります)

■ 利用規定

    ・著作権は放棄しませんが、スクリプトの一部または全部を使用・改造・再配布
      することは自由です。
    ・このスクリプトを使用したことで生じたいかなる不都合・損害にも作者は一切
      その責任を負いません。
