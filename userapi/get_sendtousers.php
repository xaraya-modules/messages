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
use xarRoles;
use Query;
use sys;

sys::import('xaraya.modules.method');

/**
 * messages userapi get_sendtousers function
 * @extends MethodClass<UserApi>
 */
class GetSendtousersMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserApi::getSendtousers()
     */

    public function __invoke(array $args = [])
    {
        /** @var UserApi $userapi */
        $userapi = $this->userapi();
        $sendtogroups = $userapi->get_sendtogroups($args);

        if (empty($sendtogroups)) {
            return [];
        }

        // Get the users these allowed groups contain
        sys::import('xaraya.structures.query');
        $xartable = $this->db()->getTables();
        $q = new Query('SELECT');
        $q->addtable($xartable['roles'], 'r');
        $q->addtable($xartable['rolemembers'], 'rm');
        $q->join('r.id', 'rm.role_id');

        $q->addfield('r.id');
        $q->addfield('r.name');
        $q->addfield('r.uname');
        $q->eq('r.state', xarRoles::ROLES_STATE_ACTIVE);
        $q->ne('r.email', '');
        $q->ne('r.name', 'Myself');
        $q->eq('r.itemtype', xarRoles::ROLES_USERTYPE);//check for user

        /*Psspl:get the selected groups only*/
        $user_c = [];
        foreach ($sendtogroups as $key => $value) {
            $user_c[] = $q->peq('rm.parent_id', $value);
        }
        $q->qor($user_c); //use OR

        //function for echo the query.
        //$q->qecho();

        if (!$q->run()) {
            return;
        }

        $users = $q->output();

        // Need to transform the display name values we got
        $nameproperty = $this->prop()->getProperty(['name' => 'username']);
        foreach ($users as $key => $value) {
            $nameproperty->value = $users[$key]['name'];
            $users[$key]['name'] = $nameproperty->getValue();
        }

        return $users;
    }
}
