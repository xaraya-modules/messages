<?php

/**
 * @package modules\messages
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Messages\UserApi;

use Xaraya\Modules\Messages\Defines;
use Xaraya\Modules\Messages\UserApi;
use Xaraya\Modules\MethodClass;
use xarDB;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages userapi get_count function
 * @extends MethodClass<UserApi>
 */
class GetCountMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Get the number of messages sent or received by a user
     * @author mikespub
     * @access public
     * @param array<mixed> $args
     * @var integer $author the id of the author you want to count messages for, or
     * @var integer $recipient the id of the recipient you want to count messages for
     * @var bool $unread (optional) count unread rather than total
     * @var bool $drafts (optional) count drafts
     * @return int the number of messages
     * @see UserApi::getCount()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if ((!isset($author) || empty($author)) && (!isset($recipient) || empty($recipient))) {
            $msg = $this->ml(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'author/recipient',
                'userapi',
                'get_count',
                'messages'
            );
            throw new BadParameterException(null, $msg);
        }

        $dbconn = $this->db()->getConn();
        $xartable = $this->db()->getTables();

        $sql = "SELECT  COUNT(id) as numitems
                  FROM  $xartable[messages]
                 WHERE  ";

        $bindvars = [];
        if (isset($recipient)) {
            $sql .= "to_delete=? AND to_id=? AND from_status!=?";
            $bindvars[] = Defines::NOTDELETED;
            $bindvars[] = (int) $recipient;
            $bindvars[] = Defines::STATUS_DRAFT;
            if (isset($unread)) {
                $sql .= " AND to_status=?";
                $bindvars[] = Defines::STATUS_UNREAD;
            }
        } elseif (isset($author)) {
            $sql .= " from_delete=? AND from_id=?";
            $bindvars[] = Defines::NOTDELETED;
            $bindvars[] = (int) $author;
            if (isset($unread)) {
                $sql .= " AND from_status=?";
                $bindvars[] = Defines::NOTDELETED;
            } elseif (isset($drafts)) {
                $sql .= " AND from_status=?";
                $bindvars[] = Defines::STATUS_DRAFT;
            } else {
                $sql .= " AND from_status!=?";
                $bindvars[] = Defines::STATUS_DRAFT;
            }
        }


        $result = & $dbconn->Execute($sql, $bindvars);

        if (!$result) {
            return 0;
        }

        if (!$result->first()) {
            return 0;
        }

        [$numitems] = $result->fields;

        $result->Close();

        return $numitems;
    }
}
