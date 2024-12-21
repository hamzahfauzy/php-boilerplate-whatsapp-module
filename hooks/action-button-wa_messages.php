<?php

return '<a href="'.routeTo('whatsapp/messages/resend', ['id' => $data->id]).'" class="btn btn-sm btn-info" onclick="if(confirm(\'Apakah anda yakin akan mengirim ulang pesan?\')){return true}else{return false}"><i class="fas fa-plane"></i> Resend</a> ';