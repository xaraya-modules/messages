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
use xarSecurity;
use xarVar;
use xarController;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages user reply function
 * @extends MethodClass<UserGui>
 */
class ReplyMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserGui::reply()
     */

    public function __invoke(array $args = [])
    {
        if (!$this->sec()->checkAccess('AddMessages')) {
            return;
        }

        if (!$this->var()->find('object', $object, 'str', 'messages_messages')) {
            return;
        }
        if (!$this->var()->find('replyto', $replyto, 'int', 0)) {
            return;
        }
        $this->ctl()->redirect($this->mod()->getURL( 'user', 'new', ['replyto' => $replyto]));
        return true;
    }
}
