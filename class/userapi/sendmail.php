<?php

/**
 * @package modules\messages
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Messages\UserApi;

use Xaraya\Modules\MethodClass;
use xarController;
use xarUser;
use xarTpl;
use xarMod;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages userapi sendmail function
 */
class SendmailMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Send an email to a message recipient
     * @author Ryan Walker (ryan@webcommunicate.net)
     * @param array $args
     * with
     *     int	$id the id of the message
     *     int	$to_id the uid of the recipient
     * @return true
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        $msgurl = xarController::URL('messages', 'user', 'display', ['id' => $id]);
        $from_name = xarUser::getVar('name');
        $msgdata['info'] = xarUser::getVar('email', $to_id);
        $msgdata['name'] = xarUser::getVar('name', $to_id);

        $data['msgurl'] = $msgurl;
        $data['id'] = $id; // message id
        $data['from_id'] = xarUser::getVar('id');
        $data['from_name'] = $from_name;
        $data['to_id'] = $to_id;
        $data['to_name'] = $msgdata['name'];
        $data['to_email'] = $msgdata['info'];
        $data['context'] ??= $this->getContext();
        $subject = xarTpl::module('messages', 'user', 'email-subject', $data);
        $body = xarTpl::module('messages', 'user', 'email-body', $data);
        $msgdata['subject'] = $subject;
        $msgdata['message']  = $body;

        $sendmail = xarMod::apiFunc('mail', 'admin', 'sendmail', $msgdata, $this->getContext());
        return true;
    }
}
