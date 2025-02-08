<?php

$having = "";

if($filter)
{
    $filter_query = [];
    foreach($filter as $f_key => $f_value)
    {
        $filter_query[] = "$f_key = '$f_value'";
    }

    $filter_query = implode(' AND ', $filter_query);

    $having = (empty($having) ? 'HAVING ' : ' AND ') . $filter_query;
}

$where = $where ." ". $having;

$query = "SELECT $this->table.id, $this->table.campaign_id, $this->table.message_id, $this->table.response, $this->table.item_status, CONCAT(substring(wa_messages.content,1, 100),'...') content, CONCAT(wa_contacts.name,' (',wa_contacts.phone,')') contact_name, wa_contacts.code contact_code FROM $this->table JOIN wa_messages ON wa_messages.id = $this->table.message_id JOIN wa_contacts ON wa_contacts.id = wa_messages.contact_id $where";
$this->db->query = $query . " ORDER BY ".$col_order." ".$order[0]['dir']." LIMIT $start,$length";
$data  = $this->db->exec('all');

$this->db->query = $query;
$total = $this->db->exec('exists');

return compact('data', 'total');