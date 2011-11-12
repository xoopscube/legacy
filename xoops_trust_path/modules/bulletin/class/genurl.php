<?php

class genUrl{

	var $path;
	var $file;
	var $parameter;

	function genUrl(){

		return true;
	}

	function setPath($path = ''){

		$this->path = $path;
		return $this->path;
	}

	function setFile($file = ''){

		$this->file = $file;
		return $this->file;
	}

	function setParameter($key='', $value=''){

		$this->parameter[$key] = $value;
		return $this->parameter;
	}
	
	function setParameters($parameters = array()){

		if( is_array( $parameters ) ){
			foreach($parameters as $k => $v ){
				$this->setParameter($k, $v);
			}
		}
		return $this->parameter;
	}

	function makeParameter($eq = '=', $and ='&'){

		$ret = '';
		foreach($this->parameter as $k => $v ){
			$ret .= "$k$eq$v$and";
		}
		$ret = substr($ret, 0, strlen($and)*-1);
		return $ret;
	}

	function makePath(){

		$ret = $this->path;
		if( !preg_match('/\/$/', $this->path) ){
			$ret .= '/';
		}
		$ret .= $this->file;
		return $ret;
	}

	function makeURL($ques = '?', $eq = '=', $and ='&'){
	
		$ret = $this->makePath();
		if( $parameter = $this->makeParameter($eq, $and) ){
			$ret .= $ques.$parameter;
		}
		return $ret;
	}

	function makeURLforHTML(){

		return htmlspecialchars($this->makeURL());
	}

	function makeStaticURL(){

		return $this->makeURL('/','.','/');
	}
}


?>