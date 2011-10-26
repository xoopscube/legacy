<?php

// a sample validator
function formprocess_validator_japanese_prefectures( $value , $field_name , &$processor )
{
	static $prefectures = array(1=>'ËÌ³¤Æ»',2=>'ÀÄ¿¹¸©',3=>'´ä¼ê¸©',4=>'µÜ¾ë¸©',5=>'½©ÅÄ¸©',6=>'»³·Á¸©',7=>'Ê¡Åç¸©',8=>'°ñ¾ë¸©',9=>'ÆÊÌÚ¸©',10=>'·²ÇÏ¸©',11=>'ºë¶Ì¸©',12=>'ÀéÍÕ¸©',13=>'ÅìµþÅÔ',14=>'¿ÀÆàÀî¸©',15=>'¿·³ã¸©',16=>'ÉÙ»³¸©',17=>'ÀÐÀî¸©',18=>'Ê¡°æ¸©',19=>'»³Íü¸©',20=>'Ä¹Ìî¸©',21=>'´ôÉì¸©',22=>'ÀÅ²¬¸©',23=>'°¦ÃÎ¸©',24=>'»°½Å¸©',25=>'¼¢²ì¸©',26=>'µþÅÔÉÜ',27=>'ÂçºåÉÜ',28=>'Ê¼¸Ë¸©',29=>'ÆàÎÉ¸©',30=>'ÏÂ²Î»³¸©',31=>'Ä»¼è¸©',32=>'Åçº¬¸©',33=>'²¬»³¸©',34=>'¹­Åç¸©',35=>'»³¸ý¸©',36=>'ÆÁÅç¸©',37=>'¹áÀî¸©',38=>'°¦É²¸©',39=>'¹âÃÎ¸©',40=>'Ê¡²¬¸©',41=>'º´²ì¸©',42=>'Ä¹ºê¸©',43=>'·§ËÜ¸©',44=>'ÂçÊ¬¸©',45=>'µÜºê¸©',46=>'¼¯»ùÅç¸©',47=>'²­Æì¸©' ) ;

	$value4check = mb_convert_encoding( @$value , 'EUC-JP' , _CHARSET ) ;
	if( ! empty( $value ) && ! in_array( $value4check , $prefectures ) ) {
		$processor->fields[ $field_name ]['errors'][] = 'invalid general' ;
	}

	return $value ;
}

?>