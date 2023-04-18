<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) {
    exit;
}

/**
 * Lenum_Status
**/
interface Lenum_Status
{
    public const DELETED = 0;
    public const REJECTED = 2;
    public const PROGRESS = 5;
    public const PUBLISHED = 9;
}
/**
 * Lenum_WorkflowStatus
**/
interface Lenum_WorkflowStatus
{
    public const DELETED = 0;
    public const BLOCKED = 1;
    public const REJECTED = 2;
    public const PROGRESS = 5;
    public const FINISHED = 9;
}
/**
 * Lenum_GroupRank
**/
class Lenum_GroupRank
{
    public const GUEST = 0;
    public const ASSOCIATE = 2;
    public const REGULAR = 5;
    public const STAFF = 7;
    public const OWNER = 9;

    public static function getList()
    {
        return [
            self::GUEST => _GROUP_RANK_GUEST,
            self::ASSOCIATE => _GROUP_RANK_ASSOCIATE,
            self::REGULAR => _GROUP_RANK_REGULAR,
            self::STAFF => _GROUP_RANK_STAFF,
            self::OWNER => _GROUP_RANK_OWNER
        ];
    }
}


class Lenum_ImageType
{
    public const GIF = 1;
    public const JPG = 2;
    public const PNG = 3;

    public static function getName(/*** Enum ***/ $ext)
    {
        switch ($ext) {
        case self::GIF:
            return 'gif';
            break;
        case self::JPG:
            return 'jpg';
            break;
        case self::PNG:
            return 'png';
            break;
        }
    }
}
