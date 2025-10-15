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
use Query;
use sys;

sys::import('xaraya.modules.method');

/**
 * messages userapi isset_grouplist function
 * @extends MethodClass<UserApi>
 */
class IssetGrouplistMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserApi::issetGrouplist()
     */

    public function __invoke(array $args = [])
    {
        extract($args);
        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        $users = $this->mod()->apiFunc(
            'roles',
            'user',
            'getall',
            ['state'   => 3,
                'include_anonymous' => false,
                'include_myself' => false, ]
        );
        $userid = $this->user()->getId();

        sys::import('xaraya.structures.query');

        $xartable = $this->db()->getTables();
        $q = new Query('SELECT');
        $q->addtable($xartable['roles'], 'r');

        $q->eq('id', $userid);

        $q->addtable($xartable['rolemembers'], 'rm');
        $q->join('r.id', 'rm.role_id');

        if (!$q->run()) {
            return;
        }
        $CurrentUser =  $q->output();

        $id = $CurrentUser[0]['parent_id'];
        $groupID = $CurrentUser[0]['parent_id'];

        $allowedsendmessages = unserialize($this->mod()->getItemVar("allowedsendmessages", $groupID));

        if (isset($allowedsendmessages)) {
            if (empty($allowedsendmessages[0])) {
                return false;
            }
            $data['users'] = $userapi->get_sendtousers();
            if (empty($data['users'])) {
                return false;
            }
            return $allowedsendmessages;
        } else {
            return false;
        }
    }
}
