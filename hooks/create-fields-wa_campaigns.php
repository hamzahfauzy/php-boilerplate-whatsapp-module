<?php

$role = get_role(auth()->id);

$fields['contacts'] = [
    'label' => 'Contact',
    'type' => 'options-obj:wa_contacts,id,name',
    'attr' => [
        'multiple' => 'multiple'
    ]
];

unset($fields['user_id']);

if($role->role_id != 1)
{
    unset($fields['user_id']);
    $fields['device_id']['type'] .= '|user_id,'.auth()->id;
    $fields['contact_id']['type'] .= '|user_id,'.auth()->id;
}

// unset($fields['start_at']);
// unset($fields['finish_at']);

$fields['content']['attr'] = [
    'class' => 'form-control select2-search__field',
    'placeholder' => "NB : variabel yang tersedia : {contact.name}, {contact.phone}"
];

$fields['import_contacts'] = [
    'label' => 'or Import Contact (<a href="'.asset('assets/whatsapp/format.xlsx').'">Download</a>)',
    'type' => 'file'
];

return $fields;