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

sys::import('modules.messages.xarincludes.defines');

function messages_user_main(array $args = [], $context = null)
{
    xarController::redirect(xarController::URL('messages', 'user', 'view'), null, $context);
    return;
}
