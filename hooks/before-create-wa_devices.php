<?php

$data['created_by'] = auth()->id;

// not super admin
if(get_role(auth()->id)->role_id != 1)
{
    $data['user_id'] = auth()->id;
}