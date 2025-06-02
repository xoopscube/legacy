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
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerAdminReactivateForm.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/BannerFinish.class.php';

class Bannerstats_BannerReactivateAction extends Bannerstats_AbstractEditAction
{
    /**
     * Gets the ID of the finished banner to be reactivated.
     * This ID is used by _setupObject() to load the BannerfinishObject
     * @return int|null
     */
    public function _getId()
    {
        return xoops_getrequest('bid') ? (int)xoops_getrequest('bid') : null;
    }

    /**
     * Gets the handler for BannerfinishObject
     * @return Bannerstats_BannerfinishHandler|false
     */
    public function &_getHandler()
    {
        $handler = xoops_getmodulehandler('bannerfinish', 'bannerstats');
        return $handler;
    }

    /**
     * Sets up the action form
     * @return void
     */
    public function _setupActionForm(): void
    {
        $this->mActionForm = new Bannerstats_BannerAdminReactivateForm();
        $this->mActionForm->prepare();
    }

    /**
     * Helper function to parse date strings using DateTime::createFromFormat
     *
     * @param string $dateTimeStr date/time string to parse
     * @return DateTime|false DateTime object if valid
     */
    protected function _parseDateTimeString(string $dateTimeStr): DateTime|false
    {
        //error_log("BannerReactivateAction - Attempting to parse date string: '" . $dateTimeStr . "'");
        $formatsToTry = [
            'Y-m-d\TH:i',    // Standard datetime-local output
            'Y-m-d H:i:s',  // Full datetime with seconds
            'Y-m-d',       // Date only
        ];

        foreach ($formatsToTry as $format) {
            $dateTimeObject = DateTime::createFromFormat($format, $dateTimeStr);
            if ($dateTimeObject !== false) {
                $errors = DateTime::getLastErrors();
                if ($errors === false || (is_array($errors) && $errors['warning_count'] === 0 && $errors['error_count'] === 0)) {
                    //error_log("BannerReactivateAction - Successfully parsed '" . $dateTimeStr . "' with format '" . $format . "'");
                    return $dateTimeObject;
                } else {
                    //error_log("BannerReactivateAction - Parsed '" . $dateTimeStr . "' with format '" . $format . "' but found errors/warnings: " . print_r($errors, true));
                }
            }
        }
        //error_log("BannerReactivateAction - Failed to parse '" . $dateTimeStr . "' with any known format.");
        return false;
    }

