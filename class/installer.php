<?php

/**
 * Handle module installer functions
 *
 * @package modules\messages
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Messages;

use Xaraya\Modules\InstallerClass;
use xarDB;
use xarMod;
use xarModVars;
use xarPrivileges;
use xarMasks;
use Query;
use sys;
use Exception;

sys::import('xaraya.modules.installer');

/**
 * Handle module installer functions
 *
 * @todo add extra use ...; statements above as needed
 * @todo replaced messages_*() function calls with $this->*() calls
 * @extends InstallerClass<Module>
 */
class Installer extends InstallerClass
{
    /**
     * Configure this module - override this method
     *
     * @todo use this instead of init() etc. for standard installation
     * @return void
     */
    public function configure()
    {
        $this->objects = [
            // add your DD objects here
            //'messages_object',
        ];
        $this->variables = [
            // add your module variables here
            'hello' => 'world',
        ];
        $this->oldversion = '2.4.1';
    }

    /** xarinit.php functions imported by bermuda_cleanup */

    public function init()
    {
        $q = new Query();
        $prefix = $this->db()->getPrefix();

        # --------------------------------------------------------
        #
        # Table structure for table messages
        #

        $query = "DROP TABLE IF EXISTS " . $prefix . "_messages";
        if (!$q->run($query)) {
            return;
        }
        $query = "CREATE TABLE " . $prefix . "_messages (
          id                integer unsigned NOT NULL auto_increment,
          from_id           integer unsigned NOT NULL default 0,
          to_id             integer unsigned NOT NULL default 0,
          time              integer unsigned NOT NULL default 0,
          from_status       tinyint unsigned NOT NULL default 0,
          to_status         tinyint unsigned NOT NULL default 0,
          from_delete       tinyint unsigned NOT NULL default 0,
          to_delete         tinyint unsigned NOT NULL default 0,
          anonpost          tinyint unsigned NOT NULL default 0,
          replyto           integer unsigned NOT NULL default 0,
          subject           varchar(254) default NULL,
          body              text,
          state             tinyint unsigned NOT NULL default 3,
          PRIMARY KEY  (id),
          KEY `messages_from_id` (`from_id`)
        )";
        if (!$q->run($query)) {
            return;
        }

        # --------------------------------------------------------
        #
        # Create DD objects
        #
        $module = 'messages';
        $objects = [
            'messages_user_settings',
            'messages_module_settings',
            'messages_messages',
        ];

