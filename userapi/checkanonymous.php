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

sys::import('xaraya.modules.method');

/**
 * messages userapi checkanonymous function
 * @extends MethodClass<UserApi>
 */
class CheckanonymousMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Check to see if a message was anonymous
     * @param array<mixed> $args
     * @var int $id the message id
     * @return bool
     * @see UserApi::checkanonymous()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        if ($id == 0) {
            return false;
        }

        sys::import('modules.dynamicdata.class.objects.factory');

        $object = $this->data()->getObject(['name' => 'messages_messages']);
        $object->getItem(['itemid' => $id]);
        $postanon = $object->properties['postanon']->value;

        return $postanon;
    }
}
