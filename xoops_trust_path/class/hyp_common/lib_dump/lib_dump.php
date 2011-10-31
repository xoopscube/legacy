<?php
/**
* Dump MySQL database
*
* Here is an inline example:
* <code>
* $connection = @mysql_connect($dbhost,$dbuser,$dbpsw);
* $dumper = new MySQLDump($dbname,'filename.sql',false,false);
* $dumper->doDump();
* </code>
*
* Special thanks to:
* - Andrea Ingaglio <andrea@coders4fun.com> helping in development of all class code
* - Dylan Pugh for precious advices halfing the size of the output file and for helping in debug
*
* @name    MySQLDump
* @author  Daniele Viganè´“ - CreativeFactory.it <daniele.vigano@creativefactory.it>
* @version 2.20 - 02/11/2007
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

/**
* Edited by nao-pon
* Optimized for environment that has selected data base.
**/

class MySQLDump {
	/**
	* @access private
	*/
	var $database = null;

	/**
	* @access private
	*/
	var $compress = false;

	/**
	* @access private
	*/
	var $hexValue = false;

  /**
	* The output filename
	* @access private
	*/
	var $filename = null;

	/**
	* The pointer of the output file
	* @access private
	*/
	var $file = null;

	/**
	* @access private
	*/
	var $isWritten = false;


	var $removePrefix = '';

	var $fileSize = 0;
	var $maxFileSize = 0;

	var $sqlFiles = array();
	var $baseName = '';
	var $extName = '';

	/**
	* Class constructor
	* @param string $db The database name
	* @param string $filepath The file where the dump will be written
	* @param boolean $compress It defines if the output file is compress (gzip) or not
	* @param boolean $hexValue It defines if the outup values are base-16 or not
	*/
	function MYSQLDump($db = null, $filepath = 'dump.sql', $compress = false, $hexValue = false){
		$this->compress = $compress;
		if ( !$this->setOutputFile($filepath) )
			return false;
		return $this->setDatabase($db);
	}

	/**
	* Sets the database to work on
	* @param string $db The database name
	*/
	function setDatabase($db){
		if (is_null($db)) return true;
		$this->database = $db;
		if ( !@mysql_select_db($this->database) )
			return false;
		return true;
  }

	/**
	* Returns the database where the class is working on
	* @return string
	*/
  function getDatabase(){
		return $this->database;
	}

	/**
	* Sets the output file type (It can be made only if the file hasn't been already written)
	* @param boolean $compress If it's true, the output file will be compressed
	*/
	function setCompress($compress){
		if ( $this->isWritten )
			return false;
		$this->compress = $compress;
		$this->openFile($this->filename);
		return true;
  }

	/**
	* Returns if the output file is or not compressed
	* @return boolean
	*/
  function getCompress(){
		return $this->compress;
	}

	/**
	* Sets the output file
	* @param string $filepath The file where the dump will be written
	*/
	function setOutputFile($filepath){
		if ( $this->isWritten )
			return false;
		$this->baseName = $this->filename = $filepath;
		$this->file = $this->openFile($this->filename);

		if (preg_match('#^(.+?)(\.[^.]+)$#', $this->filename, $match)) {
			$this->baseName = $match[1];
			$this->extName = $match[2];
		}
		if ($this->file) {
			$this->sqlFiles[] = $this->filename;
		}
		return $this->file;
  }

  /**
	* Returns the output filename
	* @return string
	*/
  function getOutputFile(){
		return $this->filename;
	}

