<?php

/**
 * @package modules\messages
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Messages;

use Xaraya\Modules\UserApiClass;
use sys;

sys::import('xaraya.modules.userapi');

/**
 * Handle the messages user API
 *
 * @method mixed checkanonymous(array $args)
 * @method mixed decodeShorturl(array $args)
 * @method mixed encodeShorturl(array $args)
 * @method mixed getCount(array $args)
 * @method mixed getSendtogroups(array $args)
 * @method mixed getSendtousers(array $args)
 * @method mixed getitemlinks(array $args)
 * @method mixed getmenulinks(array $args)
 * @method mixed issetGrouplist(array $args)
 * @method mixed sendmail(array $args)
 * @method mixed usermenu(array $args)
 * @extends UserApiClass<Module>
 */
class UserApi extends UserApiClass
{
    // ...
}
