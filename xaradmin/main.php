<?php
/**
 * Messages Module
 *
 * @package modules
 * @subpackage messages module
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 * @author Ryan Walker
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function messages_admin_main(array $args = [], $context = null)
{
    if (!xarSecurity::check('AdminMessages')) {
        return;
    }

    $refererinfo =  xarController::$request->getInfo(xarServer::getVar('HTTP_REFERER'));
    $info =  xarController::$request->getInfo();
    $samemodule = $info[0] == $refererinfo[0];

    $data = [];

    if (((bool) xarModVars::get('modules', 'disableoverview') == false) || $samemodule) {
        if (!xarVar::fetch('tab', 'str', $data['tab'], '', xarVar::NOT_REQUIRED)) {
            return;
        }
        $data['context'] = $context;
        return xarTpl::module('messages', 'admin', 'overview', $data);
    } else {
        xarController::redirect(xarController::URL('messages', 'admin', 'modifyconfig'), null, $context);
        return true;
    }
}
