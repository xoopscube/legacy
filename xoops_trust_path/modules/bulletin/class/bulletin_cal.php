<?php

class Bulletin_Cal
{

	var $year;
	var $month;
	var $day;
	var $weekday;
	var $lang_week = array('Sun','Mon','Tue','Wed','The','Fri','Sat');
	var $link = array();
	var $title = '';
	var $timestamp;
	var $lang_p_mon = '&lt;';
	var $lang_n_mon = '&gt;';
	var $startday;
	var $endday;
	var $query = 'today';
	
	
	function setDate($today="", $startday=0, $endday=0)
	{
		if(preg_match('/([0-9]{4})-([0-9]{2})/', $today, $todayarr)){
			$year  = $todayarr[1];
			$month = $todayarr[2];
		}else{
			$year  = date('Y');
			$month = date('m');
		}
		
		if(!checkdate($month,1,$year)){
			$year  = date('Y');
			$month = date('m');
		}
		
		//if( !empty($startday) && $year*100+$month < intval(date('Ym',$startday))){
		//	$year  = date('Y', $startday);
		//	$month = date('m', $startday);
		//}
		
		//if( !empty($endday) && $year*100+$month > intval(date('Ym',$endday))){
		//	$year  = date('Y', $endday);
		//	$month = date('m', $endday);
		//}
		
		if( !empty($endday) && date('Ym') > intval(date('Ym',$endday))){
			$endday = time();
		}

		$weekday = intval(date('w', mktime(0,0,0,$month,1,$year)));

		$this->year      = intval( $year );
		$this->month     = intval( $month );
		$this->weekday   = intval( $weekday );
		$this->startday  = intval( $startday );
		$this->endday    = intval( $endday );
		$this->timestamp = mktime(0,0,0,$month,1,$year);
	}
	
	function setWeekName($week="")
	{
		if( is_array($week) && count($week)==7){
			$this->lang_week = $week;
			return true;
		}
		return false;
	}
	
	function setLink($day, $url)
	{
		$day = intval($day);
		$this->link[$day] = $url;
	}
	
	function setTitle($title = 'Y-m')
	{
		$this->title = date($title, $this->timestamp);
	}

	function setQueryStr($query = 'today')
	{
		$this->query = $query;
	}
	
	function getCalendar()
	{
		$w = $this->weekday;
		$m = $this->month;
		$y = $this->year;
		$d = $this->day;
		
		$ret  = array();

		for($i=0; $i<7; $i++){
			$ret[0][$i]['label'] = $this->lang_week[$i];
			$ret[0][$i]['link']  = '';
		}
		for($i=0; $i<$w; $i++){
			$ret[1][$i]['label'] = '';
			$ret[1][$i]['link']  = '';
		}

		$i = 1;
		$l = 1;
		while(checkdate($m,$i,$y)){
		
			if( isset($this->link[$i]) ){
				$ret[$l][$w]['link'] = $this->link[$i];
			}else{
				$ret[$l][$w]['link'] = '';
			}
			
			     if($w == 0){ 
				$ret[$l][$w]['label'] = $i; 
			}elseif($w == 1){ 
				$ret[$l][$w]['label'] = $i; 
			}elseif($w == 2){ 
				$ret[$l][$w]['label'] = $i; 
			}elseif($w == 3){ 
				$ret[$l][$w]['label'] = $i; 
			}elseif($w == 4){ 
				$ret[$l][$w]['label'] = $i; 
			}elseif($w == 5){ 
				$ret[$l][$w]['label'] = $i; 
			}elseif($w == 6){ 
				$ret[$l][$w]['label'] = $i; 
			}
			
			if($w == 6){
				$l++;
			}
			
			$i++;
			$w++;
			$w = $w % 7;
		}
		
		if($w > 0){
			while($w < 7){
				$ret[$l][$w]['label'] = ''; 
				$ret[$l][$w]['link']  = ''; 
				$w++;
			}
		}
		
		return $ret;
	}

	
	function getThemeCalendar()
	{
		$w = $this->weekday;
		$m = $this->month;
		$y = $this->year;
		
		$ret  = '<table border="0" class="outer" cellspacing="1">';
		$ret .= $this->getTitleBar();
		
		foreach( $this->getCalendar() as $line => $weeks ){
		
			$ret .= '<tr align="center">';
			foreach($weeks as $weekday){
				$style = ( $line > 0 ) ? 'even' : 'head';
				//$style = ( $style == 'even' && !empty($weekday['link']) ) ? 'odd' : $style;
				$text = empty($weekday['label']) ? '&nbsp;' : $weekday['label'] ;
				$text = empty($weekday['link'])  ? $text    : '<a href="'.$weekday['link'].'">'.$text.'</a>';
				$ret .= '<td class="'.$style.'">'.$text.'</td>';
			}
			$ret .= '</tr>';
		
		}
		
		$ret .= '</tr></table>';
		
		return $ret;
	}
	
	function getTitleBar()
	{
		$w = $this->weekday;
		$m = $this->month;
		$y = $this->year;
		
		if( !empty($this->title)){
			$p_month = date("Y-m", mktime(0,0,0,$m,0,$y));
			$n_month = date("Y-m", mktime(0,0,0,$m+1,1,$y));

			$ret  = '<tr>';
			$ret .= '<th align="center" colspan="7" nowrap="nowrap">';
			if( empty($this->startday) || $y*100+$m-1 >= intval(date('Ym',$this->startday))){
				$ret .= '<a href="?'.$this->query.'='.$p_month.'">'.$this->lang_p_mon.'</a>';
			}
			$ret .= '&nbsp;&nbsp;';
			$ret .= $this->title;
			$ret .= '&nbsp;&nbsp;';
			if( empty($this->endday) || $y*100+$m+1 <= intval( date('Ym',$this->endday) ) ){
				$ret .= '<a href="?'.$this->query.'='.$n_month.'">'.$this->lang_n_mon.'</a>';
			}
			$ret .= '</th></tr>';
		}
		
		return $ret;
	}
}
?>