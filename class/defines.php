<?php

/**
 * @package modules\messages
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 * @author Carl P. Corliss <rabbitt@xaraya.com>
**/

namespace Xaraya\Modules\Messages;

/**
 * Defines constants for the messages module (from xarincludes/defines.php)
 */
class Defines
{
    // The following constants define overall status of a message
    public const NOTDELETED = 0;
    public const DELETED = 1;

    public const STATUS_DRAFT = 0;
    public const STATUS_UNREAD = 1;
    public const STATUS_READ = 2;
}
