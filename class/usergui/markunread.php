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
 * messages user markunread function
 * @extends MethodClass<UserGui>
 */
class MarkunreadMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    public function __invoke(array $args = [])
    {
        if (!$this->checkAccess('ManageMessages')) {
            return;
        }

        if (!$this->fetch('id', 'int:1', $id, 0, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!$this->fetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectFactory::getObject(['name' => 'messages_messages']);
        $data['object']->getItem(['itemid' => $id]);

        $folder = xarSession::getVar('messages_currentfolder');

        // Check the folder, and that the current user is either author or recipient
        switch ($folder) {
            case 'inbox':
                if ($data['object']->properties['to_id']->value != xarSession::getVar('role_id')) {
                    return xarTpl::module('messages', 'user', 'message_errors', ['layout' => 'bad_id']);
                } else {
                    $data['object']->properties['recipient_status']->setValue(Defines::STATUS_UNREAD);
                }
                break;
            case 'sent':
                if ($data['object']->properties['from_id']->value != xarSession::getVar('role_id')) {
                    return xarTpl::module('messages', 'user', 'message_errors', ['layout' => 'bad_id']);
                } else {
                    $data['object']->properties['author_status']->setValue(Defines::STATUS_UNREAD);
                }
                break;
        }

        $data['folder'] = $folder;

        $data['object']->updateItem();

        $this->redirect($this->getUrl( 'user', 'view', ['folder' => $folder]));

        return true;
    }
}
