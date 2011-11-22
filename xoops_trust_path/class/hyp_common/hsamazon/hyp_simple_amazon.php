<?php
class HypSimpleAmazon
{
	var $myDirectory;

	var $Location = 'JP';
	var $AccessKeyId = '0F1572ZQ7P3BTFJ28RR2';
	var $SecretAccessKey = '';
	var $ResponseGroup = 'ItemAttributes,Images,Offers,Variations';
	var $SearchIndex = 'All';
	var $Version = '2010-11-01';
	var $batchMax = 2;
	var $AssociateTag = '';
	var $Operation = '';
	var $restHost = 'ecs.amazonaws.jp';
	var $restPath = '/onca/xml';
	var $searchHost = 'www.amazon.co.jp';
	var $searchQuery = '/gp/search?ie=UTF8&amp;keywords=<keyword>&amp;tag=<tag>&amp;index=blended&amp;linkCode=ur2&amp;camp=247&amp;creative=1211';
	var $redirectQuery = '/gp/redirect.html?ie=UTF8&amp;location=<url>&amp;tag=<tag>&amp;linkCode=ur2&amp;camp=247&amp;creative=1211';
	var $beaconHost = 'www.assoc-amazon.jp';
	var $encoding = 'EUC-JP';
	var $SearchIndexes = array();
	var $templateSet = 'default';
	var $templates = array();
	var $error = '';
	var $CompactArrayRemoveAdult = FALSE;
	var $cacheDir = '';
	var $OneRequestPerSec = TRUE;
	var $retry_interval = 250; //(ms)
	var $retry_count = 20;
	var $getPages = 1;
	var $startPage = 1;
	var $batchCount = 0;
	var $batchKeys = array();
	var $configs = array(
		'makeLinkSearch' => array(
			'Attributes'  => array(
			//	'rel'      => 'nofollow',
				'target'   => '_blank',
				'class'    => 'searchService',
				'title'    => 'Lookup: %s',
			),
		),
	);
	var $marketplace_listup = FALSE;
	var $ServiceName = 'amazon';

	function HypSimpleAmazon ($AssociateTag = '', $AccessKeyId = null, $SecretAccessKey = null) {

		$this->myDirectory = dirname(__FILE__);

		include_once dirname($this->myDirectory) . '/hyp_common_func.php';
		include_once dirname($this->myDirectory) . '/hyp_simplexml.php';

		if (is_null($AccessKeyId) && is_null($SecretAccessKey)) {
			$configFile = $this->myDirectory . '/hyp_simple_amazon.ini.rename';
			if (is_file($this->myDirectory . '/hyp_simple_amazon.ini')) {
				$configFile = $this->myDirectory . '/hyp_simple_amazon.ini';
			}
			$ini = parse_ini_file($configFile);
			if (! $AssociateTag && isset($ini['AssociateTag'])) {
				$AssociateTag = $ini['AssociateTag'];
			}
			if (isset($ini['AccessKeyId'])) {
				$this->AccessKeyId = $ini['AccessKeyId'];
			}
			if (isset($ini['SecretAccessKey'])) {
				$this->SecretAccessKey = $ini['SecretAccessKey'];
			}
		} else {
			$this->AccessKeyId = $AccessKeyId;
			$this->SecretAccessKey = $SecretAccessKey;
		}

		if (! $this->AccessKeyId) $this->AccessKeyId = '';
		$this->AssociateTag = $AssociateTag? $AssociateTag : '';

		$this->beaconImg = '<img src="http://'.$this->beaconHost.'/e/ir?t='.$this->AssociateTag.'&l=ur2&o=9" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />';
		$this->mobileBeaconImg = '<!--HypKTaiOnly<img src="http://'.$this->beaconHost.'/e/ir?t='.$this->AssociateTag.'&l=msn&o=9&a=ASIN" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />HypKTaiOnly-->';
		// <img src="http://www.assoc-amazon.jp/e/ir?t=amahyp-22&l=msn&o=9&a=6131057214" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
		$this->loadSearchIndexes();

		// Set Default template
		$this->loadTemplate('default');
	}

