<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors gusagi, 2008/03/22
 * @author     Haruki Setoyama
 * @copyright  (c) 2000-2003 Authors
 * @license    GPL 2.0
 */

include_once XOOPS_ROOT_PATH . '/class/logger.php';
include_once XOOPS_ROOT_PATH . '/class/database/databasefactory.php';
include_once XOOPS_ROOT_PATH . '/class/database/' . XOOPS_DB_TYPE . 'database.php';
include_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';


class db_manager {

	public $s_tables = [];
	public $f_tables = [];
	public $db;

	public function __construct() {
		$this->db = XoopsDatabaseFactory::getDatabase();
		$this->db->setPrefix( XOOPS_DB_PREFIX );
		$this->db->setLogger( XoopsLogger::instance() );
	}

	public function connectDB( $selectdb = true ) {
		$ret = $this->db->connect( $selectdb );
		if ( false !== $ret ) {
			$fname = dirname( __DIR__ ) . '/language/' . $GLOBALS['language'] . '/charset_mysql.php';
			if ( file_exists( $fname ) ) {
				require( $fname );
			}
		}

		return $ret;
	}

	public function isConnectable() {
        return $this->connectDB(false) != false;
	}

	public function dbExists() {
        return $this->connectDB() != false;
	}

	public function createDB() {
        $this->connectDB(false);

        $result = $this->db->query("CREATE DATABASE ".XOOPS_DB_NAME);

        return $result != false;
	}

	public function queryFromFile( $sql_file_path ) {
		$tables = [];

		if ( ! file_exists( $sql_file_path ) ) {
			return false;
		}
		$sql_query = trim( fread( fopen( $sql_file_path, 'r' ), filesize( $sql_file_path ) ) );
		SqlUtility::splitMySqlFile( $pieces, $sql_query );
		$this->connectDB();
		foreach ( $pieces as $piece ) {
			$piece = trim( $piece );
			// [0] contains the prefixed query
			// [4] contains unprefixed table name
			$prefixed_query = SqlUtility::prefixQuery( $piece, $this->db->prefix() );
			if ( false !== $prefixed_query ) {
				$table = $this->db->prefix( $prefixed_query[4] );
				if ( 'CREATE TABLE' === $prefixed_query[1] ) {
					if ( false !== $this->db->query( $prefixed_query[0] ) ) {
						if ( ! isset( $this->s_tables['create'][ $table ] ) ) {
							$this->s_tables['create'][ $table ] = 1;
						}
					} else {
						if ( ! isset( $this->f_tables['create'][ $table ] ) ) {
							$this->f_tables['create'][ $table ] = 1;
						}
					}
				} elseif ( 'INSERT INTO' === $prefixed_query[1] ) {
					if ( false !== $this->db->query( $prefixed_query[0] ) ) {
						if ( ! isset( $this->s_tables['insert'][ $table ] ) ) {
							$this->s_tables['insert'][ $table ] = 1;
						} else {
							$this->s_tables['insert'][ $table ] ++;
						}
					} else if ( ! isset( $this->f_tables['insert'][ $table ] ) ) {
						$this->f_tables['insert'][ $table ] = 1;
					} else {
						$this->f_tables['insert'][ $table ] ++;
					}
				} elseif ( 'ALTER TABLE' === $prefixed_query[1] ) {
					if ( false !== $this->db->query( $prefixed_query[0] ) ) {
						if ( ! isset( $this->s_tables['alter'][ $table ] ) ) {
							$this->s_tables['alter'][ $table ] = 1;
						}
					} else if ( ! isset( $this->s_tables['alter'][ $table ] ) ) {
						$this->f_tables['alter'][ $table ] = 1;
					}
				} elseif ( 'DROP TABLE' === $prefixed_query[1] ) {
					if ( false !== $this->db->query( 'DROP TABLE ' . $table ) ) {
						if ( ! isset( $this->s_tables['drop'][ $table ] ) ) {
							$this->s_tables['drop'][ $table ] = 1;
						}
					} else if ( ! isset( $this->s_tables['drop'][ $table ] ) ) {
						$this->f_tables['drop'][ $table ] = 1;
					}
				}
			}
		}

		return true;
	}