	/**
	* Writes to file the $table's structure
	* @param string $table The table name
	*/
  function getTableStructure($table){
		if ( !$this->setDatabase($this->database) )
			return false;
		$table_name = $table;
		if ($this->removePrefix) {
			$table_name = substr($table_name, strlen($this->removePrefix));
		}
		// Structure Header
		$structure = "-- \n";
		$structure .= "-- Table structure for table `{$table_name}` \n";
		$structure .= "-- \n\n";
		// Dump Structure
		$structure .= 'DROP TABLE IF EXISTS `'.$table_name.'`;'."\n";
		$structure .= "CREATE TABLE `".$table_name."` (\n";
		$records = @mysql_query('SHOW FIELDS FROM `'.$table.'`');
		if ( @mysql_num_rows($records) == 0 )
			return false;
		while ( $record = mysql_fetch_assoc($records) ) {
			$structure .= '`'.$record['Field'].'` '.$record['Type'];
			if ( !empty($record['Default']) )
				$structure .= ' DEFAULT \''.$record['Default'].'\'';
			if ( @strcmp($record['Null'],'YES') != 0 )
				$structure .= ' NOT NULL';
			if ( !empty($record['Extra']) )
				$structure .= ' '.$record['Extra'];
			$structure .= ",\n";
		}
		$structure = @ereg_replace(",\n$", null, $structure);

		// Save all Column Indexes
		$structure .= $this->getSqlKeysTable($table);
		$structure .= "\n)";

		//Save table engine
		$records = @mysql_query("SHOW TABLE STATUS LIKE '".$table."'");

		if ( $record = @mysql_fetch_assoc($records) ) {
			if ( !empty($record['Engine']) )
				$structure .= ' ENGINE='.$record['Engine'];
			if ( !empty($record['Auto_increment']) )
				$structure .= ' AUTO_INCREMENT='.$record['Auto_increment'];
		}

		$structure .= ";\n\n-- --------------------------------------------------------\n\n";
		$this->saveToFile($this->file,$structure);
	}

	/**
	* Writes to file the $table's data
	* @param string $table The table name
	* @param boolean $hexValue It defines if the output is base 16 or not
	*/
	function getTableData($table,$hexValue = true) {
		if ( !$this->setDatabase($this->database) )
			return false;

		$table_name = $table;
		if ($this->removePrefix) {
			$table_name = substr($table_name, strlen($this->removePrefix));
		}

		// Header
		$data = "-- \n";
		$data .= "-- Dumping data for table `$table_name` \n";
		$data .= "-- \n\n";

		$records = mysql_query('SHOW FIELDS FROM `'.$table.'`');
		$num_fields = @mysql_num_rows($records);
		if ( $num_fields == 0 )
			return false;
		// Field names
		$selectStatement = "SELECT ";
		$insertStatement = "INSERT INTO `$table_name` (";
		$hexField = array();
		for ($x = 0; $x < $num_fields; $x++) {
			$record = @mysql_fetch_assoc($records);
			if ( ($hexValue) && ($this->isTextValue($record['Type'])) ) {
				$selectStatement .= 'HEX(`'.$record['Field'].'`)';
				$hexField [$x] = true;
			}
			else
				$selectStatement .= '`'.$record['Field'].'`';
			$insertStatement .= '`'.$record['Field'].'`';
			$insertStatement .= ", ";
			$selectStatement .= ", ";
		}
		$insertStatement = @substr($insertStatement,0,-2).') VALUES';
		$selectStatement = @substr($selectStatement,0,-2).' FROM `'.$table.'`';
		$closeStatement = "\n-- --------------------------------------------------------\n\n";
		$closeStatement .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";

		$records = @mysql_query($selectStatement);
		$num_rows = @mysql_num_rows($records);
		$num_fields = @mysql_num_fields($records);

		$_i = 0;
		$dataLen = 0;
		$statementLen = strlen($insertStatement);
		if (function_exists('mysql_real_escape_string')) {
			$func_mysql_escape = create_function('$str', 'return mysql_real_escape_string($str);');
		} else {
			$func_mysql_escape = create_function('$str', 'return mysql_escape_string($str);');
		}
		// Dump data
		if ( $num_rows > 0 ) {
			$data .= $insertStatement;
			$buf = '';
			for ($i = 0; $i < $num_rows; $i++) {
				$record = @mysql_fetch_assoc($records);
				$statementLen += strlen($buf);
				$next = ' (';
				for ($j = 0; $j < $num_fields; $j++) {
					$field_name = @mysql_field_name($records, $j);
					if ( isset($hexField[$j]) && (@strlen($record[$field_name]) > 0) ) {
						$next .= "0x".$record[$field_name];
					} else {
						$next .= "'".@str_replace('\"', '"', $func_mysql_escape($record[$field_name]))."'";
					}
					$next .= ',';
				}
				$next = rtrim($next, ',').')';
				$next .= ( $i < ($num_rows-1) ) ? ',' : ';';
				$next .= "\n";

				$chkSize = strlen($buf.$next.$closeStatement);
				$fileFull = ($this->maxFileSize && $this->maxFileSize < ($this->fileSize + $dataLen + $chkSize));
				$statementFull = (1048576 < $statementLen + strlen($next));

				if ($fileFull || $statementFull) {

					if ($buf) {
						$buf = substr($buf, 0, -2) . ";\n";
					}

					if ($fileFull) {
						$this->saveToFile($this->file, ($data . $buf . $closeStatement));
						$this->closeFile($this->file);
						$name = $this->baseName . (++$_i) . $this->extName;
						$this->setOutputFile($name);
					} else {
						$data .= $buf;
					}

					$buf = '';

					if ($fileFull) {
						$data = "SET FOREIGN_KEY_CHECKS = 1;\n\n";
						$data .= "-- \n";
						$data .= "-- Dumping data for table `$table_name` \n";
						$data .= "-- \n\n";
					}
					$data .= $insertStatement;
					$statementLen = strlen($insertStatement);
				}

				$data .= $buf;
				$buf = $next;

				//if data in greather than 1MB save
				$dataLen = strlen($data);
				if ($dataLen > 1048576) {
					$this->saveToFile($this->file,$data);
					$data = '';
					$dataLen = 0;
				}
			}
			$data .= $buf;
			$data .= "\n-- --------------------------------------------------------\n\n";
			$this->saveToFile($this->file,$data);
		}
	}

