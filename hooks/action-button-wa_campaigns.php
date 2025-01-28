<?php

return '<a href="'.routeTo('crud/index', ['table' => 'wa_campaign_items', 'filter' => ['campaign_id' => $data->id]]).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Detail</a> ';