<?php
/**
 * Content Sanitizer Plugin for Protector Proxy
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_TRUST_PATH . '/modules/protector/class/PluginBase.class.php';

/**
 * Content Sanitizer plugin for proxy functionality
 */
class Plugin_ContentSanitizer extends ProtectorProxyPluginBase {
    /**
     * Tags to remove from content
     */
    private $removeTags = [];
    
    /**
     * Attributes to remove from tags
     */
    private $removeAttributes = [];
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Get configuration
        $config = $this->proxy->getConfig();
        
        // Set default tags to remove if not in config
        $this->removeTags = isset($config['sanitizer_remove_tags']) ? 
            $config['sanitizer_remove_tags'] : 
            ['script', 'iframe', 'object', 'embed', 'applet'];
        
        // Set default attributes to remove if not in config
        $this->removeAttributes = isset($config['sanitizer_remove_attributes']) ? 
            $config['sanitizer_remove_attributes'] : 
            ['onload', 'onerror', 'onclick', 'onmouseover', 'onmouseout', 'onsubmit'];
        
        return true;
    }
    
    /**
     * Pre-process URL - this plugin doesn't modify URLs
     * 
     * @param string $url The URL to process
     * @return string|false The processed URL or false to block
     */
    public function preProcess($url) {
        // This plugin doesn't modify URLs
        return $url;
    }
    
    /**
     * Post-process content - sanitize HTML content
     * 
     * @param string $content The content to process
     * @param string $url The URL
     * @return string|false The processed content or false to block
     */
    public function postProcess($content, $url) {
        // Only process HTML content
        if (strpos($content, '<!DOCTYPE html') !== false || 
            strpos($content, '<html') !== false || 
            strpos($content, '<body') !== false) {
            
            // Load HTML content
            $dom = new DOMDocument();
            
            // Suppress warnings from malformed HTML
            libxml_use_internal_errors(true);
            
            // Load HTML content with UTF-8 encoding
            $dom->loadHTML('<?xml encoding="UTF-8">' . $content);
            
            // Remove dangerous tags
            $this->removeDangerousTags($dom);
            
            // Remove dangerous attributes
            $this->removeDangerousAttributes($dom);
            
            // Get sanitized HTML
            $sanitizedContent = $dom->saveHTML();
            
            // Remove XML declaration added by loadHTML
            $sanitizedContent = preg_replace('/<\?xml encoding="UTF-8"\>/', '', $sanitizedContent);
            
            // Clear libxml errors
            libxml_clear_errors();
            
            return $sanitizedContent;
        }
        
        // Return non-HTML content unchanged
        return $content;
    }
    
    /**
     * Handle domain - this plugin doesn't handle any domains directly
     * 
     * @param string $url The URL to handle
     * @return string|false The content or false if not handled
     */
    public function handleDomain($url) {
        // This plugin doesn't handle domains directly
        return false;
    }
    
    /**
     * Remove dangerous tags from DOM
     * 
     * @param DOMDocument $dom The DOM document
     */
    private function removeDangerousTags(DOMDocument $dom) {
        foreach ($this->removeTags as $tagName) {
            $elements = $dom->getElementsByTagName($tagName);
            
            // Need to remove elements in reverse order to avoid issues with the NodeList
            $elementsToRemove = [];
            for ($i = 0; $i < $elements->length; $i++) {
                $elementsToRemove[] = $elements->item($i);
            }
            
            foreach ($elementsToRemove as $element) {
                if ($element->parentNode) {
                    $element->parentNode->removeChild($element);
                }
            }
        }
    }
    
    /**
     * Remove dangerous attributes from DOM
     * 
     * @param DOMDocument $dom The DOM document
     */
    private function removeDangerousAttributes(DOMDocument $dom) {
        // Get all elements
        $xpath = new DOMXPath($dom);
        $allElements = $xpath->query('//*');
        
        // Check each element for dangerous attributes
        foreach ($allElements as $element) {
            if ($element->hasAttributes()) {
                $attributes = $element->attributes;
                $attributesToRemove = [];
                
                // Find attributes to remove
                for ($i = 0; $i < $attributes->length; $i++) {
                    $attr = $attributes->item($i);
                    $attrName = strtolower($attr->name);
                    
                    // Check if attribute name is in the remove list
                    if (in_array($attrName, $this->removeAttributes)) {
                        $attributesToRemove[] = $attr->name;
                    }
                    
                    // Also check for javascript: in URLs
                    if (($attrName == 'href' || $attrName == 'src') && 
                        stripos($attr->value, 'javascript:') !== false) {
                        $attributesToRemove[] = $attr->name;
                    }
                }
                
                // Remove dangerous attributes
                foreach ($attributesToRemove as $attrName) {
                    $element->removeAttribute($attrName);
                }
            }
        }
    }
}