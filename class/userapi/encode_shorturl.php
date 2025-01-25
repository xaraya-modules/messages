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
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages userapi encode_shorturl function
 * @extends MethodClass<UserApi>
 */
class EncodeShorturlMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup * @see UserApi::encodeShorturl()
     */

    public function __invoke(array $args = [])
    {
        $func = null;
        $module = null;
        $id = null;
        $rest = [];

        foreach ($args as $name => $value) {
            switch ($name) {
                case 'module':
                    $module = $value;
                    break;
                case 'id':
                    $id = $value;
                    break;
                case 'replyto':
                    $replyto = $value;
                    break;
                case 'func':
                    $func = $value;
                    break;
                case 'to_id':
                    $to_id = $value;
                    break;
                case 'folder':
                    $folder = $value;
                    break;
                case 'opt':
                    $opt = $value;
                    break;
                default:
                    $rest[$name] = $value;
            }
        }

        // kind of a assertion :-))
        if (isset($module) && $module != 'messages') {
            return;
        }

        /*
         * LETS GO. We start with the module.
         */
        $path = '/messages';

        if (empty($func)) {
            return;
        }

        switch ($func) {
            case 'delete':
                $path .= '/delete';
                if (isset($id)) {
                    $path .= '/' . $id;
                    unset($id);
                }
                break;
            case 'markunread':
                $path .= '/markunread';
                if (isset($id)) {
                    $path .= '/' . $id;
                    unset($id);
                }
                break;
            case 'new':
                $path .= '/new';
                if (isset($to_id)) {
                    $path .= '/' . $to_id;
                    unset($to_id);
                }
                if (isset($opt) && $opt) {
                    $path .= '/opt';
                }
                break;
            case 'modify':
                $path .= '/modify';
                if (isset($id)) {
                    $path .= '/' . $id;
                    unset($id);
                }
                break;
            case 'reply':
                $path .= '/reply';
                if (isset($replyto)) {
                    $path .= '/' . $replyto;
                }
                break;
            case 'display':
                $path .= '/' . $id;
                break;
            case 'main':
            default: // main, view
                if (isset($folder)) {
                    if ($folder == 'sent') {
                        $path .= '/sent';
                    } elseif ($folder == 'drafts') {
                        $path .= '/drafts';
                    }
                } else {
                    $path .= '/inbox'; // default
                }
                break;
        }

        if (isset($id) && $func != 'display' && $func != 'reply' && $func != 'delete') {
            $rest['id'] = $id;
        }

        if (isset($replyto)) {
            $rest['replyto'] = $replyto;
        }

        if (($func == 'markunread' || $func == 'display') && isset($folder)) {
            $rest['folder'] = $folder;
        }

        $add = [];
        foreach ($rest as $key => $value) {
            if (isset($rest[$key])) {
                $add[] =  $key . '=' . $value;
            }
        }

        if (count($add) > 0) {
            $path = $path . '?' . implode('&', $add);
        }

        return $path;
    }
}
