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
 * @extends UserGuiClass<Module>
 */
class UserGui extends UserGuiClass
{
    /**
     * User main GUI function
     * @param array<string, mixed> $args
     * @return array<mixed>
     */
    public function main(array $args = [])
    {
        $args['description'] ??= 'Description of messages';

        // Pass along the context for xarTpl::module() if needed
        $args['context'] ??= $this->getContext();
        return $args;
    }
}