    /**
     * Performs the actual execution of reactivating the banner
     * creating a new active banner and deleting the finished one.
     * This method is called by AbstractEditAction::execute() after form validation.
     *
     * @return bool True on success
     */
    public function _doExecute(): bool
    {
        if (!is_object($this->mObject) || !($this->mObject instanceof Bannerstats_BannerfinishObject)) {
            $this->mActionForm->addErrorMessage(_AD_BANNERSTATS_ERROR_LOAD_OBJECT_FAILED);
            return false;
        }
        $finishedBanner = $this->mObject;

        $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
        if (!$bannerHandler) {
            $this->mActionForm->addErrorMessage(_AD_BANNERSTATS_ERROR_HANDLER_NOT_FOUND);
            //error_log("BannerReactivateAction: Failed to get 'banner' handler.");
            return false;
        }
        /** @var Bannerstats_BannerObject $activeBanner */
        $activeBanner = $bannerHandler->create();
        if (!is_object($activeBanner)) {
            $this->mActionForm->addErrorMessage(_AD_BANNERSTATS_ERROR_CREATE_FAILED);
            //error_log("BannerReactivateAction: Failed to create new BannerObject.");
            return false;
        }

        $this->mActionForm->update($activeBanner);

        $startDateStrFromForm = trim((string)$activeBanner->get('start_date'));
        $startTimestamp = 0;
        if (!empty($startDateStrFromForm)) {
            $dateTimeObject = $this->_parseDateTimeString($startDateStrFromForm);
            if ($dateTimeObject instanceof DateTime) {
                $startTimestamp = $dateTimeObject->getTimestamp();
            } else {
                $this->mActionForm->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_DATETIME, _AD_BANNERSTATS_START_DATE, 'YYYY-MM-DDTHH:MM'));
            }
        }
        $activeBanner->set('start_date', $startTimestamp);

        $endDateStrFromForm = trim((string)$activeBanner->get('end_date'));
        $endTimestamp = 0;
        if (!empty($endDateStrFromForm)) {
            $dateTimeObject = $this->_parseDateTimeString($endDateStrFromForm);
            if ($dateTimeObject instanceof DateTime) {
                $endTimestamp = $dateTimeObject->getTimestamp();
            } else {
                $this->mActionForm->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_DATETIME, _AD_BANNERSTATS_DATE_END, 'YYYY-MM-DDTHH:MM'));
            }
        }
        $activeBanner->set('end_date', $endTimestamp);

        if (!$bannerHandler->insert($activeBanner, true)) {
            $errors = $activeBanner->getErrors();
            $errorMsg = _AD_BANNERSTATS_ERROR_DBUPDATE_FAILED;
            if (!empty($errors)) {
                $errorMsg .= ': ' . implode(', ', array_map('htmlspecialchars', $errors));
            }
            $this->mActionForm->addErrorMessage($errorMsg);
            return false;
        }

        return true;
    }

    /**
     * Prepares and sets data for the reactivation form view
     *
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     * @return void
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render): void
    {
        $render->setTemplateName('banner_reactivate.html');
        $render->setAttribute('actionForm', $this->mActionForm);

        if (is_object($this->mObject) && $this->mObject instanceof Bannerstats_BannerfinishObject) {
            $this->mObject->loadBannerclient();
            $this->mActionForm->load($this->mObject);
            if (is_object($this->mObject->mClient)) {
                $render->setAttribute('currentClient', $this->mObject->mClient);
            }
        } else {
            $controller->executeRedirect('./index.php?action=BannerfinishList', 1, _AD_BANNERSTATS_ERROR_LOAD_OBJECT_FAILED);
            return;
        }
        $render->setAttribute('object', $this->mObject);

        $bannerclientHandler = xoops_getmodulehandler('bannerclient', 'bannerstats');
        $bannerclientArr = [];
        if ($bannerclientHandler) {
            $clients = $bannerclientHandler->getObjects();
            $bannerclientArr = $clients;
        }
        $render->setAttribute('bannerclientArr', $bannerclientArr);

        $bannerTypes = [
            'image' => defined('_AD_BANNERSTATS_BTYPE_IMAGE') ? _AD_BANNERSTATS_BTYPE_IMAGE : 'Image',
            'html' => defined('_AD_BANNERSTATS_BTYPE_HTML') ? _AD_BANNERSTATS_BTYPE_HTML : 'Custom HTML',
            'ad_tag' => defined('_AD_BANNERSTATS_BTYPE_ADTAG') ? _AD_BANNERSTATS_BTYPE_ADTAG : 'Third-Party Ad Tag',
            'video' => defined('_AD_BANNERSTATS_BTYPE_VIDEO') ? _AD_BANNERSTATS_BTYPE_VIDEO : 'Video (Embed/VAST)',
        ];
        $render->setAttribute('bannerTypes', $bannerTypes);

        $statusOptions = [
            0 => defined('_AD_BANNERSTATS_STATUS_INACTIVE') ? _AD_BANNERSTATS_STATUS_INACTIVE : 'Inactive',
            1 => defined('_AD_BANNERSTATS_STATUS_ACTIVE') ? _AD_BANNERSTATS_STATUS_ACTIVE : 'Active',
        ];
        $render->setAttribute('statusOptions', $statusOptions);
    }

    /**
     * Handles successful reactivation
     *
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     * @return void
     */
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render): void
    {
        $controller->executeRedirect('./index.php?action=BannerList', 1, _AD_BANNERSTATS_REACTIVATE_SUCCESS);
    }

    /**
     * Handles errors during reactivation
     *
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     * @return void
     */
    public function executeViewError(&$controller, &$xoopsUser, &$render): void
    {
        if ($this->mActionForm->hasError()) {
            $this->executeViewInput($controller, $xoopsUser, $render);
        } else {
            $controller->executeRedirect('./index.php?action=BannerfinishList', 1, _AD_BANNERSTATS_ERROR_ACTION_FAILED);
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
        $controller->executeForward('./index.php?action=BannerfinishList');
    }
}
