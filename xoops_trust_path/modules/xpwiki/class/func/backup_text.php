<?php
class XpWikiBackupFunc extends XpWikiPukiWikiFunc {
	// ファイルシステム関数

	/**
	 * _backup_fopen
	 * バックアップファイルを開く
	 *
	 * @access    private
	 * @param     String    $page        ページ名
	 * @param     String    $mode        モード
	 *
	 * @return    Boolean   FALSE:失敗
	 */
	function _backup_fopen($page, $mode)
	{
		return fopen($this->_backup_get_filename($page), $mode);
	}

	/**
	 * _backup_fputs
	 * バックアップファイルに書き込む
	 *
	 * @access    private
	 * @param     Integer   $zp          ファイルポインタ
	 * @param     String    $str         文字列
	 *
	 * @return    Boolean   FALSE:失敗 その他:書き込んだバイト数
	 */
	function _backup_fputs($zp, $str)
	{
		return fputs($zp, $str);
	}

	/**
	 * _backup_fclose
	 * バックアップファイルを閉じる
	 *
	 * @access    private
	 * @param     Integer   $zp          ファイルポインタ
	 *
	 * @return    Boolean   FALSE:失敗
	 */
	function _backup_fclose($zp)
	{
		return fclose($zp);
	}

	/**
	 * _backup_file
	 * バックアップファイルの内容を取得する
	 *
	 * @access    private
	 * @param     String    $page        ページ名
	 *
	 * @return    Array     ファイルの内容
	 */
	function _backup_file($page)
	{
		return $this->_backup_file_exists($page) ?
			file($this->_backup_get_filename($page)) :
			array();
	}
}
?>