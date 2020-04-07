<?php

function logRequest($text, $chat_id)
{
    global $db;

    if ($db->connect_errno == 0) {

        // emoji support
        $db->set_charset('utf8mb4');

        $text = $db->real_escape_string($text);

        $db->query("INSERT INTO `requests` (`message`, `chat_id`) VALUES ('$text', '$chat_id');");

    }

}
