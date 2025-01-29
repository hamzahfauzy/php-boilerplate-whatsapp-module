<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$data['created_by'] = auth()->id;
$data['user_id'] = auth()->id;

$import = $_FILES['import_contacts'];
if(isset($import['name']) && !empty($import['name']))
{
    $allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    
    if (!in_array($import['type'], $allowedTypes)) {
        set_flash_msg(['error'=> 'Silakan unggah file Excel']);
    }
    else
    {

        $fileExtension = pathinfo($import['name'], PATHINFO_EXTENSION);
        
        if (in_array($fileExtension, ['xls', 'xlsx'])) {
            $spreadsheet = IOFactory::load($import['tmp_name']);
            $sheet = $spreadsheet->getActiveSheet();
    
            foreach ($sheet->getRowIterator(2) as $row) {
                $name = $sheet->getCell('B' . $row->getRowIndex())->getFormattedValue();
                $phone = $sheet->getCell('C' . $row->getRowIndex())->getFormattedValue();
                
                // check contacts
                $contact = $db->single('wa_contacts', ['phone' => $phone, 'user_id' => $data['user_id']]);
                if(!$contact)
                {
                    $contact = $db->insert('wa_contacts', [
                        'name' => $name,
                        'phone' => $phone,
                        'user_id' => $data['user_id']
                    ]);
                }
                else
                {
                    $db->update('wa_contacts', ['name' => $name], ['id' => $contact->id]);
                }

                $_POST['contacts'][] = $contact->id;
            }
        }
    }
}

if(!empty($data['contacts']))
{
    $_POST['contacts'] = $data['contacts'];
}

if(count($_POST['contacts']) == 0)
{
    redirectBack(['error' => 'Contact tidak boleh kosong','old' => $data]);
    die;
}

unset($data['contacts']);

if(empty($data['scheduled_at']))
{
    unset($data['scheduled_at']);
}