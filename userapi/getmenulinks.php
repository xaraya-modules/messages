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
use sys;

sys::import('xaraya.modules.method');

/**
 * messages userapi getmenulinks function
 * @extends MethodClass<UserApi>
 */
class GetmenulinksMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserApi::getmenulinks()
     */

    public function __invoke(array $args = [])
    {
        /** @var UserApi $userapi */
        $userapi = $this->userapi();
        $menulinks = [];
        if ($this->sec()->checkAccess('ReadMessages', 0)) {
            $menulinks[] = [
                'url'      => $this->mod()->getURL('user', 'view'),
                'title'    => 'Look at the Messages',
                'label'    => 'View Messages', ];
        }

        if ($this->sec()->checkAccess('AddMessages', 0) && $userapi->isset_grouplist()) {
            $menulinks[] = [
                'url'      => $this->mod()->getURL('user', 'new'),
                'title'    => 'Send a message to someone',
                'label'    => 'New Message', ];
        }

        return $menulinks;
    }
}
