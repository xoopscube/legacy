<?php

require_once XOOPS_ROOT_PATH."/class/xoopsform/formselect.php";
require_once XOOPS_ROOT_PATH."/class/xoopsform/formelementtray.php";
require_once XOOPS_ROOT_PATH."/class/xoopsform/formlabel.php";

class XoopsFormSelectTime extends XoopsFormElementTray
{

	var $year;
	var $month;
	var $day;
	var $hour;
	var $min;
	var $sec;

	function XoopsFormSelectTime($caption, $name, $value=0, $format="%y-%m-%d %h:%i")
	{
		
		$value = intval($value);
		
		if( empty( $value ) ){
			$time = time();
			$this->year  = formatTimestamp($time, 'Y');
			$this->month = formatTimestamp($time, 'n');
			$this->day   = formatTimestamp($time, 'd');
			$this->hour  = formatTimestamp($time, 'H');
			$this->min   = formatTimestamp($time, 'i');
			$this->sec   = date('s', $time);
		}else{
			$this->year  = formatTimestamp($value, 'Y');
			$this->month = formatTimestamp($value, 'n');
			$this->day   = formatTimestamp($value, 'd');
			$this->hour  = formatTimestamp($value, 'H');
			$this->min   = formatTimestamp($value, 'i');
			$this->sec   = date('s', $value);
		}

		
		$this->XoopsFormElementTray($caption, '');

		$thsy = date('Y');
		$year_select  = new XoopsFormSelect('', $name.'[year]', $this->year);
//		$year_select  ->addOptionArray(array($thsy-8=>$thsy-8, $thsy-7, $thsy-6, $thsy-5, $thsy-4, $thsy-3, $thsy-2, $thsy-1, $thsy, $thsy+1));
		for($y=1970;$y<2038;$y++) $year_select->addOption($y,$y); // GIJ
		$month_select = new XoopsFormSelect('', $name.'[month]', $this->month);
		$month_select ->addOptionArray(array(1=>1,2,3,4,5,6,7,8,9,10,11,12));
		$day_select   = new XoopsFormSelect('', $name.'[day]', $this->day);
		$day_select   ->addOptionArray(array(1=>1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31));
		$hour_select  = new XoopsFormSelect('', $name.'[hour]', $this->hour);
		$hour_select  ->addOptionArray(array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23));
		$sixty_option = array();
		for ($i=0; $i<60; $i++) $sixty_option[] = $i;
		
		$min_select   = new XoopsFormSelect('', $name.'[min]', $this->min);
		$min_select   ->addOptionArray($sixty_option);			
		$sec_select   = new XoopsFormSelect('', $name.'[sec]', $this->sec);
		$sec_select   ->addOptionArray($sixty_option);
		
		$format = preg_replace('/%y/i', $year_select->render(), $format );
		$format = preg_replace('/%m/i', $month_select->render(), $format );
		$format = preg_replace('/%d/i', $day_select->render(), $format );
		$format = preg_replace('/%h/i', $hour_select->render(), $format );
		$format = preg_replace('/%i/i', $min_select->render(), $format );
		$format = preg_replace('/%s/i', $sec_select->render(), $format );

		$base_label   = new XoopsFormLabel('', $format);

		$this->addElement($base_label);
	}
}
?>