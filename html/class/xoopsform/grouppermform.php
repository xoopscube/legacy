<?php
/**
 * Renders a form for setting module specific group permissions
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors nobunobu, 2007/11/27
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';


class XoopsGroupPermForm extends XoopsForm
{
    /**
     * Module ID
     *
     * @var int
     */
    public $_modid;
    /**
     * Tree structure of items
     *
     * @var array
     */
    public $_itemTree = [];
    /**
     * Name of permission
     *
     * @var string
     */
    public $_permName;
    /**
     * Description of permission
     *
     * @var string
     */
    public $_permDesc;

    /**
     * Constructor
     * @param        $title
     * @param        $modid
     * @param        $permname
     * @param        $permdesc
     * @param string $url
     */
    public function __construct($title, $modid, $permname, $permdesc, $url = '')
    {
        $this->XoopsForm($title, 'groupperm_form', XOOPS_URL . '/modules/legacy/include/groupperm.php', 'post');
        $this->_modid = (int)$modid;
        $this->_permName = $permname;
        $this->_permDesc = $permdesc;
        $this->addElement(new XoopsFormHidden('modid', $this->_modid));
        if ('' !== $url) {
            $this->addElement(new XoopsFormHidden('redirect_url', $url));
        }
    }

    /**
     * Adds an item to which permission will be assigned
     *
     * @param string $itemName
     * @param int $itemId
     * @param int $itemParent
     * @access public
     */
    public function addItem($itemId, $itemName, $itemParent = 0)
    {
        $this->_itemTree[$itemParent]['children'][] = $itemId;
        $this->_itemTree[$itemId]['parent'] = $itemParent;
        $this->_itemTree[$itemId]['name'] = $itemName;
        $this->_itemTree[$itemId]['id'] = $itemId;
    }

    /**
     * Loads all child ids for an item to be used in javascript
     *
     * @param int $itemId
     * @param array $childIds
     * @access private
     */
    public function _loadAllChildItemIds($itemId, &$childIds)
    {
        if (!empty($this->_itemTree[$itemId]['children'])) {
            $first_child = $this->_itemTree[$itemId]['children'];
            foreach ($first_child as $fcid) {
                $childIds[] = $fcid;
                if (!empty($this->_itemTree[$fcid]['children'])) {
                    foreach ($this->_itemTree[$fcid]['children'] as $_fcid) {
                        $childIds[] = $_fcid;
                        $this->_loadAllChildItemIds($_fcid, $childIds);
                    }
                }
            }
        }
    }

    /**
     * Renders the form
     *
     * @return string
     * @access public
     */
    public function render()
    {
        // load all child ids for javascript codes
        foreach (array_keys($this->_itemTree)as $item_id) {
            $this->_itemTree[$item_id]['allchild'] = [];
            $this->_loadAllChildItemIds($item_id, $this->_itemTree[$item_id]['allchild']);
        }
        $gperm_handler =& xoops_gethandler('groupperm');
        $member_handler =& xoops_gethandler('member');
        $glist =& $member_handler->getGroupList();
        foreach (array_keys($glist) as $i) {
            // get selected item id(s) for each group
            $selected = $gperm_handler->getItemIds($this->_permName, $i, $this->_modid);
            $ele = new XoopsGroupFormCheckBox($glist[$i], 'perms[' . $this->_permName . ']', $i, $selected);
            $ele->setOptionTree($this->_itemTree);
            $this->addElement($ele);
            unset($ele);
        }
        $tray = new XoopsFormElementTray('');
        $tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $tray->addElement(new XoopsFormButton('', 'reset', _CANCEL, 'reset'));
        $this->addElement($tray);

        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_grouppermform.html');

        $renderTarget->setAttribute('form', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}

/**
 * Renders checkbox options for a group permission form
 */
class XoopsGroupFormCheckBox extends XoopsFormElement
{
    /**
     * Pre-selected value(s)
     *
     * @var array;
     */
    public $_value = [];
    /**
     * Group ID
     *
     * @var int
     */
    public $_groupId;
    /**
     * Option tree
     *
     * @var array
     */
    public $_optionTree = [];

    /**
     * Constructor
     * @param      $caption
     * @param      $name
     * @param      $groupId
     * @param null $values
     */
    public function __construct($caption, $name, $groupId, $values = null)
    {
        $this->setCaption($caption);
        $this->setName($name);
        if (isset($values)) {
            $this->setValue($values);
        }
        $this->_groupId = $groupId;
    }

    /**
     * Sets pre-selected values
     *
     * @param mixed $value A group ID or an array of group IDs
     * @access public
     */
    public function setValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->setValue($v);
            }
        } else {
            $this->_value[] = $value;
        }
    }

    /**
     * Sets the tree structure of items
     *
     * @param array $optionTree
     * @access public
     */
    public function setOptionTree(&$optionTree)
    {
        $this->_optionTree =& $optionTree;
    }

    /**
     * Renders checkbox options for this group
     *
     * @return string
     * @access public
     */
    public function render()
    {
        $ret = '<table class="outer"><tr><td><table><tr>';
        $cols = 1;

        if ($this->_hasChildren()) {
            foreach ($this->_optionTree[0]['children'] as $topitem) {
                if ($cols > 4) {
                    $ret .= '</tr><tr>';
                    $cols = 1;
                }
                $tree = '<td>';
                $prefix = '';
                $this->_renderOptionTree($tree, $this->_optionTree[$topitem], $prefix);
                $ret .= $tree.'</td>';
                $cols++;
            }
        }
        $ret .= '</tr></table></td><td>';
        $option_ids = [];
        foreach (array_keys($this->_optionTree) as $id) {
            if (!empty($id)) {
                $option_ids[] = "'".$this->getName().'[groups]['.$this->_groupId.']['.$id.']'."'";
            }
        }
        $checkallbtn_id = $this->getName().'[checkallbtn]['.$this->_groupId.']';
        $checkallbtn_id = str_replace(['[', ']'], ['_', ''], $checkallbtn_id); // Remove injury characters for ID

        $option_ids_str = implode(', ', $option_ids);
        $option_ids_str = str_replace(['[', ']'], ['_', ''], $option_ids_str); // Remove injury characters for ID


        $ret .= _ALL . ' <input id="' . $checkallbtn_id . '" type="checkbox" value="" onclick="var optionids = new Array(' . $option_ids_str . "); xoopsCheckAllElements(optionids, '" . $checkallbtn_id . "');\" />";
        $ret .= '</td></tr></table>';
        return $ret;
    }

    /**
     * Renders checkbox options for an item tree
     *
     * @param string $tree
     * @param array $option
     * @param string $prefix
     * @param array $parentIds
     * @access private
     */
    public function _renderOptionTree(&$tree, $option, $prefix, $parentIds = [])
    {
        // Remove injury characters for ID
        $tree .= $prefix . '<input type="checkbox" name="' . $this->getName() . '[groups][' . $this->_groupId . '][' . $option['id'] . ']" id="' .
            str_replace(['[', ']'], ['_', ''], $this->getName() . '[groups][' . $this->_groupId . '][' . $option['id'] . ']') . '" onclick="';

        // If there are parent elements, add javascript that will
        // make them selecteded when this element is checked to make
        // sure permissions to parent items are added as well.
        foreach ($parentIds as $pid) {
            $parent_ele = $this->getName() . '[groups][' . $this->_groupId . '][' . $pid . ']';
            $parent_ele = str_replace(['[', ']'], ['_', ''], $parent_ele); // Remove injury characters for ID
            $tree .= "var ele = xoopsGetElementById('" . $parent_ele . "'); if(ele.checked != true) {ele.checked = this.checked;}";
        }
        // If there are child elements, add javascript that will
        // make them unchecked when this element is unchecked to make
        // sure permissions to child items are not added when there
        // is no permission to this item.
        foreach ($option['allchild'] as $cid) {
            $child_ele = $this->getName() . '[groups][' . $this->_groupId . '][' . $cid . ']';
            $child_ele = str_replace(['[', ']'], ['_', ''], $child_ele); // Remove injury characters for ID
            $tree .= "var ele = xoopsGetElementById('" . $child_ele . "'); if(this.checked != true) {ele.checked = false;}";
        }
        $tree .= '" value="1"';
        if (in_array($option['id'], $this->_value, true)) {
            $tree .= ' checked="checked"';
        }
        $tree .= '>'
            . $option['name'] . '<input type="hidden" name="'
            . $this->getName() . '[parents]['
            . $option['id'] . ']" value="'
            . implode(':', $parentIds) . '" /><input type="hidden" name="'
            . $this->getName() . '[itemname]['
            . $option['id'] . ']" value="'
            . htmlspecialchars($option['name']) . "\" /><br>\n";
        if (isset($option['children'])) {
            foreach ($option['children'] as $child) {
                $parentIds[] = $option['id'];
                $this->_renderOptionTree($tree, $this->_optionTree[$child], $prefix . '&nbsp;-', $parentIds);
            }
        }
    }

    /**
     * Gets a value indicating whether this object has children.
     * @return bool
     */
    public function _hasChildren()
    {
        return isset($this->_optionTree[0]) && is_array($this->_optionTree[0]['children']);
    }
}
