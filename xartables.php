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
 */
function messages_xartables(?string $prefix = null)
{
    // Initialise table array
    $xartable = [];
    $prefix ??= xarDB::getPrefix();

    // Name for template database entities
    $messages_table     = $prefix . '_messages';

    // Table name
    $xartable['messages']   = $messages_table;

    // Return table information
    return $xartable;
}
