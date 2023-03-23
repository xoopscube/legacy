<?php
/**
 * Error handler class
 * @package    kernel
 * @subpackage core
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors nobunobu, 2007/06/24
 * @author     Michael van Dam
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


class XoopsErrorHandler
{
    /**
     * List of errors
     *
     * @var array
     * @access private
     */
    public $_errors = [];

    /**
     * Show error messages?
     *
     * @var bool
     * @access private
     */
    public $_showErrors = false;

    /**
     * Was there a fatal error (E_USER_ERROR)
     *
     * @var bool
     * @access private
     */
    public $_isFatal = false;

    /**
     * Constructor
     *
     * Registers the error handler and shutdown functions.  NOTE: when
     * registering an error handler, the setting or 'error_reporting' is
     * ignored and *everything* is trapped.
     */
    public function __construct()
    {
        set_error_handler('XoopsErrorHandler_HandleError');
        register_shutdown_function('XoopsErrorHandler_Shutdown');
    }

    /**
     * Get the (singleton) instance of the error handler
     *
     * @access public
     */
    public static function &getInstance()
    {
        static $instance = null;
        if (empty($instance)) {
            $instance = new XoopsErrorHandler();
        }
        return $instance;
    }

    /**
     * Activate the error handler
     *
     * @access public
     * @param bool $showErrors True if debug mode is on
     * @return void
     */
    public function activate($showErrors=false)
    {
        $this->_showErrors = $showErrors;
    }

    /**
     * Handle an error
     *
     * @param array $error Associative array containing error info
     * @access public
     * @return void
     */
    public function handleError($error)
    {
        if (E_USER_ERROR == $error['errno']) {
            $this->_isFatal = true;
            exit($error['errstr']);
        }
        if (($error['errno'] & error_reporting()) !== $error['errno']) {
            return;
        }
        $this->_errors[] = $error;
    }

    /**
     * Render the list of errors
     *
     * NOTE: Unfortunately PHP 'fatal' and 'parse' errors are not trappable.
     * If the server has 'display_errors Off', then the result will be a
     * blank page.  It would be nice to print a message 'This page cannot
     * be displayed', but there seems to be no way to print this only when
     * exiting due to a fatal error rather than normal end of page.
     *
     * Thus, 'trigger_error' should be used to trap problems early and
     * display a meaningful message before a PHP fatal or parse error can
     * occur.
     *
     * @TODO Use CSS
     * @TODO Use language? or allow customized message?
     *
     * @access public
     * @return void
     */
    public function renderErrors()
    {
        //
        // TODO We should plan new style about the following lines.
        //
        $output = '';
        if ($this->_isFatal) {
            $output .= 'This page cannot be displayed due to an internal error.<br><br>';
            $output .= 'If you are the administrator of this site, please visit the <a href="https://github.com/xoopscube/">XOOPSCube Project Site</a> for assistance.<br><br>';
        }
        if (!$this->_showErrors || empty($this->_errors)) {
            return $output;
        }

        $output = [];
        foreach ($this->_errors as $error) {
            switch ($error['errno']) {
                case E_USER_NOTICE:
                    $out = 'Notice [Xoops]: ';
                    break;
                case E_USER_WARNING:
                    $out = 'Warning [Xoops]: ';
                    break;
                case E_USER_ERROR:
                    $out = 'Error [Xoops]: ';
                    break;
                case E_USER_DEPRECATED:
                    $out = 'Deprecated [Xoops]: ';
                    break;
                case E_USER_STRICT:
                    $out = 'Strict [Xoops]: ';
                    break;
                case E_NOTICE:
                    $out = 'Notice [PHP]: ';
                    break;
                case E_WARNING:
                    $out = 'Warning [PHP]: ';
                    break;
                case E_DEPRECATED:
                    $out = 'Deprecated [PHP]: ';
                    break;
                case E_STRICT:
                    $out = 'Strict [PHP]: ';
                    break;
                case E_ERROR:
                    $out = 'Fatal [PHP]: ';
                    break;
                default:
                    $out = 'Unknown Condition [' . $error['errno'] . ']: ';
            }


            // output
            $out .= sprintf('%s in file %s line %s', $error['errstr'], $error['errfile'], $error['errline']);
            $md5 = md5($out);
            $count =[];
            if (isset($output[$md5])) {

                $output[$md5] = preg_replace('/\(\d+\)$/', '(' . ++$count[$md5] . ')', $output[$md5]);
            } else {
                $output[$md5] = $out . ' (1)';
                $count[$md5] = 1;
            }
        }
        $ret = '<div class="alert error">';
        $ret .= implode("<br>\n", $output);
        $ret .= '</div>';
        return $ret;
    }
}

/**
 * User-defined error handler (called from 'trigger_error')
 *
 * NOTE: Some recent versions of PHP have a 5th parameter, &$p_ErrContext
 * which is an associative array of all variables defined in scope in which
 * error occurred.  We cannot support this, for compatibility with older PHP.
 *
 * @access public
 * @param int $errNo Type of error
 * @param string $errStr Error message
 * @param string $errFile File in which error occurred
 * @param int $errLine Line number on which error occurred
 * @return void
 */
function XoopsErrorHandler_HandleError($errNo, $errStr, $errFile, $errLine)
{
    // NOTE: we only store relative pathnames
    $new_error = [
        'errno' => $errNo,
        'errstr' => $errStr,
        'errfile' => str_replace([XOOPS_ROOT_PATH, XOOPS_TRUST_PATH], ['(html)', '(trust)'], $errFile),
        'errline' => $errLine
    ];
    $error_handler =& XoopsErrorHandler::getInstance();
    $error_handler->handleError($new_error);
}

/**
 * User-defined shutdown function (called from 'exit')
 *
 * @access public
 * @return void
 */
function XoopsErrorHandler_Shutdown()
{
    $error = error_get_last();
    if (E_ERROR === $error['type']) {
        XoopsErrorHandler_HandleError($error['type'], $error['message'], $error['file'], $error['line']);
    }
    $error_handler =& XoopsErrorHandler::getInstance();
    echo $error_handler->renderErrors();
}
