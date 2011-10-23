<?php
class Hdinstaller_Renderer_Php extends Ethna_Renderer
{
	/**
	 * 
	 */
	var $template_vars = array();

	
		/// 
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	function Hdinstaller_Renderer_Php(&$c)
	{
		$this->Ethna_Renderer($c);
		
        $template_dir = $this->ctl->getTemplatedir();
        $this->setTemplateDir($template_dir);
	}

    /**
     *  ビューを出力する
     *
     *  @param  string  $template   テンプレート名
     *  @param  bool    $capture    true ならば出力を表示せずに返す
     *
     *  @access public
     */
    function perform($template = null, $capture = false)
    {
        if ($template === null && $this->template === null) {
            return Ethna::raiseWarning('template is not defined');
        }

        if ($template !== null) {
            $this->template = $template;
        }
		
		if (!is_absolute_path($this->template)){
			$this->template = sprintf('%s%s', $this->template_dir, $this->template);
		}

		if (is_readable($this->template)){
			ob_start();
			include $this->template;
			$captured = ob_get_contents();
			ob_end_clean();

			if ($capture === true) {
				return $captured;
			} else {
				echo $captured;
			}
        } else {
            return Ethna::raiseWarning('template not found ' . $this->template);
        }
    }
    
    /**
     * テンプレート変数を取得する
     * 
     *  @param string $name  変数名
     *
     *  @return mixed　変数
     *
     *  @access public
     */
    function getProp($name = null)
    {
		$ret = null;
		if (isset($this->template_vars['app'][$name])){
			$ret = $this->template_vars['app'][$name];
		}
        return $ret;
    }

	/**
	 * @brief テンプレート用の表示関数(echo短縮形)
	 * @param string
	 * @retval string
	 */
	function e($name)
	{
		echo $this->getProp($name);
	}
	
	
	/**
	 * @brief テンプレート用の表示関数(get短縮形)
	 * @param string
	 * @retval string
	 */
	function g($name)
	{
		return $this->getProp($name);
	}
	
	
	/// 
	/**
	 * @brief テンプレート用の表示関数(短縮形:定数)
	 * @param 
	 * @retval
	 */
	function c($name)
	{
		echo defined($name) ? constant($name) : $name;
	}
	
	
		/// 
	/**
	 * @brief テンプレート用の表示関数(gettext)
	 * @param 
	 * @retval
	 */
	function _($message)
	{
		$i18n =& $this->ctl->getI18N();
		echo $i18n->get($message);
	}
	
	
    /**
     *  テンプレート変数を削除する
     * 
     *  @param name    変数名
     * 
     *  @access public
     */
    function removeProp($name)
    {
		if (isset($this->template_vars[$name])){
			unset($this->template_vars[$name]);
		}
    }

    /**
     *  テンプレート変数に配列を割り当てる
     * 
     *  @param array $array
     * 
     *  @access public
     */
    function setPropArray($array)
    {
		$this->template_vars = $array;
    }

    /**
     *  テンプレート変数に配列を参照として割り当てる
     * 
     *  @param array $array
     * 
     *  @access public
     */
    function setPropArrayByRef(&$array)
    {
		$this->template_vars &= $array;
    }

    /**
     *  テンプレート変数を割り当てる
     * 
     *  @param string $name 変数名
     *  @param mixed $value 値
     * 
     *  @access public
     */
    function setProp($name, $value)
    {
		$this->template_vars[$name] = $value;
    }

    /**
     *  テンプレート変数に参照を割り当てる
     * 
     *  @param string $name 変数名
     *  @param mixed $value 値
     * 
     *  @access public
     */
    function setPropByRef($name, &$value)
    {
		$this->template_vars[$name] =& $value;
    }
	
	
	/// ActionErrorを返す
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	function getErrors()
	{
		$ae =& $this->ctl->getActionError();
		return $ae->getMessageList();
	}
	
	
	/// Form Valueを返す
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	function fv($form_name)
	{
		if (isset($this->template_vars['form'][$form_name])){
			echo htmlspecialchars($this->template_vars['form'][$form_name], ENT_QUOTES);
		}
	}
	
	
	/// Smarty Pluginのform_nameを模したもの
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	function fn($name)
	{
		$af =& $this->ctl->getActionForm();
		$ae =& $this->ctl->getActionError();
		$def = $af->getDef($name);
		
		if (is_null($def) || isset($def['name']) == false) {
			$form_name = $name;
		} else {
			$form_name = $def['name'];
		}

		if ($ae->isError($name)) {
			// 入力エラーの場合の表示
			//		print '<span class="error">' . $form_name . '</span>';
			print $form_name ;
		} else {
			// 通常時の表示
			//		print '<span class="">' . $form_name . '</span>';
			print $form_name ;
		}
		if (isset($def['required']) && $def['required'] == true) {
			// 必須時の表示
			print '<span class="required">*</span>';
		}
	}
	
	
	/// Smarty plugin の form_inputを模したもの
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	public function  f($name, $attr='')
	{
		echo $this->_f($name, $attr);
	}
	
