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

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerAdminEditForm.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/Banner.class.php';

class Bannerstats_BannerEditAction extends Bannerstats_AbstractEditAction
{
    // Default values for new banners or fetch from config
    const DEFAULT_BANNER_TYPE = 'image'; // default
    const DEFAULT_STATUS = 1; // Active
    const DEFAULT_WEIGHT = 10;

    /**
     * Gets the ID of the banner to be edited/created
     * @return int|null
     */
    public function _getId()
    {
        return xoops_getrequest('bid') ? (int)xoops_getrequest('bid') : null;
    }

    /**
     * Gets the handler for banner objects
     * @return Bannerstats_BannerHandler|false
     */
    public function &_getHandler()
    {
        $handler = xoops_getmodulehandler('banner', 'bannerstats');
        return $handler;
    }

    /**
     * Sets up the object for editing or creation
     * Called by the parent AbstractEditAction
     * @return void
     */
    public function _setupObject(): void
    {
        parent::_setupObject();

        if (is_object($this->mObject) && $this->mObject instanceof Bannerstats_BannerObject && $this->mObject->isNew()) {
            
            // Set cid if provided in request (e.g., "Add banner for this client" link)
            $cid = xoops_getrequest('cid') ? (int)xoops_getrequest('cid') : 0;
            if ($cid > 0) {
                $this->mObject->set('cid', $cid);
            }

            // Get default imptotal from module configuration
            $root = XCube_Root::getSingleton();
            $moduleConfig = $root->mContext->mModuleConfig ?? [];
            $minImpressions = isset($moduleConfig['min_impressions']) ? (int)$moduleConfig['min_impressions'] : 1000;

            $this->mObject->set('imptotal', $minImpressions > 0 ? $minImpressions : 1000);
            $this->mObject->set('banner_type', self::DEFAULT_BANNER_TYPE);
            $this->mObject->set('status', self::DEFAULT_STATUS);
            $this->mObject->set('weight', self::DEFAULT_WEIGHT);
            $this->mObject->set('date_created', time());
            
            $this->mObject->set('last_impression_time', 0);
            $this->mObject->set('last_click_time', 0);
            
            $this->mObject->set('imageurl', '');
            $this->mObject->set('clickurl', '');
            $this->mObject->set('htmlcode', '');

            // default timezone from site configuration: default_TZ
            $xoopsConfig = $root->mContext->getXoopsConfig();
            $defaultTimezone = $xoopsConfig['default_TZ'] ?? ($xoopsConfig['server_TZ'] ?? '');
            $this->mObject->set('timezone', $defaultTimezone);

            if (!$this->mObject->get('campaign_id')) { 
                $this->mObject->set('campaign_id', 0);
            }
        }
    }

    /**
     * Sets up the action form
     * @return void
     */
    public function _setupActionForm(): void
    {
        $this->mActionForm = new Bannerstats_BannerAdminEditForm();
        $this->mActionForm->prepare();
    }

    /**
     * Helper function to parse date strings using DateTime::createFromFormat
     *
     * @param string $dateTimeStr The date/time string to parse
     * @return DateTime|false DateTime object
     */
    protected function _parseDateTimeString(string $dateTimeStr): DateTime|false
    {
        //error_log("BannerEditAction - Attempting to parse date string: '" . $dateTimeStr . "'");

        // Formats: datetime-local first, then the DB format (with seconds)
        $formatsToTry = [
            'Y-m-d\TH:i',    // Standard datetime-local output (e.g., "2025-05-23T18:30")
            'Y-m-d H:i:s',  // Full datetime with seconds (e.g., "2025-05-23 18:30:00")
            'Y-m-d',       // Date only (e.g., "2025-05-23", time will be 00:00:00)
        ];

        foreach ($formatsToTry as $format) {
            $dateTimeObject = DateTime::createFromFormat($format, $dateTimeStr);
            
            if ($dateTimeObject !== false) {
                $errors = DateTime::getLastErrors();
                // A successful parse means $errors is false (no errors) OR
                // $errors is an array with 0 warnings and 0 errors
                if ($errors === false || (is_array($errors) && $errors['warning_count'] === 0 && $errors['error_count'] === 0)) {
                    return $dateTimeObject;
                }
            }
        }
        
        //error_log("BannerEditAction - Failed to parse '" . $dateTimeStr . "' with any known format.");
        return false;
    }