  /**
	* Writes to file all the selected database tables structure
	* @return boolean
	*/
	function getDatabaseStructure($table){
		if ($table) {
			$this->getTableStructure($table);
		} else {
			$records = @mysql_query('SHOW TABLES');
			if ( @mysql_num_rows($records) == 0 )
				return false;
			while ( $record = @mysql_fetch_row($records) ) {
				$this->getTableStructure($record[0]);
			}
		}
		return true;
  }

	/**
	* Writes to file all the selected database tables data
	* @param boolean $hexValue It defines if the output is base-16 or not
	*/
	function getDatabaseData($hexValue = true){
		$records = @mysql_query('SHOW TABLES');
		if ( @mysql_num_rows($records) == 0 )
			return false;
		while ( $record = @mysql_fetch_row($records) ) {
			$this->getTableData($record[0],$hexValue);
		}
  }

	/**
	* Writes to file the selected database dump
	*/
	function doDump($table = null) {
		$this->saveToFile($this->file,"SET FOREIGN_KEY_CHECKS = 0;\n\n");
		$this->getDatabaseStructure($table);
		if ($table) {
			$this->getTableData($table, $this->hexValue);
		} else {
			$this->getDatabaseData($this->hexValue);
		}
		$this->saveToFile($this->file,"SET FOREIGN_KEY_CHECKS = 1;\n\n");
		$this->closeFile($this->file);
		return true;
	}

	/**
	* @deprecated Look at the doDump() method
	*/
	function writeDump($filename) {
		if ( !$this->setOutputFile($filename) )
			return false;
		$this->doDump();
    $this->closeFile($this->file);
    return true;
	}

