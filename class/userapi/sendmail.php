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


use Xaraya\Modules\Messages\UserApi;
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
 * @extends MethodClass<UserApi>
 */
class SendmailMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Send an email to a message recipient
     * @author Ryan Walker (ryan@webcommunicate.net)
     * @param array<mixed> $args
     * @var int $id the id of the message
     * @var int $to_id the uid of the recipient
     * @return true
     * @see UserApi::sendmail()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        $msgurl = $this->mod()->getURL( 'user', 'display', ['id' => $id]);
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
        $subject = $this->mod()->template('email-subject', $data);
        $body = $this->mod()->template('email-body', $data);
        $msgdata['subject'] = $subject;
        $msgdata['message']  = $body;

        $sendmail = xarMod::apiFunc('mail', 'admin', 'sendmail', $msgdata, $this->getContext());
        return true;
    }
}
