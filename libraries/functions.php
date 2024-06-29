<?php

function compileMessageContent($data, $content)
{
    foreach([
        'name' => '{contact.name}',
        'phone' => '{contact.phone}'
    ] as $key => $param)
    {
        $content = str_replace($param, $data[$key], $content);
    }

    return $content;
}