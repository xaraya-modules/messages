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
use Xaraya\Modules\Messages\UserApi;
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
    /** functions imported by bermuda_cleanup * @see UserGui::modify()
     */

    public function __invoke(array $args = [])
    {
        /** @var UserApi $userapi */
        $userapi = $this->userapi();
        if (!$this->sec()->checkAccess('EditMessages', 0)) {
            return;
        }

        $this->var()->find('send', $send, 'str', '');
        $this->var()->find('draft', $draft, 'str', '');
        $this->var()->find('saveandedit', $saveandedit, 'str', '');
        $this->var()->find('id', $id, 'id');

        $send = (!empty($send)) ? true : false;
        $draft = (!empty($draft)) ? true : false;
        $saveandedit = (!empty($saveandedit)) ? true : false;

        $this->tpl()->setPageTitle($this->ml('Edit Draft'));
        $data = [];
        $data['input_title']    = $this->ml('Edit Draft');

        // Check if we still have no id of the item to modify.
        if (empty($id)) {
            $msg = $this->ml(
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
        $object = $this->data()->getObject(['name' => 'messages_messages']);
        $object->getItem(['itemid' => $id]);
        $replyto = $object->properties['replyto']->value;
        $data['replyto'] = $replyto;

        $data['reply'] = ($replyto > 0) ? true : false;

        $data['object'] = $object;

        $data['to_id'] = null;

        if ($data['reply']) {
            $reply = $this->data()->getObject(['name' => 'messages_messages']);
            $reply->getItem(['itemid' => $replyto]); // get the message we're replying to
            $data['to_id'] = $reply->properties['from_id']->value; // get the user we're replying to
            $data['display'] = $reply;
            $this->tpl()->setPageTitle($this->ml('Reply to Message'));
            $data['input_title']    = $this->ml('Reply to Message');
        }

        $data['label'] = $object->label;

        if ($send || $draft || $saveandedit) {
            // Check for a valid confirmation key
            if (!$this->sec()->confirmAuthKey()) {
                return $this->ctl()->badRequest('bad_author');
            }

            // Get the data from the form
            $isvalid = $object->checkInput();

            if (!$isvalid) {
                $data['context'] = $this->getContext();
                return $this->mod()->template('modify', $data);
            } else {
                // Good data: update the item

                if ($send) {
                    $object->properties['time']->setValue(time());
                    $object->properties['author_status']->setValue(Defines::STATUS_UNREAD);
                }

                $object->updateItem(['itemid' => $id]);

                if ($saveandedit) {
                    $this->ctl()->redirect($this->mod()->getURL( 'user', 'modify', ['id' => $id]));
                    return true;
                } elseif ($draft) {
                    $this->ctl()->redirect($this->mod()->getURL( 'user', 'view', ['folder' => 'drafts']));
                    return true;
                } elseif ($send) {
                    if ($this->mod()->getVar('sendemail')) {
                        $to_id = $object->properties['to_id']->value;
                        $userapi->sendmail(['id' => $id, 'to_id' => $to_id]);
                    }
                    $this->ctl()->redirect($this->mod()->getURL('user', 'view'));
                    return true;
                }
            }
        }

        $data['folder'] = 'drafts';

        return $data;
    }
}
