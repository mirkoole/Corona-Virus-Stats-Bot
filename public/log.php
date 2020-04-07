<?php

function log_request($text, $chat_id)
{
    global $db;

    if ($db->connect_errno != 0) return;
    
    $stmt = $db->prepare("INSERT INTO `requests`  (`message`, `chat_id`) VALUES (?, ?);");
    $stmt->bind_param("si", $text, $chat_id);
    $stmt->execute();

}
