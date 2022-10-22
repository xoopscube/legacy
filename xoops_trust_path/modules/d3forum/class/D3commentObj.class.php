<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

// a class for D3comment Authorization
if ( ! class_exists( 'D3commentObj' ) ) {

	class D3commentObj {

		public $d3comObj = null;

		public function __construct( $params ) //  $params['forum_dirname'] , $params['external_dirname'] , $params['classname'] , $params['external_trustdirname']
		{
			$mytrustdirpath = dirname( __DIR__ );

			if ( empty( $params['classname'] ) ) {

				include_once $mytrustdirpath . '/class/D3commentAbstract.class.php';

				$this->d3comObj = new D3commentAbstract( $params['forum_dirname'], '' );

				return;
			}

			// search the class file
			$class_bases = [
				XOOPS_ROOT_PATH . '/modules/' . $params['external_dirname'] . '/class',
				XOOPS_TRUST_PATH . '/modules/' . $params['external_trustdirname'] . '/class',
				XOOPS_TRUST_PATH . '/modules/d3forum/class',
			];

			foreach ( $class_bases as $class_base ) {

				if ( file_exists( $class_base . '/' . $params['classname'] . '.class.php' ) ) {

					require_once $mytrustdirpath . '/class/D3commentAbstract.class.php';

					require_once $class_base . '/' . $params['classname'] . '.class.php';

					break;
				}
			}

			// check the class
			if ( ! $params['classname'] || ! class_exists( $params['classname'] ) ) {

				include_once $mytrustdirpath . '/class/D3commentAbstract.class.php';

				$this->d3comObj = new D3commentAbstract( $params['forum_dirname'], $params['external_dirname'] );

				return;
			}

			$this->d3comObj = new $params['classname']( $params['forum_dirname'],
				$params['external_dirname'], $params['external_trustdirname'] );
		}

		public static function & getInstance( $params ) {
			$external_dirname = $params['external_dirname'];

			static $instance;

			if ( ! isset( $instance[ $external_dirname ] ) ) {
				$instance[ $external_dirname ] = new D3commentObj( $params );
			}

			return $instance[ $external_dirname ];
		}

	} // end class D3commentObj
}
