<?php

/**
 * @package modules\messages
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 **/

namespace Xaraya\Modules\Messages;

use Xaraya\Modules\UserGuiClass;
use sys;

sys::import('xaraya.modules.usergui');
sys::import('modules.messages.class.userapi');

/**
 * Handle the messages user GUI
 *
 * @method mixed delete(array $args)
 * @method mixed display(array $args)
 * @method mixed main(array $args)
 * @method mixed markunread(array $args)
 * @method mixed modify(array $args)
 * @method mixed new(array $args)
 * @method mixed reply(array $args)
 * @method mixed view(array $args)
 * @extends UserGuiClass<Module>
 */
class UserGui extends UserGuiClass
{
    // ...
}
