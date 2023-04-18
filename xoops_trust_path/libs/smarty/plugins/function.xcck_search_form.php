<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 xcck_search_form
 * Version:  1.0
 * Date:	 Jul 26, 2011
 * Author:	 HIKAWA Kilica
 * Purpose:  show search input fields
 * Input:	 Xcck_DefinitionObject def: xcck definition object
 *			 string	condition: filter condition
 *                             condition....= * like < > <= >=
 *           int num: if you set several conditions in one field 
 *                    like '<' and '>', set this parameter.
 *           mixed	option: various options
 * Examples: {xcck_search_form def=$def condition="<" num=0}
 * sample result:
 * 1) <input type="text" name="content[0][0]" value="" size=20 />
 *    <input type="hidden" name="content[0][1]" value="=" />
 * -------------------------------------------------------------
 */
function smarty_function_xcck_search_form($params, &$smarty)
{
	define('_XCCK_TAG_INPUT', '<input type="text" name="%s[%d][0]" value="%s" size="%d" />'."\n");
	define('_XCCK_TAG_HIDDEN', '<input type="hidden" name="%s[%d][1]" value="%s" />'."\n");
	define('_XCCK_TAG_CHECKBOX', '<input type="checkbox" name="%s" value="1"%s />'."\n");
	define('_XCCK_TAG_DATE', '<input type="text" name="%s[%d][0]" value="%s" size="%d" maxlength="%d" class="%s" />'."\n");
	define('_XCCK_TAG_SELECT', '<select name="%s">%s</select>'."\n");
	define('_XCCK_TAG_OPTION', '<option value="%s"%s>%s</option>'."\n");
	$html = null;

	//parameters
	$def = $params['def'];
	if(! isset($params['condition'])){
		$condition = Xcck_Cond::EQ;
	}
	elseif($params['condition']==='*'){
		$condition = $params['condition'];
	}
	else{
		$condition = Xcck_Cond::getValue($params['condition']);
	}
	$default = $params['default'] ?? null;
	$num = isset($params['num']) ? intval($params['num']) : 0;
	$option = $params['option'] ?? null;

	//main
	switch($def->get('field_type')){
	case Xcck_FieldType::STRING:
	case Xcck_FieldType::TEXT:
	case Xcck_FieldType::URI:
		$html .= sprintf(_XCCK_TAG_INPUT, $def->get('field_name'), $num, $default, 20);
		if($condition=='*'){
			$html .= Xcck_Utils::makeCondSelector($def, $num, [Xcck_Cond::EQ, Xcck_Cond::NE, Xcck_Cond::LIKE]);
		}
		else{
			$html .= sprintf(_XCCK_TAG_HIDDEN, $def->get('field_name'), $num, $condition);
		}
		break;
	case Xcck_FieldType::INT:
	case Xcck_FieldType::FLOAT:
		$html .= sprintf(_XCCK_TAG_INPUT, $def->get('field_name'), $num, $default, 5);
		if($condition=='*'){
			$html .= Xcck_Utils::makeCondSelector($def, $num, [Xcck_Cond::EQ, Xcck_Cond::NE, Xcck_Cond::LT, Xcck_Cond::LE, Xcck_Cond::GT, Xcck_Cond::GE]);
		}
		else{
			$html .= sprintf(_XCCK_TAG_HIDDEN, $def->get('field_name'), $num, $condition);
		}
		break;
	case Xcck_FieldType::DATE:
		if($option==='yyyy'){	//input year
			$html .= sprintf(_XCCK_TAG_DATE, $def->get('field_name'), $num, $default, 10, 4, 'year');
		}
		elseif($option==='yyyymm'){	//input year-month
			$html .= sprintf(_XCCK_TAG_DATE, $def->get('field_name'), $num, $default, 10, 7, 'year');
		}
		elseif($option==='yyyymmdd'){	//input year-month-day
			$html .= sprintf(_XCCK_TAG_DATE, $def->get('field_name'), $num, $default, 10, 10, 'datepicker');
		}
		elseif(isset($option['yyyy']) || $option['yyyymm']){	//select year
			$optionHtml = null;
			$options = $option['yyyy'] ?? $option['yyyymm'];
			$selected = null;
			foreach(array_keys($options) as $k){	//$k is value
				$selected = ($k==$default) ? ' selected="selected"' : null;
				$optionHtml .= sprintf(_XCCK_TAG_OPTION, $k, $selected, $option[$k]);
			}
			$html = sprintf(_XCCK_TAG_SELECT, $def->get('field_name'), $optionHtml);
			$html .= sprintf(_XCCK_TAG_HIDDEN, $def->get('field_name'), $num, $condition);
		}
	
		if($condition=='*'){
			$html .= Xcck_Utils::makeCondSelector($def, $num, [Xcck_Cond::EQ, Xcck_Cond::LIKE, Xcck_Cond::LE, Xcck_Cond::GE]);
		}
		else{
			$html .= sprintf(_XCCK_TAG_HIDDEN, $def->get('field_name'), $num, $condition);
		}
		break;
	case Xcck_FieldType::CHECKBOX:
		$checked = isset($default) ? ' checked="checked"' : null;
		$html = sprintf(_XCCK_TAG_CHECKBOX, $def->get('field_name'), $checked);
		
		break;
	case Xcck_FieldType::SELECTBOX:
		$optionHtml = null;
		$options = $def->getOptions();
		foreach($options as $opt){
			$selected = ($opt==$default) ? ' selected="selected"' : null;
			$optionHtml .= sprintf(_XCCK_TAG_OPTION, $opt, $selected, $opt);
		}
		$html = sprintf(_XCCK_TAG_SELECT, $def->get('field_name'), $optionHtml);
		$html .= sprintf(_XCCK_TAG_HIDDEN, $def->get('field_name'), $num, $condition);
		break;
	case Xcck_FieldType::CATEGORY:
		$tree = null;
		$category = Xcck_Utils::getAccessController($def->getDirname());
		XCube_DelegateUtils::call('Legacy_Category.'.$category->dirname().'.GetTree', new XCube_Ref($tree));
		require_once "function.legacy_category_select.php";
		$params =['tree'=>$tree];
		$html .= smarty_function_legacy_category_select($params, null);
		$html .= sprintf(_XCCK_TAG_HIDDEN, $def->get('field_name'), $num, $condition);
		break;
	}
	print $html;
}

?>
