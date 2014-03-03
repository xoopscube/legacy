<?php
class protectorDbIntegrate {
	private $link = null;
	private $api = 'mysql';
	
	function __construct($link) {
		$this->link = $link;
		if (is_object($link) && get_class($link) === 'mysqli') {
			$this->api = 'mysqli';
		}
	}
	
	function FieldFlags($result, $field_offset) {
		switch($this->api) {
			case 'mysqli':
				$res = mysqli_fetch_field_direct($result, $field_offset);
				if  ($res && $res->flags) {
					$flags = $res->flags;
					if (defined('MYSQLI_BINARY_FLAG') and ($flags & MYSQLI_BINARY_FLAG)) {
						$flags .= ' BINARY';
					}
					return $flags;
				} else {
					return false;
				}
			default :
				return mysql_field_flags($result, $field_offset);
		}
	}
	
	function FetchField($result, $field_offset) {
		$type_hash = array(
			1=>'tinyint',
			2=>'smallint',
			3=>'int',
			4=>'float',
			5=>'double',
			7=>'timestamp',
			8=>'bigint',
			9=>'mediumint',
			10=>'date',
			11=>'time',
			12=>'datetime',
			13=>'year',
			16=>'bit',
			252=>'blob',
			253=>'varchar',
			254=>'char',
			246=>'decimal'
		);
		switch($this->api) {
			case 'mysqli':
				$res = mysqli_fetch_field_direct($result, $field_offset);
				if (isset($type_hash[$res->type])) {
					$res->type = $type_hash[$res->type];
				}
				return $res;
			default :
				return mysql_fetch_field($result, $field_offset);
		}
	}
}