	// 実体
	private function _f($name, $attr)
	{

		$c =& $this->ctl;

		$af =& $this->ctl->getActionForm();

		$def = $af->getDef($name);

		if (isset($def['form_type']) == false) {
			$def['form_type'] = FORM_TYPE_TEXT;
		}

		/// params
//		$params = func_get_args();

 // attributes 未対応
/*		!isset($attr) and $attr = '';
		!isset($key_value) and $key_value = '';
		!isset($id) and $id = '';
		!isset($value) and $value = '';
		!isset($delimiter) and $delimiter = '&nbsp;';
		!isset($postfix) and $postfix = '&nbsp;'; */

		$form_value = $af->get($name);
		if (is_array($def['type']) && is_null($form_value)){
			$form_value = array();
		}

		$form_type = to_array($def['form_type']);
		$form_type = current($form_type);

		switch($form_type){
		  case FORM_TYPE_RADIO :
			/**
			 * type/form_type が arrayかそうでないかは関係ない
			 * key_valueが
			 *     1. ある場合、そのkey_valueのkeyを持つform_optionsのvalueをラベルにした単一のradioを作る。idはあればそれを使い、なければ自動生成
			 *     2. 無い場合、全てのform_optionsをforeachで展開し複数のradioを作るidは自動生成
			 */
			if ($key_value){
				// 単一radio
				!$id and $id = $name."_".$key_value;
				$attr .= sprintf(' id="%s"', $id);
				$label = $def['form_options'] && isset($def['form_options'][$key_value]) ? $def['form_options'][$key_value] : '' ;
				if ($label){
					$label = sprintf('<label for="%s">%s</label>', $id, $label);
				}
				$checked = $form_value==$key_value ? "checked" : "";
				$input   = sprintf('<input type="radio" name="%s" value="%s" %s %s />%s%s%s',
								   $name, $key_value, $attr, $checked, $prefix, $label, $postfix) ;
			} else {
				// 複数radio
				$input = "";
				if(isset($def['form_options']) && is_array($def['form_options'])){
					foreach($def['form_options'] as $key_value=>$label){
						$id = $name."_".$key_value;
						$checked = $form_value==$key_value ? "checked" : "";
						$input .= sprintf('<input type="radio" name="%s" id="%s" value="%s" %s %s />',
										  $name, $id, $key_value, $attr, $checked);
						$input .= sprintf('%s<label for="%s">%s</label>%s',
										  $prefix, $id, $label, $postfix);
					}
				}
			}
			break;


		  case FORM_TYPE_CHECKBOX :
			/**
			 * type が arrayの場合は、name=form_name[key]となる。arrayで無い場合は、単一のcheckboxを作る
			 * form_type が arrayの場合は、form_optionsをforeachで展開して複数のcheckboxを作る。idは自動生成
			 *     ただし、key_value がある場合、そのkey_valueのkeyを持つform_optionsのvalueをラベルにした単一のcheckboxを作る。idはあればそれを使い、なければ自動生成
			 */
			if (!is_array($def['type']) || $key_value){
				// 単数checkbox
				if ($key_value){
					$name = $name."[".$key_value."]";
					!$id and $id = $name."_".$key_value ;
				} else {
					!$id and $id = substr(md5(mt_rand()), 2,10) ;
				}
				$attr .= sprintf(' id="%s"', $id);
				$checked = $form_value ? 'checked' : "";
				$input = sprintf('<input type="checkbox" name="%s" value="1" %s %s />%s<label for="%s">%s</label>%s',
								 $name, $attr, $checked, $prefix, $id, $def['name'], $postfix);
			} else {
				// 複数checkbox
				$input = "";
				if(isset($def['form_options']) && is_array($def['form_options'])){
					foreach ($def['form_options'] as $key_value => $label){
						$id = substr(md5(mt_rand()), 2,10) ;
						$checked = in_array($key_value, array_keys($form_value)) ? 'checked' : '';
						$input .= sprintf('<input type="checkbox" name="%s[%s]" value="1" id="%s" %s %s />%s<label for="%s">%s</label>%s',
										  $name, $key_value, $id, $attr, $checked, $prefix, $id, $label, $postfix);
					}
				}
			}
			break;


		  case FORM_TYPE_SELECT :
			/**
			 * type が arrayの場合nameに[]が付く、key_valueは必須でname=form_name[key_value]
			 * form_type が  arrayの場合は、マルチセレクトになる。自動的にtypeがarrayの扱いになる(ただし、Ethna側Validateで強制的にtype=arrayを指定される)
			 */

			// multiple
			if (is_array($def['form_type'])){
				$multiple = 'multiple="multiple"';
				!isset($size) and $size = 3;
				if (!is_array($def['type'])){
					return sprintf("[ERROR] You must set ActionForm type to array() when you want to use multiple select. %s. %s:%d ", $def['name'], __FILE__, __LINE__);
				}
				$key_value = false;
			} else {
				$multiple = "";
				$size = 1;
			}

			// value type
			if (is_array($def['type'])){
				if ($key_value || $key_value!==""){
					$name .= '['.$key_value.']';
				} else {
					// only for multiple
					if (is_array($def['form_type'])){
						$name .= '[]';
					} else {
						// need *NOT* key_value
						//					return sprintf("[ERROR] no key_value set at form %s. %s:%d ", $def['name'], __FILE__, __LINE__);
						$name .= '[]';
					}

				}
			}

			// value
			$form_value = to_array($form_value);
			if (is_array($def['form_type'])){
				// multiple select...pass thru
			} else {
				if (is_array($def['type'])){
					// single & form_name[key]
					$form_value = $form_value[$key_value];
				} else {
					$form_value = current($form_value);
				}
			}

			$input = sprintf('<select name="%s" size="%d" %s %s>', $name, $size, $attr, $multiple)."\n" ;
			if(isset($def['form_options']) && is_array($def['form_options'])){
				foreach($def['form_options'] as $option_value=>$option_label){
					if (is_array($form_value)){
						$selected = in_array($option_value, $form_value) ? ' selected' : '';
					} else {
						$selected = $option_value==$form_value ? ' selected' : '';
					}
					$input .= sprintf('<option value="%s" %s>%s</option>'."\n",
									  $option_value, $selected ,$option_label) ;
				}
			}
			$input .= "</select>\n";
			break;


		  case FORM_TYPE_FILE:
			is_array($def['type']) and 	$name = $name."[".$key_value."]";
			$input = sprintf('<input type="file" name="%s"', $name);
			if ($attr) {
				$input .= " $attr";
			}
			$input .= " />";
			break;


		  case FORM_TYPE_TEXTAREA:
			is_array($def['type']) and 	$name = $name."[".$key_value."]";
			$input = sprintf('<textarea name="%s"', $name);
			if ($attr) {
				$input .= " $attr";
			}
			$input .= sprintf('>%s</textarea>', htmlspecialchars($af->get($name), ENT_QUOTES));
			break;


		  case FORM_TYPE_TEXT:
		  case FORM_TYPE_PASSWORD:
		  default:
			if (is_array($def['type'])){
				$form_value = isset($form_value[$key_value]) ? $form_value[$key_value]: "";
			}
			is_array($def['type']) and 	$name = $name."[".$key_value."]";
			$input = sprintf('<input type="%s" name="%s" value="%s"',
							 $form_type==FORM_TYPE_PASSWORD ? 'password' : 'text',
							 $name, htmlspecialchars($form_value, ENT_QUOTES));
			if ($attr) {
				$input .= " $attr";
			}
			if (isset($def['max']) && $def['max']) {
				$input .= sprintf(' maxlength="%d"', $def['max']);
			}
			$input .= " />";

			break;

		}
		return $input ;

	}
}
