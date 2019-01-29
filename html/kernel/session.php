<?php
// $Id: session.php,v 1.1 2007/05/15 02:34:37 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://xoopscube.jp/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
/**
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */


/**
 * Handler for a session
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsSessionHandler
{

    /**
     * Database connection
     * 
     * @var	object
     * @access	private
     */
    public $db;

    /**
     * Constructor
     * 
     * @param	object  &$mf    reference to a XoopsManagerFactory
     * 
     */
    public function __construct(&$db)
    {
        $this->db =& $db;
    }
    public function XoopsSessionHandler(&$db)
    {
        return self::__construct($db);
    }

    /**
     * Open a session
     * 
     * @param	string  $save_path
     * @param	string  $session_name
     * 
     * @return	bool
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * Close a session
     * 
     * @return	bool
     */
    public function close()
    {
        return true;
    }

    /**
     * Read a session from the database
     * 
     * @param	string  &sess_id    ID of the session
     * 
     * @return	array   Session data
     */
    public function read($sess_id)
    {
        $sql = sprintf('SELECT sess_data FROM %s WHERE sess_id = %s', $this->db->prefix('session'), $this->db->quoteString($sess_id));
        if (false != $result = $this->db->query($sql)) {
            if (list($sess_data) = $this->db->fetchRow($result)) {
                return $sess_data;
            }
        }
        return '';
    }

    /**
     * Write a session to the database
     * 
     * @param   string  $sess_id
     * @param   string  $sess_data
     * 
     * @return  bool    
     **/
    public function write($sess_id, $sess_data)
    {
        $sess_id = $this->db->quoteString($sess_id);
        list($count) = $this->db->fetchRow($this->db->query("SELECT COUNT(*) FROM ".$this->db->prefix('session')." WHERE sess_id=".$sess_id));
        if ($count > 0) {
            $sql = sprintf('UPDATE %s SET sess_updated = %u, sess_data = %s WHERE sess_id = %s', $this->db->prefix('session'), time(), $this->db->quoteString($sess_data), $sess_id);
        } else {
            $sql = sprintf('INSERT INTO %s (sess_id, sess_updated, sess_ip, sess_data) VALUES (%s, %u, %s, %s)', $this->db->prefix('session'), $sess_id, time(), $this->db->quoteString($_SERVER['REMOTE_ADDR']), $this->db->quoteString($sess_data));
        }
        if (!$this->db->queryF($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Destroy a session
     * 
     * @param   string  $sess_id
     * 
     * @return  bool
     **/
    public function destroy($sess_id)
    {
        $sql = sprintf('DELETE FROM %s WHERE sess_id = %s', $this->db->prefix('session'), $this->db->quoteString($sess_id));
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Garbage Collector
     * 
     * @param   int $expire Time in seconds until a session expires
     * @return  bool
     **/
    public function gc($expire)
    {
        $mintime = time() - (int)$expire;
        $sql = sprintf('DELETE FROM %s WHERE sess_updated < %u', $this->db->prefix('session'), $mintime);
        return $this->db->queryF($sql);
    }
}
