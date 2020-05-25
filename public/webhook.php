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
                "text" => "🌎🌍🌏",
            ],
            [
                "text" => "🇩🇪",
            ],
            [
                "text" => "🇬🇧",
            ],
        ],
        [
            [
                "text" => "🇸🇪",
            ],
            [
                "text" => "🇮🇹",
            ],
            [
                "text" => "🇪🇸",
            ],
        ],
        [
            [
                "text" => "🇧🇷",
            ],
            [
                "text" => "🇺🇸",
            ],
            [
                "text" => "🇷🇺",
            ],
        ],
    ];

    if (LOGGING_ENABLED) {
        log_request($text, $chat_id);
    }

    if ($text === "/start") {
        $client->sendMessage($chat_id, "Hello! 🙂");
        $client->sendMessage($chat_id, "Press a button to use me.", null, null, null, null, $menu);
        return;
    }

    if ($text === "🌎🌍🌏" || $text === "🌎" || $text === "🌍" || $text === "🌏") {
        $client->sendMessage($chat_id, get_world_status(), 'HTML', null, null, null, $menu);
        return;
    }

    if ($text === "Magic Button") {
        $client->sendMessage($chat_id, " 🎩🐇  ");
        $next = 'Features (coming soon):' . PHP_EOL . '- visual stats ' . PHP_EOL . '- more countries' . PHP_EOL . '- world peace';
        $client->sendMessage($chat_id, $next, null, null, null, null, $menu);
        return;
    }

    if ($text === "🇩🇪") {
        country_wrapper('DE', 'germany', $client, $chat_id, $menu);
        return;
    }

    if ($text === "🇬🇧") {
        country_wrapper('GB', 'united-kingdom', $client, $chat_id, $menu);
        return;
    }

    if ($text === "🇸🇪") {
        country_wrapper('sweden', 'SE', $client, $chat_id, $menu);
        return;
    }

    if ($text === "🇺🇸") {
        country_wrapper('US', 'united-states', $client, $chat_id, $menu);
        return;
    }

    if ($text === "🇮🇹") {
        country_wrapper('IT', 'italy', $client, $chat_id, $menu);
        return;
    }

    if ($text === "🇪🇸") {
        country_wrapper('ES', 'spain', $client, $chat_id, $menu);
        return;
    }

    if ($text === "🇧🇷") {
        country_wrapper('BR', 'brazil', $client, $chat_id, $menu);
        return;
    }

    if ($text === "🇷🇺") {
        country_wrapper('RU', 'russia', $client, $chat_id, $menu);
        return;
    }

    // received invalid / old command, show help / new menu:
    $client->sendMessage($chat_id, "Press a button to use me. 😏", null, null, null, null, $menu);

}


function country_wrapper($countrycode, $country, $client, $chat_id, $menu)
{
    sleep(1);

    // text stats
    $result = get_country_status($countrycode);

    $client->sendMessage($chat_id, $result, 'HTML', null, null, null, $menu);

    // check if api results are correct
    // $result contains results or error msg
    // print both to client, but halt on error
    if (substr($result, 0, 6) == 'Sorry') {
        return;
    }

    // delay request
    sleep(2);

    // image active
    $client->sendPhoto($chat_id, "https://codepunks.net/telegrambot/corona/public/image_active.php?v=2&country=$country&date=" . date("y-m-d-H"), null, null, null, null, $menu);

    // delay request
    sleep(2);

    // image history
    $client->sendPhoto($chat_id, "https://codepunks.net/telegrambot/corona/public/image.php?v=2&country=$country&date=" . date("y-m-d-H"), null, null, null, null, $menu);

}