        if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
            return;
        }

        $this->mod()->setVar('sendemail', false); // Note the 'e' in 'sendemail'
        $this->mod()->setVar('allowautoreply', true);
        $this->mod()->setVar('allowanonymous', false);
        $this->mod()->setVar('allowedsendmessages', serialize([]));
        $this->mod()->setVar('strip_tags', true);
        $this->mod()->setVar('send_redirect', 1);
        $this->mod()->setVar('allowusersendredirect', false);

        // not sure if the following are needed?
        $this->mod()->setVar('user_sendemail', true); // Note the 'e' in 'user_sendemail'
        $this->mod()->setVar('enable_autoreply', false);
        $this->mod()->setVar('autoreply', '');
        $this->mod()->setVar('user_send_redirect', 1);

        //$this->mod()->setVar('buddylist', 0);
        //$this->mod()->setVar('limitsaved', 12);
        //$this->mod()->setVar('limitout', 10);
        //$this->mod()->setVar('limitinbox', 10);
        //$this->mod()->setVar('smilies', false);
        //$this->mod()->setVar('allow_html', false);
        //$this->mod()->setVar('allow_bbcode', false);
        //$this->mod()->setVar('mailsubject', 'You have a new private message !');
        //$this->mod()->setVar('fromname', 'Webmaster');
        //$this->mod()->setVar('from', 'Webmaster@YourSite.com');
        //$this->mod()->setVar('inboxurl', 'http://www.yoursite.com/index.php?module=messages&type=user&func=display');
        //$this->mod()->setVar('serverpath', '/home/yourdir/public_html/modules/messages');
        //$this->mod()->setVar('away_message', '');

        # --------------------------------------------------------
        #
        # Set up configuration modvars (general)
        #

        $module_settings = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'messages']);
        $module_settings->initialize();


        /*
         * REGISTER BLOCKS
         */

        if (!xarMod::apiFunc(
            'blocks',
            'admin',
            'register_block_type',
            ['modName'  => 'messages',
                'blockType' => 'newmessages', ]
        )) {
            return;
        }
        /*
         * REGISTER HOOKS
         */

        // Hook into the roles module (Your Account page)
        xarMod::apiFunc(
            'modules',
            'admin',
            'enablehooks',
            [
                'hookModName'       => 'roles','callerModName'    => 'messages', ]
        );

        /*
             // Hook into the Dynamic Data module
            xarMod::apiFunc(
                'modules'
                ,'admin'
                ,'enablehooks'
                ,array(
                    'hookModName'       => 'dynamicdata'
                    ,'callerModName'    => 'messages'));

            $objectid = xarMod::apiFunc('dynamicdata','util','import',
                                      array('file' => 'modules/messages/messages.data.xml'));
            if (empty($objectid)) return;
            // save the object id for later
            $this->mod()->setVar('objectid',$objectid);
        */

        # --------------------------------------------------------
        #
        # Create privilege instances
        #

        xarPrivileges::defineInstance('messages', 'Block', []);
        xarPrivileges::defineInstance('messages', 'Item', []);

        /*
         * REGISTER MASKS
         */

        // Register Block types (this *should* happen at activation/deactivation)
        //xarBlockTypeRegister('messages', 'incomming');
        xarMasks::register('ReadMessagesBlock', 'All', 'messages', 'Block', 'All', 'ACCESS_OVERVIEW');
        xarMasks::register('ViewMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_OVERVIEW');
        xarMasks::register('ReadMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_READ');
        xarMasks::register('EditMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_EDIT');
        xarMasks::register('AddMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_ADD');
        xarMasks::register('DenyReadMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_NONE');
        xarMasks::register('ManageMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_DELETE');
        xarMasks::register('AdminMessages', 'All', 'messages', 'Item', 'All', 'ACCESS_ADMIN');
        /*********************************************************************
        * Enter some default privileges
        * Format is
        * register(Name,Realm,Module,Component,Instance,Level,Description)
        *********************************************************************/
        xarPrivileges::register('ManageMessages', 'All', 'messages', 'All', 'All', 'ACCESS_DELETE', $this->ml('Delete access to messages'));
        xarPrivileges::register('DenyReadMessages', 'All', 'messages', 'All', 'All', 'ACCESS_NONE', $this->ml('Deny access to messages'));
        /*********************************************************************
        * Assign the default privileges to groups/users
        * Format is
        * assign(Privilege,Role)
        *********************************************************************/

        xarPrivileges::assign('ManageMessages', 'Users');
        xarPrivileges::assign('DenyReadMessages', 'Everybody');

        // Initialisation successful
        return true;
    }

    /**
     * upgrade the messages module from an old version
     * This function can be called multiple times
     */
    public function upgrade($oldversion)
    {
        // Upgrade dependent on old version number
        switch ($oldversion) {
            case '0.5':
                break;
            case '1.0':
                // Code to upgrade from version 1.0 goes here
                break;
            case '1.8':
            case '1.8.0':
                // compatability upgrade
                $this->mod()->setVar('away_message', '');
                // no break
            case '1.8.1':
                // nothing to do for this rev
                break;
            case '1.9':
            case '1.9.0':

                xarMod::apiFunc('dynamicdata', 'util', 'import', [
                    'file' => sys::code() . 'modules/messages/xardata/messages_module_settings-def.xml',
                    'overwrite' => true,
                ]);

                // new module vars
                $this->mod()->setVar('allowautoreply', true);
                $this->mod()->setVar('send_redirect', true);
                $this->mod()->setVar('allowusersendredirect', false);

                xarMod::apiFunc('dynamicdata', 'util', 'import', [
                    'file' => sys::code() . 'modules/messages/xardata/messages_user_settings-def.xml',
                    'overwrite' => true,
                ]);

                // new user vars
                $this->mod()->setVar('user_send_redirect', 1);

                break;
            case '2.0.0':
                // Code to upgrade from version 2.0 goes here
                break;
        }

        // Update successful
        return true;
    }

    /**
     * delete the messages module
     * This function is only ever called once during the lifetime of a particular
     * module instance
     */
    public function delete()
    {
        /*
         * UNREGISTER BLOCKS
         */

        if (!xarMod::apiFunc(
            'blocks',
            'admin',
            'unregister_block_type',
            ['modName'  => 'messages',
                'blockType' => 'newmessages', ]
        )) {
            return;
        }

        //	xarPrivileges::removeModule('messages');

        return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => 'messages']);
    }
}
