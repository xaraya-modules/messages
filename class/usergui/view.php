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
    /** functions imported by bermuda_cleanup */

    public function __invoke(array $args = [])
    {
        if (!xarSecurity::check('ViewMessages')) {
            return;
        }

        if (!xarVar::fetch('startnum', 'isset', $startnum, null, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('numitems', 'int', $numitems, null, xarVar::NOT_REQUIRED)) {
            return;
        }

        if (!xarVar::fetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', xarVar::NOT_REQUIRED)) {
            return;
        }
        xarSession::setVar('messages_currentfolder', $folder);

        $data['startnum'] = $startnum;

        if (empty($numitems)) {
            $numitems = xarModVars::get('messages', 'items_per_page');
        }

        //Psspl:Added the code for configuring the user-menu
        //$data['allow_newpm'] = xarMod::apiFunc('messages' , 'user' , 'isset_grouplist');

        switch ($folder) {
            case 'inbox':
                $where = 'to_id eq ' . xarUser::getVar('id');
                $where .= ' and recipient_delete eq ' . Defines::NOTDELETED;
                $where .= ' and author_status ne ' . Defines::STATUS_DRAFT;
                $data['fieldlist'] = 'from_id,subject,time,recipient_status';
                xarTpl::setPageTitle(xarML('Inbox'));
                $data['input_title']    = xarML('Inbox');
                break;
            case 'sent':
                $where = 'from_id eq ' . xarUser::getVar('id');
                $where .= ' and author_delete eq ' . Defines::NOTDELETED;
                $where .= ' and author_status ne ' . Defines::STATUS_DRAFT;
                $data['fieldlist'] = 'to_id,subject,time,author_status,recipient_status';
                if (xarModVars::get('messages', 'allowanonymous')) {
                    $data['fieldlist'] .= ',postanon';
                }
                xarTpl::setPageTitle(xarML('Sent Messages'));
                $data['input_title']    = xarML('Sent Messages');
                break;
            case 'drafts':
                $where = 'author_status eq 0';
                $where .= ' and from_id eq ' . xarUser::getVar('id');
                $where .= ' and author_delete eq ' . Defines::NOTDELETED;
                $data['fieldlist'] = 'to_id,subject,time,author_status';
                xarTpl::setPageTitle(xarML('Drafts'));
                $data['input_title']    = xarML('Drafts');
                break;
        }

        $sort = xarMod::apiFunc('messages', 'admin', 'sort', [
            //how to sort if the URL or config say otherwise...
            //'object' => $object,
            'sortfield_fallback' => 'time',
            'ascdesc_fallback' => 'DESC',
        ]);
        $data['sort'] = $sort;

        $total = DataObjectFactory::getObjectList([
            'name' => 'messages_messages',
            'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
            'numitems' => null,
            'where' => $where,
        ]);
        $data['total'] = count($total->getItems());

        $list = DataObjectFactory::getObjectList([
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
            if (!xarVar::fetch('away', 'str', $away, null, xarVar::NOT_REQUIRED)) {
                return;
            }
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
