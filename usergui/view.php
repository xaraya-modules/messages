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
use Xaraya\Modules\Messages\AdminApi;
use Xaraya\Modules\MethodClass;
use xarSecurity;
use xarVar;
use xarSession;
use xarModVars;
use xarMod;
use xarUser;
use xarTpl;
use xarModUserVars;
use DataObjectFactory;
use DataPropertyMaster;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages user view function
 * @extends MethodClass<UserGui>
 */
class ViewMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserGui::view()
     */

    public function __invoke(array $args = [])
    {
        /** @var AdminApi $adminapi */
        $adminapi = $this->adminapi();
        if (!$this->sec()->checkAccess('ViewMessages')) {
            return;
        }

        $this->var()->find('startnum', $startnum);
        $this->var()->find('numitems', $numitems, 'int');

        $this->var()->find('folder', $folder, 'enum:inbox:sent:drafts', 'inbox');
        $this->session()->setVar('messages_currentfolder', $folder);

        $data['startnum'] = $startnum;

        if (empty($numitems)) {
            $numitems = $this->mod()->getVar('items_per_page');
        }

        //Psspl:Added the code for configuring the user-menu
        //$data['allow_newpm'] = $this->mod()->apiFunc('messages' , 'user' , 'isset_grouplist');

        switch ($folder) {
            case 'inbox':
                $where = 'to_id eq ' . xarUser::getVar('id');
                $where .= ' and recipient_delete eq ' . Defines::NOTDELETED;
                $where .= ' and author_status ne ' . Defines::STATUS_DRAFT;
                $data['fieldlist'] = 'from_id,subject,time,recipient_status';
                $this->tpl()->setPageTitle($this->ml('Inbox'));
                $data['input_title']    = $this->ml('Inbox');
                break;
            case 'sent':
                $where = 'from_id eq ' . xarUser::getVar('id');
                $where .= ' and author_delete eq ' . Defines::NOTDELETED;
                $where .= ' and author_status ne ' . Defines::STATUS_DRAFT;
                $data['fieldlist'] = 'to_id,subject,time,author_status,recipient_status';
                if ($this->mod()->getVar('allowanonymous')) {
                    $data['fieldlist'] .= ',postanon';
                }
                $this->tpl()->setPageTitle($this->ml('Sent Messages'));
                $data['input_title']    = $this->ml('Sent Messages');
                break;
            case 'drafts':
                $where = 'author_status eq 0';
                $where .= ' and from_id eq ' . xarUser::getVar('id');
                $where .= ' and author_delete eq ' . Defines::NOTDELETED;
                $data['fieldlist'] = 'to_id,subject,time,author_status';
                $this->tpl()->setPageTitle($this->ml('Drafts'));
                $data['input_title']    = $this->ml('Drafts');
                break;
        }

        $sort = $adminapi->sort([
            //how to sort if the URL or config say otherwise...
            //'object' => $object,
            'sortfield_fallback' => 'time',
            'ascdesc_fallback' => 'DESC',
        ]);
        $data['sort'] = $sort;

        $total = $this->data()->getObjectList([
            'name' => 'messages_messages',
            'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
            'numitems' => null,
            'where' => $where,
        ]);
        $data['total'] = count($total->getItems());

        $list = $this->data()->getObjectList([
            'name' => 'messages_messages',
            'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
            'startnum'  => $startnum,
            'numitems' => $numitems,
            'sort' => $data['sort'],
            'where' => $where,
        ]);

        $list->getItems();
        $data['list'] = $list;

        if (xarUser::isLoggedIn()) {
            $this->var()->find('away', $away, 'str');
            if (isset($away)) {
                xarModUserVars::set('messages', 'away_message', $away);
            }
            $data['away_message'] = xarModUserVars::get('messages', 'away_message');
        } else {
            $data['away_message'] = '';
        }

        $data['folder'] = $folder;

        return $data;
    }
}
