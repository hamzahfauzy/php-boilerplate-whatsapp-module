<?php

unset($fields['phone']);
unset($fields['status']);

// not super admin
if(get_role(auth()->id)->role_id != 1)
{
    unset($fields['user_id']);
}

return $fields;