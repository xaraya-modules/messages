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

use Xaraya\Modules\MethodClass;
use xarSecurity;
use xarVar;
use xarController;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages user reply function
 */
class ReplyMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    public function __invoke(array $args = [])
    {
        if (!xarSecurity::check('AddMessages')) {
            return;
        }

        if (!xarVar::fetch('object', 'str', $object, 'messages_messages', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('replyto', 'int', $replyto, 0, xarVar::NOT_REQUIRED)) {
            return;
        }
        xarController::redirect(xarController::URL('messages', 'user', 'new', ['replyto' => $replyto]), null, $this->getContext());
        return true;
    }
}
