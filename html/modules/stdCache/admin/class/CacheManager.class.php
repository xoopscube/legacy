<?php
/**
 * Standard cache - Module for XCL
 * CacheManager.class.php
 * 
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8 
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
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
    
    /**
     * Load module configuration
     */
    protected function loadModuleConfig()
    {
        $module_handler = xoops_gethandler('module');
        $config_handler = xoops_gethandler('config');
        
        $module = $module_handler->getByDirname('stdCache');
        if (is_object($module)) {
            // Fetch all config items for this module by category 0 (module configs)
            // and module ID. This ensures we get the actual values from the DB.
            $this->moduleConfig = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
        } else {
            // Log an error if the module can't be found, though this is unlikely
            // if the CacheManager is being used within the module itself.
            if ($this->logger) {
                $this->logger->addWarning('stdCache module object not found in CacheManager::loadModuleConfig.');
            }
        }
    }
    
    /**
     * Get configuration value
     * 
     * @param string $name Configuration name
     * @param mixed $default Default value if config not found
     * @return mixed Configuration value
     */
    public function getConfig($name, $default = null)
    {
        // Uses the locally loaded moduleConfig array
        return isset($this->moduleConfig[$name]) ? $this->moduleConfig[$name] : $default;
    }
    
    /**
     * Get all configuration values
     * 
     * @return array Configuration values
     */
    public function getConfigs()
    {
        // Returns the locally loaded moduleConfig array
        return $this->moduleConfig;
    }


    /**
     * Save module configuration values to the database.
     * Creates config items if they don't exist.
     *
     * @param array $configData Associative array of config names and their new values
     *                          (e.g., ['cache_limit' => 1000000, 'notification_enabled' => 1]).
     * @return bool True on success, false on failure.
     */
    public function saveConfig(array $configData)
    {
        if (empty($configData)) {
            return true; // Nothing to save, so consider it a success.
        }

        $module_handler = xoops_gethandler('module');
        $config_handler = xoops_gethandler('config');

        if (!is_object($module_handler)) {
            $this->logOperation('Failed to get module_handler in saveConfig.', 'error');
            return false;
        }
        if (!is_object($config_handler)) {
            $this->logOperation('Failed to get config_handler in saveConfig.', 'error');
            return false;
        }

        $module = $module_handler->getByDirname('stdCache');
        if (!is_object($module)) {
            $this->logOperation('Failed to get stdCache module object in saveConfig.', 'error');
            return false;
        }
        $mid = $module->getVar('mid');

        $success = true; // Assume success until an error occurs

        foreach ($configData as $name => $value) {
            // Criteria to find the specific config item for this module
            $criteria = new CriteriaCompo(new Criteria('conf_modid', $mid));
            $criteria->add(new Criteria('conf_name', $name));

            $configObjects = $config_handler->getConfigs($criteria);

            if (count($configObjects) > 0) {
                // Config item exists, so update its value
                $configObj = $configObjects[0];
                $configObj->setVar('conf_value', $value);
                if (!$config_handler->insertConfig($configObj)) { // insertConfig handles updates too
                    $this->logOperation("Failed to update config: {$name} to value: {$value}", 'error');
                    $success = false; // Mark as failed
                } else {
                    $this->logOperation("Successfully updated config: {$name} to value: {$value}", 'info');
                }
            } else {
                // Config item does not exist, so create it
                $configObj = $config_handler->createConfig();
                $configObj->setVar('conf_modid', $mid);
                $configObj->setVar('conf_catid', 0); // Standard category for module configs
                $configObj->setVar('conf_name', $name);
                $configObj->setVar('conf_value', $value);

                // Define title and description, trying to use language constants
                $titleConst = '_MI_STDCACHE_CONF_' . strtoupper($name);
                $descConst = '_MI_STDCACHE_CONF_' . strtoupper($name) . '_DESC';
                $configObj->setVar('conf_title', defined($titleConst) ? constant($titleConst) : $name);
                $configObj->setVar('conf_desc', defined($descConst) ? constant($descConst) : '');

                if ($name === 'notification_enabled') {
                    $configObj->setVar('conf_formtype', 'yesno');
                    $configObj->setVar('conf_valuetype', 'int');
                } else { // For limit fields
                    $configObj->setVar('conf_formtype', 'textbox');
                    $configObj->setVar('conf_valuetype', 'int');
                }

                // Determine a new order for the config item
                $orderCriteria = new CriteriaCompo(new Criteria('conf_modid', $mid));
                $orderCriteria->setSort('conf_order');
                $orderCriteria->setOrder('DESC');
                $orderCriteria->setLimit(1);
                $lastOrderConfigs = $config_handler->getConfigs($orderCriteria);
                $newOrder = 0;
                if (count($lastOrderConfigs) > 0) {
                    $newOrder = $lastOrderConfigs[0]->getVar('conf_order') + 1;
                }
                $configObj->setVar('conf_order', $newOrder);

                if (!$config_handler->insertConfig($configObj)) {
                    $this->logOperation("Failed to create new config: {$name} with value: {$value}", 'error');
                    $success = false; // Mark as failed
                } else {
                    $this->logOperation("Successfully created new config: {$name} with value: {$value}", 'info');
                }
            }
        }

        if ($success) {
            // Clear the XOOPSCube config cache so changes take effect immediately
            if (method_exists($config_handler, 'clearConfigCache')) { // For XCL 2.3+
                $config_handler->clearConfigCache();
            } elseif (function_exists('xoops_module_clear_config_cache')) { // For older XCL/XOOPS
                xoops_module_clear_config_cache($mid);
            }
            // Reload the module's configuration into this CacheManager instance
            $this->loadModuleConfig();
        }
        
        return $success;
    }



    /**
     * Update last notification time
     * 
     * @param int $time Timestamp
     * @return bool Success or failure
     */
    public function updateLastNotificationTime($time)
    {
        // This can now leverage the saveConfig
        return $this->saveConfig(['last_notification_time' => $time]);
    }

    
    /**
     * Get cache statistics
     * 
     * @return array Cache statistics
     */
    public function getCacheStats()
    {
        $stats = [];
        
        // Smarty Cache directory stats (XOOPS_TRUST_PATH . '/cache')
        $smartyCachePath = $this->cacheDirs['cache']; 
        $stats['cache_size'] = $this->calculateSize($smartyCachePath);
        $stats['cache_limit'] = (int)$this->getConfig('cache_limit', 50000000); 
        $stats['cache_notification_limit'] = (int)$this->getConfig('cache_notification_limit', 40000000); 
        $stats['cache_cleanup_limit'] = (int)$this->getConfig('cache_cleanup_limit', 45000000); 
        $stats['smarty_cache_file_count'] = $this->countFiles($smartyCachePath);
        $stats['smarty_cache_subdirs'] = $this->countSubdirs($smartyCachePath);
        
        // Compiled templates stats (XOOPS_TRUST_PATH . '/templates_c')
        $compiledPath = $this->cacheDirs['templates_c'];
        $stats['compiled_size'] = $this->calculateSize($compiledPath);
        $stats['compiled_limit'] = (int)$this->getConfig('compiled_templates_limit', 20000000); 
        $stats['compiled_file_count'] = $this->countFiles($compiledPath);
        $stats['compiled_subdirs'] = $this->countSubdirs($compiledPath);

        // Logs directory stats (XOOPS_TRUST_PATH . '/cache/logs')
        $logsPath = $this->cacheDirs['logs'];
        if (is_dir($logsPath)) {
            $stats['logs_size'] = $this->calculateSize($logsPath);
            $stats['logs_file_count'] = $this->countFiles($logsPath);
            $stats['logs_subdirs'] = $this->countSubdirs($logsPath); 
        } else {
            $stats['logs_size'] = 0;
            $stats['logs_file_count'] = 0;
            $stats['logs_subdirs'] = 0;
            $this->logOperation("Logs cache directory not found: {$logsPath}", 'warning');
        }

        // Uploads directory stats (XOOPS_TRUST_PATH . '/uploads') changed from public uploads with avatars
        $uploadsPath = $this->cacheDirs['uploads'];
        if (is_dir($uploadsPath)) {
            $stats['upload_size'] = $this->calculateSize($uploadsPath);
            $stats['upload_file_count'] = $this->countFiles($uploadsPath);
            $stats['upload_subdirs'] = $this->countSubdirs($uploadsPath);
        } else {
            $stats['upload_size'] = 0;
            $stats['upload_file_count'] = 0;
            $stats['upload_subdirs'] = 0;
            $this->logOperation("Uploads directory not found: {$uploadsPath}", 'warning');
        }
        
        // Total size - Sum of independent cache areas
        $stats['total_size'] = $stats['cache_size'] + $stats['compiled_size'] + $stats['logs_size'] + $stats['upload_size'];
        
        // Check if we need to send notification (based on Smarty Cache size)
        if ($stats['cache_size'] > $stats['cache_notification_limit'] && 
            (int)$this->getConfig('notification_enabled', 1)) {
            $this->sendNotification($stats); 
        }
        
        // Check if we need to clean up Smarty Cache
        if ($stats['cache_size'] > $stats['cache_cleanup_limit']) {
            $this->cleanupCache(); // This will call cleanupCacheDirectory for smarty_cache
        }
        
        // Check if we need to clean up compiled templates
        if ($stats['compiled_size'] > $stats['compiled_limit']) {
            $this->cleanupCompiledTemplates();
        }
        
        return $stats;
    }
    
    /**
     * Calculate directory size
     * 
     * @param string $dir Directory path
     * @return int Directory size in bytes
     */
    public function calculateSize($dir)
    {
        $size = 0;
        if (!is_dir($dir) || !is_readable($dir)) {
            $this->logOperation("Directory not found or not readable for size calculation: {$dir}", 'warning');
            return 0;
        }
        
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS)
            );
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        } catch (UnexpectedValueException $e) {
            $this->logOperation("Error iterating directory for size calculation {$dir}: " . $e->getMessage(), 'error');
            return 0;
        } catch (Exception $e) { 
            $this->logOperation("General error during size calculation for {$dir}: " . $e->getMessage(), 'error');
            return 0;
        }
        return $size;
    }
    
    /**
     * Count files in directory
     * 
     * @param string $dir Directory path
     * @return int File count
     */
    public function countFiles($dir)
    {
        $count = 0;
        if (!is_dir($dir) || !is_readable($dir)) {
            $this->logOperation("Directory not found or not readable for file count: {$dir}", 'warning');
            return 0;
        }

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $count++;
                }
            }
        } catch (UnexpectedValueException $e) {
            $this->logOperation("Error iterating directory for file count {$dir}: " . $e->getMessage(), 'error');
            return 0;
        } catch (Exception $e) { 
            $this->logOperation("General error during file count for {$dir}: " . $e->getMessage(), 'error');
            return 0;
        }
        return $count;
    }

    /**
     * Count subdirectories in directory (excluding the root directory itself)
     * 
     * @param string $dir Directory path
     * @return int Subdirectory count
     */
    public function countSubdirs($dir)
    {
        $count = 0;
        if (!is_dir($dir) || !is_readable($dir)) {
            $this->logOperation("Directory not found or not readable for subdir count: {$dir}", 'warning');
            return 0;
        }

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
                RecursiveIteratorIterator::CHILD_FIRST 
            );
            
            $realDir = realpath($dir); 

            foreach ($iterator as $item) {
                if ($item->isDir()) {
                    if (realpath($item->getPathname()) !== $realDir) {
                        $count++;
                    }
                }
            }
        } catch (UnexpectedValueException $e) {
            $this->logOperation("Error iterating directory for subdir count {$dir}: " . $e->getMessage(), 'error');
            return 0;
        } catch (Exception $e) { 
            $this->logOperation("General error during subdir count for {$dir}: " . $e->getMessage(), 'error');
            return 0;
        }
        return $count;
    }
    
    protected function sendNotification($stats)
    {
        $lastNotification = (int)$this->getConfig('last_notification_time', 0);
        $currentTime = time();
        
        if (!(bool)$this->getConfig('notification_enabled', true)) {
            return;
        }

        if ($currentTime - $lastNotification > 86400) { 
            $member_handler = xoops_gethandler('member');
            $adminUsers = $member_handler->getUsersByGroup(XOOPS_GROUP_ADMIN);
            
            if (count($adminUsers) > 0) {
                $xoopsMailer = null;
                if (class_exists('My_Mailer') && file_exists(XOOPS_ROOT_PATH . '/modules/message/class/MyMailer.class.php')) {
                    require_once XOOPS_ROOT_PATH . '/modules/message/class/MyMailer.class.php';
                    $xoopsMailer = new My_Mailer();
                } else {
                    $xoopsMailer = getMailer(); 
                }
                
                if (!$xoopsMailer) {
                    $this->logOperation('Mailer could not be initialized for notification.', 'error');
                    return;
                }

                $xoopsMailer->useMail();
                $language = $GLOBALS['xoopsConfig']['language'] ?? 'english';
                $templateDirModule = XOOPS_ROOT_PATH . '/modules/stdCache/language/' . $language . '/mail_template/';
                $templateDirEnglish = XOOPS_ROOT_PATH . '/modules/stdCache/language/english/mail_template/';

                if (is_dir($templateDirModule)) {
                    $xoopsMailer->setTemplateDir($templateDirModule);
                } elseif (is_dir($templateDirEnglish)) {
                    $xoopsMailer->setTemplateDir($templateDirEnglish);
                } else {
                     $this->logOperation('Mail template directory not found for stdCache.', 'error');
                     return; 
                }

                $xoopsMailer->setTemplate('cache_limit_notification.tpl'); 
                $xoopsMailer->setFromEmail($GLOBALS['xoopsConfig']['adminmail']);
                $xoopsMailer->setFromName($GLOBALS['xoopsConfig']['sitename']);
                $xoopsMailer->setSubject(sprintf(defined('_AD_STDCACHE_MAIL_SUBJECT') ? _AD_STDCACHE_MAIL_SUBJECT : 'Cache Size Warning: %s', $GLOBALS['xoopsConfig']['sitename']));
                
                $recipientEmails = [];
                foreach ($adminUsers as $user) {
                    if (is_object($user) && method_exists($user, 'getVar') && $user->getVar('email')) {
                        $recipientEmails[] = $user->getVar('email');
                    }
                }

                if (empty($recipientEmails)) {
                    $this->logOperation('No admin users with valid emails found for notification.', 'warning');
                    return;
                }
                $xoopsMailer->setToEmails($recipientEmails);
                
                $xoopsMailer->assign('SITENAME', $GLOBALS['xoopsConfig']['sitename']);
                $xoopsMailer->assign('SITEURL', XOOPS_URL);
                $xoopsMailer->assign('CACHE_SIZE', $this->formatSize($stats['cache_size'])); 
                $xoopsMailer->assign('CACHE_LIMIT', $this->formatSize($stats['cache_notification_limit'])); 
                $xoopsMailer->assign('ADMIN_URL', XOOPS_URL . '/modules/stdCache/admin/index.php?action=CacheStats');
                
                if ($xoopsMailer->send(true)) { 
                    $this->updateLastNotificationTime($currentTime);
                    $this->logOperation('Cache limit notification sent.');
                } else {
                    $this->logOperation('Failed to send cache limit notification: ' . $xoopsMailer->getErrors(false), 'error');
                }
            }
        }
    }
    
    /**
     * Cleanup a specific cache directory.
     * @param string $cachePath Path to the cache directory to clean.
     * @param string $configLimitKey The module config key for this directory's cleanup limit.
     * @return bool
     */
    protected function cleanupCacheDirectory($cachePath, $configLimitKey)
    {
        $files = [];
        $this->getFilesRecursive($cachePath, $files);
        
        if (empty($files)) {
            return true;
        }

        usort($files, function($a, $b) {
            return ($a['atime'] ?? 0) <=> ($b['atime'] ?? 0); 
        });
        
        $currentSize = $this->calculateSize($cachePath);
        $limitConfigValue = (int)$this->getConfig($configLimitKey, $this->getConfig('cache_cleanup_limit', 45000000));
        $targetSize = $limitConfigValue * 0.8; 
        
        $deletedCount = 0;
        $initialSize = $currentSize;

        foreach ($files as $file) {
            if ($currentSize <= $targetSize) {
                break;
            }
            if (is_file($file['path'])) {
                $fileSize = @filesize($file['path']); 
                if ($fileSize === false) continue;

                if (@unlink($file['path'])) {
                    $currentSize -= $fileSize;
                    $deletedCount++;
                } else {
                    $this->logOperation("Failed to delete file during cleanup: {$file['path']}", 'warning');
                }
            }
        }
        if ($deletedCount > 0) {
            $this->logOperation(basename($cachePath) . " cleanup: Deleted {$deletedCount} files. Size reduced from " . $this->formatSize($initialSize) . " to " . $this->formatSize($currentSize) . ".", 'info');
        }
        return true;
    }

    protected function cleanupCache() 
    {
        return $this->cleanupCacheDirectory($this->cacheDirs['cache'], 'cache_cleanup_limit');
    }
    
    protected function cleanupCompiledTemplates()
    {
        return $this->cleanupCacheDirectory($this->cacheDirs['templates_c'], 'compiled_templates_limit');
    }
    
    protected function getFilesRecursive($dir, &$results)
    {
        if (!is_dir($dir) || !is_readable($dir)) {
            return;
        }
        try {
            $items = new DirectoryIterator($dir);
            foreach ($items as $item) {
                if ($item->isDot()) {
                    continue;
                }
                $path = $item->getPathname(); 
                if ($item->isDir()) {
                    $this->getFilesRecursive($path, $results);
                } else {
                    $results[] = [
                        'path' => $path,
                        'atime' => @$item->getATime(), 
                        'mtime' => @$item->getMTime()  
                    ];
                }
            }
        } catch (Exception $e) {
            $this->logOperation("Error reading directory {$dir} in getFilesRecursive: " . $e->getMessage(), 'error');
        }
    }
    
    public function formatSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $size = (float)$size;
        
        if ($size == 0) {
            return '0 ' . $units[0];
        }
        
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
    
    /**
     * Clears specified cache directories.
     *
     * @param array $options Associative array where keys are cache types (e.g., 'smarty_cache', 'compiled_templates', 'logs', 'uploads')
     *                       and values are arrays containing 'age' in seconds (0 for all files).
     *                       Example: ['smarty_cache' => ['age' => 0], 'logs' => ['age' => 86400 * 7]]
     * @return array Results of the clear operation.
     */
    public function clearCache($options)
    {
        $results = [
            'success' => true,
            'messages' => [],
            'errors' => []
        ];

        $cacheTypeMap = [
            'smarty_cache' => [
                'path_key' => 'cache', 
                'lang_key' => _AD_STDCACHE_SMARTY_CACHE ?? 'Smarty Cache'
            ],
            'compiled_templates' => [
                'path_key' => 'templates_c',
                'lang_key' => _AD_STDCACHE_COMPILED_TEMPLATES ?? 'Compiled Templates'
            ],
            'logs' => [
                'path_key' => 'logs',
                'lang_key' => defined('_AD_STDCACHE_LOG_FILES_SECTION') ? _AD_STDCACHE_LOG_FILES_SECTION : 'Log Files' 
            ],
            'uploads' => [
                'path_key' => 'uploads',
                'lang_key' => defined('_AD_STDCACHE_UPLOAD_DIRECTORY') ? _AD_STDCACHE_UPLOAD_DIRECTORY : 'Uploads Directory'
            ]
        ];

        foreach ($cacheTypeMap as $optionKey => $details) {
            // Check if this cache type was selected for clearing and options for it are provided
            if (isset($options[$optionKey]) && is_array($options[$optionKey])) { 
                $path = $this->cacheDirs[$details['path_key']];
                
                // Extract age from the options. Default to 0 (clear all) if not specified or invalid.
                $age = 0; // Default to clear all files
                if (isset($options[$optionKey]['age']) && is_numeric($options[$optionKey]['age'])) {
                    $age = (int)$options[$optionKey]['age'];
                }
                
                if (is_dir($path)) {
                    $count = $this->clearDirectory($path, $age);
                    $results['messages'][] = sprintf(
                        defined('_AD_STDCACHE_CLEARED_FILES_FROM_WITH_AGE') ? _AD_STDCACHE_CLEARED_FILES_FROM_WITH_AGE : 'Cleared %d files from %s%s.', 
                        $count, 
                        $details['lang_key'],
                        ($age > 0 ? sprintf(' (older than %d seconds)', $age) : '') // Add age info to message
                    );
                } else {
                    $results['errors'][] = sprintf('Directory for %s not found: %s', $details['lang_key'], $path);
                    $results['success'] = false;
                }
            }
        }
        
        return $results;
    }
    
    protected function clearDirectory($dir, $age = 0) // age in seconds, 0 means all files
    {
        $count = 0;
        if (!is_dir($dir) || !is_readable($dir)) {
            $this->logOperation("Directory not found or not readable for clearing: {$dir}", 'warning');
            return 0;
        }
        $cutoffTime = ($age > 0) ? (time() - $age) : 0;
        
        $files = [];
        $this->getFilesRecursive($dir, $files);
        
        foreach ($files as $file) {
            if ($cutoffTime === 0 || ($file['mtime'] ?? 0) < $cutoffTime) {
                if (is_file($file['path'])) {
                    if (@unlink($file['path'])) {
                        $count++;
                    } else {
                         $this->logOperation("Failed to delete file during clear: {$file['path']}", 'warning');
                    }
                }
            }
        }
        if ($count > 0) {
            $this->logOperation("Cleared {$count} files from directory: {$dir}" . ($age > 0 ? " older than " . ($age/3600) . " hours." : "."), 'info');
        }
        return $count;
    }
}
