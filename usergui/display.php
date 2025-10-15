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
use xarModHooks;
use sys;

sys::import('xaraya.modules.method');

/**
 * messages user display function
 * @extends MethodClass<UserGui>
 */
class DisplayMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserGui::display()
     */

    public function __invoke(array $args = [])
    {
        extract($args);

        if (!$this->sec()->checkAccess('ReadMessages')) {
            return;
        }

        //$this->var()->find('object', $object, 'str', 'messages_messages');
        $this->var()->find('id', $id, 'int', 0);
        $this->var()->find('folder', $data['folder'], 'enum:inbox:sent:drafts', 'inbox');

        $data['id'] = $id;

        $this->tpl()->setPageTitle($this->ml('Read Message'));
        $data['input_title']    = $this->ml('Read Message');

        //Psspl:Added the code for configuring the user-menu
        //$data['allow_newpm'] = $this->mod()->apiFunc('messages' , 'user' , 'isset_grouplist');

        $object = $this->data()->getObject(['name' => 'messages_messages']);
        $object->getItem(['itemid' => $id]);

        $data['replyto'] = $object->properties['replyto']->value;

        $current_user = $this->user()->getId();

        // Check that the current user is either author or recipient
        if (($object->properties['to_id']->value != $current_user) &&
            ($object->properties['from_id']->value != $current_user)) {
            return $this->mod()->template('message_errors', ['layout' => 'bad_id']);
        }

        //    $data['message'] = $messages[0];
        $data['action']  = 'display';

        // added call to transform text srg 09/22/03
        $body = $object->properties['body']->getValue();
        [$body] = xarModHooks::call(
            'item',
            'transform',
            $id,
            [$body]
        );
        $object->properties['body']->setValue($body);

        /*
         * Mark this message as read
         * Handle author and recipient for 'mark unread' (future)
         */
        if ($current_user == $object->properties['from_id']->value) {
            // don't update drafts
            if ($object->properties['author_status']->value != Defines::STATUS_DRAFT) {
                $object->properties['author_status']->setValue(Defines::STATUS_READ);
                $object->updateItem();
            }
        }
        if ($current_user == $object->properties['to_id']->value) {
            $object->properties['recipient_status']->setValue(Defines::STATUS_READ);
            $object->updateItem();
        }

        $data['object'] = $object;

        return $data;
    }
}
