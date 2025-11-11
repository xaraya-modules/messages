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

/**
 * messages userapi decode_shorturl function
 * @extends MethodClass<UserApi>
 */
class DecodeShorturlMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserApi::decodeShorturl()
     */

    public function __invoke(array $params = [])
    {
        if ($params[0] != 'messages') {
            return;
        }

        if (empty($params[1])) {
            $params[1] = '';
        }

        if (is_numeric($params[1])) {
            return ['display', ['id' => $params[1]]];
        }

        switch ($params[1]) {
            case 'new':
                $args = [];
                if (isset($params[2])) {
                    $args['to_id'] = $params[2];
                }
                if (isset($params[3]) && $params[3] == 'opt') {
                    $args['opt'] = true;
                }
                return ['new', $args];

            case 'modify':
                return ['modify', ['id' => $params[2]]];

            case 'reply':
                return ['reply', ['replyto' => $params[2]]];

            case 'markunread':
                return ['markunread', ['id' => $params[2]]];

            case 'sent':
                return ['view', ['folder' => 'sent']];

            case 'drafts':
                return ['view', ['folder' => 'drafts']];

            case 'delete':
                return ['delete', ['id' => $params[2]]];

            default:
            case 'inbox':
                return ['view', []];
        }
    }
}
