<?php
/**
 * Standard cache - Module for XCL
 * CacheManager.class.php
 * 
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once __DIR__ . '/AbstractCacheManager.class.php';

class stdCache_CacheManager extends stdCache_AbstractCacheManager
{
    protected $moduleConfig = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->loadModuleConfig();
    }
    
    protected function loadModuleConfig()
    {
        $module_handler = xoops_gethandler('module');
        $config_handler = xoops_gethandler('config');
        
        $module = $module_handler->getByDirname('stdCache');
        if (is_object($module)) {
            $this->moduleConfig = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
        } else {
            // This is a critical error, so keep this log
            $this->logOperation('stdCache module object not found in loadModuleConfig. This is a critical error.', 'critical');
        }
    }
    
    public function getConfig($name, $default = null)
    {
        return isset($this->moduleConfig[$name]) ? $this->moduleConfig[$name] : $default;
    }
    
    public function getConfigs()
    {
        return $this->moduleConfig;
    }

    public function saveConfig(array $configData)
    {
        if (empty($configData)) {
            return true; 
        }

        $module_handler = xoops_gethandler('module');
        $config_handler = xoops_gethandler('config'); 

        if (!is_object($module_handler) || !is_object($config_handler)) {
            $this->logOperation('saveConfig: Failed to get module_handler or config_handler.', 'error');
            return false;
        }

        $module = $module_handler->getByDirname('stdCache');
        if (!is_object($module)) {
            $this->logOperation('saveConfig: Failed to get stdCache module object.', 'error');
            return false;
        }
        $mid = $module->getVar('mid');

        $overallSuccess = true; 

        foreach ($configData as $name => $value) {
            $criteria = new CriteriaCompo(new Criteria('conf_modid', $mid));
            $criteria->add(new Criteria('conf_name', $name));

            $configObjects = $config_handler->getConfigs($criteria);

            if (count($configObjects) > 0) {
                $configObj = $configObjects[0]; 
                $originalValue = $configObj->getVar('conf_value');
                $confId = $configObj->getVar('conf_id');
                
                $stringValue = (string)$value;
                $configObj->setVar('conf_value', $stringValue); 

                if (!$configObj->isDirty() && $originalValue === $stringValue) {
                    // No change, skip update for this item
                    continue; 
                }
                
                $insertSuccess = $config_handler->insertConfig($configObj, true); 
                
                if (!$insertSuccess) {
                    $error_message_handler = "saveConfig: XOOPS HANDLER FAILED to update config: '{$name}' (ID: {$confId}) to value: '{$stringValue}'.";
                    $handler_errors_arr = [];
                    if (method_exists($config_handler, 'getErrors')) {
                        $handler_errors_arr = $config_handler->getErrors();
                        if (!empty($handler_errors_arr)) {
                            $error_message_handler .= " Handler errors: [" . implode('; ', $handler_errors_arr) . "]";
                        }
                    } else {
                         $error_message_handler .= " getErrors() not available on handler.";
                    }
                    $this->logOperation($error_message_handler, 'error');

                    if ($name === 'last_cache_alert_time') {
                        $this->logOperation("saveConfig: Attempting DIRECT DB UPDATE for 'last_cache_alert_time' as handler failed.", 'warning');
                        $db = XoopsDatabaseFactory::getDatabaseConnection();
                        $configTable = $db->prefix('config');
                        $sql = sprintf(
                            "UPDATE %s SET conf_value = %s WHERE conf_modid = %u AND conf_name = %s AND conf_id = %u",
                            $configTable,
                            $db->quoteString($stringValue),
                            $mid,
                            $db->quoteString($name),
                            $confId
                        );
                        if ($db->queryF($sql)) {
                            if ($db->getAffectedRows() > 0) {
                                $this->logOperation("saveConfig: DIRECT DB UPDATE for 'last_cache_alert_time' SUCCEEDED.", 'info');
                                $insertSuccess = true; 
                            } else {
                                $this->logOperation("saveConfig: DIRECT DB UPDATE for 'last_cache_alert_time' executed, but 0 rows affected.", 'warning');
                                $insertSuccess = true; 
                            }
                        } else {
                            $this->logOperation("saveConfig: DIRECT DB UPDATE for 'last_cache_alert_time' FAILED. DB Error: " . $db->error(), 'error');
                            $overallSuccess = false; 
                        }
                    } else {
                        $overallSuccess = false;
                    }
                }
                
                if ($insertSuccess) { 
                    $this->logOperation("saveConfig: Successfully processed/updated config: '{$name}' to value: '{$stringValue}'", 'info');
                } else if ($name !== 'last_cache_alert_time') { 
                    $overallSuccess = false;
                } else if ($name === 'last_cache_alert_time' && !$insertSuccess) {
                    $overallSuccess = false;
                }

            } else {
                $this->logOperation("saveConfig: CRITICAL - Config item '{$name}' not found in database for module ID {$mid}.", 'error');
                $overallSuccess = false; 
            }
        } // End foreach

        if ($overallSuccess) {
            if (method_exists($config_handler, 'clearConfigCache')) { 
                $config_handler->clearConfigCache();
            } elseif (function_exists('xoops_module_clear_config_cache')) { 
                xoops_module_clear_config_cache($mid);
            }
            $this->loadModuleConfig(); 
        } else {
            $this->logOperation("saveConfig: One or more config items failed to update definitively.", 'warning');
        }
        
        return $overallSuccess;
    }

    public function updateLastNotificationTime($time)
    {
        $timestampInt = (int)$time;
        $timestampString = (string)$timestampInt; 
        
        $result = $this->saveConfig(['last_cache_alert_time' => $timestampString]); 
        
        if (!$result) {
            $this->logOperation("updateLastNotificationTime: FAILED to update last_cache_alert_time to '{$timestampString}'.", 'error');
        }
        return $result;
    }

    // CacheManager methods: getCacheStats, calculateSize, etc.
    public function getCacheStats()
    {
        $stats = [];
        $smartyCachePath = $this->cacheDirs['cache']; 
        $stats['cache_size'] = $this->calculateSize($smartyCachePath);
        $stats['cache_limit_smarty'] = (int)$this->getConfig('cache_limit_smarty', 50000000); 
        $stats['cache_limit_alert_trigger'] = (int)$this->getConfig('cache_limit_alert_trigger', 40000000); 
        $stats['cache_limit_cleanup'] = (int)$this->getConfig('cache_limit_cleanup', 45000000); 
        $stats['smarty_cache_file_count'] = $this->countFiles($smartyCachePath);
        $stats['smarty_cache_subdirs'] = $this->countSubdirs($smartyCachePath);
        $compiledPath = $this->cacheDirs['templates_c'];
        $stats['compiled_size'] = $this->calculateSize($compiledPath);
        $stats['compiled_limit'] = (int)$this->getConfig('cache_limit_compiled', 20000000); 
        $stats['compiled_file_count'] = $this->countFiles($compiledPath);
        $stats['compiled_subdirs'] = $this->countSubdirs($compiledPath);
        $logsPath = $this->cacheDirs['logs'];
        if (is_dir($logsPath)) {
            $stats['logs_size'] = $this->calculateSize($logsPath);
            $stats['logs_file_count'] = $this->countFiles($logsPath);
            $stats['logs_subdirs'] = $this->countSubdirs($logsPath); 
        } else {
            $stats['logs_size'] = 0; $stats['logs_file_count'] = 0; $stats['logs_subdirs'] = 0;
            $this->logOperation("Logs cache directory not found: {$logsPath}", 'warning');
        }
        $uploadsPath = $this->cacheDirs['uploads'];
        if (is_dir($uploadsPath)) {
            $stats['upload_size'] = $this->calculateSize($uploadsPath);
            $stats['upload_file_count'] = $this->countFiles($uploadsPath);
            $stats['upload_subdirs'] = $this->countSubdirs($uploadsPath);
        } else {
            $stats['upload_size'] = 0; $stats['upload_file_count'] = 0; $stats['upload_subdirs'] = 0;
            $this->logOperation("Uploads directory not found: {$uploadsPath}", 'warning');
        }
        $stats['total_size'] = ($stats['cache_size'] ?? 0) + ($stats['compiled_size'] ?? 0) + ($stats['logs_size'] ?? 0) + ($stats['upload_size'] ?? 0);
        if (($stats['cache_size'] ?? 0) > ($stats['cache_limit_cleanup'] ?? PHP_INT_MAX)) { $this->cleanupCache(); }
        if (($stats['compiled_size'] ?? 0) > ($stats['compiled_limit'] ?? PHP_INT_MAX)) { $this->cleanupCompiledTemplates(); }
        return $stats;
    }
    public function calculateSize($dir){ $size = 0; if (!is_dir($dir) || !is_readable($dir)) { return 0; } try { $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS)); foreach ($iterator as $file) { if ($file->isFile()) { $size += $file->getSize(); } } } catch (Exception $e) { $this->logOperation("Error calculating size for {$dir}: " . $e->getMessage(), 'error'); return 0; } return $size; }
    public function countFiles($dir){ $count = 0; if (!is_dir($dir) || !is_readable($dir)) { return 0; } try { $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS), RecursiveIteratorIterator::LEAVES_ONLY); foreach ($iterator as $file) { if ($file->isFile()) { $count++; } } } catch (Exception $e) { $this->logOperation("Error counting files for {$dir}: " . $e->getMessage(), 'error'); return 0; } return $count; }
    public function countSubdirs($dir){ $count = 0; if (!is_dir($dir) || !is_readable($dir)) { return 0; } try { $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS), RecursiveIteratorIterator::CHILD_FIRST ); $realDir = realpath($dir); foreach ($iterator as $item) { if ($item->isDir()) { if (realpath($item->getPathname()) !== $realDir) { $count++; } } } } catch (Exception $e) { $this->logOperation("Error counting subdirs for {$dir}: " . $e->getMessage(), 'error'); return 0; } return $count; }
    protected function cleanupCacheDirectory($cachePath, $configLimitKey){ $files = []; $this->getFilesRecursive($cachePath, $files); if (empty($files)) { return true; } usort($files, function($a, $b) { return ($a['atime'] ?? 0) <=> ($b['atime'] ?? 0); }); $currentSize = $this->calculateSize($cachePath); $limitConfigValue = (int)$this->getConfig($configLimitKey, PHP_INT_MAX); $targetSize = $limitConfigValue; $deletedCount = 0; $initialSize = $currentSize; foreach ($files as $file) { if ($currentSize <= $targetSize) { break; } if (is_file($file['path'])) { $fileSize = @filesize($file['path']); if ($fileSize === false) continue; if (@unlink($file['path'])) { $currentSize -= $fileSize; $deletedCount++; } else { $this->logOperation("Failed to delete file during cleanup: {$file['path']}", 'warning'); } } } if ($deletedCount > 0) { $this->logOperation(basename($cachePath) . " cleanup: Deleted {$deletedCount} files. Size reduced from " . $this->formatSize($initialSize) . " to " . $this->formatSize($currentSize) . ".", 'info'); } return true; }
    protected function cleanupCache() { return $this->cleanupCacheDirectory($this->cacheDirs['cache'], 'cache_limit_cleanup'); }
    protected function cleanupCompiledTemplates() { return $this->cleanupCacheDirectory($this->cacheDirs['templates_c'], 'cache_limit_compiled'); }
    protected function getFilesRecursive($dir, &$results){ if (!is_dir($dir) || !is_readable($dir)) { return; } try { $items = new DirectoryIterator($dir); foreach ($items as $item) { if ($item->isDot()) { continue; } $path = $item->getPathname(); if ($item->isDir()) { $this->getFilesRecursive($path, $results); } else { $results[] = [ 'path' => $path, 'atime' => @$item->getATime(), 'mtime' => @$item->getMTime() ]; } } } catch (Exception $e) { $this->logOperation("Error reading directory {$dir} in getFilesRecursive: " . $e->getMessage(), 'error'); } }
    public function formatSize($size){ $units = ['B', 'KB', 'MB', 'GB', 'TB']; $i = 0; $size = (float)$size; if ($size == 0) { return '0 ' . $units[0]; } while ($size >= 1024 && $i < count($units) - 1) { $size /= 1024; $i++; } return round($size, 2) . ' ' . $units[$i]; }
    public function clearCache($options){ $results = [ 'success' => true, 'messages' => [], 'errors' => [] ]; $cacheTypeMap = [ 'smarty_cache' => [ 'path_key' => 'cache', 'lang_key' => defined('_AD_STDCACHE_CACHE_FILES') ? _AD_STDCACHE_CACHE_FILES : 'Cache Files' ], 'compiled_templates' => [ 'path_key' => 'templates_c', 'lang_key' => defined('_AD_STDCACHE_COMPILED_TEMPLATES') ? _AD_STDCACHE_COMPILED_TEMPLATES : 'Compiled Templates' ], 'logs' => [ 'path_key' => 'logs', 'lang_key' => defined('_AD_STDCACHE_LOG_FILES_SECTION') ? _AD_STDCACHE_LOG_FILES_SECTION : 'Log Files' ], 'uploads' => [ 'path_key' => 'uploads', 'lang_key' => defined('_AD_STDCACHE_UPLOAD_DIRECTORY') ? _AD_STDCACHE_UPLOAD_DIRECTORY : 'Uploads Directory' ] ]; foreach ($cacheTypeMap as $optionKey => $details) { if (isset($options[$optionKey]) && is_array($options[$optionKey])) { $path = $this->cacheDirs[$details['path_key']]; $age = 0; if (isset($options[$optionKey]['age']) && is_numeric($options[$optionKey]['age'])) { $age = (int)$options[$optionKey]['age']; } if (is_dir($path)) { $count = $this->clearDirectory($path, $age); $ageString = ''; if ($age > 0) { if ($age == 86400) $ageString = ' (older than 1 day)'; elseif ($age == 86400 * 7) $ageString = ' (older than 7 days)'; elseif ($age == 86400 * 30) $ageString = ' (older than 30 days)'; else $ageString = sprintf(' (older than %d seconds)', $age); } $results['messages'][] = sprintf( defined('_AD_STDCACHE_CLEARED_FILES_FROM') ? _AD_STDCACHE_CLEARED_FILES_FROM : 'Cleared %d files from %s%s.', $count, $details['lang_key'], $ageString ); } else { $results['errors'][] = sprintf('Directory for %s not found: %s', $details['lang_key'], $path); $results['success'] = false; } } } return $results; }
    protected function clearDirectory($dir, $age = 0) { $count = 0; if (!is_dir($dir) || !is_readable($dir)) { return 0; } $cutoffTime = ($age > 0) ? (time() - $age) : 0; $files = []; $this->getFilesRecursive($dir, $files); foreach ($files as $fileInfo) { if ($cutoffTime === 0 || ($fileInfo['mtime'] ?? 0) < $cutoffTime) { if (is_file($fileInfo['path'])) { if (@unlink($fileInfo['path'])) { $count++; } else { $this->logOperation("Failed to delete file during clear: {$fileInfo['path']}", 'warning'); } } } } if ($count > 0) { $this->logOperation("Cleared {$count} files from directory: {$dir}" . ($age > 0 ? " older than " . ($age/3600) . " hours." : " (all files)."), 'info'); } return $count; }

}
