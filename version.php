<?php

/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 * @author Ryan Walker
 */

namespace Xaraya\Modules\Messages;

class Version
{
    /**
     * Get module version information
     *
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'name' => 'messages',
            'id' => '6',
            'version' => '1.9.3',
            'displayname' => 'Messages',
            'description' => 'Xaraya Messages module',
            'credits' => 'docs/credits.txt',
            'help' => 'docs/help.txt',
            'changelog' => 'docs/changelog.txt',
            'license' => 'docs/license.txt',
            'official' => 1,
            'author' => 'XarayaGeek',
            'contact' => 'http://www.XarayaGeek.com/',
            'admin' => 1,
            'user' => 1,
            'class' => 'Admin',
            'category' => 'Content',
            'namespace' => 'Xaraya\\Modules\\Messages',
            'twigtemplates' => true,
            'dependencyinfo'
             => [
                 0
                  => [
                      'name' => 'Xaraya Core',
                      'version_ge' => '2.4.1',
                  ],
             ],
        ];
    }
}
