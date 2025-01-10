<?php

/**
 * @package modules\messages
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Messages\AdminApi;

use Xaraya\Modules\MethodClass;
use xarVar;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * messages adminapi sort function
 */
class SortMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Sorting
     * @author Ryan Walker
     * @return string $sort (ex. 'subject ASC');
     */
    public function __invoke(array $args = [])
    {
        // Default URL strings to look for
        $url_sortfield = 'sortfield';
        $url_ascdesc = 'ascdesc';

        extract($args);

        if (!xarVar::fetch($url_sortfield, 'isset', $sortfield, null, xarVar::DONT_SET)) {
            return '';
        }
        if (!xarVar::fetch($url_ascdesc, 'isset', $ascdesc, null, xarVar::NOT_REQUIRED)) {
            return '';
        }

        /*if (isset($object) && !isset($sortfield) && !isset($ascdesc)) {
            $config = $object->configuration;
            if (!empty($config['sort'])) {
                $sort = $config['sort'];
            }
        }*/

        if (!isset($sort)) {
            if (!isset($sortfield)) {
                $sortfield = $sortfield_fallback;
            }

            if (!isset($ascdesc)) {
                $ascdesc = $ascdesc_fallback;
            }

            $sort = $sortfield . ' ' . $ascdesc;
        }

        return $sort;
    }
}
