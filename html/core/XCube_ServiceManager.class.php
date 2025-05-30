<?php
/**
 * /core/XCube_ServiceManager.class.php
 * @package    XCube
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief This class manages XCube_Service instances, searches for them, and creates the appropriate
 * client instance. Currently, the purpose of this class is for internal use within the XOOPSCube
 * site. In other words, this class is not designed for publishing external web services.
 * The core team will examine the possibility of separating these functionalities.
 *
 * The XCube namespace cannot directly contain the SOAP library.
 * The delegate mechanism is well-suited for this class. This class creates a client instance
 * that connects to a service based on the service type.
 * For example, if the specified service is indeed an external web service,
 * a SOAP client must be created. However, if the service is an internal virtual service of XCube,
 * a virtual client must be created instead.
 */

if ( ! defined( 'XCUBE_CORE_PATH' ) ) {
	define( 'XCUBE_CORE_PATH', __DIR__ );
}

require_once XCUBE_CORE_PATH . '/XCube_Delegate.class.php';

class XCube_ServiceUtils {
	public function isXSD( $typeName ) {
		return 'string' === $typeName || 'int' === $typeName;
	}
}


class XCube_ServiceManager {
	/**
	 * Array of XCube_Service instances.
	 *
	 * @var
	 */
	public $mServices = [];

	/**
	 * @param &$client
	 * @param $service
	 *
	 * @var XCube_Delegate
	 */
	public $mCreateClient;

	/**
	 * @var XCube_Delegate
	 */
	public $mCreateServer;

	public function __construct() {
		$this->mCreateClient = new XCube_Delegate();
		$this->mCreateClient->register( 'XCube_ServiceManager.CreateClient' );

		$this->mCreateServer = new XCube_Delegate();
		$this->mCreateServer->register( 'XCube_ServiceManager.CreateServer' );
	}

	/**
	 * Add service object. $name must be unique in the list of service. If the
	 * service which has the same name, is a member of the list, return false.
	 *
	 * @param string $name
	 * @param XCube_Service $service
	 *
	 * @return bool
	 */
	public function addService( $name, &$service ) {
		if ( isset( $this->mServices[ $name ] ) ) {
			return false;
		}

		$this->mServices[ $name ] =& $service;

		return true;
	}

	/**
	 * Add WSDL URL. $name must be unique in the list of service. If the
	 * service which has the same name, is a member of the list, return false.
	 *
	 * @param $name
	 * @param $url
	 *
	 * @return bool
	 */
	public function addWSDL( $name, $url ) {
		if ( isset( $this->mServices[ $name ] ) ) {
			return false;
		}

		$this->mServices[ $name ] =& $url;

		return true;
	}

	/**
	 * This member function will be removed at beta version.
	 *
	 * @param $name
	 * @param $service
	 *
	 * @return bool
	 * @see XCube_ServiceManager::addService()
	 * @deprecated
	 */
	public function addXCubeService( $name, &$service ) {
		return $this->addService( $name, $service );
	}

	public function &getService( $name ) {
		$ret = null;

		if ( isset( $this->mServices[ $name ] ) ) {
			return $this->mServices[ $name ];
		}

		return $ret;
	}

	/**
	 * This member function will be removed at beta version.
	 *
	 * @param $name
	 *
	 * @return mixed|null
	 * @deprecated
	 * @see XCube_ServiceManager::getService()
	 */
	public function &searchXCubeService( $name ) {
		return $this->getService( $name );
	}

	/**
	 * Create client instance which to connect to a service, with following the
	 * kind of the service. Then return that instance. For example, if the
	 * specified service is really web service, SOAP client has to be created.
	 * But, if the service is a virtual service of XCube, virtual client has to
	 * be created.
	 *
	 * @param $service
	 *
	 * @return null
	 */
	public function &createClient( &$service ) {
		$client = null;
		$this->mCreateClient->call( new XCube_Ref( $client ), new XCube_Ref( $service ) );

		return $client;
	}

	public function &createServer( &$service ) {
		$server = null;
		$this->mCreateServer->call( new XCube_Ref( $server ), new XCube_Ref( $service ) );

		return $server;
	}
}
