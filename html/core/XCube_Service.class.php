<?php
/**
 * XCube_Service.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      [Abstract] This class is a collection for functions.
 * @bug        This class does NOT work perfectly. It's fatal...
 * @todo Improve XCube Service for SOAP and REST
 */

/**
 * @param $definition
 *
 * @return array|null
 * @internal
 * @brief This is a kind of MACRO like C for XCube_Service.
 */
function S_PUBLIC_FUNC( $definition ) {
	$pos = strpos( $definition, '(' );
	if ( $pos > 0 ) {
		$params = [];
		foreach ( explode( ',', substr( $definition, $pos + 1, - 1 ) ) as $t_param ) {
			if ( $t_param ) {
				list( $k, $v ) = explode( ' ', trim( $t_param ) );
				$params[ $k ] = $v;
			}
		}
		$ret = [ 'in' => $params ];
		list( $ret['out'], $ret['name'] ) = explode( ' ', substr( $definition, 0, $pos ) );

		return $ret;
	}

	return null;
}


class XCube_Service {
	/**
	 * @protected
	 * @brief string
	 */
	public $mServiceName = '';

	/**
	 * @protected
	 * @brief string
	 */
	public $mNameSpace = '';

	/**
	 * @protected
	 */
	public $mClassName = 'XCube_Service';

	/**
	 * @protected
	 * @brief XCube_ActionStrategy(?) --- 'deprecated'
	 * @deprecated
	 */
	public $_mActionStrategy;

	public $_mTypes = [];

	public $_mFunctions = [];

	public function __construct() {
	}

	public function prepare() {
	}

	public function addType( $className ) {
		$this->_mTypes[] = $className;
	}

	public function addFunction() {
		$args = func_get_args();
		$n    = func_num_args();
		$arg0 = &$args[0];

		if ( 3 === $n ) {
			$this->_addFunctionStandard( $arg0, $args[1], $args[2] );
		} elseif ( 1 === $n && is_array( $arg0 ) ) {
			$this->_addFunctionStandard( $arg0['name'], $arg0['in'], $arg0['out'] );
		}
	}

	public function _addFunctionStandard( $name, $in, $out ) {
		$this->_mFunctions[ $name ] = [
			'out'  => $out,
			'name' => $name,
			'in'   => $in
		];
	}

	/**
	 * XCube_Procedure
	 *
	 * @param  $procedure
	 *
	 * @var   string $name
	 */
	public function register( $name, &$procedure ) {
	}
}

/**
 * @public
 * @brief [Experiment Class] The adapter for a service class.
 *
 * This class is the adapter of a service class.
 * I give a caller the interface that resembled NUSOAP.
 */
class XCube_AbstractServiceClient {
	public $mService;
	public $mClientErrorStr;

	public $mUser;

	public function __construct( &$service ) {
		$this->mService =& $service;
	}

	public function prepare() {
	}

	public function setUser( &$user ) {
		$this->mUser =& $user;
	}

	public function call( $operation, $params ) {
	}

	public function getOperationData( $operation ) {
	}

	public function setError( $message ) {
		$this->mClientErrorStr = $message;
	}

	public function getError() {
		return ! empty( $this->mClientErrorStr ) ? $this->mClientErrorStr : $this->mService->mErrorStr;
	}
}

/**
 * @public
 * @brief [Abstract] Interface to be used for accessing a Service.
 *
 * The client object for XCube_Service(Inner service). This class calls
 * functions directly, but exchanges the request object of the context to
 * enable the service logic to get values by the request object. After calls,
 * restores the original request object.
 */
class XCube_ServiceClient extends XCube_AbstractServiceClient {
	public function call( $operation, $params ) {
		$this->mClientErrorStr = null;

		if ( ! is_object( $this->mService ) ) {
			$this->mClientErrorStr = 'This instance is not connected to service';

			return null;
		}

		$root        =& XCube_Root::getSingleton();
		$request_bak =& $root->mContext->mRequest;
		unset( $root->mContext->mRequest );

		$root->mContext->mRequest = new XCube_GenericRequest( $params );

		if ( isset( $this->mService->_mFunctions[ $operation ] ) ) {
			$ret = call_user_func( [ $this->mService, $operation ] );

			unset( $root->mContext->mRequest );
			$root->mContext->mRequest =& $request_bak;

			return $ret;
		}

		$this->mClientErrorStr = "operation ${operation} not present.";

		return null;
	}
}
