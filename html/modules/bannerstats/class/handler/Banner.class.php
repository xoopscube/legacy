<?php
/**
 * Bannerstats - Module for XCL
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

require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/handler.php';
require_once dirname(__DIR__) . '/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/mail/AdminAlertMail.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/mail/ClientAlertMail.class.php';

class Bannerstats_BannerHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'banner';
    public $mPrimary = 'bid';
    public $mClass = 'Bannerstats_BannerObject';
    public string $_mDirname;
    // Add a static property to track counted impressions in the current request
    public static $countedImpressions = [];

    // Constants for notification strings
    public const DEFAULT_CLIENT_NAME = 'Valued Client';
    public const ADMIN_RECIPIENT_NAME = 'Site Admin';

    public const TPL_LOW_IMP_CLIENT = 'banner_low_impressions_client.tpl';
    public const TPL_LOW_IMP_ADMIN  = 'banner_low_impressions_admin.tpl';
    public const TPL_FINISHED_CLIENT = 'banner_finished_client.tpl';
    public const TPL_FINISHED_ADMIN  = 'banner_finished_admin.tpl';

    public function __construct(&$db)
    {
        parent::__construct($db);
    }

    /**
     * Public method to Adnin send test notification emails.
     * @param int $bid Banner ID
     * @param string $emailType One of 'low_client', 'low_admin', 'finished_client', 'finished_admin'
     * @return bool True on success, false on failure
     */
    public function sendTestNotification(int $bid, string $emailType): bool
    {
        $banner = $this->get($bid);
        if (!$banner instanceof Bannerstats_BannerObject) {
            error_log("sendTestNotification: Banner ID {$bid} not found.");
            return false;
        }
        $banner->loadClient();
        $client = $banner->mClient;
        $clientName = ($client instanceof Bannerstats_BannerclientObject && $client->get('name')) ? $client->get('name') : self::DEFAULT_CLIENT_NAME;
        $clientEmail = ($client instanceof Bannerstats_BannerclientObject && $client->get('email')) ? $client->get('email') : null;

        $root = XCube_Root::getSingleton();
        $configHandler = xoops_gethandler('config');
        $moduleConfig = $configHandler->getConfigsByDirname('bannerstats');
        $adminEmail = $moduleConfig['banner_alert_admin_email'] ?? $root->mContext->getXoopsConfig('adminmail');
        $bannerName = $banner->getShow('name');

        error_log("sendTestNotification: Sending test notification for banner ID {$bid} with email type '{$emailType}'.");

        switch ($emailType) {
            case 'low_client':
                if (!$clientEmail) {
                    error_log("sendTestNotification: Client email not found for banner ID {$bid}.");
                    return false;
                }
                error_log("sendTestNotification: Sending low impressions notification to client email {$clientEmail}.");
                $this->_sendLowImpressionsNotification($banner);
                error_log("sendTestNotification: Low impressions notification sent to client.");
                break;

            case 'low_admin':
                error_log("sendTestNotification: Sending low impressions notification to admin email {$adminEmail}.");
                $this->_sendLowImpressionsNotification($banner);
                error_log("sendTestNotification: Low impressions notification sent to admin.");
                break;

            case 'finished_client':
                if (!$clientEmail) {
                    error_log("sendTestNotification: Client email not found for banner ID {$bid}.");
                    return false;
                }
                error_log("sendTestNotification: Sending finished notification to client email {$clientEmail}.");
                $this->_sendBannerFinishedNotification(null, $banner, false);
                error_log("sendTestNotification: Finished notification sent to client.");
                break;

            case 'finished_admin':
                error_log("sendTestNotification: Sending finished notification to admin email {$adminEmail}.");
                $this->_sendBannerFinishedNotification(null, $banner, false);
                error_log("sendTestNotification: Finished notification sent to admin.");
                break;

            default:
                error_log("sendTestNotification: Invalid email type '{$emailType}' for banner ID {$bid}.");
                return false;
        }
        return true;
    }

    /**
     * Gets a displayable banner based on various criteria.
     * Priority: bid > campaign_id > cid > global random.
     *
     * @param int $bid Specific Banner ID. If > 0, this is prioritized.
     * @param int|null $cid Client ID.
     *                        - null: No client filter (used with bid, or for global random if campaign_id is also 0).
     *                        - 0: Filter by banners with cid=0 (if campaign_id is also 0 or matches).
     *                        - >0: Filter by specific client ID.
     * @param int $campaign_id Campaign ID. If > 0, filters by this campaign.
     * @return Bannerstats_BannerObject|null The banner object or null if none found.
     */
    public function getDisplayBanner(int $bid = 0, ?int $cid = null, int $campaign_id = 0): ?Bannerstats_BannerObject
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('status', 1)); // Must be active

        // Add common active checks (date, impressions)
        $currentTime = time();
        $dateCriteriaStart = new CriteriaCompo();
        $dateCriteriaStart->add(new Criteria('start_date', 0));
        $dateCriteriaStart->add(new Criteria('start_date', $currentTime, '<='), 'OR');
        $criteria->add($dateCriteriaStart);

        $dateCriteriaEnd = new CriteriaCompo();
        $dateCriteriaEnd->add(new Criteria('end_date', 0));
        $dateCriteriaEnd->add(new Criteria('end_date', $currentTime, '>='), 'OR');
        $criteria->add($dateCriteriaEnd);

        if ($bid > 0) {
            $criteria->add(new Criteria('bid', $bid));
            if ($campaign_id > 0) {
                $criteria->add(new Criteria('campaign_id', $campaign_id));
            }
            if ($cid !== null) {
                $criteria->add(new Criteria('cid', $cid));
            }
        } else {
            if ($campaign_id > 0) {
                $criteria->add(new Criteria('campaign_id', $campaign_id));
                if ($cid !== null) {
                    $criteria->add(new Criteria('cid', $cid));
                }
            } elseif ($cid !== null) {
                if ($cid === 0) {
                     $criteria->add(new Criteria('cid', 0));
                } elseif ($cid > 0) {
                     $criteria->add(new Criteria('cid', $cid));
                }
            }
        }

        $whereClause = $criteria->renderWhere();
        $sql = "SELECT * FROM " . $this->mTable;
        if (!empty($whereClause)) {
            $sql .= " " . $whereClause;
        }

        $impressionCondition = "(imptotal = 0 OR impmade < imptotal)";
        if (!empty($whereClause)) {
            $sql .= " AND " . $impressionCondition;
        } else {
            $sql .= " WHERE " . $impressionCondition;
        }

        if ($bid == 0) {
            $sql .= " ORDER BY RAND()";
        }
        $sql .= " LIMIT 1";

        $result = $this->db->query($sql);
        if (!$result) {
            //error_log("Bannerstats_BannerHandler::getDisplayBanner - SQL Error: " . $this->db->error() . " | SQL: " . $sql);
            return null;
        }

        if ($this->db->getRowsNum($result) > 0) {
            $row = $this->db->fetchArray($result);
            $banner = new $this->mClass();
            $banner->assignVars($row);
            return $banner;
        }
        return null;
    }


 



    public function countImpression(int $bid): bool
    {
    // Use a global variable to track counted impressions per request
    global $xoopsBannerImpressionTracker;
    if (!is_array($xoopsBannerImpressionTracker)) {
        $xoopsBannerImpressionTracker = [];
    }
    if (isset($xoopsBannerImpressionTracker[$bid])) {
        //error_log("Bannerstats_BannerHandler::countImpression - BID {$bid}: Impression already counted in this request (global), skipping duplicate count.");
        return true;
    }
    $xoopsBannerImpressionTracker[$bid] = true;

        // Check if impression already counted
        if (isset(self::$countedImpressions[$bid])) {
            //error_log("Bannerstats_BannerHandler::countImpression - BID {$bid}: Impression already counted in this request.");
            return true;
        }

        // Retrieve banner object
        /** @var Bannerstats_BannerObject|null $banner */
        $banner = $this->get($bid); // Targets prefix_banner

        // Check if impression limit is reached
        if ($banner->get('imptotal') > 0 && $banner->get('impmade') >= $banner->get('imptotal')) {
            // Change banner status to inactive (0) and call finishBanner method
            $banner->set('status', 0);
            // Ensure impmade is exactly imptotal and end_date is set if finishing due to impressions
            $banner->set('impmade', $banner->get('imptotal'));
            if ($banner->get('end_date') == 0 || $banner->get('end_date') > time()) {
                $banner->set('end_date', time());
            }
            //error_log("Bannerstats_BannerHandler::countImpression - BID {$bid}: Attempting to set banner to inactive (status 0) before calling finishBanner.");
            if (!$this->insert($banner, true)) { // Save status 0 to prefix_banner
                //error_log("Bannerstats_BannerHandler::countImpression - BID {$bid}: CRITICAL - FAILED to set banner to inactive (status 0) before calling finishBanner. DB Error: " . $this->db->error() . ". Aborting finish process from countImpression.");
                return false; // Critical failure, banner might still be active in DB.
            } else {
                //error_log("Bannerstats_BannerHandler::countImpression - BID {$bid}: Successfully set banner to inactive (status 0) in database.");
            }

            // Call finishBanner method
            //error_log("Bannerstats_BannerHandler::countImpression - Calling finishBanner for BID {$bid}. Banner object status is now " . $banner->get('status'));
            
            $impressionsReachedReason = 'Impressions Reached';
            if (!defined('BANNER_FINISH_REASON_IMPRESSIONS')) {
                $moduleDirnameForConstants = $this->_mDirname ?? 'bannerstats';
                $bannerFinishHandlerFile = XOOPS_MODULE_PATH . '/' . $moduleDirnameForConstants . '/class/handler/BannerFinish.class.php';
                if (file_exists($bannerFinishHandlerFile)) require_once $bannerFinishHandlerFile;
            }
            if (defined('BANNER_FINISH_REASON_IMPRESSIONS')) $impressionsReachedReason = BANNER_FINISH_REASON_IMPRESSIONS;

            $finishResult = $this->finishBanner($banner, $impressionsReachedReason); 
                
            if ($finishResult === false) {
            } elseif ($finishResult instanceof Bannerstats_BannerfinishObject) {
                //error_log("finish result")
            }
            return true;
        } else {
            // Other checks
            if (!($banner instanceof Bannerstats_BannerObject) || $banner->get('status') != 1) {
                return false;
            }

            $currentImpmade = $banner->get('impmade');
            $newImpmade = $currentImpmade + 1;
            $banner->set('impmade', $newImpmade);
            $banner->set('last_impression_time', time());
            
            //error_log("Bannerstats_BannerHandler::countImpression - BID {$bid}: Before save: current_impmade={$currentImpmade}, new_impmade={$newImpmade}, imptotal=" . $banner->get('imptotal'));

            if (!$this->insert($banner, true)) { 
                // Save to prefix_banner failed
                return false;
            }
            // Impression counted and saved
            // error_log("Bannerstats Impression counted and saved");

            // Check if banner now reached its limit after increment
            if ($banner->get('imptotal') > 0 && $banner->get('impmade') >= $banner->get('imptotal')) {
                $impressionsReachedReason = defined('BANNER_FINISH_REASON_IMPRESSIONS') ? BANNER_FINISH_REASON_IMPRESSIONS : 'Impressions Reached';
                $this->finishBanner($banner, $impressionsReachedReason);
            }

            return true;
        }
        // Fallback, should never be reached
        return false;
    }
    
    public function finishBanner(Bannerstats_BannerObject $banner, string $reason = 'Manually Finished', int $finished_by_uid = 0)
    {
        if (!($banner instanceof Bannerstats_BannerObject) || !$banner->get('bid')) {
            return false;
        }
        $originalBannerBID = $banner->get('bid');
        // Assuming $this->_mDirname is set in the constructor. If not, use 'bannerstats' directly.
        $moduleDirname = $this->_mDirname ?? 'bannerstats';


        //Change banner-id status to '0' (inactive) and save to 'banner' table
        $banner->set('status', 0);
        //error_log("Bannerstats change as inactive in its table.");


        $impressionsReachedReason = 'Impressions Reached'; // Fallback

        if (!defined('BANNER_FINISH_REASON_IMPRESSIONS')) {
            $bannerFinishHandlerFile = XOOPS_MODULE_PATH . '/' . $moduleDirname . '/class/handler/BannerFinish.class.php';
            if (file_exists($bannerFinishHandlerFile)) {
                require_once $bannerFinishHandlerFile;
            }
        }
        if (defined('BANNER_FINISH_REASON_IMPRESSIONS')) {
            $impressionsReachedReason = BANNER_FINISH_REASON_IMPRESSIONS;
        }

        if ($reason === $impressionsReachedReason && $banner->get('imptotal') > 0) {
            $banner->set('impmade', $banner->get('imptotal'));
            if ($banner->get('end_date') == 0 || $banner->get('end_date') > time()) {
                $banner->set('end_date', time());
            }
        }

        // uses $this->mTable configured in Bannerstats_BannerHandler constructor
        if (!$this->insert($banner, true)) { // Force update
            //error_log("CRITICAL STEP FAILED): Failed to mark banner ID {$originalBannerBID} as inactive in its table. DB Error: " . $this->db->error() . ". Aborting finish process.");
            return false; // Banner remains active if this fails.
        }
        //error_log("Bannerstats_BannerHandler::finishBanner - Step 1 SUCCESS: Banner ID {$originalBannerBID} marked as inactive in its table.");

        // --- Step 2: Copy banner-id data to table 'bannerfinish' ---
        $bannerfinishHandler = xoops_getmodulehandler('bannerfinish', 'bannerstats');

        if (!$bannerfinishHandler instanceof Bannerstats_BannerfinishHandler) {
            $this->_sendBannerFinishedNotification(null, $banner, true);

            return false;
        }

        /** @var Bannerstats_BannerfinishObject $bannerfinish */
        $bannerfinish = $bannerfinishHandler->create();
        $bannerfinish->mDirname = 'bannerstats'; // Force correct module dirname

        if (!$bannerfinish instanceof Bannerstats_BannerfinishObject) {
            // Directly call the method as it's part of this class
            $this->_sendBannerFinishedNotification(null, $banner, true);

            return false;
        }

        // $bannerfinish->mDirname = 'bannerstats'; // Force correct module dirname

        // Populate $bannerfinish from the $banner object
        $bannerfinish->set('bid', $banner->get('bid'));
        $bannerfinish->set('cid', $banner->get('cid'));
        $bannerfinish->set('campaign_id', $banner->get('campaign_id'));
        $bannerfinish->set('name', $banner->get('name'));
        $bannerfinish->set('banner_type', $banner->get('banner_type'));
        $bannerfinish->set('imageurl', $banner->get('imageurl'));
        $bannerfinish->set('clickurl', $banner->get('clickurl'));
        $bannerfinish->set('htmlcode', $banner->get('htmlcode'));
        $bannerfinish->set('width', is_numeric($banner->get('width')) ? $banner->get('width') : null);
        $bannerfinish->set('height', is_numeric($banner->get('height')) ? $banner->get('height') : null);
        $bannerfinish->set('imptotal_allocated', $banner->get('imptotal'));
        $bannerfinish->set('impressions_made', $banner->get('impmade'));
        $bannerfinish->set('clicks_made', $banner->get('clicks'));
        $bannerfinish->set('datestart_original', $banner->get('start_date'));
        $bannerfinish->set('dateend_original', $banner->get('end_date'));
        $bannerfinish->set('timezone_original', $banner->get('timezone'));
        $bannerfinish->set('date_created_original', $banner->get('date_created'));
        $bannerfinish->set('date_finished', time());
        $bannerfinish->set('finish_reason', $reason);
        $bannerfinish->set('finished_by_uid', $finished_by_uid);
        
        $copySuccess = $bannerfinishHandler->insert($bannerfinish, true); // important, true (force)

        // Conditional Deletion
        if ($copySuccess) {
            // Copy succeeded, proceed with deletion from 'banner' table
            if ($this->delete($banner, true)) {
                //error_log("STEP SUCCESS: Deleted banner ID {$originalBannerBID} from its table.");
                $this->_sendBannerFinishedNotification($bannerfinish, $banner, false); // Full success

                return $bannerfinish;
            } else {
                $this->_sendBannerFinishedNotification($bannerfinish, $banner, true); // Admin review needed

                return $bannerfinish; // Return archived object, but original (inactive) wasn't deleted
            }
        } else {
            // Copy failed. Preserve banner-id in table 'banner' with status '0'.
            $this->_sendBannerFinishedNotification(null, $banner, true); // Notify admin of archival failure

            return false; // Indicate archival failed. Banner is inactive
        }
    }


    /**
     * Sends notification when a banner's impressions are running low
     * @param Bannerstats_BannerObject $banner
     */
    protected function _sendLowImpressionsNotification(Bannerstats_BannerObject $banner): void
    {
        $root = XCube_Root::getSingleton();
        $configHandler = xoops_gethandler('config');
        $moduleConfig = $configHandler->getConfigsByDirname('bannerstats');

        if (empty($moduleConfig['banner_alert_enable'])) {
            return;
        }

        $banner->loadClient(); // Load client data
        $client = $banner->mClient;
        
        $clientEmail = ($client instanceof Bannerstats_BannerclientObject && $client->get('email')) ? $client->get('email') : null;
        $clientName = ($client instanceof Bannerstats_BannerclientObject && $client->get('name')) ? $client->getShow('name') : self::DEFAULT_CLIENT_NAME;
        
        $adminEmail = $moduleConfig['banner_alert_admin_email'] ?? $root->mContext->getXoopsConfig('adminmail');
        $xoopsConfig = $root->mContext->getXoopsConfig(); // For XCube MailBuilder director

        $bannerName = $banner->getShow('name');
        $remainingImpressions = $banner->get('imptotal') - $banner->get('impmade');

        // --- Send to Client ---
        if ($clientEmail) {
            $clientSubjectArgs = [$bannerName];
            $clientBodyVarsCallback = function($bannerObj) use ($remainingImpressions) { // Pass $remainingImpressions
                return [
                    'impressions_made'      => $bannerObj->get('impmade'),
                    'impressions_total'     => $bannerObj->get('imptotal'),
                    'impressions_remaining' => $remainingImpressions,
                    'stats_link'            => XOOPS_URL . '/modules/bannerstats/index.php?action=Stats',
                ];
            };

            $clientBuilder = new Bannerstats_ClientAlertMail(
                $clientEmail,
                $clientName,
                self::TPL_LOW_IMP_CLIENT,
                '_MD_BANNERSTATS_EMAIL_LOW_IMP_SUBJECT_CLIENT',
                $clientSubjectArgs,
                $clientBodyVarsCallback
            );

            $director = new XCube_MailDirector($clientBuilder, $banner, $xoopsConfig, $moduleConfig);
            $director->constructMail();
            $mailer = $clientBuilder->getResult();
            if (!$mailer->send()) {
                //error_log("BannerStats: FAILED to send Low Impressions (Client) email to {$clientEmail} for banner BID " . $banner->get('bid') . ". Errors: " . implode(', ', $mailer->getErrors(false)));
            } else {
                //error_log("BannerStats: Sent Low Impressions (Client) email to {$clientEmail} for banner BID " . $banner->get('bid'));
            }
        }

        // --- Send to Admin ---
        if ($adminEmail) {
            $adminSubjectArgs = [$bannerName, $clientName];
            $adminBodyVarsCallback = function($bannerObj) use ($remainingImpressions, $clientName) { // Pass $remainingImpressions and $clientName
                return [
                    // 'client_name' is already assigned by AdminAlertMail if $bannerObj->mClient is loaded
                    'impressions_made'      => $bannerObj->get('impmade'),
                    'impressions_total'     => $bannerObj->get('imptotal'),
                    'impressions_remaining' => $remainingImpressions,
                    'admin_link'            => XOOPS_URL . '/modules/bannerstats/admin/index.php?action=BannerEdit&bid=' . $bannerObj->get('bid'),
                ];
            };

            $adminBuilder = new Bannerstats_AdminAlertMail(
                self::TPL_LOW_IMP_ADMIN,
                '_MD_BANNERSTATS_EMAIL_LOW_IMP_SUBJECT_ADMIN',
                $adminSubjectArgs,
                $adminBodyVarsCallback
            );
            
            $director = new XCube_MailDirector($adminBuilder, $banner, $xoopsConfig, $moduleConfig);
            $director->constructMail();
            $mailer = $adminBuilder->getResult();
            if (!$mailer->send()) {
                //error_log("BannerStats: FAILED to send Low Impressions (Admin) email for banner BID " . $banner->get('bid') . ". Errors: " . implode(', ', $mailer->getErrors(false)));
            } else {
                //error_log("BannerStats: Sent Low Impressions (Admin) email for banner BID " . $banner->get('bid'));
            }
        }
    }

    /**
     * Sends notification when a banner has finished.
     * @param Bannerstats_BannerfinishObject|null $finishedBanner The archived banner object, or null if archival failed
     * @param Bannerstats_BannerObject $originalBanner For client info and original banner details
     * @param bool $adminReviewNeeded Indicates if admin attention is required (e.g., delete failed or archival failed)
     */
    protected function _sendBannerFinishedNotification(?Bannerstats_BannerfinishObject $finishedBanner, Bannerstats_BannerObject $originalBanner, bool $adminReviewNeeded = false): void
    {
        $root = XCube_Root::getSingleton();
        $configHandler = xoops_gethandler('config');
        $moduleConfig = $configHandler->getConfigsByDirname('bannerstats');

        if (empty($moduleConfig['banner_alert_enable'])) {
            return;
        }

        $originalBanner->loadClient();
        $client = $originalBanner->mClient;

        $clientEmail = ($client instanceof Bannerstats_BannerclientObject && $client->get('email')) ? $client->get('email') : null;
        $clientName = ($client instanceof Bannerstats_BannerclientObject && $client->get('name')) ? $client->getShow('name') : self::DEFAULT_CLIENT_NAME;
        
        $adminEmail = $moduleConfig['banner_alert_admin_email'] ?? $root->mContext->getXoopsConfig('adminmail');
        $xoopsConfig = $root->mContext->getXoopsConfig();

        // Determine banner name and BID based on whether archival was successful
        $bannerNameForEmail = $finishedBanner ? $finishedBanner->getShow('name') : $originalBanner->getShow('name');
        $bannerBidForEmail = $finishedBanner ? $finishedBanner->get('bid') : $originalBanner->get('bid');

        // Send to Client
        // Only send to client if the banner was successfully archived ($finishedBanner is not null)
        if ($clientEmail && $finishedBanner instanceof Bannerstats_BannerfinishObject) {
            $clientSubjectArgs = [$bannerNameForEmail];
            $clientBodyVarsCallback = function($finBannerObj) { // $finBannerObj is $finishedBanner
                return [
                    'impressions_served'    => $finBannerObj->get('impressions_made'),
                    'clicks_received'       => $finBannerObj->get('clicks_made'),
                    'finish_reason'         => $finBannerObj->getFinishReasonText(), // Use the helper method
                    'date_finished'         => formatTimestamp($finBannerObj->get('date_finished')),
                    'stats_link'            => XOOPS_URL . '/modules/bannerstats/index.php?action=Stats',
                ];
            };

            $clientBuilder = new Bannerstats_ClientAlertMail(
                $clientEmail,
                $clientName,
                self::TPL_FINISHED_CLIENT,
                '_MD_BANNERSTATS_EMAIL_FINISHED_SUBJECT_CLIENT',
                $clientSubjectArgs,
                $clientBodyVarsCallback
            );

            $director = new XCube_MailDirector($clientBuilder, $finishedBanner, $xoopsConfig, $moduleConfig);
            $director->constructMail();
            $mailer = $clientBuilder->getResult();
            if (!$mailer->send()) {
                //error_log("BannerStats: FAILED to send Finished (Client) email to {$clientEmail} for banner BID " . $bannerBidForEmail . ". Errors: " . implode(', ', $mailer->getErrors(false)));
            } else {
                //error_log("BannerStats: Sent Finished (Client) email to {$clientEmail} for banner BID " . $bannerBidForEmail);
            }
        }

        // Send to Admin
        if ($adminEmail) {
            $adminSubjectArgs = [$bannerNameForEmail, $clientName];
            $adminSubjectKey = '_MD_BANNERSTATS_EMAIL_FINISHED_SUBJECT_ADMIN';
            
            // Modify subject if admin review is needed
            $emailSubjectSuffix = '';
            if ($adminReviewNeeded) {
                $emailSubjectSuffix = ' (' . (_MD_BANNERSTATS_EMAIL_ADMIN_REVIEW_NEEDED ?? 'Admin Review Needed') . ')';
            }

            $adminBodyVarsCallback = function($contextObj) use ($adminReviewNeeded, $originalBanner) {
                // $contextObj will be $finishedBanner if available, otherwise $originalBanner
                $vars = [
                    'admin_review_needed'   => $adminReviewNeeded,
                    'admin_link'            => XOOPS_URL . '/modules/bannerstats/admin/index.php?action=' . ($contextObj instanceof Bannerstats_BannerfinishObject ? 'BannerfinishView' : 'BannerEdit') . '&bid=' . $contextObj->get('bid'),
                ];
                if ($contextObj instanceof Bannerstats_BannerfinishObject) {
                    $vars['impressions_served'] = $contextObj->get('impressions_made');
                    $vars['clicks_received']    = $contextObj->get('clicks_made');
                    $vars['finish_reason']      = $contextObj->getFinishReasonText();
                    $vars['date_finished']      = formatTimestamp($contextObj->get('date_finished'));
                } else { // Archival failed, use original banner data
                    $vars['impressions_served'] = $contextObj->get('impmade');
                    $vars['clicks_received']    = $contextObj->get('clicks');
                    $vars['finish_reason']      = _MD_BANNERSTATS_FINISH_OTHER;
                    $vars['date_finished']      = _MD_BANNERSTATS_FINISH_DATE_EXPIRED;
                }
                return $vars;
            };

            $adminBuilder = new Bannerstats_AdminAlertMail(
                self::TPL_FINISHED_ADMIN,
                $adminSubjectKey, // The base subject key
                $adminSubjectArgs,
                $adminBodyVarsCallback
            );
            
            // The XCube_MailDirector will call setSubject on the builder.
            // We need to adjust the subject *after* the builder sets it initially if a suffix is needed.
            $contextObjectForAdminMail = $finishedBanner ?? $originalBanner; // Pass the most relevant object
            $director = new XCube_MailDirector($adminBuilder, $contextObjectForAdminMail, $xoopsConfig, $moduleConfig);
            $director->constructMail();
            $mailer = $adminBuilder->getResult();

            // Append suffix to subject if needed
            if (!empty($emailSubjectSuffix)) {
                $currentSubject = $mailer->getSubject();
                $mailer->setSubject($currentSubject . $emailSubjectSuffix);
            }

            if (!$mailer->send()) {
                // error_log("BannerStats: FAILED to send Finished (Admin) email for banner BID " . $bannerBidForEmail . ". Errors: " . implode(', ', $mailer->getErrors(false)));
            } else {
                // error_log("BannerStats: Sent Finished (Admin) email for banner BID " . $bannerBidForEmail);
            }
        }
    }

    public function countClick(int $bid): bool
    {
        /** @var Bannerstats_BannerObject|null $banner */
        $banner = $this->get($bid);
        if ($banner instanceof Bannerstats_BannerObject && $banner->get('status') == 1) {
            $banner->incrementClicks();
            $banner->set('last_click_time', time());
            return $this->insert($banner, true);
        }
        return false;
    }
}
