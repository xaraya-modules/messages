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
use xarServer;
use xarModVars;
use xarVar;
use xarTpl;
use xarController;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages admin main function
 * @extends MethodClass<AdminGui>
 */
class MainMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Messages Module
     * @package modules
     * @subpackage messages module
     * @copyright (C) copyright-placeholder
     * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
     * @link http://xaraya.com/index.php/release/6.html
     * @author XarayaGeek
     * @author Ryan Walker
     * @author Marc Lutolf <mfl@netspan.ch>
     * @see AdminGui::main()
     */
    public function __invoke(array $args = [])
    {
        if (!$this->sec()->checkAccess('AdminMessages')) {
            return;
        }

        $samemodule = xarController::isRefererSameModule();

        $data = [];

        if (((bool) $this->mod('modules')->getVar('disableoverview') == false) || $samemodule) {
            $this->var()->find('tab', $data['tab'], 'str', '');
            $data['context'] = $this->getContext();
            return $this->mod()->template('overview', $data);
        } else {
            $this->ctl()->redirect($this->mod()->getURL('admin', 'modifyconfig'));
            return true;
        }
    }
}
