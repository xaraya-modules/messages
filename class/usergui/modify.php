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

use Xaraya\Modules\Messages\Defines;
use Xaraya\Modules\Messages\UserGui;
use Xaraya\Modules\MethodClass;
use xarSecurity;
use xarVar;
use xarTpl;
use xarSec;
use xarController;
use xarModVars;
use xarMod;
use DataObjectFactory;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * messages user modify function
 * @extends MethodClass<UserGui>
 */
class ModifyMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    public function __invoke(array $args = [])
    {
        if (!xarSecurity::check('EditMessages', 0)) {
            return;
        }

        if (!xarVar::fetch('send', 'str', $send, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('draft', 'str', $draft, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('saveandedit', 'str', $saveandedit, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('id', 'id', $id, null, xarVar::NOT_REQUIRED)) {
            return;
        }

        $send = (!empty($send)) ? true : false;
        $draft = (!empty($draft)) ? true : false;
        $saveandedit = (!empty($saveandedit)) ? true : false;

        xarTpl::setPageTitle(xarML('Edit Draft'));
        $data = [];
        $data['input_title']    = xarML('Edit Draft');

        // Check if we still have no id of the item to modify.
        if (empty($id)) {
            $msg = xarML(
                'Invalid #(1) for #(2) function #(3)() in module #(4)',
                'id',
                'user',
                'modify',
                'messages'
            );
            throw new Exception($msg);
        }

        $data['id'] = $id;

        // Load the DD master object class. This line will likely disappear in future versions
        sys::import('modules.dynamicdata.class.objects.factory');

        // Get the object name
        $object = DataObjectFactory::getObject(['name' => 'messages_messages']);
        $object->getItem(['itemid' => $id]);
        $replyto = $object->properties['replyto']->value;
        $data['replyto'] = $replyto;

        $data['reply'] = ($replyto > 0) ? true : false;

        $data['object'] = $object;

        $data['to_id'] = null;

        if ($data['reply']) {
            $reply = DataObjectFactory::getObject(['name' => 'messages_messages']);
            $reply->getItem(['itemid' => $replyto]); // get the message we're replying to
            $data['to_id'] = $reply->properties['from_id']->value; // get the user we're replying to
            $data['display'] = $reply;
            xarTpl::setPageTitle(xarML('Reply to Message'));
            $data['input_title']    = xarML('Reply to Message');
        }

        $data['label'] = $object->label;

        if ($send || $draft || $saveandedit) {
            // Check for a valid confirmation key
            if (!xarSec::confirmAuthKey()) {
                return xarController::badRequest('bad_author', $this->getContext());
            }

            // Get the data from the form
            $isvalid = $object->checkInput();

            if (!$isvalid) {
                $data['context'] = $this->getContext();
                return xarTpl::module('messages', 'user', 'modify', $data);
            } else {
                // Good data: update the item

                if ($send) {
                    $object->properties['time']->setValue(time());
                    $object->properties['author_status']->setValue(Defines::STATUS_UNREAD);
                }

                $object->updateItem(['itemid' => $id]);

                if ($saveandedit) {
                    xarController::redirect(xarController::URL('messages', 'user', 'modify', ['id' => $id]), null, $this->getContext());
                    return true;
                } elseif ($draft) {
                    xarController::redirect(xarController::URL('messages', 'user', 'view', ['folder' => 'drafts']), null, $this->getContext());
                    return true;
                } elseif ($send) {
                    if (xarModVars::get('messages', 'sendemail')) {
                        $to_id = $object->properties['to_id']->value;
                        xarMod::apiFunc('messages', 'user', 'sendmail', ['id' => $id, 'to_id' => $to_id]);
                    }
                    xarController::redirect(xarController::URL('messages', 'user', 'view'), null, $this->getContext());
                    return true;
                }
            }
        }

        $data['folder'] = 'drafts';

        return $data;
    }
}
