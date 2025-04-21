<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/PicoControllerAbstract.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/gtickets.php';

class PicoControllerEditCategory extends PicoControllerAbstract {
    protected array $categoryObjs = [];

    // get samples of category options
    public function getCategoryOptions4edit(): string {
        include dirname( __DIR__ ) . '/include/configs_can_override.inc.php';

        $lines = [];
        foreach ( $pico_configs_can_be_override as $key => $type ) {
            if ( isset( $this->mod_config[ $key ] ) ) {
                $val = $this->mod_config[ $key ];
                if ( 'int' === $type || 'bool' === $type ) {
                    $val = (int) $val;
                }
                $lines[] = htmlspecialchars( $key . ':' . $val, ENT_QUOTES );
            }
        }

        return implode( '<br>', $lines );
    }
    
    // New method to prepare structured config options for the UI
    public function prepareConfigOptions() {
        include dirname( __DIR__ ) . '/include/configs_can_override.inc.php';
        
        $config_options = [];
        $current_options = [];
        
        // Parse current options from the category
        if (!empty($this->assign['category']['options'])) {
            $lines = explode("\n", $this->assign['category']['options']);
            foreach ($lines as $line) {
                if (preg_match('/^([^:]+):(.*)$/', $line, $matches)) {
                    $current_options[trim($matches[1])] = trim($matches[2]);
                }
            }
        }
        
        // Prepare structured options
        foreach ($pico_configs_can_be_override as $key => $type) {
            if (isset($this->mod_config[$key])) {
                $default_val = $this->mod_config[$key];
                if ('int' === $type || 'bool' === $type) {
                    $default_val = (int) $default_val;
                }
                
                // Use current value if set, otherwise use default
                $value = isset($current_options[$key]) ? $current_options[$key] : $default_val;
                
                $config_options[$key] = [
                    'type' => $type,
                    'value' => $value,
                    'default' => $default_val
                ];
            }
        }
        
        $this->assign['category']['config_options'] = $config_options;
    }
    
    public function execute( $request ) {
        parent::execute( $request );

        // makecategory/categorymanager
        $page = empty( $request['makecategory'] ) ? 'categorymanager' : 'makecategory';

        // $categoryObj (not parent)
        $picoPermission = &PicoPermission::getInstance();
        $categoryObj    = new PicoCategory( $this->mydirname, $request['cat_id'], $picoPermission->getPermissions( $this->mydirname ), 'makecategory' === $page, $this->currentCategoryObj );

        // check existence
        if ( $categoryObj->isError() ) {
            redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 1, _MD_PICO_ERR_READCONTENT );
            exit;
        }

        // fetch data from DB
        $cat_data                            = $categoryObj->getData();
        $this->assign['category_base']       = $categoryObj->getData4html();
        $this->categoryObjs['category_base'] = &$categoryObj;
        $this->assign['category']            = $categoryObj->getData4edit();

        // permission check
        if ( 'makecategory' === $page ) {
            $pcat_data = $this->currentCategoryObj->getData();
            if ( empty( $pcat_data['can_makesubcategory'] ) ) {
                redirect_header( XOOPS_URL . '/', 1, _MD_PICO_ERR_CREATECATEGORY );
            }
        } else {
            if ( empty( $cat_data['isadminormod'] ) ) {
                redirect_header( XOOPS_URL . '/', 1, _MD_PICO_ERR_CATEGORYMANAGEMENT );
            }
        }

        // category list can be read for category jumpbox etc.
        $categoryHandler                     = new PicoCategoryHandler( $this->mydirname, $this->permissions );
        $categories                          = $categoryHandler->getAllCategories();
        $this->assign['categories_can_post'] = [];
        foreach ( $categories as $tmpObj ) {
            $tmp_data = $tmpObj->getData();
            if ( empty( $tmp_data['can_makesubcategory'] ) ) {
                continue;
            }
            $this->assign['categories_can_makesubcategory'][ $tmp_data['id'] ] = str_repeat( '--', $tmp_data['cat_depth_in_tree'] ) . $tmp_data['cat_title'];
        }

        // breadcrumbs
        $breadcrumbsObj = AltsysBreadcrumbs::getInstance();
        if ( 'makecategory' === $page ) {
            $breadcrumbsObj->appendPath( '', _MD_PICO_LINK_MAKECATEGORY );
            $this->assign['xoops_pagetitle'] = _MD_PICO_LINK_MAKECATEGORY;
        } else {
            //		$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/'.$this->mydirname.'/'.$this->assign['category']['link'] , $this->assign['category']['title'] ) ;
            $breadcrumbsObj->appendPath( '', _MD_PICO_CATEGORYMANAGER );
            $this->assign['xoops_pagetitle'] = _MD_PICO_CATEGORYMANAGER;
        }
        $this->assign['xoops_breadcrumbs'] = $breadcrumbsObj->getXoopsbreadcrumbs();

        // misc assigns
        $this->assign['page']                          = $page;
        $this->assign['formtitle']                     = 'makecategory' === $page ? _MD_PICO_LINK_MAKECATEGORY : _MD_PICO_CATEGORYMANAGER;
        $this->assign['gticket_hidden']                = $GLOBALS['xoopsGTicket']->getTicketHtml( __LINE__, 1800, 'pico' );
        $this->assign['category']['option_desc']       = $this->getCategoryOptions4edit();
        $this->assign['category']['wraps_directories'] = [ '' => '---' ] + pico_main_get_wraps_directories_recursively( $this->mydirname, '/' );
        
        // Prepare structured config options for the UI
        $this->prepareConfigOptions();

        // views
        $this->template_name         = $this->mydirname . '_main_category_form.html';
        $this->is_need_header_footer = true;
    }
}
