<?php
/**Psspl:Added the code for allow the members of a
 * given group to only send messages to another group.
 * 
 * 
 */
 
sys::import('modules.messages.xarincludes.defines');

    function messages_userapi_get_sendtousers( $args )
    {
        $sendtogroups = xarModAPIFunc('messages','user','get_sendtogroups',$args);
        ;  
        // Get the uses these allowed groups contain
        sys::import('xaraya.structures.query');
        $xartable = xarDB::getTables();
        $q = new Query('SELECT');
        $q->addtable($xartable['roles'], 'r');
        $q->addtable($xartable['rolemembers'],'rm');
        $q->join('r.id', 'rm.role_id');
        
        $q->addfield('r.id');
        $q->addfield('r.name');
        $q->addfield('r.uname');
        $q->eq('r.state', xarRoles::ROLES_STATE_ACTIVE);
        $q->ne('r.email', '');
        $q->ne('r.name' , 'Myself');
        $q->eq('r.itemtype' , xarRoles::ROLES_USERTYPE);//check for user
            
        /*Psspl:get the selected groups only*/
        $user_c = array();
        foreach ($sendtogroups as $key => $value){
            $user_c[]=$q->peq('rm.parent_id' , $value);
        }
        $q->qor($user_c); //use OR
                
        //function for echo the query.
        //$q->qecho();
            
        if(!$q->run()) return;
    
        $users = $q->output();

        return $users;
    }
?>