	function _getUrl ($params) {
		$_params = $params;
		foreach($_params as $key => $val){
			$this->_setBatchQuery($params, $key, $val);
		}

		$params['Service'] = 'AWSECommerceService';
		$params['AssociateTag'] = $this->AssociateTag;
		$params['AWSAccessKeyId'] = $this->AccessKeyId;
		$params['Version'] = $this->Version;
		$params['ContentType'] = 'text/xml';
		$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
		$params['Operation'] = $this->Operation;

		$params['ResponseGroup'] = $this->ResponseGroup;

		$params['MerchantId'] = 'All';

		if (isset($params['SearchIndex']) && ($params['SearchIndex'] === 'Blended' || $params['SearchIndex'] === 'All')) {
			unset($params['Sort'], $params['MerchantId'], $params['MinimumPrice'], $params['MaximumPrice']);
		}

		if (! $this->batchCount && $this->getPages > 1) {
			$max = min($this->getPages, $this->batchMax);
			for ($i = 1; $i <= $max; $i++) {
				$params[$this->Operation . '.' . $i .'.ItemPage'] = $i;
			}
			$this->batchCount = --$i;
		} else {
			$this->getPages = 1;
		}

		if ($this->batchCount) {
			$_params = array();
			foreach($params as $key=>$val) {
				$_params[$this->_makeSharedKey($key)] = $val;
			}
			$params = $_params;
		}

		ksort($params);
		$querys = array();
		foreach($params as $key=>$val) {
			$querys[] = $this->urlencode_rfc3986($key) . '=' . $this->urlencode_rfc3986($val);
		}
		$query = join ('&', $querys);

		$signature = $this->HMAC_SHA256_encode('GET' . "\n" . $this->restHost . "\n" . $this->restPath . "\n" . $query . '', $this->SecretAccessKey);
		if ($signature) {
			$signature = '&Signature=' . rawurlencode($signature);
		}

		$url = 'http://' . $this->restHost . $this->restPath . '?' . $query . $signature;

		return $url;
	}

	function _sendQuery ($params) {

		$url = $this->_getUrl($params);

		$timer = $this->cacheDir . 'hyp_hsa_' . $this->AccessKeyId . '.timer';
		$loop = 0;
		if ($this->OneRequestPerSec) {
			while($loop < $this->retry_count && is_file($timer) && filemtime($timer) >= time()){
				$loop++;
				clearstatcache();
				usleep($this->retry_interval * 1000); // 250ms
			}
		}
		if ($this->OneRequestPerSec && $loop >= $this->retry_count) {
			$this->xml = '';
			$this->error = 'Request Error: Too busy.';
		} else {
			if ($this->OneRequestPerSec) HypCommonFunc::touch($timer);
			$ht = new Hyp_HTTP_Request();
			$ht->init();
			$this->url = $ht->url = $url;
			$ht->get();

			if ($ht->rc === 200 || $ht->rc === 403) {
				$data = $ht->data;

				$xm = new HypSimpleXML();

				$this->xml = $xm->XMLstr_in($data);
				//var_dump($this->xml);exit();
				if ($xm->error) {
					$this->error = $xm->error;
				} else if ($error = @ $this->xml['Items']['Request']['Errors']['Error']) {
					$this->error = mb_convert_encoding($error['Message'], $this->encoding, 'UTF-8');
				} else if ($error = @ $this->xml['Items'][0]['Request']['Errors']['Error']) {
					$this->error = mb_convert_encoding($error['Message'], $this->encoding, 'UTF-8');
				} else if ($error = @ $this->xml['OperationRequest']['Errors']['Error']) {
					$this->error = $error['Code'] . ': ' . mb_convert_encoding($error['Message'], $this->encoding, 'UTF-8');
				} else if ($error = @ $this->xml['Error']) {
					$this->error = $error['Code'] . ': ' . mb_convert_encoding($error['Message'], $this->encoding, 'UTF-8');
				}
			} else {
				$this->xml = '';
				$this->error = 'HTTP Error: ' . $ht->rc;
			}
		}
	}

	function _makeSharedKey($key) {
		$noKeys = array('Service', 'AssociateTag', 'AWSAccessKeyId', 'Version', 'ContentType', 'Timestamp', 'Operation');
		if (!in_array($key, array_merge($noKeys, $this->batchKeys)) && strpos($key, '.') === FALSE) {
			$key = $this->Operation . '.Shared.' . $key;
		}
		return $key;
	}

	function _setBatchQuery(& $options, $key, $vals) {
		$ret = $vals;
		if (strpos($vals, ',') !== FALSE) {
			$ret = array();
			$vals = explode(',', $vals);
			$i = 1;
			foreach($vals as $val) {
				$ret[] = $options[$this->Operation . '.' . $i++ . '.' . $key] = trim($val);
				$this->batchKeys[] = $key;
				unset($options[$key]);
				if ($i > $this->batchMax) break;
			}
			$this->batchCount = max($this->batchCount, (count($ret) - 1));
		} else {
			//$options[$key] = $vals;
		}
		return $ret;
	}

