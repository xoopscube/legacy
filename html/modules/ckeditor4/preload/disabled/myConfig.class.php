<?php

if (!defined('XOOPS_ROOT_PATH')) exit;

class ckeditor4_myConfig extends XCube_ActionFilter
{
	public function postFilter()
	{
		$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PreBuild_ckconfig',      [$this, 'PreBuild']);
		$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PreParseBuild_ckconfig', [$this, 'PreParseBuild']);
		$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PostBuild_ckconfig',     [$this, 'PostBuild']);
	}

	public function PreBuild(&$params)
	{
		/******************************
		 * Before generating JavaScript for ckeditor,
		 * Smarty can change params obtained from plugins.
		 *
		 * Attributes of Textarea
		 * $params['id']     : <textarea> id
		 * $params['name']   : <textarea> name
		 * $params['class']  : <textarea> class
		 * $params['style']  : <textarea> style
		 * $params['cols']   : <textarea> cols
		 * $params['rows']   : <textarea> rows
		 * $params['value']  : <textarea> value
		 * $params['editor'] : ckeditor4 editor mode "html" or "bbcode"
		 * $params['toolbar']: Toolbar to display (JavaScript array notation)
		 *
		 ******************************/
	}

	public function PreParseBuild(&$config, $params)
	{
		/******************************
		 * Before generating JavaScript for ckeditor,
		 * the config values can be changed.
		 * $config  the key names in the array correspond to the key names in ckeditor.config
		 * Settings here overwrite the values of general settings in ckeditor4 module.
		 * Also, ['toolbar'] is overwritten with $ params ['toolbar'].
		 *
		 ******************************/

		// Example: config.removePlugins the set "save,about"
		$config['removePlugins'] = 'preview,save,about';
	}

	public function PostBuild(&$config, $params)
	{
		/******************************
		 * Before generating JavaScript for ckeditor,
		 * the config values can be changed.
		 * The key name of the $ config array corresponds to the key name of ckeditor.config.
		 * Settings here overwrite the values of general settings in ckeditor4 module.
		 * The settings of each mode ("html", "bbcode") can also be overwritten with $config['_modeconf']
		 * Since the key is saved, the value for each mode toolbar is :
		 * $config['_modeconf']['html']['toolbar']
		 * The present mode toolbar can be modified as follows.
		 * ['fontSize_sizes'], ['extraPlugins'], ['enterMode'], ['shiftEnterMode'], ['toolbar']
		 *
		 ******************************/

		// Example: Setting the html mode toolbar when the current module is d3forum
		if ($this->mRoot->mContext->mXoopsModule->get('trust_dirname') === 'd3forum') {
			$config['_modeconf']['html']['toolbar'] = '[
				["PasteText","-","Undo","Redo"],
				["Bold","Italic","Underline","Strike","-","TextColor","-","RemoveFormat","FontSize"],
				["NumberedList","BulletedList","Outdent","Indent","Blockquote"],
				["Link","Image","Smiley","PageBreak"],["Maximize", "ShowBlocks"]
				]';
		}
	}
}
