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
use xarSession;
use xarTpl;
use xarController;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages user delete function
 */
class DeleteMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    public function __invoke(array $args = [])
    {
        if (!xarSecurity::check('ManageMessages')) {
            return;
        }

        if (!xarVar::fetch('action', 'enum:confirmed:check', $data['action'], 'check', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('object', 'str', $object, 'messages_messages', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('replyto', 'int', $data['replyto'], 0, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('id', 'int:1', $id, 0, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectFactory::getObject(['name' => $object]);
        $data['object']->getItem(['itemid' => $id]);

        $folder = xarSession::getVar('messages_currentfolder');

        // Check the folder, and that the current user is either author or recipient
        switch ($folder) {
            case 'inbox':
                if ($data['object']->properties['to_id']->value != xarSession::getVar('role_id')) {
                    return xarTpl::module('messages', 'user', 'message_errors', ['layout' => 'bad_id']);
                }
                break;
            case 'drafts':
                if ($data['object']->properties['from_id']->value != xarSession::getVar('role_id')) {
                    return xarTpl::module('messages', 'user', 'message_errors', ['layout' => 'bad_id']);
                }
                break;
            case 'sent':
                if ($data['object']->properties['from_id']->value != xarSession::getVar('role_id')) {
                    return xarTpl::module('messages', 'user', 'message_errors', ['layout' => 'bad_id']);
                }
                break;
        }

        $data['folder'] = $folder;

        switch ($data['action']) {
            case "confirmed":

                /*
                 * Then go ahead and delete the message :)
                 */

                if ($folder == 'inbox') {
                    $data['object']->properties['recipient_delete']->setValue(MESSAGES_DELETED);
                } elseif ($folder == 'sent') {
                    $data['object']->properties['author_delete']->setValue(MESSAGES_DELETED);
                } else {
                    $data['object']->properties['recipient_delete']->setValue(MESSAGES_DELETED);
                    $data['object']->properties['author_delete']->setValue(MESSAGES_DELETED);
                }

                $data['object']->updateItem();

                xarController::redirect(xarController::URL('messages', 'user', 'view', ['folder' => $folder]), null, $this->getContext());
                break;

            case "check":
                // nothing to do here, just return the object
                $data['id'] = $data['object']->properties['id']->getValue();
                break;
        }
        return $data;
    }
}