	function setLocation($loc) {
		$loc = strtoupper($loc);
		$this->Location = $loc;
		switch($loc) {
			case 'JP':
				$this->restHost = 'ecs.amazonaws.jp';
				$this->searchHost = 'www.amazon.co.jp';
				break;
			case 'US':
				$this->restHost = 'ecs.amazonaws.com';
				$this->searchHost = 'www.amazon.com';
				break;
			case 'UK':
				$this->restHost = 'ecs.amazonaws.co.uk';
				$this->searchHost = 'www.amazon.co.uk';
				break;
			case 'DE':
				$this->restHost = 'ecs.amazonaws.de';
				$this->searchHost = 'www.amazon.de';
				break;
			case 'FR':
				$this->restHost = 'ecs.amazonaws.fr';
				$this->searchHost = 'www.amazon.fr';
				break;
			case 'CA':
				$this->restHost = 'ecs.amazonaws.ca';
				$this->searchHost = 'www.amazon.ca';
				break;
			default :
				$this->restHost = 'ecs.amazonaws.com';
				$this->searchHost = 'www.amazon.com';
				$this->Location = 'US';
		}
		$this->loadSearchIndexes();
	}

	function loadSearchIndexes () {
		$file = $this->myDirectory . '/res/' . $this->Location . '/SerachIndexes';
		$this->SearchIndexes = array();
		foreach(file($file) as $line) {
			if ($line && $line[0] !== '#') {
				$this->SearchIndexes[] = trim($line);
			}
		}
	}

