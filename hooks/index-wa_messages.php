<?php
$role = get_role(auth()->id);
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

if($role->role_id != 1)
{
    // get user group : SELECT group_id FROM exam_group_member WHERE user_id = auth()->id
    $where = (empty($where) ? 'WHERE ' : $where .' AND ')  . " device_id IN (SELECT id FROM wa_devices WHERE user_id = ".auth()->id.")";
}

$where = $where ." ". $having;
$baseQuery = "SELECT id, device_id, template_id, contact_id, CONCAT(substring(content,1, 100),'...') content, record_type, status, created_at  FROM $this->table $where ORDER BY ".$col_order." ".$order[0]['dir'];
$db->query = $baseQuery." LIMIT $start,$length";

$data  = $this->db->exec('all');

$this->db->query = $baseQuery;
$total = $this->db->exec('exists');

return compact('data','total');