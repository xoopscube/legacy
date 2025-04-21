<?php

/**
 * HTML Purifier Filter
 *
 * This filter sanitizes POST data using HTMLPurifier
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

class postcommon_post_htmlpurify extends ProtectorFilterAbstract
{
    /**
     * Apply HTML Purifier to POST data
     */
    public function execute(): bool
    {
        // Check if HTMLPurifier is available
        if (!file_exists(XOOPS_TRUST_PATH . '/vendor/htmlpurifier/library/HTMLPurifier.auto.php')) {
            return true;
        }

        // Get config
        $enabled = $this->getConf('postcommon_post_htmlpurify', 1);
        if (empty($enabled)) {
            return true;
        }

        // Get HTMLPurifier
        require_once XOOPS_TRUST_PATH . '/vendor/htmlpurifier/library/HTMLPurifier.auto.php';
        
        // Create config
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', XOOPS_TRUST_PATH . '/cache');
        $config->set('Core.Encoding', _CHARSET);
        
        // Allow embedded YouTube videos if configured
        if ($this->getConf('enable_embedded_youtube', 0)) {
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
        }
        
        // Create purifier
        $purifier = new HTMLPurifier($config);
        
        // Process all POST data
        $this->purifyRecursive($_POST, $purifier);
        
        return true;
    }
    
    /**
     * Recursively purify array values
     */
    private function purifyRecursive(array &$data, HTMLPurifier $purifier): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->purifyRecursive($data[$key], $purifier);
            } else {
                // Skip non-string values and password fields
                if (!is_string($value) || strpos($key, 'pass') !== false) {
                    continue;
                }
                
                // Skip values that don't contain HTML
                if (!preg_match('/<[a-z\!].*>/i', $value)) {
                    continue;
                }
                
                // Purify the value
                $data[$key] = $purifier->purify($value);
            }
        }
    }
}