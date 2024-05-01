<?php
/**
 * Protector module for XCL
 *
 * @package    Protector
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class protectorDbIntegrate {
	private $link = null;
	private string $api = 'mysql';

	public function __construct( $link ) {
		$this->link = $link;
		if ( is_object( $link ) && $link instanceof \mysqli ) {
			$this->api = 'mysqli';
		}
	}

	public function FieldFlags( $result, $field_offset ) {
		switch ( $this->api ) {
			case 'mysqli':
				$res = mysqli_fetch_field_direct( $result, $field_offset );
				if ( $res && $res->flags ) {
					$flags = $res->flags;
					if ( defined( 'MYSQLI_BINARY_FLAG' ) and ( $flags & MYSQLI_BINARY_FLAG ) ) {
						$flags .= ' BINARY';
					}

					return $flags;
				} else {
					return false;
				}
			default:
				return mysqli_field_flags( $result, $field_offset );
		}
	}

	public function FetchField( $result, $field_offset ) {
		$type_hash = [
			1   => 'tinyint',
			2   => 'smallint',
			3   => 'int',
			4   => 'float',
			5   => 'double',
			7   => 'timestamp',
			8   => 'bigint',
			9   => 'mediumint',
			10  => 'date',
			11  => 'time',
			12  => 'datetime',
			13  => 'year',
			16  => 'bit',
			252 => 'blob',
			253 => 'varchar',
			254 => 'char',
			246 => 'decimal'
		];
		switch ( $this->api ) {
			case 'mysqli':
				$res = mysqli_fetch_field_direct( $result, $field_offset );
				if ( isset( $type_hash[ $res->type ] ) ) {
					$res->type = $type_hash[ $res->type ];
				}

				return $res;
			default:
				return mysqli_fetch_field( $result );
		}
	}
}