	function loadTemplate ($file) {
		if ($template = @ file_get_contents(dirname(__FILE__) . '/templates/' . $file)) {
			$this->addTemplateSet(basename($file), $template);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function getTemplateSource ($file) {
		if ($template = @ file_get_contents(dirname(__FILE__) . '/templates/' . $file)) {
			return $template;
		} else {
			return '';
		}
	}

	function getTemplates () {
	    $templates = array();
	    $base = dirname(__FILE__) . '/templates/';
	    if ($dh = opendir($base)) {
			while (($file = readdir($dh)) !== false) {
				if ($file[0] !== '.' && is_file($base . $file)) {
					$templates[] = $file;
				}
			}
			closedir($dh);
		}
		return $templates;
    }

	function addTemplateSet ($name, $template) {
		if (!is_array($template)) {
			$_temp = array();
			$_temp['maxResult'] = 0;
			$_temp['base'] = rtrim(preg_replace('#<!--(.+?)(?:\(([\d]+)\))?-->.*?<!--/\\1-->#s', '', $template));
			if (preg_match('#<!--EACH(?:\(([\d]+)\))?-->(.+?)<!--/EACH-->#s', $template, $match)) {
				$_temp['each'] = $match[2];
				if (!empty($match[1])) {
					$_temp['maxResult'] = $match[1];
				}
			}
			if (preg_match('#<!--IMG-->(.+?)<!--/IMG-->#s', $template, $match)) {
				$_temp['img'] = trim($match[1]);
			}
			$template = $_temp;
		}
		if ($name === 'default') {
			$this->templates[$name] = $template;
		} else {
			$this->templates[$name] = array_merge($this->templates['default'], $template);
		}
	}

	function setSearchIndex ($strs, $target = '') {
		$this->SearchTarget = '';
		if ($target === 'title') {
			$this->searchTarget = 'Title';
		}
		$index = array();
		foreach(explode(',', $strs) as $str) {
			// Remove '-jp' etc. for AWS 3.0 compat.
			$str = preg_replace('/-[^\-]+$/', '', $str);
			foreach($this->SearchIndexes as $_index) {
				if (strtoupper($str) === strtoupper($_index)) {
					$index[] = $_index;
					continue(2);
				}
			}
			$index[] = 'Blended';
		}
		$this->SearchIndex = join(',', $index);
	}

	function init() {
		$this->xml = '';
		$this->compactArray = array();
		$this->html = '';
		$this->searchKey = '';
		$this->newestTime = 0;
		$this->error = '';
		$this->batchCount = 0;
		$this->batchKeys = array();

		if (! $this->cacheDir) $this->cacheDir = (defined(XOOPS_TRUST_PATH)? XOOPS_TRUST_PATH : dirname(dirname(dirname(dirname(__FILE__))))) . '/cache/';

	}

	function getItemSearchUrl($key, $options = array()) {
		// Init
		$this->init();

		$this->Operation = 'ItemSearch';
		$options['SearchIndex'] = $this->SearchIndex;
		$this->searchKey = $this->searchQueryOptimize(mb_convert_encoding($key, 'UTF-8', $this->encoding));
		if (!empty($this->searchTarget)) {
			$options[$this->searchTarget] = $this->searchKey;
		} else {
			$options['Keywords'] = $this->searchKey;
		}
		return $this->_getUrl($options);
	}

	function getResultType() {
		return 'xml';
	}

	function itemSearch($key, $options = array()) {

		// Init
		$this->init();

		$this->Operation = 'ItemSearch';
		$options['SearchIndex'] = $this->SearchIndex;
		$this->searchKey = $this->searchQueryOptimize(mb_convert_encoding($key, 'UTF-8', $this->encoding));
		if (!empty($this->searchTarget)) {
			$options[$this->searchTarget] = $this->searchKey;
		} else {
			$options['Keywords'] = $this->searchKey;
		}

		$this->_sendQuery($options);
	}

	function browseNodeSearch($node, $options = array()) {

		// Init
		$this->init();

		$this->Operation = 'ItemSearch';
		$options['SearchIndex'] = ($this->SearchIndex === 'Blended')? 'Music' : $this->SearchIndex;
		$options['BrowseNode'] = $node;

		$this->_sendQuery($options);
	}

	function itemLookup($key, $options = array()) {

		// Init
		$this->init();

		$this->Operation = 'ItemLookup';
		$options['ItemId'] = $key;

		$this->_sendQuery($options);
	}

	function makeSearchLink($key, $alias = '', $needEncode = TRUE, $category='') {
		if (is_array($key)) {
			$_key = array();
			foreach($key as $_k =>$_v) {
				$_key[$_k] = $this->makeSearchLink($_v, $alias, $needEncode, $category='');
			}
			return $_key;
		}
		if (!$alias) $alias = $key;
		$alias = htmlspecialchars($alias);

		if ($needEncode) {
			$e_key = mb_convert_encoding($key, 'UTF-8', $this->encoding);
		} else {
			$e_key = $key;
		}
		$e_key = $this->searchQueryOptimize($e_key);

		$url = 'http://' . $this->searchHost . str_replace(array('<keyword>', '<tag>'), array(rawurlencode($e_key), $this->AssociateTag), $this->searchQuery);
		//if ($category) $url .= '&amp;url=search-alias%3D'.strtolower($category);
		//if ($category) $url .= '&amp;rs=&amp;rh=i%3Aaps%2Ck%3A'.rawurlencode($e_key).'%2Ci%3A'.strtolower($category);

		$s_key = htmlspecialchars($key);
		$attrs = '';
		if ($attr = $this->configs['makeLinkSearch']['Attributes']) {
			if (isset($attr['title'])) {
				$attr['title'] = sprintf($attr['title'], $s_key);
			}
			$attrs = array();
			foreach ($attr as $key => $val) {
				$attrs[] = $key . '="' . $val . '"';
			}
			$attrs = ' ' . join(' ', $attrs);
		}

		//$beacon = '<img src="http://'.$this->beaconHost.'/e/ir?t='.$this->AssociateTag.'&l=ur2&o=9" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />';

		return '<a href="' . $url . '"' . $attrs . '>' . $alias . '</a>' . $this->beaconImg;
	}

	function toCompactArray() {
		if (!$this->xml) return;

		$compact = array();

		$items_top = $this->xml['Items'];
		$this->check_array($items_top);

		$compact['request'] = (isset($items_top[0]['Request']))? $items_top[0]['Request'] : NULL;
		$compact['totalresults'] = (isset($items_top[0]['TotalResults']))? $items_top[0]['TotalResults'] : NULL;
		$compact['totalpages'] = (isset($items_top[0]['TotalPages']))? $items_top[0]['TotalPages'] : NULL;

		if (! $compact['totalresults'] && isset($compact['request']['Errors']['Error']['Code']) && $compact['request']['Errors']['Error']['Code'] === 'AWS.ECommerceService.NoExactMatches') {
			$compact['totalresults'] = 0;
		}

		$i = 0;
		$sortkeys = array();
		$items = array();
		while(! empty($items_top[$i]['Item'])) {
			$_item = $items_top[$i]['Item'];
			$this->check_array($_item);
			$sortkey = array();
			for($i2=0; $i2<count($_item); $i2++) {
				$sortkey[] = $i2;
			}
			$items = array_merge($items, $_item);
			$sortkeys = array_merge($sortkeys, $sortkey);
			$i++;
		}
		if ($items) {
			if ($this->getPages < 2) {
				array_multisort($sortkeys, $items);
			}
		} else {
			$items = NULL;
		}

		if ($items) {
			$this->check_array($items);

//if (isset($_GET['debug'])) {
//	var_dump($items);
//	exit();
//}

			foreach ($items as $item) {

				if ($this->CompactArrayRemoveAdult && isset($item['ItemAttributes']['IsAdultProduct'])) {
					continue;
				}

				$_item = array();

				$item['MobileBeaconImg'] = str_replace('ASIN', $item['ASIN'], $this->mobileBeaconImg);

				// For template values
				$_item['_SERVICE'] = $_item['SERVICE'] = $this->ServiceName;
				$_item['URL'] = $item['DetailPageURL'];
				$_item['ASIN'] = $item['ASIN'];
				$_item['JAN'] = @ $item['ItemAttributes']['EAN'];
				$_item['ADDCARTURL'] = $this->getAddCartURL($item['ASIN']);
				$_item['TITLE'] = trim(htmlspecialchars(htmlspecialchars_decode(@ $item['ItemAttributes']['Title'] . '/' . $this->get_artist($item, 1), ENT_QUOTES)), '/');
				if (isset($item["EditorialReviews"]["EditorialReview"])) {
					$this->check_array($item["EditorialReviews"]["EditorialReview"]);
					$_item['DISCRIPTION'] = $this->toPlainText(@ $item["EditorialReviews"]["EditorialReview"][0]["Content"]);
				}
				$_item['BINDING'] = @ $item['ItemAttributes']['Binding'];
				$_item['PRODUCTGROUP'] = @ $item['ItemAttributes']['ProductGroup'];
				$_item['MANUFACTURER'] = $this->get_manufacturer($item);
				$_item['RELEASEDATE'] = $this->get_releasedate($item);
				$_item['RELEASEUTIME'] = @ $item['ReleaseUTIME'];
				$_item['AVAILABILITY'] = '';
				if (isset($item['Offers']['Offer'])) {
					$this->check_array($item['Offers']['Offer']);
					$_item['AVAILABILITY'] = $item['Offers']['Offer'][0]['OfferListing']['Availability'];
				}
				$_item['SIMG'] = @$item['SmallImage']['URL'];
				$_item['MIMG'] = @$item['MediumImage']['URL'];
				$_item['LIMG'] = @$item['LargeImage']['URL'];
				$_item['LINKED_SIMG'] = $this->get_image($item, 's');
				$_item['LINKED_MIMG'] = $this->get_image($item, 'm');
				$_item['LINKED_LIMG'] = $this->get_image($item, 'l');
				$_item['CATEGORY'] = $this->get_category($item);
				$_item['PRESENTER'] = $this->get_presenter($item);
				$_item['CREATOR'] = $this->get_creator($item);
				$_price = $this->get_listprice($item);
				$_item['LISTPRICE']= $_price[0];
				$_item['LISTPRICE_FORMATTED'] = $_price[1];
				$_price = $this->get_price($item);
				$_item['PRICE']= $_price[0];
				$_item['MERCHANTID'] = $this->get_merchantid($item);
				if ($_item['MERCHANTID']) {
					$_item['GUIDEURL'] = 'http://' . $this->searchHost . str_replace(array('<url>', '<tag>'), array(rawurlencode('http://www.amazon.co.jp/gp/help/seller/shipping.html?ie=UTF8&asin='.$_item['ASIN'].'&seller='.$_item['MERCHANTID']), $this->AssociateTag), $this->redirectQuery);
				} else if (! $_item['AVAILABILITY']) {
					$_item['GUIDEURL'] = 'http://' . $this->searchHost . str_replace(array('<url>', '<tag>'), array(rawurlencode('http://www.amazon.co.jp/gp/help/customer/display.html?nodeId=1104814'), $this->AssociateTag), $this->redirectQuery);
				} else if ($_item['PRICE'] < 1500) {
					$_item['GUIDEURL'] = 'http://' . $this->searchHost . str_replace(array('<url>', '<tag>'), array(rawurlencode('http://www.amazon.co.jp/gp/help/customer/display.html?nodeId=642982'), $this->AssociateTag), $this->redirectQuery);
				} else {
					$_item['GUIDEURL'] = 'free';
				}
				$_item['PRICE_FORMATTED'] = $_price[1];
				$_price = $this->get_usedprice($item);
				$_item['USEDPRICE']= $_price[0];
				$_item['USEDPRICE_FORMATTED'] = $_price[1];

				// Array data (For not template)
				foreach($this->get_creators($item) as $key => $val) {
					$key = mb_convert_encoding($key, $this->encoding, 'UTF-8');
					$_item['CREATORS'][$key] = $val;
				}
				//$_item['RAW'] = $item;
				$_item['BEACON_IMG_TAG'] = $this->beaconImg;
				$_item['MOBILE_BEACON_IMG_TAG'] = $item['MobileBeaconImg'];

				$_item['IS_ADULT'] = (isset($item['ItemAttributes']['IsAdultProduct']))? 1 : 0;

				$compact['Items'][] = $_item;

				if ($this->marketplace_listup && $_item['USEDPRICE'] && $_item['USEDPRICE'] < $_item['PRICE']) {
					$item['DetailPageURL'] = $item['ItemLinks']['ItemLink'][3]['URL'];
					$_item['SERVICE'] = 'amazon_m';
					$_item['TITLE'] .= ' (Market Place)';
					$_item['URL'] = $item['DetailPageURL'];
					$_item['LINKED_SIMG'] = $this->get_image($item, 's');
					$_item['LINKED_MIMG'] = $this->get_image($item, 'm');
					$_item['LINKED_LIMG'] = $this->get_image($item, 'l');
					$_item['PRICE'] = $_item['USEDPRICE'];
					$_item['PRICE_FORMATTED'] = $_item['USEDPRICE_FORMATTED'];
					$_item['AVAILABILITY'] = '';
					$_item['GUIDEURL'] = 'http://' . $this->searchHost . str_replace(array('<url>', '<tag>'), array(rawurlencode('http://www.amazon.co.jp/gp/help/customer/display.html?nodeId=1104814'), $this->AssociateTag), $this->redirectQuery);

					$compact['Items'][] = $_item;
					$compact['totalresults']++;
				}
			}
		}
		mb_convert_variables($this->encoding , 'UTF-8', $compact);
		$this->compactArray = $compact;
	}

	function getCompactArray($templateSet = '') {
		if ($templateSet && isset($this->templates[$templateSet])) {
			$this->templateSet = $templateSet;
		} else {
			if ($templateSet && $this->loadTemplate($templateSet)) {
				$this->templateSet = $templateSet;
			} else {
				$this->templateSet = 'default';
			}
		}

		$this->toCompactArray();

		return 	$this->compactArray;
	}

	function getResultArray() {
		return $this->xml;
	}

	function getAddCartURL ($asin) {

		$url = 'http://' . $this->searchHost
		     . '/gp/aws/cart/add.html?AWSAccessKeyId=' . $this->AccessKeyId
		     . '&amp;AssociateTag=' . $this->AssociateTag
		     . '&amp;ASIN.1=' . $asin
		     . '&amp;Quantity.1=1';

		return $url;
	}

	function getHTML($templateSet = '') {
		if ($templateSet && isset($this->templates[$templateSet])) {
			$this->templateSet = $templateSet;
		} else {
			if ($templateSet && $this->loadTemplate($templateSet)) {
				$this->templateSet = $templateSet;
			} else {
				$this->templateSet = 'default';
			}
		}

		$this->toCompactArray();

		$template = $this->templates[$this->templateSet];
		if (!$this->error) {
			$each = '';
			$i = 0;
			$from = array();
			$from_make = FALSE;
			foreach ($this->compactArray['Items'] as $item) {
				if ($template['maxResult'] && ++$i > $template['maxResult']) break;

				$to = array();
				//foreach($keys as $key) {
				foreach(array_keys($item) as $key) {
					if (!$from_make) {
						$from[] = '<_' . $key . '_>';
					}
					$to[] = (is_string($item[$key]))? $item[$key] : '';
				}
				if (!$from_make) {
					$from[] = '<_ASSTAG_>';
					$from[] = '<_DEVKEY_>';
				}
				$from_make = TRUE;
				$to[] = $this->AssociateTag;
				$to[] = $this->AccessKeyId;

				$_html = str_replace($from, $to, $template['each']);

				$_html = preg_replace('#<_count!=(?:[\d]+,)*'.$i.'(?:,[\d]+)*_>.+?<_/count_>#s', '', $_html);
				$_html = preg_replace('#<_count!=(?:[\d]+,)*[\d]+_>(.+?)<_/count_>#s', '$1', $_html);

				$_html = preg_replace('#<_count=(?:[\d]+,)*'.$i.'(?:,[\d]+)*_>(.+?)<_/count_>#s', '$1', $_html);
				$_html = preg_replace('#<_count=(?:[\d]+,)*[\d]+_>.+?<_/count_>#s', '', $_html);

				$each .=$_html;
			}

			$this->html = str_replace('<_EACH_>', $each, $template['base']);

		} else {
			if (! $error = @ $this->compactArray['request']['Errors']['Error']['Message']) {
				$error = $this->error;
			}
			$this->html = str_replace('<_EACH_>', $error, $template['base']);
		}

		return $this->html;
	}

	function ISBN2ASIN ($isbn) {
		$_isbn = str_replace('-', '', $isbn);

		if (strlen($_isbn) !== 13) return $isbn;

		$head = intval(substr($_isbn, 0, 3));

		if ($head === 978 || $head === 979) {
			$asin = substr($_isbn, 3, 9);
			$sum = 0;
			$n = 10;
			for($i = 0; $i < 9; $i++) {
				$sum += $asin[$i] * $n--;
			}
			$des = 11 - ($sum % 11);
			if ($des === 10) {
				$des = 'X';
			} else if ($des === 11) {
				$des = '0';
			}
			$asin .= $des;
			return $asin;
		} else {
			$this->setSearchIndex('Blended');
			$this->itemSearch($isbn);
			$xml = $this->xml;
			if (isset($xml['Items']) && isset($xml['Items']['Item']) && isset($xml['Items']['Item']['ASIN'])) {
				return $xml['Items']['Item']['ASIN'];
			} else {
				return $isbn;
			}
		}
	}

	function check_array(& $items) {
		if (!is_array($items) || !isset($items[0])) {
			$tmp[0] = $items;
			$items = $tmp;
		}
	}

	function get_category($item) {
		$binding = '';
		if (@ $item['ItemAttributes']['Binding']) {
			$binding = $item['ItemAttributes']['Binding'];
		} else if ($item['ItemAttributes']['ProductGroup']) {
			$binding = $item['ItemAttributes']['ProductGroup'];
		}

		//if ($this->searchKey) $binding = $this->makeSearchLink($this->searchKey, $binding, FALSE, @ $item['ItemAttributes']['ProductGroup']);

		return $binding;
	}

	function get_image($item, $size = 's') {
		$img = '';
		if ($size === 's') {
			$img = @ $item['SmallImage'];
		} else if ($size === 'm') {
			$img = @ $item['MediumImage'];
		} else {
			$img = @ $item['LargeImage'];
		}
		$from = array(
			'<_TITLE_>',
			'<_URL_>',
			'<_IMGSRC_>',
			'<_IMGSIZE_>',
		);

		if ($img) {
			$height =$img['Height']['content'];
			$width = $img['Width']['content'];

			$to['title'] = htmlspecialchars($item['ItemAttributes']['Title']);
			$to['url'] = $item['DetailPageURL'];
			$to['imgsrc'] = $img['URL'];
			$to['imgsize'] = 'height="' . $height . '" width="' . $width . '"';

			$img = str_replace($from, $to, $this->templates[$this->templateSet]['img']) . $item['MobileBeaconImg'];
		}
		return $img;
	}

	function get_artist($item, $max = null) {
		$artist = '';
		if (@ $item['ItemAttributes']['Artist']) {
			$artist = $item['ItemAttributes']['Artist'];
		}
		if ($artist) {
			$this->check_array($artist);
			if ($max) {
				$artist = array_slice($artist, 0, $max);
			}
			$artist = join(', ', $artist);
		}
		return $artist;
	}

	function get_presenter($item, $retArray = FALSE) {
		$author = '';
		if (@ $item['ItemAttributes']['Artist']) {
			$author = $item['ItemAttributes']['Artist'];
		} else if (@ $item['ItemAttributes']['Author']) {
			$author = $item['ItemAttributes']['Author'];
		} else if (@ $item['ItemAttributes']['Actor']) {
			$author = $item['ItemAttributes']['Actor'];
		} else if (@ $item['ItemAttributes']['Manufacturer']) {
			$author = $item['ItemAttributes']['Manufacturer'];
		} else if (@ $item['ItemAttributes']['Brand']) {
			$author = $item['ItemAttributes']['Brand'];
		}
		if ($author) {
			$this->check_array($author);
			if ($retArray) return $author;
			$author = $this->makeSearchLink($author, '', FALSE);
			$author = 'by: '. join(', ', $author);
		}
		return $author;
	}

	function get_creators($item) {
		$creators = array();
		$dones = array();
		if (@ $item['ItemAttributes']['Creator']) {
			$this->check_array($item['ItemAttributes']['Creator']);
			foreach($item['ItemAttributes']['Creator'] as $dat) {
				$creators[$dat['Role']][] = $this->makeSearchLink($dat['content'], '', FALSE);
				$dones[$dat['content']] = TRUE;
			}
		}
		if ($presenter = $this->get_presenter($item, TRUE)) {
			foreach($presenter as $dat) {
				if (! isset($dones[$dat])) {
					$creators['by'][] = $this->makeSearchLink($dat, '', FALSE);
				}
			}
		}
		return $creators;
	}

	function get_creator($item) {
		$creators = array();
		foreach ($this->get_creators($item) as $key => $arg) {
			$creators[] = $key . ': ' . join(', ', $arg);
		}
		return join('<br />', $creators);
	}

	function get_listprice($item) {
		$listprice = array(0 => '', 1 => '');
		if (@ $item['ItemAttributes']['ListPrice']['Amount']) {
			$listprice[0] = $item['ItemAttributes']['ListPrice']['Amount'];
			$listprice[1] = $item['ItemAttributes']['ListPrice']['FormattedPrice'];
		}
		return $listprice;
	}

	function get_price($item) {
		$price = array(0 => '', 1 => '');
		if (@ $item['Offers']['Offer']['OfferListing']['Price']['Amount']) {
			$price[0] = $item['Offers']['Offer']['OfferListing']['Price']['Amount'];
			$price[1] = $item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'];
		} else if (@ $item['OfferSummary']['LowestNewPrice']['Amount']) {
			$price[0] = $item['OfferSummary']['LowestNewPrice']['Amount'];
			$price[1] = $item['OfferSummary']['LowestNewPrice']['FormattedPrice'];
		} else if (@ $item['VariationSummary']['LowestPrice']['Amount']) {
			$price[0] = $item['VariationSummary']['LowestPrice']['Amount'];
			$price[1] = $item['VariationSummary']['LowestPrice']['FormattedPrice'];
		}
		return $price;
	}

	function get_usedprice($item) {
		$usedprice = array(0 => '', 1 => '');
		if (@ $item['OfferSummary']['LowestUsedPrice']['Amount']) {
			$usedprice[0] = $item['OfferSummary']['LowestUsedPrice']['Amount'];
			$usedprice[1] = $item['OfferSummary']['LowestUsedPrice']['FormattedPrice'];
		}
		return $usedprice;
	}

	function get_manufacturer($item) {
		if ($manufacturer = @ $item['ItemAttributes']['Manufacturer']) {
			return $this->makeSearchLink($manufacturer, '', FALSE);
		} else if ($manufacturer = @ $item['ItemAttributes']['Brand']) {
			return $this->makeSearchLink($manufacturer, '', FALSE);
		}
		return '';
	}

	function get_releasedate(& $item) {
		$timeString = '';
		if (isset($item['ItemAttributes']['ReleaseDate'])) {
			$timeString =  $item['ItemAttributes']['ReleaseDate'];
		} else if (isset($item['ItemAttributes']['PublicationDate'])) {
			$timeString =  $item['ItemAttributes']['PublicationDate'];
		}
		if ($timeString) {
			$item['ReleaseUTIME'] = intval(@ strtotime($timeString, strtotime(date('Y').'/1/1')));
			$this->newestTime = max($item['ReleaseUTIME'], $this->newestTime);
		}
		return $timeString;
	}

	function get_merchantid($item) {
		$merchantid = '';
		if (isset($item['ImageSets']) && isset($item['ImageSets']['MerchantId'])) {
			$merchantid = $item['ImageSets']['MerchantId'];
		}
		return $merchantid;
	}

	function urlencode_rfc3986($str) {
		return str_replace('%7E', '~', rawurlencode($str));
	}

	function HMAC_SHA256_encode($data, $key) {
		if (! $key) return '';
		if (defined('HYP_SHA256INC_INCLUDED') || (function_exists('hash_hmac') && function_exists('hash_algos') && (in_array('sha256', hash_algos())))) {
			return base64_encode(hash_hmac('sha256' , $data, $key, TRUE));
		} else if (function_exists('mhash') && defined('MHASH_SHA256')) {
			return base64_encode(mhash(MHASH_SHA256 , $data, $key));
		} else {
			return '';
		}
	}

	function searchQueryOptimize($str) {
		$str = str_replace('-', ' ', $str);
		$str = preg_replace('/\s+/', ' ', $str);
		return $str;
	}

	function toPlainText($str, $len = 1000) {
		$str = preg_replace('#<(style|script).+?/$1>#is', '', $str);
		$str = preg_replace('#</?(?:br|p|td|tr)[^>]*?>#i', ' ', $str);
		$str = htmlspecialchars(strip_tags(str_replace('&nbsp;', ' ', $str)));
		$str = preg_replace('#\s+#', ' ', $str);
		$str = str_replace('&amp;amp;', '&amp;', $str);
		if (mb_strlen($str) > $len + 4) {
			$str = mb_substr($str, 0, $len) . '...';
		}
		return $str;
	}
}

if (!function_exists('hash_hmac') && !(function_exists('mhash') && defined('MHASH_SHA256'))) {
	// for PHP4
	define('HYP_SHA256INC_INCLUDED', TRUE);
	require_once dirname(dirname(__FILE__)) . '/sha256.inc.php';
	function hash_hmac($algo, $data, $key, $raw_output=False) {
		// RFC 2104 HMAC implementation for php.
		// Creates a sha256 HMAC.
		// Eliminates the need to install mhash to compute a HMAC
		// Hacked by Lance Rushing
		// source: http://www.php.net/manual/en/function.mhash.php
		// modified by Ulrich Mierendorff to work with sha256 and raw output

		$b = 64; // block size of md5, sha256 and other hash functions
		if (strlen($key) > $b) {
			$key = pack("H*",$algo($key));
		}
		$key = str_pad($key, $b, chr(0x00));
		$ipad = str_pad('', $b, chr(0x36));
		$opad = str_pad('', $b, chr(0x5c));
		$k_ipad = $key ^ $ipad ;
		$k_opad = $key ^ $opad;

		$hmac = $algo($k_opad . pack("H*", $algo($k_ipad . $data)));
		if ($raw_output) {
			return pack("H*", $hmac);
		} else {
			return $hmac;
		}
	}
}
