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
                "text" => "🇫🇷",
            ],
            [
                "text" => "🇵🇱",
            ],
        ],
        [
            [
                "text" => "🇮🇹",
            ],
            [
                "text" => "🇪🇸",
            ],
            [
                "text" => "🇵🇹",
            ],
            [
                "text" => "🇬🇷",
            ],
        ],
        [
            [
                "text" => "🇳🇴",
            ],
            [
                "text" => "🇸🇪",
            ],
            [
                "text" => "🇫🇮",
            ],
            [
                "text" => "🇩🇰",
            ],
        ],
        [
            [
                "text" => "🇦🇹",
            ],
            [
                "text" => "🇨🇭",
            ],
            [
                "text" => "🇳🇱",
            ],
            [
                "text" => "🇧🇪",
            ],
        ],
        [
            [
                "text" => "🇬🇧",
            ],
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
        [
            [
                "text" => "🇨🇳",
            ],
            [
                "text" => "🇯🇵",
            ],
            [
                "text" => "🇰🇷",
            ],
            [
                "text" => "🇮🇩",
            ],
        ],
        [
            [
                "text" => "🇦🇺",
            ],
            [
                "text" => "🇳🇿",
            ],
            [
                "text" => "🇮🇱",
            ],
            [
                "text" => "🇮🇳",
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
        //$client->sendMessage($chat_id, get_my_error_message_no_data(), 'HTML', null, null, null, $menu);
        return;
    }

    if ($text === "Magic Button") {
        $client->sendMessage($chat_id, " 🎩🐇  ");
        $next = 'Features (coming soon):' . PHP_EOL . '- visual stats ' . PHP_EOL . '- more countries' . PHP_EOL . '- world peace';
        $client->sendMessage($chat_id, $next, null, null, null, null, $menu);
        return;
    }

    $countrys = array(
        "🇩🇪" => array('DE', 'germany'),
        '🇫🇷' => array('FR', 'france'),
        '🇬🇧' => array('GB', 'united-kingdom'),
        '🇸🇪' => array('SE', 'sweden'),
        '🇺🇸' => array('US', 'united-states'),
        '🇮🇹' => array('IT', 'italy'),
        '🇪🇸' => array('ES', 'spain'),
        '🇧🇷' => array('BR', 'brazil'),
        '🇷🇺' => array('RU', 'russia'),
        '🇨🇳' => array('CN', 'china'),
        '🇯🇵' => array('JP', 'japan'),
        '🇰🇷' => array('KR', 'korea-south'),
        '🇦🇺' => array('AU', 'australia'),
        '🇳🇿' => array('NZ', 'new-zealand'),
        '🇦🇹' => array('AT', 'austria'),
        '🇨🇭' => array('CH', 'switzerland'),
        '🇳🇱' => array('NL', 'netherlands'),
        '🇮🇱' => array('IL', 'israel'),
        '🇮🇳' => array('IN', 'india'),
        '🇵🇱' => array('PL', 'poland'),
        '🇵🇹' => array('PT', 'portugal'),
        '🇳🇴' => array('NO', 'norway'),
        '🇫🇮' => array('FI', 'finland'),
        '🇩🇰' => array('DK', 'denmark'),
        '🇬🇷' => array('GR', 'greece'),
        '🇧🇪' => array('BE', 'belgium'),
        '🇮🇩' => array('ID', 'indonesia'),
    );

    if (array_key_exists($text, $countrys)) {
        country_wrapper($countrys[$text][0], $countrys[$text][1], $client, $chat_id, $menu);
        return;
    }

    // received invalid / old command, show help / new menu:
    $client->sendMessage($chat_id, "Press a button to use me. 😏", null, null, null, null, $menu);

}


function country_wrapper($countrycode, $country, $client, $chat_id, $menu)
{
    // text stats
    $result = get_country_status($countrycode);

    // check if api results are correct
    // $result contains results or error msg
    if (substr($result, 0, 5) == 'Sorry') {

        // attempt one retry after delay
        sleep(2);
        $result = get_country_status($countrycode);

    }

    // send result
    $client->sendMessage($chat_id, $result, 'HTML', null, null, null, $menu);

    // halt on error as charts would be broken
    if (substr($result, 0, 5) == 'Sorry') return;


    // delay next request
    sleep(1);

    // image active
    $client->sendPhoto($chat_id, "https://codepunks.net/telegrambot/corona/public/image_active.php?v=10&country=$country&date=" . date("y-m-d-H"), null, null, null, null, null);


    // delay next request
    #sleep(2);

    // image history
    #$client->sendPhoto($chat_id, "https://codepunks.net/telegrambot/corona/public/image.php?v=3&country=$country&date=" . date("y-m-d-H"), null, null, null, null, $menu);

    // show error: outdated data
    //$client->sendMessage($chat_id, get_my_error_message_no_data(), 'HTML', null, null, null, $menu);

}
