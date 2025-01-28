<?php

$role = get_role(auth()->id);

if($role->role_id != 1)
{
    unset($fields['user_id']);
}

return $fields;