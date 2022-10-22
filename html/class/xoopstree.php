<?php
/**
 * Xoops Tree
 * @package    class
 * @subpackage core
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

class XoopsTree
{
    public $table;   //table with parent-child structure
    public $id;      //name of unique id for records in table $table
    public $pid;     // name of parent id used in table $table
    public $order;   //specifies the order of query results
    public $title;   // name of a field in table $table which will be used when  selection box and paths are generated
    public $db;

    //constructor of class XoopsTree
    //sets the names of table, unique id, and parend id
    public function __construct($table_name, $id_name, $pid_name)
    {
        $this->db =& Database::getInstance();
        $this->table = $table_name;
        $this->id = $id_name;
        $this->pid = $pid_name;
    }
    public function XoopsTree($table_name, $id_name, $pid_name)
    {
        return $this->__construct($table_name, $id_name, $pid_name);
    }


    // returns an array of first child objects for a given id($sel_id)
    public function getFirstChild($sel_id, $order= '')
    {
        $arr = [];
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id . '';
        if ('' !== $order) {
            $sql .= " ORDER BY $order";
        }
        $result = $this->db->query($sql);
        $count = $this->db->getRowsNum($result);
        if (0 == $count) {
            return $arr;
        }
        while ($myrow=$this->db->fetchArray($result)) {
            $arr[] = $myrow;
        }
        return $arr;
    }

    // returns an array of all FIRST child ids of a given id($sel_id)
    public function getFirstChildId($sel_id)
    {
        $idarray = [];
        $result = $this->db->query('SELECT ' . $this->id . ' FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id . '');
        $count = $this->db->getRowsNum($result);
        if (0 == $count) {
            return $idarray;
        }
        while (list($id) = $this->db->fetchRow($result)) {
            $idarray[] = $id;
        }
        return $idarray;
    }

    //returns an array of ALL child ids for a given id($sel_id)
    public function getAllChildId($sel_id, $order= '', $idarray = [])
    {
        $sql = 'SELECT ' . $this->id . ' FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id . '';
        if ('' !== $order) {
            $sql .= " ORDER BY $order";
        }
        $result=$this->db->query($sql);
        $count = $this->db->getRowsNum($result);
        if (0 == $count) {
            return $idarray;
        }
        while (list($r_id) = $this->db->fetchRow($result)) {
            $idarray[] = $r_id;
            $idarray = $this->getAllChildId($r_id, $order, $idarray);
        }
        return $idarray;
    }

    //returns an array of ALL parent ids for a given id($sel_id)
    public function getAllParentId($sel_id, $order= '', $idarray = [])
    {
        $sql = 'SELECT ' . $this->pid . ' FROM ' . $this->table . ' WHERE ' . $this->id . '=' . $sel_id . '';
        if ('' !== $order) {
            $sql .= " ORDER BY $order";
        }
        $result=$this->db->query($sql);
        [$r_id] = $this->db->fetchRow($result);
        if (0 == $r_id) {
            return $idarray;
        }
        $idarray[] = $r_id;
        $idarray = $this->getAllParentId($r_id, $order, $idarray);
        return $idarray;
    }

    //generates path from the root id to a given id($sel_id)
    // the path is delimetered with "/"
    public function getPathFromId($sel_id, $title, $path= '')
    {
        $result = $this->db->query('SELECT ' . $this->pid . ', ' . $title . ' FROM ' . $this->table . ' WHERE ' . $this->id . "=$sel_id");
        if (0 == $this->db->getRowsNum($result)) {
            return $path;
        }
        [$parentid, $name] = $this->db->fetchRow($result);
        $myts =& MyTextSanitizer::sGetInstance();
        $name = $myts->makeTboxData4Show($name);
        $path = '/' . $name . $path . '';
        if (0 == $parentid) {
            return $path;
        }
        $path = $this->getPathFromId($parentid, $title, $path);
        return $path;
    }

    //makes a nicely ordered selection box
    //$preset_id is used to specify a preselected item
    //set $none to 1 to add a option with value 0
    public function makeMySelBox($title, $order= '', $preset_id=0, $none=0, $sel_name= '', $onchange= '')
    {
        if ('' == $sel_name) {
            $sel_name = $this->id;
        }
        $myts =& MyTextSanitizer::sGetInstance();
        echo "<select name='".$sel_name."'";
        if ('' !== $onchange) {
            echo " onchange='".$onchange."'";
        }
        echo ">\n";
        $sql = 'SELECT ' . $this->id . ', ' . $title . ' FROM ' . $this->table . ' WHERE ' . $this->pid . '=0';
        if ('' !== $order) {
            $sql .= " ORDER BY $order";
        }
        $result = $this->db->query($sql);
        if ($none) {
            echo "<option value='0'>----</option>\n";
        }
        while (list($catid, $name) = $this->db->fetchRow($result)) {
            $sel = '';
            if ($catid == $preset_id) {
                $sel = " selected='selected'";
            }
            echo "<option value='$catid'$sel>$name</option>\n";
            $sel = '';
            $arr = $this->getChildTreeArray($catid, $order);
            foreach ($arr as $option) {
                $option['prefix'] = str_replace('.', '--', $option['prefix']);
                $catpath = $option['prefix'] . '&nbsp;' . $myts->makeTboxData4Show($option[$title]);
                if ($option[$this->id] == $preset_id) {
                    $sel = " selected='selected'";
                }
                echo "<option value='".$option[$this->id]."'$sel>$catpath</option>\n";
                $sel = '';
            }
        }
        echo "</select>\n";
    }

    //generates nicely formatted linked path from the root id to a given id
    public function getNicePathFromId($sel_id, $title, $funcURL, $path= '')
    {
        $sql = 'SELECT ' . $this->pid . ', ' . $title . ' FROM ' . $this->table . ' WHERE ' . $this->id . "=$sel_id";
        $result = $this->db->query($sql);
        if (0 == $this->db->getRowsNum($result)) {
            return $path;
        }
        list($parentid, $name) = $this->db->fetchRow($result);
        $myts =& MyTextSanitizer::sGetInstance();
        $name = $myts->makeTboxData4Show($name);
        $path = "<a href='".$funcURL . '&amp;' . $this->id . '=' . $sel_id . "'>" . $name . '</a>&nbsp;:&nbsp;' . $path . '';
        if (0 == $parentid) {
            return $path;
        }
        $path = $this->getNicePathFromId($parentid, $title, $funcURL, $path);
        return $path;
    }

    //generates id path from the root id to a given id
    // the path is delimetered with "/"
    public function getIdPathFromId($sel_id, $path= '')
    {
        $result = $this->db->query('SELECT ' . $this->pid . ' FROM ' . $this->table . ' WHERE ' . $this->id . "=$sel_id");
        if (0 == $this->db->getRowsNum($result)) {
            return $path;
        }
        [$parentid] = $this->db->fetchRow($result);
        $path = '/' . $sel_id . $path . '';
        if (0 == $parentid) {
            return $path;
        }
        $path = $this->getIdPathFromId($parentid, $path);
        return $path;
    }

    public function getAllChild($sel_id=0, $order= '', $parray = [])
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id . '';
        if ('' !== $order) {
            $sql .= " ORDER BY $order";
        }
        $result = $this->db->query($sql);
        $count = $this->db->getRowsNum($result);
        if (0 == $count) {
            return $parray;
        }
        while ($row = $this->db->fetchArray($result)) {
            $parray[] = $row;
            $parray=$this->getAllChild($row[$this->id], $order, $parray);
        }
        return $parray;
    }

    public function getChildTreeArray($sel_id=0, $order= '', $parray = [], $r_prefix= '')
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id . '';
        if ('' !== $order) {
            $sql .= " ORDER BY $order";
        }
        $result = $this->db->query($sql);
        $count = $this->db->getRowsNum($result);
        if (0 == $count) {
            return $parray;
        }
        while ($row = $this->db->fetchArray($result)) {
            $row['prefix'] = $r_prefix . '.';
            $parray[] = $row;
            $parray = $this->getChildTreeArray($row[$this->id], $order, $parray, $row['prefix']);
        }
        return $parray;
    }
}
