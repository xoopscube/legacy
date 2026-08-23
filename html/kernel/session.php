<?php
/**
 * Handler for a session
 * @package    kernel
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

class XoopsSessionHandler implements SessionHandlerInterface
{
    /**
     * Database connection
     * @var object
     */
    public $db;

    /**
     * Constructor
     * @param object $db
     */
    public function __construct(&$db)
    {
        $this->db =& $db;
    }

    /** @deprecated */
    public function XoopsSessionHandler(&$db)
    {
        return self::__construct($db);
    }

    /**
     * Open a session
     */
    public function open($save_path, $session_name): bool
    {
        return true;
    }

    /**
     * Close a session
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * Read a session from the database
     */
    public function read($sess_id): string
    {
        $sql = sprintf(
            'SELECT sess_data FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quoteString($sess_id)
        );
        if (false != ($result = $this->db->query($sql))) {
            if ([$sess_data] = $this->db->fetchRow($result)) {
                return (string)$sess_data;
            }
        }
        return '';
    }

    /**
     * Write a session to the database
     */
    public function write($sess_id, $sess_data): bool
    {
        $sess_id_q = $this->db->quoteString($sess_id);
        [$count] = $this->db->fetchRow(
            $this->db->query('SELECT COUNT(*) FROM ' . $this->db->prefix('session') . ' WHERE sess_id=' . $sess_id_q)
        );
        if ($count > 0) {
            $sql = sprintf(
                'UPDATE %s SET sess_updated = %u, sess_data = %s WHERE sess_id = %s',
                $this->db->prefix('session'),
                time(),
                $this->db->quoteString($sess_data),
                $sess_id_q
            );
        } else {
            $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
            $sql = sprintf(
                'INSERT INTO %s (sess_id, sess_updated, sess_ip, sess_data) VALUES (%s, %u, %s, %s)',
                $this->db->prefix('session'),
                $sess_id_q,
                time(),
                $this->db->quoteString($remoteAddr),
                $this->db->quoteString($sess_data)
            );
        }
        if (!$this->db->queryF($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Destroy a session
     */
    public function destroy($sess_id): bool
    {
        $sql = sprintf(
            'DELETE FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quoteString($sess_id)
        );
        if (!$this->db->queryF($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Garbage Collector
     * SessionHandlerInterface では削除件数 (int) または false を返す
     */
    public function gc($expire): int|false
    {
        $mintime = time() - (int)$expire;
        $sql = sprintf(
            'DELETE FROM %s WHERE sess_updated < %u',
            $this->db->prefix('session'),
            $mintime
        );
        $result = $this->db->queryF($sql);
        if ($result === false) {
            return false;
        }
        // 削除件数を返す（取れなければ 0）
        if (is_object($this->db) && method_exists($this->db, 'getAffectedRows')) {
            return (int)$this->db->getAffectedRows();
        }
        return 0;
    }
}