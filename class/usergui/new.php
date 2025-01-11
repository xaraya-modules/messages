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
use xarController;
use xarTpl;
use xarSec;
use xarUser;
use xarModVars;
use xarModItemVars;
use xarMod;
use DataObjectFactory;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages user new function
 * @extends MethodClass<UserGui>
 */
class NewMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    public function __invoke(array $args = [])
    {
        if (!xarSecurity::check('AddMessages')) {
            return;
        }

        if (!xarVar::fetch('replyto', 'int', $replyto, 0, xarVar::NOT_REQUIRED)) {
            return;
        }
        $reply = ($replyto > 0) ? true : false;
        $data = [];
        $data['reply'] = $reply;
        $data['replyto'] = $replyto;

        if (!xarVar::fetch('send', 'str', $send, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('draft', 'str', $draft, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('saveandedit', 'str', $saveandedit, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('to_id', 'id', $data['to_id'], null, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('opt', 'bool', $data['opt'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('id', 'id', $id, null, xarVar::NOT_REQUIRED)) {
            return;
        }

        $send = (!empty($send)) ? true : false;
        $draft = (!empty($draft)) ? true : false;
        $saveandedit = (!empty($saveandedit)) ? true : false;

        $object = DataObjectFactory::getObject(['name' => 'messages_messages']);
        $data['object'] = $object;

        $data['post_url']       = xarController::URL('messages', 'user', 'new');

        xarTpl::setPageTitle(xarML('Post Message'));
        $data['input_title']    = xarML('Compose Message');

        if ($draft) { // where to send people next
            $folder = 'drafts';
        } else {
            $folder = 'inbox';
        }
        $data['folder'] = 'new';

        if ($send) {
            $time = $object->properties['time']->value;
            $object->properties['author_status']->setValue(Defines::STATUS_UNREAD);
        } else {
            $object->properties['author_status']->setValue(Defines::STATUS_DRAFT);
        }

        if ($reply) {
            $reply = DataObjectFactory::getObject(['name' => 'messages_messages']);
            $reply->getItem(['itemid' => $replyto]); // get the message we're replying to
            $data['to_id'] = $reply->properties['from_id']->value; // get the user we're replying to
            $data['display'] = $reply;
            xarTpl::setPageTitle(xarML('Reply to Message'));
            $data['input_title']    = xarML('Reply to Message');
        }

        if ($send || $draft || $saveandedit) {
            // Check for a valid confirmation key
            if (!xarSec::confirmAuthKey()) {
                return xarController::badRequest('bad_author', $this->getContext());
            }

            $isvalid = $object->checkInput();

            if ($reply) { // we really only need this if we're saving a draft
                $object->properties['replyto']->setValue($replyto);
            } else {
                $object->properties['replyto']->setValue(0);
            }

            $object->properties['from_id']->setValue(xarUser::getVar('uname'));

            if (!$isvalid) {
                $data['context'] = $this->getContext();
                return xarTpl::module('messages', 'user', 'new', $data);
            }

            $object->properties['recipient_status']->setValue(Defines::STATUS_UNREAD);

            if ($send) {
                $object->properties['author_status']->setValue(Defines::STATUS_UNREAD);
            } else {
                $object->properties['author_status']->setValue(Defines::STATUS_DRAFT);
            }

            $id = $object->createItem();

            $to_id = $object->properties['to_id']->value;

            // admin setting
            if ($send && xarModVars::get('messages', 'sendemail')) {
                // user setting
                if (xarModItemVars::get('messages', "user_sendemail", $to_id)) {
                    xarMod::apiFunc('messages', 'user', 'sendmail', ['id' => $id, 'to_id' => $to_id]);
                }
            }

            $uid = xarUser::getVar('id');

            // Send the autoreply if one is enabled by the admin and by the recipient
            if ($send && xarModVars::get('messages', 'allowautoreply')) {
                $autoreply = '';
                if (xarModItemVars::get('messages', "enable_autoreply", $to_id)) {
                    $autoreply = xarModItemVars::get('messages', "autoreply", $to_id);
                }
                if (!empty($autoreply)) {
                    $autoreplyobj = DataObjectFactory::getObject(['name' => 'messages_messages']);
                    $autoreplyobj->properties['author_status']->setValue(Defines::STATUS_UNREAD);
                    $autoreplyobj->properties['from_id']->setValue(xarUser::getVar('uname', $to_id));
                    $autoreplyobj->properties['to_id']->setValue($uid);
                    $data['from_name'] = xarUser::getVar('name', $to_id);
                    $data['context'] = $this->getContext();
                    $subject = xarTpl::module('messages', 'user', 'autoreply-subject', $data);
                    $data['autoreply'] = $autoreply;
                    $autoreply = xarTpl::module('messages', 'user', 'autoreply-body', $data);
                    // useful for eliminating html template comments
                    if (xarModVars::get('messages', 'strip_tags')) {
                        $subject = strip_tags($subject);
                        $autoreply = strip_tags($autoreply);
                    }
                    $autoreplyobj->properties['subject']->setValue($subject);
                    $autoreplyobj->properties['body']->setValue($autoreply);
                    $itemid = $autoreplyobj->createItem();
                }
            }

            if ($saveandedit) {
                xarController::redirect(xarController::URL('messages', 'user', 'modify', ['id' => $id]), null, $this->getContext());
                return true;
            }

            if (xarModVars::get('messages', 'allowusersendredirect')) {
                $redirect = xarModItemVars::get('messages', 'user_send_redirect', $uid);
            } else {
                $redirect = xarModVars::get('messages', 'send_redirect');
            }
            $tabs = [1 => 'inbox', 2 => 'sent', 3 => 'drafts', 4 => 'new'];
            $redirect = $tabs[$redirect];

            if ($redirect == 'new') {
                xarController::redirect(xarController::URL('messages', 'user', 'new'), null, $this->getContext());
            } else {
                xarController::redirect(xarController::URL('messages', 'user', 'view', ['folder' => $redirect]), null, $this->getContext());
            }
            return true;
        }

        return $data;
    }
}
