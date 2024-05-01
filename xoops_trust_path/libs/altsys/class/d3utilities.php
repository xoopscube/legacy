<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Class d3utilities
 * @package    Altsys
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class d3utilities
{

    public $dirname = '' ; // directory name under xoops_trust_path
    public $mydirname = '' ; // each directory name under xoops_root_path
    public $mid = 0 ; // id of each module instance
    public $table = '' ; // table with prefix and dirname
    public $primary_key = '' ; // column for primary_key
    public $cols = []; // settings of each columns
    public $form_mode = 'new' ; // 'new','edit' are available
    public $page_name = '' ; // controller's name  eg) page=(controller) in URI
    public $action_base_hiddens = [];


    /**
     * d3utilities constructor.
     * @param $mydirname
     * @param $table_body
     * @param $primary_key
     * @param $cols
     * @param $page_name
     * @param $action_base_hiddens
     */
    public function __construct($mydirname, $table_body, $primary_key, $cols, $page_name, $action_base_hiddens)
    {
        $db =& XoopsDatabaseFactory::getDatabaseConnection();

        $this->dirname = \basename(\dirname(__DIR__));

        $this->mydirname = $mydirname;

        $this->table = $db->prefix($mydirname ? $mydirname . '_' . $table_body : $table_body);

        $this->primary_key = $primary_key;

        $this->cols = $cols;

        $module_handler =& xoops_gethandler('module');

        $module =& $module_handler->getByDirname($this->mydirname);

        if (!empty($module)) {
            $this->mid = (int)$module->getVar('mid');
        }

        $this->page_name = $page_name;

        $this->action_base_hiddens = $action_base_hiddens;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get_language_constant($name)
    {
        return constant(mb_strtoupper('_MD_A_' . $this->dirname . '_' . $this->page_name . '_' . $name));
    }

    /**
     * @param $value
     * @param $col
     * @return string
     */
    public function get_set4sql($value, $col)
    {
        switch ($col['type']) {
            case 'text':
            case 'blob':
                $length = empty($col['length']) ? 65535 : (int)$col['length'];
                return "`{$col['name']}`='".addslashes(xoops_substr($value, 0, $length))."'" ;
            case 'char' :
            case 'varchar' :
            case 'string' :
                $length = empty($col['length']) ? 191 : (int)$col['length'];
                return "`{$col['name']}`='".addslashes(xoops_substr($value, 0, $length))."'" ;
            case 'int' :
            case 'integer' :
                $value = (int)$value;
                if (!empty($col['max'])) {
                    $value = min($value, (int)$col['max']);
                }
                if (! empty($col['min'])) {
                    $value = max($value, (int)$col['min']);
                }
                return "`{$col['name']}`=$value";
        }
    }

    /**
     * Single update or insert
     * @return array
     */
    public function insert()
    {
        $db =& XoopsDatabaseFactory::getDatabaseConnection();

        $id = $this->init_default_values();

        $set4sql = '';

        foreach ($this->cols as $col) {
            if (empty($col['edit_edit'])) {
                continue;
            }
            if ($col['name'] == $this->primary_key) {
                continue;
            }
            $set4sql .= $this->get_set4sql(@$_POST[ $col['name'] ], $col) . ',' ;
        }
        if (!empty($set4sql)) {
            if ($id > 0) {
                // UPDATE
                $db->queryF("UPDATE $this->table SET ".substr($set4sql, 0, -1)." WHERE $this->primary_key='".addslashes($id)."'");
                return [$id, 'update'];
            } else {
                // INSERT
                $db->queryF("INSERT INTO $this->table SET ".substr($set4sql, 0, -1));
                return [$db->getInsertId(), 'insert'];
            }
        }
    }

    /**
     * Multiple update
     * @return array
     */
    public function update()
    {
        $db =& XoopsDatabaseFactory::getDatabaseConnection();

        // search appropriate column for getting primary_key
        foreach ($this->cols as $col) {
            if (in_array(@$col['list_edit'], ['text', 'textarea', 'hidden'], true)) {
                $column4key = $col['name'];

                break;
            }
        }
        if (empty($column4key)) {
            $column4key = $this->cols[0]['name'];
        }

        $ret = [];
        foreach (array_keys($_POST[$column4key]) as $id) {
            $id = (int)$id;    // primary_key should be 'integer'

            $set4sql = '';

            foreach ($this->cols as $col) {
                if (empty($col['list_edit'])) {
                    continue;
                }
                if ($col['name'] == $this->primary_key) {
                    continue;
                }
                $set4sql .= $this->get_set4sql(@$_POST[ $col['name'] ][$id], $col) . ',' ;
            }
            if (!empty($set4sql)) {
                $result = $db->query("SELECT * FROM $this->table WHERE $this->primary_key=$id");
                if (1 == $db->getRowsNum($result)) {
                    $db->queryF("UPDATE $this->table SET " . mb_substr($set4sql, 0, -1) . " WHERE $this->primary_key=$id");

                    if (1 == $db->getAffectedRows()) {
                        $ret[$id] = $db->fetchArray($result);
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * @param bool $delete_comments
     * @param bool $delete_notifications
     * @return array
     */
    public function delete($delete_comments = false, $delete_notifications = false)
    {
        $db =& XoopsDatabaseFactory::getDatabaseConnection();

        $ret = [];
        foreach (array_keys($_POST['admin_main_checkboxes']) as $id) {
            $id = (int)$id;    // primary_key should be 'integer'

            $result = $db->query("SELECT * FROM $this->table WHERE $this->primary_key=$id");

            if (1 == $db->getRowsNum($result)) {
                $ret[$id] = $db->fetchArray($result);

                $db->queryF("DELETE FROM $this->table WHERE $this->primary_key=$id");

                $db->queryF("DELETE FROM $this->table WHERE $this->primary_key=$id") ;
                if ($delete_comments) {
                    // remove comments

                    $db->queryF('DELETE FROM ' . $db->prefix('xoopscomments') . " WHERE com_modid=$this->mid AND com_itemid=$id");
                }

                if ($delete_notifications) {
                    // remove notifications

                    $db->queryF('DELETE FROM ' . $db->prefix('xoopsnotifications') . " WHERE not_modid=$this->mid AND not_itemid=$id");
                }
            }
        }

        return $ret;
    }

    /**
     * @return int
     */
    public function init_default_values(): int
    {
        $db =& XoopsDatabaseFactory::getDatabaseConnection();

        if (@$_GET['id']) {
            $id = (int)$_GET['id'];
            $rs = $db->query("SELECT * FROM $this->table WHERE $this->primary_key=$id");
            if (1 == $db->getRowsNum($rs)) {
                $row = $db->fetchArray($rs);
                foreach (array_keys($this->cols) as $key) {
                    if (empty($this->cols[$key]['edit_show'])) {
                        continue;
                    }
                    $this->cols[$key]['default_value'] = $row[ $this->cols[$key]['name'] ];
                }

                $this->form_mode = 'edit';

                return $id;
            }
        }

        $this->form_mode = 'new';

        return 0;
    }

    /**
     * @return array
     */
    public function get_view_edit()
    {
        $id = $this->init_default_values();

        $lines = [];

        foreach ($this->cols as $col) {
            if (empty($col['edit_show'])) {
                continue;
            }

            if (!isset($col['default_value'])) {
                switch ($col['type']) {
                    case 'int':
                    case 'integer':
                        $col['default_value'] = 0;
                        break;
                    default:
                        $col['default_value'] = '';
                        break;
                }
            }
            switch ($col['edit_edit']) {
                case 'checkbox':
                    $checked = empty($col['default_value']) ? '' : "checked='checked'";
                    $value = empty($col['checkbox_value']) ? 1 : htmlspecialchars($col['checkbox_value'], ENT_QUOTES);

                    $lines[$col['name']] = "<input type='checkbox' name='{$col['name']}' value='$value' $checked>";
                    break;
                case 'text':
                default:
                    $size = empty($col['edit_size']) ? 32 : (int)$col['edit_size'];
                    $length = empty($col['length']) ? 191 : (int)$col['length'];
                    $lines[ $col['name'] ] = "<input type='text' name='{$col['name']}' size='$size' maxlength='$length' value='" . htmlspecialchars($col['default_value'], ENT_QUOTES) . "'>" ;
                    break;
                case false:
                    $lines[ $col['name'] ] = htmlspecialchars($col['default_value'], ENT_QUOTES);
                    break;
            }
        }

        return [$id, $lines];
    }

    /**
     * @param $controllers
     * @return string
     */
    public function get_control_form($controllers)
    {
        $hiddens = '';

        foreach ($this->action_base_hiddens as $key => $val) {
            $key4disp = htmlspecialchars($key, ENT_QUOTES);

            $val4disp = htmlspecialchars($val, ENT_QUOTES);

            $hiddens .= "<input type='hidden' name='$key4disp' value='$val4disp'>\n";
        }

        $controllers_html = '';

        foreach ($controllers as $type => $body) {
            if ('num' == $type) {
                $controllers_html .= $this->get_select('num', $body, $GLOBALS['num']);
            }
        }

        return "
            <form action='' method='get' name='admin_control' id='admin_control'>
                $hiddens
                $controllers_html
                <input type='submit' value='" . _SUBMIT . "' class='button'>
            </form>\n";
    }

    /**
     * @param $name
     * @param $options
     * @param $current_value
     * @return string
     */
    public function get_select($name, $options, $current_value)
    {
        $ret = "<select name='" . htmlspecialchars($name, ENT_QUOTES) . "'>\n";

        foreach ($options as $key => $val) {
            $selected = $val == $current_value ? "selected='selected'" : '';

            $ret .= "<option value='" . htmlspecialchars($key, ENT_QUOTES) . "' $selected>" . htmlspecialchars($val, ENT_QUOTES) . "</option>\n";
        }

        $ret .= "</select>\n";

        return $ret;
    }
}
