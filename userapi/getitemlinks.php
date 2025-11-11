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

/**
 * messages userapi getitemlinks function
 * @extends MethodClass<UserApi>
 */
class GetitemlinksMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserApi::getitemlinks()
     */

    public function __invoke(array $args = [])
    {
        $itemlinks = [];
        if (!$this->sec()->checkAccess('ViewMessages', 0)) {
            return $itemlinks;
        }

        foreach ($args['itemids'] as $itemid) {
            $item = $this->mod()->apiFunc(
                'roles',
                'user',
                'get',
                ['id' => $itemid]
            );
            if (!isset($item)) {
                return;
            }
            $itemlinks[$itemid] = ['url' => $this->ctl()->getModuleURL(
                'roles',
                'user',
                'display',
                ['id' => $itemid]
            ),
                'title' => $this->ml('Display User'),
                'label' => $this->prep()->text($item['name']), ];
        }
        return $itemlinks;
    }
}
