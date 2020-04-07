<?php

function logRequest($text, $chat_id)
{
    global $db;

    if ($db->connect_errno == 0) {

        // emoji support
        $db->set_charset('utf8mb4');

        $text = $db->real_escape_string($text);
        $stmt = $db->prepare("INSERT INTO `requests`  (`message`, `chat_id`) VALUES (?, ?)");
        $stmt->bind_param("s", $text);
        $stmt->bind_param("i", $chat_id);
        $stmt->execute();
    }

}