	public function report() {
		$reports = [];
		if ( isset( $this->s_tables['create'] ) ) {
			foreach ( $this->s_tables['create'] as $key => $val ) {
				$reports[] = _OKIMG . sprintf( _INSTALL_L45, "<b>$key</b>" );
			}
		}
		if ( isset( $this->s_tables['insert'] ) ) {
			foreach ( $this->s_tables['insert'] as $key => $val ) {
				$reports[] = _OKIMG . sprintf( _INSTALL_L119, $val, "<b>$key</b>" );
			}
		}
		if ( isset( $this->s_tables['alter'] ) ) {
			foreach ( $this->s_tables['alter'] as $key => $val ) {
				$reports[] = _OKIMG . sprintf( _INSTALL_L133, "<b>$key</b>" );
			}
		}
		if ( isset( $this->s_tables['drop'] ) ) {
			foreach ( $this->s_tables['drop'] as $key => $val ) {
				$reports[] = _OKIMG . sprintf( _INSTALL_L163, "<b>$key</b>" );
			}
		}
//        $content .= "<br>\n";	//< What's!?
		if ( isset( $this->f_tables['create'] ) ) {
			foreach ( $this->f_tables['create'] as $key => $val ) {
				$reports[] = _NGIMG . sprintf( _INSTALL_L118, "<b>$key</b>" );
			}
		}
		if ( isset( $this->f_tables['insert'] ) ) {
			foreach ( $this->f_tables['insert'] as $key => $val ) {
				$reports[] = _NGIMG . sprintf( _INSTALL_L120, $val, "<b>$key</b>" );
			}
		}
		if ( isset( $this->f_tables['alter'] ) ) {
			foreach ( $this->f_tables['alter'] as $key => $val ) {
				$reports[] = _NGIMG . sprintf( _INSTALL_L134, "<b>$key</b>" );
			}
		}
		if ( isset( $this->f_tables['drop'] ) ) {
			foreach ( $this->f_tables['drop'] as $key => $val ) {
				$reports[] = _NGIMG . sprintf( _INSTALL_L164, "<b>$key</b>" );
			}
		}

		return $reports;
	}

	public function query( $sql ) {
		$this->connectDB();

		return $this->db->query( $sql );
	}

	public function prefix( $table ) {
		$this->connectDB();

		return $this->db->prefix( $table );
	}

	public function fetchArray( $ret ) {
		$this->connectDB();

		return $this->db->fetchArray( $ret );
	}

	public function insert( $table, $query ) {
		$this->connectDB();
		$table = $this->db->prefix( $table );
		$query = 'INSERT INTO ' . $table . ' ' . $query;
		if ( ! $this->db->queryF( $query ) ) {
			if ( ! isset( $this->f_tables['insert'][ $table ] ) ) {
				$this->f_tables['insert'][ $table ] = 1;
			} else {
				$this->f_tables['insert'][ $table ] ++;
			}

			return false;
		}

		if ( ! isset( $this->s_tables['insert'][ $table ] ) ) {
			$this->s_tables['insert'][ $table ] = 1;
		} else {
			$this->s_tables['insert'][ $table ] ++;
		}

		return $this->db->getInsertId();
	}

	public function isError() {
		//return ( isset( $this->f_tables ) ) ? true : false;
        return isset( $this->f_tables );
	}

	public function deleteTables( $tables ) {
		$deleted = [];
		$this->connectDB();
		foreach ( $tables as $key => $val ) {
			if ( ! $this->db->query( 'DROP TABLE ' . $this->db->prefix( $key ) ) ) {
				$deleted[] = $ct;
			}
		}

		return $deleted;
	}

	public function tableExists( $table ) {
		$table = trim( $table );
		$ret   = false;
		if ( $table !== '' ) {
			$this->connectDB();
			$sql = 'SELECT * FROM ' . $this->db->prefix( $table );
			$ret = false !== $this->db->query( $sql );
		}

		return $ret;
	}
}
