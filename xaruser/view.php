<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */

sys::import('modules.messages.xarincludes.defines');

function messages_user_view( )
{
    if (!xarSecurityCheck('ReadMessages')) return;

    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', XARVAR_NOT_REQUIRED)) return;
    xarSession::setVar('messages_currentfolder', $folder);

    xarVarFetch('startnum', 'int', $startnum , 1 , XARVAR_NOT_REQUIRED);

    $data['startnum'] = $startnum ;
    
    //Psspl:Added the code for paging
    $link_data = xarModAPIFunc('messages', 
                               'user', 
                               'get_prev_next_link',
                                array('folder'   => $folder,
                                      'startnum' => $startnum));
    
    $data = array_merge($data,$link_data);

    //Psspl:Added the code for configuring the user-menu
//    $data['allow_newpm'] = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
    
    $messages = xarModAPIFunc('messages', 'user', 'getall', array('folder' => $folder,
                                                                    'startnum' => $startnum));    
    if (is_array($messages)) {

        //Psspl:Comment the code for sorting messages.
        //krsort($messages);

        $data['messages']                = $messages;
        
        //Psspl:Added the code for read unread messages.
        /*$messages_inbox = xarModAPIFunc('messages', 'user', 'getall', array('folder' => 'inbox'));
        $unread = 0;
        foreach($messages_inbox as $k => $message) {
            if($message['status_alt'] == 'unread') {
               $unread++;
            }
        } 
        */ 
    } else {
        $list = array();
    }
    if (xarUserIsLoggedIn()) {
        if (!xarVarFetch('away','str',$away,null,XARVAR_NOT_REQUIRED)) return;
        if (isset($away)) {
            xarModUserVars::set('messages','away_message',$away);
        }
        $data['away_message'] = xarModUserVars::get('messages','away_message');
    } else {
        $data['away_message'] = '';
    }

    $data['folder'] = $folder;

    return $data;
}

?>
