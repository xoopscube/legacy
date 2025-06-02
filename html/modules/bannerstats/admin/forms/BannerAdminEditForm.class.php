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

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/class/Legacy_Validator.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/Banner.class.php';


class Bannerstats_BannerAdminEditForm extends XCube_ActionForm
{
    protected array $allowedBannerTypes = ['image', 'html', 'ad_tag', 'video'];

    public function getTokenName(): string
    {
        $bid = $this->get('bid');
        return 'module.bannerstats.BannerAdminEditForm.TOKEN' . ($bid ? '.' . $bid : '.NEW');
    }

    public function prepare()
    {
        // Set form properties for all editable banner fields
        $this->mFormProperties['bid'] = new XCube_IntProperty('bid');
        $this->mFormProperties['cid'] = new XCube_IntProperty('cid');
        $this->mFormProperties['campaign_id'] = new XCube_IntProperty('campaign_id');
        $this->mFormProperties['name'] = new XCube_StringProperty('name');
        $this->mFormProperties['banner_type'] = new XCube_StringProperty('banner_type');
        $this->mFormProperties['imptotal'] = new XCube_IntProperty('imptotal');
        $this->mFormProperties['imageurl'] = new XCube_StringProperty('imageurl');
        $this->mFormProperties['clickurl'] = new XCube_StringProperty('clickurl');
        $this->mFormProperties['htmlcode'] = new XCube_TextProperty('htmlcode');
        $this->mFormProperties['width'] = new XCube_IntProperty('width');
        $this->mFormProperties['height'] = new XCube_IntProperty('height');
        $this->mFormProperties['start_date'] = new XCube_StringProperty('start_date');
        $this->mFormProperties['end_date'] = new XCube_StringProperty('end_date');
        $this->mFormProperties['timezone'] = new XCube_StringProperty('timezone');
        $this->mFormProperties['status'] = new XCube_IntProperty('status');
        $this->mFormProperties['weight'] = new XCube_IntProperty('weight');

        // Set field properties (validations)
        $this->mFieldProperties['bid'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cid'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cid']->setDependsByArray(['required', 'objectExist']);
        $this->mFieldProperties['cid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_CID);
        $this->mFieldProperties['cid']->addMessage('objectExist', _AD_BANNERSTATS_ERROR_OBJECT_EXIST, _AD_BANNERSTATS_CID);
        $this->mFieldProperties['cid']->addVar('handler', 'bannerclient');
        $this->mFieldProperties['cid']->addVar('module', 'bannerstats');

        $this->mFieldProperties['name'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['name']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['name']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_BANNER_NAME);
        $this->mFieldProperties['name']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_BANNER_NAME, '255');
        $this->mFieldProperties['name']->addVar('maxlength', '255');

        $this->mFieldProperties['banner_type'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['banner_type']->setDependsByArray(['required']);
        $this->mFieldProperties['banner_type']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_BANNER_TYPE);

        $this->mFieldProperties['imptotal'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['imptotal']->setDependsByArray(['required', 'min']);
        $this->mFieldProperties['imptotal']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_IMPTOTAL);
        $this->mFieldProperties['imptotal']->addMessage('min', _AD_BANNERSTATS_ERROR_MIN, _AD_BANNERSTATS_IMPTOTAL, '0');
        $this->mFieldProperties['imptotal']->addVar('min', '0');

        $this->mFieldProperties['imageurl'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['imageurl']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['imageurl']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_IMAGEURL, '255');
        $this->mFieldProperties['imageurl']->addVar('maxlength', '255');

        $this->mFieldProperties['clickurl'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['clickurl']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['clickurl']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_CLICKURL, '255');
        $this->mFieldProperties['clickurl']->addVar('maxlength', '255');

        $this->mFieldProperties['htmlcode'] = new XCube_FieldProperty($this);

        $this->mFieldProperties['width'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['width']->setDependsByArray(['min']);
        $this->mFieldProperties['width']->addMessage('min', _AD_BANNERSTATS_ERROR_MIN, _AD_BANNERSTATS_WIDTH, '0');
        $this->mFieldProperties['width']->addVar('min', '0');

        $this->mFieldProperties['height'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['height']->setDependsByArray(['min']);
        $this->mFieldProperties['height']->addMessage('min', _AD_BANNERSTATS_ERROR_MIN, _AD_BANNERSTATS_HEIGHT, '0');
        $this->mFieldProperties['height']->addVar('min', '0');

        $this->mFieldProperties['start_date'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['end_date'] = new XCube_FieldProperty($this);

        $this->mFieldProperties['timezone'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['timezone']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['timezone']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_TIMEZONE, '50');
        $this->mFieldProperties['timezone']->addVar('maxlength', '50');

        $this->mFieldProperties['status'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['status']->setDependsByArray(['intRange']);
        $this->mFieldProperties['status']->addMessage('intRange', _AD_BANNERSTATS_ERROR_STATUS, _AD_BANNERSTATS_STATUS);
        $this->mFieldProperties['status']->addVar('min', 0);
        $this->mFieldProperties['status']->addVar('max', 1);

        $this->mFieldProperties['weight'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['weight']->setDependsByArray(['required', 'min']);
        $this->mFieldProperties['weight']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_WEIGHT);
        $this->mFieldProperties['weight']->addMessage('min', _AD_BANNERSTATS_ERROR_MIN, _AD_BANNERSTATS_WEIGHT, '0');
        $this->mFieldProperties['weight']->addVar('min', '0');
    }

    public function validate()
    {
        parent::validate();

        $bannerType = $this->get('banner_type');
        if (!in_array($bannerType, $this->allowedBannerTypes, true)) {
            $this->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_BANNER_TYPE, _AD_BANNERSTATS_BANNER_TYPE));
        }

        if ($bannerType === 'image') {
            if (strlen((string)$this->get('imageurl')) === 0) {
                $this->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_IMAGEURL));
            }
            $clickUrl = $this->get('clickurl');
            if (!empty($clickUrl) && filter_var($clickUrl, FILTER_VALIDATE_URL) === false) {
                $this->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_URL, _AD_BANNERSTATS_CLICKURL));
            }

        } elseif (in_array($bannerType, ['html', 'ad_tag', 'video'], true)) {
            if (strlen((string)$this->get('htmlcode')) === 0) {
                $this->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_HTMLCODE));
            }
        }

        $startDateStr = trim((string)$this->get('start_date'));
        $endDateStr = trim((string)$this->get('end_date'));
        
        $startDateObject = null;
        $endDateObject = null;

        if (!empty($startDateStr)) {
            $startDateObject = $this->parseValidDateTimeFormat($startDateStr);
            if ($startDateObject === false) {
                $this->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_DATETIME, _AD_BANNERSTATS_START_DATE, 'YYYY-MM-DDTHH:MM or YYYY-MM-DD HH:MM:SS'));
            }
        }

        if (!empty($endDateStr)) {
            $endDateObject = $this->parseValidDateTimeFormat($endDateStr);
            if ($endDateObject === false) {
                $this->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_DATETIME, _AD_BANNERSTATS_DATE_END, 'YYYY-MM-DDTHH:MM or YYYY-MM-DD HH:MM:SS'));
            }
        }

        if ($startDateObject instanceof DateTime && $endDateObject instanceof DateTime) {
            if ($endDateObject < $startDateObject) {
                $this->addErrorMessage(_AD_BANNERSTATS_ERROR_DATE_ORDER);
            }
        }

        $status = $this->get('status');
        if ($status !== 0 && $status !== 1) {
             if (is_numeric($status) && ((int)$status === 0 || (int)$status === 1)) {
                // form property will cast to int
             } else {
                $this->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_STATUS, _AD_BANNERSTATS_STATUS));
             }
        }
    }

    /**
     * Parses the given string against known valid date/datetime formats.
     * Primarily expects 'Y-m-d\TH:i' from datetime-local inputs.
     * Returns a DateTime object on successful parse, false otherwise.
     *
     * @param string $dateTimeStr date/time string to validate
     * @param string|null &$parsedFormat Optional output parameter
     * @return DateTime|false DateTime object if valid
     */
    protected function parseValidDateTimeFormat(string $dateTimeStr, ?string &$parsedFormat = null): DateTime|false
    {
        //error_log("BannerAdminEditForm - Attempting to parse date string: '" . $dateTimeStr . "'");

        $primaryFormat = 'Y-m-d\TH:i';
        $dateTimeObject = DateTime::createFromFormat($primaryFormat, $dateTimeStr);
        
        if ($dateTimeObject !== false) {
            $errors = DateTime::getLastErrors();
            if ($errors === false || (is_array($errors) && $errors['warning_count'] === 0 && $errors['error_count'] === 0)) {
                $parsedFormat = 'datetime-local';
                return $dateTimeObject;
            }
        }

        // If the primary format failed, try other fallback formats
        $fallbackFormats = [
            'db-datetime'    => 'Y-m-d H:i:s', // Full datetime with seconds
            'db-date'        => 'Y-m-d',      // Date only
        ];

        foreach ($fallbackFormats as $key => $format) {
            $dateTimeObject = DateTime::createFromFormat($format, $dateTimeStr);
            if ($dateTimeObject !== false) {
                $errors = DateTime::getLastErrors();
                if ($errors === false || (is_array($errors) && $errors['warning_count'] === 0 && $errors['error_count'] === 0)) {
                    $parsedFormat = $key;
                    //error_log("BannerAdminEditForm - Successfully parsed '" . $dateTimeStr . "' with fallback format '" . $format . "'");
                    return $dateTimeObject;
                } else {
                     //error_log("BannerAdminEditForm - Parsed '" . $dateTimeStr . "' with fallback format '" . $format . "' but found errors/warnings: " . print_r($errors, true));
                }
            }
        }
        
        // If none of the formats matched successfully
        //error_log("BannerAdminEditForm - Failed to parse '" . $dateTimeStr . "' with any known format.");
        $parsedFormat = null;
        return false;
    }

    public function load(&$obj)
    {
        if (!($obj instanceof Bannerstats_BannerObject)) {
            return;
        }

        $this->set('bid', $obj->get('bid'));
        $this->set('cid', $obj->get('cid'));
        $this->set('campaign_id', $obj->get('campaign_id'));
        $this->set('name', $obj->get('name', 'n'));
        $this->set('banner_type', $obj->get('banner_type'));
        $this->set('imptotal', $obj->get('imptotal'));
        $this->set('imageurl', $obj->get('imageurl', 'n'));
        $this->set('clickurl', $obj->get('clickurl', 'n'));
        $this->set('htmlcode', $obj->get('htmlcode', 'n'));
        $this->set('width', $obj->get('width'));
        $this->set('height', $obj->get('height'));

        $startDateTs = $obj->get('start_date');
        $this->set('start_date', ($startDateTs > 0) ? date('Y-m-d\TH:i', $startDateTs) : '');

        $endDateTs = $obj->get('end_date');
        $this->set('end_date', ($endDateTs > 0) ? date('Y-m-d\TH:i', $endDateTs) : '');

        $this->set('timezone', $obj->get('timezone', 'n'));
        $this->set('status', $obj->get('status'));
        $this->set('weight', $obj->get('weight'));
    }

    public function update(&$obj)
    {
        if (!($obj instanceof Bannerstats_BannerObject)) {
            return;
        }

        $obj->set('cid', $this->get('cid'));
        $obj->set('campaign_id', $this->get('campaign_id'));
        $obj->set('name', $this->get('name'));
        $obj->set('banner_type', $this->get('banner_type'));
        $obj->set('imptotal', $this->get('imptotal'));

        $bannerType = $this->get('banner_type');
        if ($bannerType === 'image') {
            $obj->set('imageurl', $this->get('imageurl'));
            $obj->set('clickurl', $this->get('clickurl'));
            $obj->set('htmlcode', '');
        } elseif (in_array($bannerType, $this->allowedBannerTypes, true)) {
            $obj->set('htmlcode', $this->get('htmlcode'));
            $obj->set('imageurl', '');
            $obj->set('clickurl', '');
        }

        $obj->set('width', $this->get('width'));
        $obj->set('height', $this->get('height'));

        $startDateStr = $this->get('start_date');
        $obj->set('start_date', !empty($startDateStr) ? $startDateStr : null);

        $endDateStr = $this->get('end_date');
        $obj->set('end_date', !empty($endDateStr) ? $endDateStr : null);

        $obj->set('timezone', $this->get('timezone'));
        $obj->set('status', $this->get('status'));
        $obj->set('weight', $this->get('weight'));
    }
}
