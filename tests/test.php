<?php

// ha ha, don't expect real tests here

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../public/restapi.php";
require_once __DIR__ . "/../public/settings.php";
require_once __DIR__ . "/../public/log.php";
require_once __DIR__ . "/../public/helper.php";
require_once __DIR__ . "/../public/stats.php";

#echo get_country_history($country = 'brazil', 'BR', $history = 30);
#echo get_country_history_table($country = 'germany', $history = 30);
echo get_country_status($countryCode = 'DE');
#echo get_world_status();
