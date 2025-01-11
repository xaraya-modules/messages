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


use Xaraya\Modules\Messages\UserApi;
use Xaraya\Modules\MethodClass;
use xarSecurity;
use xarController;
use xarMod;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages userapi getmenulinks function
 * @extends MethodClass<UserApi>
 */
class GetmenulinksMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    public function __invoke(array $args = [])
    {
        $menulinks = [];
        if (xarSecurity::check('ReadMessages', 0)) {
            $menulinks[] = [
                'url'      => xarController::URL('messages', 'user', 'view'),
                'title'    => 'Look at the Messages',
                'label'    => 'View Messages', ];
        }

        if (xarSecurity::check('AddMessages', 0) && xarMod::apiFunc('messages', 'user', 'isset_grouplist')) {
            $menulinks[] = [
                'url'      => xarController::URL('messages', 'user', 'new'),
                'title'    => 'Send a message to someone',
                'label'    => 'New Message', ];
        }

        return $menulinks;
    }
}
