<?php

$isSuperAdmin = get_role(auth()->id)->role_id == 1;

if(!$isSuperAdmin)
{
    $fields['device_id']['type'] .= '|user_id,'.auth()->id;
}

$fields['keyword']['attr'] = [
    'class' => 'form-control select2-search__field'
];

$fields['content']['attr'] = [
    'class' => 'form-control select2-search__field'
];

return $fields;