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
function messages_userapi_count_sent()
{

    $total = xarModAPIFunc('comments',
                            'user',
                            'get_author_count',
                             array('modid'  => xarModGetIDFromName('messages'),
                                   'status' => 2,
                                   'author' => xarSession::getVar('role_id')));

    return $total;
}
?>