    /**
     * Performs specific pre-save logic (like date conversion)
     * and then calls the parent's _doExecute to save the object.
     * This method is called by AbstractEditAction::execute() after form validation
     * and after $this->mActionForm->update($this->mObject) has been called.
     *
     * @return bool True on success
     */
    public function _doExecute(): bool
    {
        if (!is_object($this->mObject) || !($this->mObject instanceof Bannerstats_BannerObject)) {
            if ($this->mActionForm) $this->mActionForm->addErrorMessage("Banner object not available for saving.");
            return false;
        }

        $startDateStrFromForm = trim((string)$this->mActionForm->get('start_date'));
        $startTimestamp = 0;
        if (!empty($startDateStrFromForm)) {
            $dateTimeObject = $this->_parseDateTimeString($startDateStrFromForm);
            if ($dateTimeObject instanceof DateTime) {
                $startTimestamp = $dateTimeObject->getTimestamp();
            } else {
                $this->mActionForm->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_DATETIME, _AD_BANNERSTATS_START_DATE, 'YYYY-MM-DDTHH:MM'));
            }
        }
        error_log("BannerEditAction _doExecute - StartDate String (from ActionForm): '{$startDateStrFromForm}', Parsed Timestamp: {$startTimestamp}");
        $this->mObject->set('start_date', $startTimestamp);

        $endDateStrFromForm = trim((string)$this->mActionForm->get('end_date'));
        $endTimestamp = 0;
        if (!empty($endDateStrFromForm)) {
            $dateTimeObject = $this->_parseDateTimeString($endDateStrFromForm);
            if ($dateTimeObject instanceof DateTime) {
                $endTimestamp = $dateTimeObject->getTimestamp();
            } else {
                $this->mActionForm->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_DATETIME, _AD_BANNERSTATS_DATE_END, 'YYYY-MM-DDTHH:MM'));
            }
        }
        error_log("BannerEditAction _doExecute - EndDate String (from ActionForm): '{$endDateStrFromForm}', Parsed Timestamp: {$endTimestamp}");
        $this->mObject->set('end_date', $endTimestamp);
        
        if ($this->mObject->isNew()) {
            if ($this->mObject->get('impmade') === null) {
                $this->mObject->set('impmade', 0);
            }
            if ($this->mObject->get('clicks') === null) {
                $this->mObject->set('clicks', 0);
            }
        }

        return parent::_doExecute(); // call handler->insert($this->mObject)
    }


    /**
     * Prepares and sets data for the edit/create form view
     *
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     * @return void
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render): void
    {
        $render->setTemplateName('banner_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);

        if (is_object($this->mObject) && $this->mObject instanceof Bannerstats_BannerObject) {
            $this->mObject->loadBannerclient(); 
        }
        $render->setAttribute('object', $this->mObject); 

        $bannerclientHandler = xoops_getmodulehandler('bannerclient', 'bannerstats');
        $bannerclientArr = [];
        if ($bannerclientHandler) {
            $bannerclientArr = $bannerclientHandler->getObjects();
        }
        $render->setAttribute('bannerclientArr', $bannerclientArr);


        $bannerTypes = [
            'image' => defined('_AD_BANNERSTATS_BTYPE_IMAGE') ? _AD_BANNERSTATS_BTYPE_IMAGE : 'Image',
            'video' => defined('_AD_BANNERSTATS_BTYPE_VIDEO') ? _AD_BANNERSTATS_BTYPE_VIDEO : 'Video (Embed/VAST)',
            'html' => defined('_AD_BANNERSTATS_BTYPE_HTML') ? _AD_BANNERSTATS_BTYPE_HTML : 'Custom HTML',
            'ad_tag' => defined('_AD_BANNERSTATS_BTYPE_ADTAG') ? _AD_BANNERSTATS_BTYPE_ADTAG : 'Third-Party Ad Tag',
        ];
        $render->setAttribute('bannerTypes', $bannerTypes);

        $statusOptions = [
            0 => defined('_AD_BANNERSTATS_STATUS_INACTIVE') ? _AD_BANNERSTATS_STATUS_INACTIVE : 'Inactive',
            1 => defined('_AD_BANNERSTATS_STATUS_ACTIVE') ? _AD_BANNERSTATS_STATUS_ACTIVE : 'Active',
        ];
        $render->setAttribute('statusOptions', $statusOptions);

        $root = XCube_Root::getSingleton();
        $xoopsConfig = $root->mContext->getXoopsConfig();
        $timezoneOptions = [
            $xoopsConfig['default_TZ'] ?? '' => (defined('_AD_BANNERSTATS_TZ_DEFAULT') ? _AD_BANNERSTATS_TZ_DEFAULT : 'Site Default') . ' (' . ($xoopsConfig['default_TZ'] ?? 'Not set') . ')',
            $xoopsConfig['server_TZ'] ?? ''  => (defined('_AD_BANNERSTATS_TZ_SERVER') ? _AD_BANNERSTATS_TZ_SERVER : 'Server Default') . ' (' . ($xoopsConfig['server_TZ'] ?? 'Not set') . ')',
        ];
        if (!empty($xoopsConfig['default_TZ']) && $xoopsConfig['default_TZ'] === ($xoopsConfig['server_TZ'] ?? '')) {
            unset($timezoneOptions[$xoopsConfig['server_TZ'] ?? '']);
        }
        $render->setAttribute('timezoneOptions', $timezoneOptions);
    }

    /**
     * Handles successful save
     *
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     * @return void
     */
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render): void
    {
        $message = $this->mObject->isNew() ?
                   (defined('_AD_BANNERSTATS_MESSAGE_CREATE_SUCCESS') ? _AD_BANNERSTATS_MESSAGE_CREATE_SUCCESS : 'Banner created successfully.') :
                   (defined('_AD_BANNERSTATS_MESSAGE_UPDATE_SUCCESS') ? _AD_BANNERSTATS_MESSAGE_UPDATE_SUCCESS : 'Banner updated successfully.');

        if (class_exists('XCube_DelegateUtils')) {
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddMessage', $message);
        } else {
            error_log("Bannerstats_BannerEditAction: Success message (DelegateUtils not available): " . $message);
        }

        $controller->executeForward('./index.php?action=BannerList');
    }

    /**
     * Handles errors during save or validation
     *
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     * @return void
     */
    public function executeViewError(&$controller, &$xoopsUser, &$render): void
    {
        if ($this->mActionForm && $this->mActionForm->hasError()) {
            $this->executeViewInput($controller, $xoopsUser, $render);
        } else {
            $errorMessage = defined('_AD_BANNERSTATS_ERROR_DBUPDATE_FAILED') ? _AD_BANNERSTATS_ERROR_DBUPDATE_FAILED : 'Database update failed.';
            if (class_exists('XCube_DelegateUtils')) {
                XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', $errorMessage);
            } else {
                error_log("Bannerstats_BannerEditAction: DB Update Failed (DelegateUtils not available): " . $errorMessage);
            }
            $controller->executeRedirect('./index.php?action=BannerList', 1, $errorMessage);
        }
    }

    /**
     * Handles cancellation
     *
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     * @return void
     */
    public function executeViewCancel(&$controller, &$xoopsUser, &$render): void
    {
        if (class_exists('XCube_DelegateUtils')) {
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddMessage', defined('_AD_BANNERSTATS_MESSAGE_CANCELLED') ? _AD_BANNERSTATS_MESSAGE_CANCELLED : 'Operation cancelled.');
        } else {
             error_log("Bannerstats_BannerEditAction: Cancel message (DelegateUtils not available).");
        }
        $controller->executeForward('./index.php?action=BannerList');
    }
}
