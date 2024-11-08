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
/**
 * This is a standard function to modify and update the configuration parameters of the
 * module
 */
function messages_admin_modifyconfig(array $args = [], $context = null)
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurity::check('AdminMessages')) {
        return;
    }

    $data = [];
    $data['groups'] = xarMod::apiFunc('roles', 'user', 'getallgroups');

    // Check if this template has been submitted, or if we just got here
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'messages']);
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls');
    $data['module_settings']->getItem();

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.factory');
    // Get the object we'll be working with for content-specific configuration
    $data['object'] = DataObjectFactory::getObject(['name' => 'messages_module_settings']);
    // Get the appropriate item of the dataobject. Using itemid 0 (not passing an itemid parameter) is standard convention
    $data['object']->getItem();

    // Run the appropriate code depending on whether the template was submitted or not
    switch (strtolower($phase)) {
        case 'modify':
        default:
            break;

        case 'update':
            # --------------------------------------------------------
            #
            # Confirm the authorisation code
            #
            # This is done to make sure this was submitted by a human to this module and not called programatically
            # However in this case we need to disable ît because although the common configuration below is
            # handled in this module, the module-specific part at the end of the page is sent to be done by
            # the dynamicdata module, where the same check is done. Since both checks cannot simultaneously
            # be passed, (the act of checking resets the check) the one below is disabled in this example.
            #
            //if (!xarSec::confirmAuthKey()) return;

            # --------------------------------------------------------
            #
            # Updating the common configuration
            #
            # We do this before anything else, because the checkInput error checking might
            # decide something is not right and redisplay the form. In such a case we don't want to
            # already have saved data. The module-specific code active at the bottom of the page does no
            # such error checking. This is not really a problem because you can't really get input errors
            # when you're dealing with checkboxes (they're either checked or they aren't)
            #

            $isvalid = $data['module_settings']->checkInput();
            if (!$isvalid) {
                $data['context'] = $context;
                return xarTpl::module('messages', 'admin', 'modifyconfig', $data);
            } else {
                $itemid = $data['module_settings']->updateItem();
            }

            foreach ($data['groups'] as $key => $value) {
                //$property = DataPropertyMaster::getProperty(array('name' => 'roleid_'.$key));
                //$property->checkInput('roleid_'.$key);
                $the_key = $value['id'];
                if (!xarVar::fetch('roleid_' . $the_key, 'array', $roleid_[$the_key], 0, xarVar::NOT_REQUIRED)) {
                    return;
                }
                xarModItemVars::set('messages', "allowedsendmessages", serialize($roleid_[$the_key]), $the_key);
            }

            # --------------------------------------------------------
            #
            # Updating the content configuration without using DD
            #
            # In this case we get each value from the template and set the appropriate modvar
            # Note that in this case we are setting modvars, whereas in the other two ways below we are actually
            # setting moditemvars with the itemid = 1
            # This can work because in the absence of a moditemvar the corresponding modvar is returned
            # What we cannot do however is mix these methods, because once we have a moditemvar defined, we can
            # no longer default back to the modvar (unless called specifically as below).
            #
            /*
                // Get parameters from whatever input we need.  All arguments to this
                // function should be obtained from xarVar::fetch(), getting them
                // from other places such as the environment is not allowed, as that makes
                // assumptions that will not hold in future versions of Xaraya
                if (!xarVar::fetch('bold', 'checkbox', $bold, false, xarVar::NOT_REQUIRED)) return;

                // Confirm authorisation code.  This checks that the form had a valid
                // authorisation code attached to it.  If it did not then the function will
                // proceed no further as it is possible that this is an attempt at sending
                // in false data to the system
                if (!xarSec::confirmAuthKey()) return;

                xarModVars::set('content', 'bold', $bold);
            */

            # --------------------------------------------------------
            #
            # Updating the content configuration with DD class calls
            #
            # This is the same as the examples of creating, modifying or deleting an item
            # Note that, as in those examples, this code could be placed in the modifyconfig.php file
            # and this file dispensed with.
            #

            // Load the DD master object class. This line will likely disappear in future versions
            sys::import('modules.dynamicdata.class.objects.factory');
            // Get the object we'll be working with
            $object = DataObjectFactory::getObject(['name' => 'messages_module_settings']);
            // Get the data from the form
            $isvalid = $object->checkInput();
            // Update the item with itemid = 0


            $item = $object->updateItem(['itemid' => 0]);

            xarController::redirect(xarController::URL('messages', 'admin', 'modifyconfig'), null, $context);

            # --------------------------------------------------------
            #
            # Updating the content configuration with a DD API call
            #
            # This is a special case using the dynamicdata_admin_update function.
            # It depends on finding all the relevant information on the template we are submitting, i.e.
            # - objectid of the content_module_settings object
            # - itemid (1 in this case)
            # - returnurl, telling us where to jump to after update
            #
            # This needs to be the last thing happening on this page because it redirects. Code below
            # this point will not execute

            if (!xarMod::guiFunc('messages', 'admin', 'update')) {
                return;
            }

            break;
    }

    // Return the template variables defined in this function
    return $data;
}
