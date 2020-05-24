<?php

require_once __DIR__ . "/../vendor/autoload.php";

require_once 'settings.php';
require_once 'log.php';
require_once 'helper.php';
require_once 'restapi.php';
require_once 'stats.php';

use TuriBot\Client;


if (!isset($_GET["api"])) {
    exit();
}

$client = new Client($_GET["api"], false);

$update = $client->getUpdate();

if (!isset($update)) {
    exit('json error');
}

if (isset($update->message) or isset($update->edited_message)) {

    $chat_id = $client->easy->chat_id;
    $message_id = $client->easy->message_id;
    $text = $client->easy->text;

    $menu["keyboard"] = [
        [
            [
                "text" => "World 🚑",
            ],
            [
                "text" => "Germany 🚑",
            ],
            [
                "text" => "Germany 🗓",
            ],
        ],
        [
            [
                "text" => "Sweden 🚑",
            ],
            [
                "text" => "Sweden 🗓",
            ],
            [
                "text" => "Italy 🚑",
            ],
            [
                "text" => "Italy 🗓",
            ],
            [
                "text" => "Spain 🚑",
            ],
            [
                "text" => "Spain 🗓",
            ],
        ],
        [
            [
                "text" => "Brazil 🚑",
            ],
            [
                "text" => "Brazil 🗓",
            ],
            [
                "text" => "USA 🚑",
            ],
            [
                "text" => "USA 🗓",
            ],
            [
                "text" => "Russia 🚑",
            ],
            [
                "text" => "Russia 🗓",
            ],
        ],
    ];

    if ($text === "/start") {
        $client->sendMessage($chat_id, "Hello! :)");
        $client->sendMessage($chat_id, "Press a button to use me.", null, null, null, null, $menu);
        return;
    }


    if ($text === "World 🚑") {
        $world_status_data = get_world_status_data();
        $result = get_world_status($world_status_data);
        $client->sendMessage($chat_id, $result, 'HTML', null, null, null, $menu);
        return;
    }

    if ($text === "Magic Button") {
        $client->sendMessage($chat_id, " 🎩🐇  ");
        $next = 'Features (coming soon):' . PHP_EOL . '- visual stats ' . PHP_EOL . '- more countries' . PHP_EOL . '- world peace';
        $client->sendMessage($chat_id, $next, null, null, null, null, $menu);
        return;
    }

    if ($text === "Germany 🗓") {
        country_history_wrapper('germany', 'DE', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Sweden 🗓") {
        country_history_wrapper('sweden', 'SE', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Germany 🚑") {
        country_status_wrapper('DE', 'germany', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Sweden 🚑") {
        country_status_wrapper('SE', 'sweden', $client, $chat_id, $menu);
        return;
    }

    if ($text === "USA 🚑") {
        country_status_wrapper('US', 'united-states', $client, $chat_id, $menu);
        return;
    }

    if ($text === "USA 🗓") {
        country_history_wrapper('united-states', 'US', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Italy 🚑") {
        country_status_wrapper('IT', 'italy', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Italy 🗓") {
        country_history_wrapper('italy', 'IT', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Spain 🚑") {
        country_status_wrapper('ES', 'spain', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Spain 🗓") {
        country_history_wrapper('spain', 'ES', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Brazil 🚑") {
        country_status_wrapper('BR', 'brazil', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Brazil 🗓") {
        country_history_wrapper('brazil', 'BR', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Russia 🚑") {
        country_status_wrapper('RU', 'russia', $client, $chat_id, $menu);
        return;
    }

    if ($text === "Russia 🗓") {
        country_history_wrapper('russia', 'RU', $client, $chat_id, $menu);
        return;
    }

    $client->sendMessage($chat_id, "Press a button to use me. 😏", null, null, null, null, $menu);

    if (LOGGING_ENABLED) {
        log_request($text, $chat_id);
    }

}

function country_history_wrapper($country, $countrycode, $client, $chat_id, $menu)
{
    $result = get_country_history($country, $countrycode, 30);
    $client->sendMessage($chat_id, $result, 'HTML', null, null, null, $menu);
    if (substr($result, 0, 6) != 'Sorry') {
        $client->sendPhoto($chat_id, "https://codepunks.net/telegrambot/corona/public/image.php?v=2&country=$country&date=" . date("y-m-d-H"), null, null, null, null, $menu);
    }
}

function country_status_wrapper($countrycode, $country, $client, $chat_id, $menu)
{
    $result = get_country_status($countrycode);
    $client->sendMessage($chat_id, $result, 'HTML', null, null, null, $menu);
    if (substr($result, 0, 6) != 'Sorry') {
        $client->sendPhoto($chat_id, "https://codepunks.net/telegrambot/corona/public/image_active.php?v=2&country=$country&date=" . date("y-m-d-H"), null, null, null, null, $menu);
    }
}
