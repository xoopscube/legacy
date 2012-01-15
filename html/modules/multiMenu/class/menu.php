<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class MultimenuMenuObject extends XoopsSimpleObject
{
  public function __construct()
  {
    $this->initVar('id', XOBJ_DTYPE_INT, '0', true);
    $this->initVar('title', XOBJ_DTYPE_STRING, '');
    $this->initVar('hide', XOBJ_DTYPE_INT, 0);
    $this->initVar('link', XOBJ_DTYPE_STRING, '');
    $this->initVar('weight', XOBJ_DTYPE_INT, 0);
    $this->initVar('target', XOBJ_DTYPE_STRING, '_self');
    $this->initVar('groups', XOBJ_DTYPE_STRING, '');
  }
}

class MultimenuMenuHandler extends XoopsObjectGenericHandler
{
  public $mTable = 'multimenu01';
  public $mPrimary = 'id';
  public $mClass = 'MultimenuMenuObject';

  public function __construct(&$db)
  {
    parent::XoopsObjectGenericHandler($db);
  }
  
  public function setTable($mnum)
  {
    $this->mTable = $this->db->prefix('multimenu'.$mnum);
  }
  
  public function im_admin_clean()
  {
    $i = 0;
    $sql = "SELECT `id` FROM `".$this->mTable."` ORDER BY `weight`";
    $result = $this->db->query($sql);
    while(list($id) = $this->db->fetchRow($result)) {
      $this->db->queryF("UPDATE `".$this->mTable."` SET `weight` = '".$i."' WHERE `id` = ".$id);
      $i++;
    }
  }
}
?>
