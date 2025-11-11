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

/**
 * messages user markunread function
 * @extends MethodClass<UserGui>
 */
class MarkunreadMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserGui::markunread()
     */

    public function __invoke(array $args = [])
    {
        if (!$this->sec()->checkAccess('ManageMessages')) {
            return;
        }

        $this->var()->find('id', $id, 'int:1', 0);
        $this->var()->find('folder', $folder, 'enum:inbox:sent:drafts', 'inbox');

        $data['object'] = $this->data()->getObject(['name' => 'messages_messages']);
        $data['object']->getItem(['itemid' => $id]);

        $folder = $this->session()->getVar('messages_currentfolder');

        // Check the folder, and that the current user is either author or recipient
        switch ($folder) {
            case 'inbox':
                if ($data['object']->properties['to_id']->value != $this->user()->getId()) {
                    return $this->mod()->template('message_errors', ['layout' => 'bad_id']);
                } else {
                    $data['object']->properties['recipient_status']->setValue(Defines::STATUS_UNREAD);
                }
                break;
            case 'sent':
                if ($data['object']->properties['from_id']->value != $this->user()->getId()) {
                    return $this->mod()->template('message_errors', ['layout' => 'bad_id']);
                } else {
                    $data['object']->properties['author_status']->setValue(Defines::STATUS_UNREAD);
                }
                break;
        }

        $data['folder'] = $folder;

        $data['object']->updateItem();

        $this->ctl()->redirect($this->mod()->getURL('user', 'view', ['folder' => $folder]));

        return true;
    }
}
