<?php
/**
 * Namespaced version of the Plugin Base Class
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

namespace Proxy\Plugin;

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// For backward compatibility with plugins that use the namespaced class
class AbstractPlugin extends \ProtectorProxyPluginBase {}