<?php

    function messages_user_reply()
    {
        if (!xarSecurityCheck('AddMessages')) return;

        if (!xarVarFetch('object', 'str', $object, 'messages_messages', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('id', 'int:1', $id, 0, XARVAR_NOT_REQUIRED)) return;
        xarResponseRedirect(xarModURL('messages','user','new',array('id' => $id, 'action' => 'reply', 'object' => $object)));
        return true;
    }

?>