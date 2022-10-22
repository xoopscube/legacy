<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.3.1
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}

require_once dirname( __FILE__, 2 ) . '/class/Ckeditor4Utiles.class.php';

class ckeditor4_TextArea extends Ckeditor4_ParentTextArea
{
	private $Legacy_TextareaEditor_delete = false;

	public function __construct(&$controler) {
		parent::__construct($controler);
		if (! XC_CLASS_EXISTS('Legacy_TextareaEditor')) {
			$this->Legacy_TextareaEditor_delete = true;
		}
	}

	public function preBlockFilter() {
		$this->mRoot->mDelegateManager->reset('Site.TextareaEditor.HTML.Show');
		$this->mRoot->mDelegateManager->add('Site.TextareaEditor.HTML.Show',[&$this, 'render']);
	}

	public function postFilter() {
		if ($this->Legacy_TextareaEditor_delete) {
			$this->mRoot->mDelegateManager->delete('Site.TextareaEditor.HTML.Show', 'Legacy_TextareaEditor::renderHTML');
		}
	}
}
