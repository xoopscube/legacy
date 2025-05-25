<?php
/**
 * Bannerstats - Module for XCL
 * BannerstatsInstaller.class.php
 *
 * Custom installer class for the Bannerstats module.
 * Handles tasks like inserting sample data after tables are created.
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// Ensure the base installer class is loaded
require_once XOOPS_ROOT_PATH . '/modules/legacy/admin/class/ModuleInstaller.class.php';

class Bannerstats_Installer extends Legacy_ModuleInstaller
{
    /**
     * Constructor.
     * Initializes the installer.
     */
    public function __construct()
    {
        parent::__construct();
        // specific options here if needed
        // $this->setForce(true); // Example: Force table overwrite on re-install (use with caution)
    }

    /**
     * Executes the installation process.
     * This method is called by the core installer.
     * The SQL file specified in xoops_version.php is executed BEFORE this method.
     *
     * @return bool True on success, false on failure.
     */
    public function executeInstall()
    {
        // The core installer should have already executed sql/mysql.sql.
        // We proceed directly to inserting sample data.
        // Use $this->mLog->addReport() or $this->mLog->addError() for logging.

        // Now $_mTargetXoopsModule is documented via @property-read
        $this->mLog->addReport(sprintf("Executing custom installer script for %s...", $this->_mTargetXoopsModule->get('dirname')));
        $this->mLog->addReport("Attempting to insert sample data...");

        // Get the database object (available via parent class)
        $db = $this->db;

        $modDirname = $this->_mTargetXoopsModule->get('dirname');
        $bannerclientTable = $db->prefix($modDirname . '_bannerclient');
        $bannerTable = $db->prefix($modDirname . '_banner');
        $bannerfinishTable = $db->prefix($modDirname . '_bannerfinish');

        $currentTime = time();
        // Using Unix timestamps for date_created, start_date, end_date as per recent refactoring
        // $currentSqlDateTime = date('Y-m-d H:i:s', $currentTime); // Not needed if storing timestamps
        $startDateDefault = $currentTime;
        $endDate30Days = $currentTime + (30 * 24 * 60 * 60);
        $endDate60Days = $currentTime + (60 * 24 * 60 * 60);
        $endDate15Days = $currentTime + (15 * 24 * 60 * 60);
        $endDate45Days = $currentTime + (45 * 24 * 60 * 60);
        $finishedOriginalStart = $currentTime - (15 * 24 * 60 * 60);
        $finishedOriginalEnd = $currentTime - (7 * 24 * 60 * 60);
        $finishedDateCreatedOriginal = $currentTime - (30 * 24 * 60 * 60);
        $finishedDateActual = $currentTime - (7 * 24 * 60 * 60);


        // Sample Banner Client Data
        $sampleClientPasswordHash = password_hash('samplepass', PASSWORD_DEFAULT);

        $sql = sprintf(
            "INSERT INTO %s (`cid`, `name`, `contact`, `email`, `tel`, `address1`, `city`, `region`, `postal_code`, `country_code`, `login`, `passwd`, `extrainfo`, `status`, `date_created`, `last_updated`) VALUES " .
            "(1, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 1, %d, %d)",
            $bannerclientTable,
            $db->quoteString('Advertiser Inc.'),
            $db->quoteString('Jane Smith'),
            $db->quoteString('advertiser@example.com'),
            $db->quoteString('+1-555-0100'),
            $db->quoteString('456 Ad Lane'),
            $db->quoteString('New City'),
            $db->quoteString('NY'),
            $db->quoteString('10001'),
            $db->quoteString('US'),
            $db->quoteString('advclient'),
            $db->quoteString($sampleClientPasswordHash),
            $db->quoteString('This is a sample advertiser client record.'),
            $currentTime,
            $currentTime
        );
        if (!$db->query($sql)) {
            $this->mLog->addError(sprintf("Failed to insert sample client data: %s", $db->error()));
        } else {
             $this->mLog->addReport("Sample client data inserted.");
        }


        $default_sample_banner_imptotal = 10000;

        // Sample Image Banner
        $sql = sprintf(
            "INSERT INTO %s (`cid`, `campaign_id`, `name`, `banner_type`, `imageurl`, `clickurl`, `width`, `height`, `imptotal`, `impmade`, `clicks`, `start_date`, `end_date`, `timezone`, `date_created`, `status`, `weight`) VALUES " .
            "(1, NULL, %s, 'image', %s, %s, 728, 90, %d, 0, 0, %d, %d, 'UTC', %d, 1, 10)",
            $bannerTable,
            $db->quoteString('Standard Image Ad'),
            $db->quoteString('modules/bannerstats/assets/images/sample_banner_728x90.png'),
            $db->quoteString('https://xoopscube.org/'),
            $default_sample_banner_imptotal,
            $startDateDefault, // Unix timestamp
            $endDate30Days,    // Unix timestamp
            $currentTime
        );
        if (!$db->query($sql)) {
            $this->mLog->addError(sprintf("Failed to insert sample image banner: %s", $db->error()));
        } else {
             $this->mLog->addReport("Sample image banner inserted.");
        }

        // Sample Third-Party Ad Tag
        $googleAdTagHtml = '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-YOUR_CLIENT_ID" crossorigin="anonymous"></script>\n<!-- Responsive Ad -->\n<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-YOUR_CLIENT_ID" data-ad-slot="YOUR_AD_SLOT_ID" data-ad-format="auto" data-full-width-responsive="true"></ins>\n<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
        $sql = sprintf(
            "INSERT INTO %s (`cid`, `campaign_id`, `name`, `banner_type`, `htmlcode`, `width`, `height`, `imptotal`, `impmade`, `clicks`, `start_date`, `end_date`, `timezone`, `date_created`, `status`, `weight`) VALUES " .
            "(1, NULL, %s, 'ad_tag', %s, 300, 250, 0, 0, 0, %d, %d, 'UTC', %d, 1, 10)",
            $bannerTable,
            $db->quoteString('Google Ad Unit (300x250)'),
            $db->quoteString($googleAdTagHtml),
            $startDateDefault, // Unix timestamp
            $endDate60Days,    // Unix timestamp
            $currentTime
        );
        if (!$db->query($sql)) {
            $this->mLog->addError(sprintf("Failed to insert sample ad_tag banner: %s", $db->error()));
        } else {
             $this->mLog->addReport("Sample ad_tag banner inserted.");
        }

        // Sample Custom HTML Code Banner
        $customHtmlBannerCode = '<div style="width:100%; height:100px; background-color: #e0f7fa; border: 1px solid #00796b; display:flex; align-items:center; justify-content:center; font-family: Arial, sans-serif;">\n  <h3 style="color: #004d40; margin:0;">Special Discount Inside!</h3>\n  <a href="https://xoopscube.org/shop" target="_blank" style="margin-left:15px; padding: 5px 10px; background-color:#00796b; color:white; text-decoration:none; border-radius:3px;">Shop Now</a>\n</div>';
        $sql = sprintf(
            "INSERT INTO %s (`cid`, `campaign_id`, `name`, `banner_type`, `htmlcode`, `width`, `height`, `imptotal`, `impmade`, `clicks`, `start_date`, `end_date`, `timezone`, `date_created`, `status`, `weight`) VALUES " .
            "(1, NULL, %s, 'html', %s, NULL, 100, 5000, 0, 0, %d, %d, 'UTC', %d, 1, 10)",
            $bannerTable,
            $db->quoteString('Custom HTML Promotion'),
            $db->quoteString($customHtmlBannerCode),
            $startDateDefault, // Unix timestamp
            $endDate15Days,    // Unix timestamp
            $currentTime
        );
        if (!$db->query($sql)) {
            $this->mLog->addError(sprintf("Failed to insert sample html banner: %s", $db->error()));
        } else {
             $this->mLog->addReport("Sample html banner inserted.");
        }

        // Sample Video Ad
        $videoAdHtmlCode = '<!-- Placeholder for Video Ad Tag (e.g., VAST URL or iframe embed) -->\n<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        $sql = sprintf(
            "INSERT INTO %s (`cid`, `campaign_id`, `name`, `banner_type`, `htmlcode`, `width`, `height`, `imptotal`, `impmade`, `clicks`, `start_date`, `end_date`, `timezone`, `date_created`, `status`, `weight`) VALUES " .
            "(1, NULL, %s, 'video', %s, 560, 315, 20000, 0, 0, %d, %d, 'UTC', %d, 1, 10)",
            $bannerTable,
            $db->quoteString('Promotional Video Ad'),
            $db->quoteString($videoAdHtmlCode),
            $startDateDefault, // Unix timestamp
            $endDate45Days,    // Unix timestamp
            $currentTime
        );
        if (!$db->query($sql)) {
            $this->mLog->addError(sprintf("Failed to insert sample video banner: %s", $db->error()));
        } else {
             $this->mLog->addReport("Sample video banner inserted.");
        }

        // --- Sample Finished Banner Data ---
        $sql = sprintf(
            "INSERT INTO %s (`bid`, `cid`, `campaign_id`, `name`, `banner_type`, `imageurl`, `clickurl`, `htmlcode`, `width`, `height`, `imptotal_allocated`, `impressions_made`, `clicks_made`, `datestart_original`, `dateend_original`, `timezone_original`, `date_created_original`, `date_finished`, `finish_reason`) VALUES " .
            "(101, 1, NULL, %s, 'image', %s, %s, NULL, 468, 60, 5000, 5000, 150, %d, %d, 'UTC', %d, %d, %s)",
            $bannerfinishTable,
            $db->quoteString('Finished Sample Banner'),
            $db->quoteString('modules/bannerstats/assets/images/sample_banner_468x60.png'),
            $db->quoteString('https://xoopscube.org/finished'),
            $finishedOriginalStart,         // Unix timestamp
            $finishedOriginalEnd,           // Unix timestamp
            $finishedDateCreatedOriginal,   // Unix timestamp
            $finishedDateActual,            // Unix timestamp
            $db->quoteString('Impressions Reached')
        );
         if (!$db->query($sql)) {
            $this->mLog->addError(sprintf("Failed to insert sample finished banner: %s", $db->error()));
        } else {
             $this->mLog->addReport("Sample finished banner inserted.");
        }

        $this->mLog->addReport("Sample data insertion complete.");
        $this->mLog->addReport(sprintf("Custom installer script for %s completed.", $modDirname));

        return true; // Indicate successful execution
    }
}
