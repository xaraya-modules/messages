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
use xarSession;
use xarTpl;
use xarController;
use DataObjectFactory;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages user delete function
 * @extends MethodClass<UserGui>
 */
class DeleteMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserGui::delete()
     */

    public function __invoke(array $args = [])
    {
        if (!$this->sec()->checkAccess('ManageMessages')) {
            return;
        }

        if (!$this->var()->find('action', $data['action'], 'enum:confirmed:check', 'check')) {
            return;
        }
        if (!$this->var()->find('object', $object, 'str', 'messages_messages')) {
            return;
        }
        if (!$this->var()->find('replyto', $data['replyto'], 'int', 0)) {
            return;
        }
        if (!$this->var()->find('id', $id, 'int:1', 0)) {
            return;
        }
        if (!$this->var()->find('folder', $folder, 'enum:inbox:sent:drafts', 'inbox')) {
            return;
        }

        $data['object'] = $this->data()->getObject(['name' => $object]);
        $data['object']->getItem(['itemid' => $id]);

        $folder = xarSession::getVar('messages_currentfolder');

        // Check the folder, and that the current user is either author or recipient
        switch ($folder) {
            case 'inbox':
                if ($data['object']->properties['to_id']->value != xarSession::getVar('role_id')) {
                    return $this->mod()->template('message_errors', ['layout' => 'bad_id']);
                }
                break;
            case 'drafts':
                if ($data['object']->properties['from_id']->value != xarSession::getVar('role_id')) {
                    return $this->mod()->template('message_errors', ['layout' => 'bad_id']);
                }
                break;
            case 'sent':
                if ($data['object']->properties['from_id']->value != xarSession::getVar('role_id')) {
                    return $this->mod()->template('message_errors', ['layout' => 'bad_id']);
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
                    $data['object']->properties['recipient_delete']->setValue(Defines::DELETED);
                } elseif ($folder == 'sent') {
                    $data['object']->properties['author_delete']->setValue(Defines::DELETED);
                } else {
                    $data['object']->properties['recipient_delete']->setValue(Defines::DELETED);
                    $data['object']->properties['author_delete']->setValue(Defines::DELETED);
                }

                $data['object']->updateItem();

                $this->ctl()->redirect($this->mod()->getURL( 'user', 'view', ['folder' => $folder]));
                break;

            case "check":
                // nothing to do here, just return the object
                $data['id'] = $data['object']->properties['id']->getValue();
                break;
        }
        return $data;
    }
}
