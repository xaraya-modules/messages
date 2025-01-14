<?php

/**
 * @package modules\messages
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Messages\AdminGui;


use Xaraya\Modules\Messages\AdminGui;
use Xaraya\Modules\MethodClass;
use xarSecurity;
use xarTpl;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages admin overview function
 * @extends MethodClass<AdminGui>
 */
class OverviewMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Overview function that displays the standard Overview page
     */
    public function __invoke(array $args = [])
    {
        /* Security Check */
        if (!$this->checkAccess('AdminMessages')) {
            return;
        }

        $data = [];

        $data['context'] = $this->getContext();
        return xarTpl::module('messages', 'admin', 'overview', $data);
    }
}
