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
$modversion['name']         = 'messages';
$modversion['id']           = '6';
$modversion['version']      = '1.9.3';
$modversion['displayname']  = xarMLS::translate('Messages');
$modversion['description']  = 'Xaraya Messages module';
$modversion['credits']      = 'docs/credits.txt';
$modversion['help']         = 'docs/help.txt';
$modversion['changelog']    = 'docs/changelog.txt';
$modversion['license']      = 'docs/license.txt';
$modversion['official']     = 1;
$modversion['author']       = 'XarayaGeek';
$modversion['contact']      = 'http://www.XarayaGeek.com/';
$modversion['admin']        = 1;
$modversion['user']         = 1;
$modversion['class']        = 'Admin';
$modversion['category']     = 'Content';
$modversion['namespace']      = 'Xaraya\Modules\Messages';
$modversion['twigtemplates']  = true;
$modversion['dependencyinfo'] = [
    0 => [
        'name' => 'Xaraya Core',
        'version_ge' => '2.4.1',
    ],
];
