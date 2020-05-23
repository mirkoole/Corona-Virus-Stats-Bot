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
                "text" => "World Status",
            ],
            [
                "text" => "Magic Button",
            ],
        ],
        [
            [
                "text" => "Germany Status",
            ],
            [
                "text" => "Germany History",
            ],
        ],
        [
            [
                "text" => "Sweden Status",
            ],
            [
                "text" => "Sweden History",
            ],
        ],
    ];

    if ($text === "/start") {
        $client->sendMessage($chat_id, "Hello! :)");
        $client->sendMessage($chat_id, "Press a button to use me.", null, null, null, null, $menu);
    }


    if ($text === "World Status") {
        $world_status_data = get_world_status_data();
        $result = get_world_status($world_status_data);
        $client->sendMessage($chat_id, $result, 'HTML', null, null, null, $menu);
    }

    if ($text === "Magic Button") {
        $client->sendMessage($chat_id, " 🎩🐇  ");
        $next = 'Features (coming soon):' . PHP_EOL . '- visual stats ' . PHP_EOL . '- more countries' . PHP_EOL . '- world peace';
        $client->sendMessage($chat_id, $next, null, null, null, null, $menu);
    }

    if ($text === "Germany History") {
        country_history_wrapper('germany', $client, $chat_id, $menu);
    }

    if ($text === "Sweden History") {
        country_history_wrapper('sweden', $client, $chat_id, $menu);
    }

    if ($text === "Germany Status") {
        country_status_wrapper('DE', 'germany', $client, $chat_id, $menu);
    }

    if ($text === "Sweden Status") {
        country_status_wrapper('SE', 'sweden', $client, $chat_id, $menu);
    }

    if ($text === "USA Status") {
        country_status_wrapper('US', 'united-states', $client, $chat_id, $menu);
    }

    if ($text === "USA History") {
        country_history_wrapper('united-states', $client, $chat_id, $menu);
    }

    if (LOGGING_ENABLED) {
        log_request($text, $chat_id);
    }

}

function country_history_wrapper($country, $client, $chat_id, $menu)
{
    $result = get_country_history($country, 30);
    $client->sendMessage($chat_id, $result, 'HTML', null, null, null, $menu);
    if (substr($result, 0, 6) != 'Sorry') {
        $client->sendPhoto($chat_id, "https://codepunks.net/telegrambot/corona/public/image.php?v=2&country=$country&date=" . date("y-m-d-H"), null, null, null, null, $menu);
    }
}

function country_status_wrapper($countrycode, $country, $client, $chat_id, $menu)
{
    $result = get_country_status($countrycode);
    $client->sendMessage($chat_id, $result, 'HTML', null, null, null, $menu);
    $client->sendMessage($chat_id, "Our data source is missing data on some dates. We are sorry.", 'HTML', null, null, null, $menu);
    if (substr($result, 0, 6) != 'Sorry') {
        $client->sendPhoto($chat_id, "https://codepunks.net/telegrambot/corona/public/image_active.php?v=2&country=$country&date=" . date("y-m-d-H"), null, null, null, null, $menu);
    }
}
