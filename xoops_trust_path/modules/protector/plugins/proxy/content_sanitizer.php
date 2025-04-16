<?php
/**
 * Content Sanitizer Plugin for Protector Proxy
 *
 * This plugin removes potentially harmful elements from proxied content
 * including scripts, iframes, and other risky elements.
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class ProtectorProxyContentSanitizerPlugin {
    private $proxy;
    
    /**
     * Constructor
     * 
     * @param ProtectorProxy $proxy The proxy instance
     */
    public function __construct($proxy) {
        $this->proxy = $proxy;
    }
    
    /**
     * Process content
     * 
     * @param string $url The original URL
     * @param string $content The content to process
     * @return string The processed content
     */
    public function processContent($url, $content) {
        // Add a notification banner
        $banner = '<div style="background:#f8f9fa;padding:10px;text-align:center;border-bottom:1px solid #ddd;">
                    <strong>Security Notice:</strong> This content is being viewed through the Protector secure proxy.
                    Some elements may have been removed for security reasons.
                   </div>';
        
        // Insert banner after the body tag
        $content = preg_replace('/<body([^>]*)>/i', '<body$1>' . $banner, $content);
        
        // Remove potentially harmful elements
        $elements_to_remove = [
            // Scripts
            '/<script\b[^>]*>(.*?)<\/script>/is',
            // Inline event handlers
            '/on\w+\s*=\s*"[^"]*"/is',
            '/on\w+\s*=\s*\'[^\']*\'/is',
            // Iframes
            '/<iframe\b[^>]*>(.*?)<\/iframe>/is',
            // Object/embed
            '/<object\b[^>]*>(.*?)<\/object>/is',
            '/<embed\b[^>]*>(.*?)<\/embed>/is',
            // Base tags (which can redirect references)
            '/<base\b[^>]*>/is',
            // Meta refreshes
            '/<meta\s+http-equiv\s*=\s*["\']?refresh["\']?[^>]*>/is'
        ];
        
        foreach ($elements_to_remove as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        
        // Add CSS to prevent hidden elements
        $security_css = '<style>
            /* Security styles added by Protector Proxy */
            [style*="position:absolute"],
            [style*="position: absolute"],
            [style*="z-index:"],
            [style*="z-index: "] {
                position: relative !important;
                z-index: auto !important;
            }
        </style>';
        
        // Add security CSS to head
        $content = preg_replace('/<\/head>/i', $security_css . '</head>', $content);
        
        return $content;
    }
}