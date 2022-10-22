<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 * @brief      You can access pico contents via URI like ...
 *             XOOPS_URL/modules/pico/index.php?subject=(subject of the content)
 */


class PicoUriMapperBySubject extends PicoUriMapper {

	public function judgeController( &$cat_id, &$content_id ) {
		parent::judgeController( $cat_id, $content_id );

		if ( ! empty( $_GET['subject'] ) ) {

			// get content_id from $_GET['subject']
			$db = XoopsDatabaseFactory::getDatabaseConnection();

			$sql = 'SELECT content_id FROM ' . $db->prefix( $this->mydirname . '_contents' ) . " WHERE subject='" . addslashes( $_GET['subject'] ) . "' LIMIT 1";

			[ $content_id_tmp ] = $db->fetchRow( $db->query( $sql ) );

			if ( ! empty( $content_id_tmp ) ) {
				$this->request['controller'] = 'content';
				$this->request['view']       = 'detail';
				$content_id                  = $content_id_tmp;
			}

		}
	}
}