	/**
	* @access private
	*/
	function getSqlKeysTable ($table) {
		$primary = "";
		unset($unique);
		unset($index);
		unset($fulltext);
		$results = mysql_query("SHOW KEYS FROM `{$table}`");
		if ( @mysql_num_rows($results) == 0 )
			return false;
		while($row = mysql_fetch_object($results)) {
			if (($row->Key_name == 'PRIMARY') AND ($row->Index_type == 'BTREE')) {
				if ( $primary == "" )
					$primary = "  PRIMARY KEY  (`{$row->Column_name}`";
				else
					$primary .= ", `{$row->Column_name}`";
			}
			if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '0') AND ($row->Index_type == 'BTREE')) {
				if ( (!is_array($unique)) OR ($unique[$row->Key_name]=="") )
					$unique[$row->Key_name] = "  UNIQUE KEY `{$row->Key_name}` (`{$row->Column_name}`";
				else
					$unique[$row->Key_name] .= ", `{$row->Column_name}`";
				if (! empty($row->Sub_part)) {
					$index[$row->Key_name] .= "({$row->Sub_part})";
				}
			}
			if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'BTREE')) {
				if ( (!is_array($index)) OR ($index[$row->Key_name]=="") )
					$index[$row->Key_name] = "  KEY `{$row->Key_name}` (`{$row->Column_name}`";
				else
					$index[$row->Key_name] .= ", `{$row->Column_name}`";
				if (! empty($row->Sub_part)) {
					$index[$row->Key_name] .= "({$row->Sub_part})";
				}
			}
			if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'FULLTEXT')) {
				if ( (!is_array($fulltext)) OR ($fulltext[$row->Key_name]=="") )
					$fulltext[$row->Key_name] = "  FULLTEXT `{$row->Key_name}` (`{$row->Column_name}`";
				else
					$fulltext[$row->Key_name] .= ", `{$row->Column_name}`";
			}
		}
		$sqlKeyStatement = '';
		// generate primary, unique, key and fulltext
		if ( $primary != "" ) {
			$sqlKeyStatement .= ",\n";
			$primary .= ")";
			$sqlKeyStatement .= $primary;
		}
		if (is_array($unique)) {
			foreach ($unique as $keyName => $keyDef) {
				$sqlKeyStatement .= ",\n";
				$keyDef .= ")";
				$sqlKeyStatement .= $keyDef;

			}
		}
		if (is_array($index)) {
			foreach ($index as $keyName => $keyDef) {
				$sqlKeyStatement .= ",\n";
				$keyDef .= ")";
				$sqlKeyStatement .= $keyDef;
			}
		}
		if (is_array($fulltext)) {
			foreach ($fulltext as $keyName => $keyDef) {
				$sqlKeyStatement .= ",\n";
				$keyDef .= ")";
				$sqlKeyStatement .= $keyDef;
			}
		}
		return $sqlKeyStatement;
	}

  /**
	* @access private
	*/
	function isTextValue($field_type) {
		switch ($field_type) {
			case "tinytext":
			case "text":
			case "mediumtext":
			case "longtext":
			case "binary":
			case "varbinary":
			case "tinyblob":
			case "blob":
			case "mediumblob":
			case "longblob":
				return True;
				break;
			default:
				return False;
		}
	}

	/**
	* @access private
	*/
	function openFile($filename) {
		$file = false;
		if ( $this->compress )
			$file = @gzopen($filename, "w9");
		else
			$file = @fopen($filename, "w");
		return $file;
	}

  /**
	* @access private
	*/
	function saveToFile($file, $data) {
		$this->fileSize += strlen($data);
		if ( $this->compress ) {
			@gzwrite($file, $data);
		} else {
			@fwrite($file, $data);
		}
		fflush($file);
		$this->isWritten = true;
	}

  /**
	* @access private
	*/
	function closeFile($file) {
		$this->fileSize = 0;
		if ( $this->compress )
			@gzclose($file);
		else
			@fclose($file);
		$this->isWritten = false;
	}
}
?>