<?php

/**
 * @package modules\messages
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Messages\UserGui;


use Xaraya\Modules\Messages\UserGui;
use Xaraya\Modules\MethodClass;
use xarController;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages user main function
 * @extends MethodClass<UserGui>
 */
class MainMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserGui::main()
     */

    public function __invoke(array $args = [])
    {
        $this->ctl()->redirect($this->mod()->getURL('user', 'view'));
        return;
    }
}
