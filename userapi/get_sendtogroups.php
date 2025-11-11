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

/**
 * messages userapi get_sendtogroups function
 * @extends MethodClass<UserApi>
 */
class GetSendtogroupsMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserApi::getSendtogroups()
     */

    public function __invoke(array $args = [])
    {
        extract($args);

        if (!isset($currentuser)) {
            $currentuser = $this->user()->getId();
        }

        // First we get all the parents of the current user
        $xartable = $this->db()->getTables();
        $q = new Query('SELECT');
        $q->addtable($xartable['roles'], 'r');
        $q->addtable($xartable['rolemembers'], 'rm');
        $q->join('r.id', 'rm.role_id');

        $q->addfield('rm.parent_id');
        $q->eq('r.id', $currentuser);

        if (!$q->run()) {
            return;
        }
        $parents =  $q->output();

        // Find the groups these parents can send to
        $sendtogroups = [];
        foreach ($parents as $parent) {
            $allowedgroups = unserialize($this->mod()->getItemVar("allowedsendmessages", $parent['parent_id']));
            if (!empty($allowedgroups)) {
                foreach ($allowedgroups as $allowedgroup) {
                    $sendtogroups[$allowedgroup] = $allowedgroup;
                }
            }
        }

        return $sendtogroups;
    }
}
