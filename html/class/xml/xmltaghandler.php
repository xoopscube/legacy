<?php
/**
 * Xml Tag Handler
 * @package    kernel
 * @subpackage xml
 * @version    XCL 2.4.0
 * @author     Other authors Minahito, 2007/05/15 
 * @author     Ken Egervari, Remi Michalski
 * @copyright  2001 eXtremePHP
 * @license    
 */

class XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return '';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    public function handleBeginElement(&$parser, &$attributes)
    {
    }

    /**
     * @param $parser
     */
    public function handleEndElement(&$parser)
    {
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData(&$parser,  &$data)
    {
    }
}
